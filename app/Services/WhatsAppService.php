<?php

namespace App\Services;

use App\Support\WhatsApp\WhatsAppChatsNormalizer;
use App\Support\WhatsApp\WhatsAppMessagesHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

/**
 * طبقة موحّدة فوق WhatsBridge (نفس خطوات التوثيق: جلسة، شاتات، رسائل، إرسال، بروكسي ميديا).
 */
class WhatsAppService
{
    public function __construct(
        protected WhatsBridgeService $bridge
    ) {}

    public function resolveSessionId(?Request $request = null): ?string
    {
        if ($request !== null) {
            $s = $request->input('session_id') ?? $request->input('sessionId');
            if (is_string($s) && trim($s) !== '') {
                return trim($s);
            }
        }

        return $this->bridge->resolveSessionId();
    }

    /**
     * @return array{ok: bool, status: int|null, body: mixed, error?: string}
     */
    public function getChats(string $sessionId, int $limit = 50, int $offset = 0, bool $refresh = false): array
    {
        return $this->bridge->getChats($sessionId, $limit, $offset, $refresh);
    }

    /**
     * @return array{ok: bool, status: int|null, body: mixed, error?: string}
     */
    public function getMessages(string $sessionId, string $chatId, int $limit = 50, int $offset = 0, string $order = 'desc'): array
    {
        return $this->bridge->getMessages($sessionId, $chatId, $limit, $offset, $order);
    }

    /**
     * @param  array<int, mixed>  $messages
     * @return array<int, mixed>
     */
    public function sortMessagesOldestFirst(array $messages): array
    {
        return WhatsAppMessagesHelper::sortOldestFirst($messages);
    }

    public function normalizeChatsPayload(mixed $body): mixed
    {
        return WhatsAppChatsNormalizer::normalizePayload($body);
    }

    /**
     * استخراج مصفوفة رسائل من جسم JSON متداخل.
     *
     * @return array<int, mixed>
     */
    public function extractMessagesArray(mixed $body): array
    {
        if (! is_array($body)) {
            return [];
        }
        if (isset($body['messages']) && is_array($body['messages'])) {
            return $body['messages'];
        }
        if (isset($body['data']['messages']) && is_array($body['data']['messages'])) {
            return $body['data']['messages'];
        }
        if (isset($body['data']['data']['messages']) && is_array($body['data']['data']['messages'])) {
            return $body['data']['data']['messages'];
        }
        if (isset($body['data']) && is_array($body['data']) && $this->isListArray($body['data'])) {
            return $body['data'];
        }

        return [];
    }

    /**
     * @param  array<mixed>  $arr
     */
    protected function isListArray(array $arr): bool
    {
        if ($arr === []) {
            return true;
        }

        return array_keys($arr) === range(0, count($arr) - 1);
    }

    public function proxyMessageMedia(Request $request): Response
    {
        $data = $request->validate([
            'message_id' => ['required', 'string', 'max:4096'],
            'chat_id' => ['nullable', 'string', 'max:512'],
        ]);

        $baseUrl = rtrim(config('services.whatsbridge.base_url'), '/');
        $mediaBase = rtrim((string) (config('services.whatsbridge.media_base_url') ?: $baseUrl), '/');
        $apiKey = config('services.whatsbridge.api_key');
        $authHeader = config('services.whatsbridge.auth_header', 'Authorization');
        $authPrefix = config('services.whatsbridge.auth_prefix', 'Bearer ');

        if (! $apiKey || ! $baseUrl) {
            abort(503);
        }

        $sessionId = $this->resolveSessionId($request);
        if (! $sessionId) {
            abort(503);
        }

        $configuredPath = (string) config('services.whatsbridge.media_path', '/message-media');
        if ($configuredPath === '' || $configuredPath[0] !== '/') {
            $configuredPath = '/'.$configuredPath;
        }

        $pathsToTry = array_values(array_unique(array_filter([
            $configuredPath,
            $configuredPath !== '/api/whatsapp/message-media' ? '/api/whatsapp/message-media' : null,
            $configuredPath !== '/message-media' ? '/message-media' : null,
        ])));

        $headers = [
            $authHeader => $authPrefix.$apiKey,
        ];

        $paramVariants = [];
        $baseOnly = [
            'sessionId' => $sessionId,
            'messageId' => $data['message_id'],
        ];
        if (! empty($data['chat_id'])) {
            $paramVariants[] = array_merge($baseOnly, [
                'chatId' => $data['chat_id'],
                'chat_id' => $data['chat_id'],
            ]);
        }
        $paramVariants[] = $baseOnly;

        $response = null;
        $body = '';

        foreach ($pathsToTry as $mediaPath) {
            foreach ($paramVariants as $queryParams) {
                $upstream = $mediaBase.$mediaPath.'?'.http_build_query($queryParams);

                try {
                    $response = Http::accept('*/*')
                        ->timeout(120)
                        ->withHeaders($headers)
                        ->get($upstream);
                } catch (\Throwable $e) {
                    abort(502);
                }

                if (! $response->successful()) {
                    if ($response->status() !== 404) {
                        abort($response->status() >= 400 && $response->status() < 600 ? $response->status() : 502);
                    }

                    continue;
                }

                $body = $response->body();
                $trim = ltrim($body);
                if ($trim === '' || strlen($body) < 16) {
                    continue;
                }
                if ($trim[0] === '<' || str_starts_with($trim, '<!')) {
                    continue;
                }

                break 2;
            }
        }

        if ($body === '' || ! $response || ! $response->successful()) {
            abort(404);
        }

        $ctype = $response->header('Content-Type');
        if (! $ctype || str_contains(strtolower((string) $ctype), 'text/html')) {
            $ctype = 'audio/ogg';
        }

        return response($body, 200)
            ->header('Content-Type', $ctype)
            ->header('Content-Length', (string) strlen($body))
            ->header('Accept-Ranges', 'bytes')
            ->header('Cache-Control', 'private, max-age=300');
    }

    /**
     * إرسال نص لمحادثة (مجموعة / رقم / JID مثل @lid).
     */
    public function sendMessageToChat(string $sessionId, string $chatId, string $message): bool
    {
        $baseUrl = rtrim(config('services.whatsbridge.base_url'), '/');
        $apiKey = config('services.whatsbridge.api_key');
        $authHeader = config('services.whatsbridge.auth_header', 'Authorization');
        $authPrefix = config('services.whatsbridge.auth_prefix', 'Bearer ');

        if (! $apiKey || ! $baseUrl) {
            return false;
        }

        if (str_ends_with($chatId, '@g.us')) {
            return $this->sendGroupMessage($sessionId, $chatId, $message, $baseUrl, $apiKey, $authHeader, $authPrefix);
        }

        $phone = $this->chatIdToPhoneNumber($chatId);
        if ($phone !== null) {
            return $this->bridge->sendMessage($phone, $message);
        }

        return $this->bridge->sendMessageToChatJid($sessionId, $chatId, $message);
    }

    protected function chatIdToPhoneNumber(string $chatId): ?string
    {
        if (preg_match('/^(\d+)@c\.us$/', $chatId, $m)) {
            return $m[1];
        }

        if (preg_match('/^(\d+)@s\.whatsapp\.net$/', $chatId, $m)) {
            return $m[1];
        }

        return null;
    }

    protected function sendGroupMessage(
        string $sessionId,
        string $groupId,
        string $message,
        string $baseUrl,
        string $apiKey,
        string $authHeader,
        string $authPrefix
    ): bool {
        $payload = [
            'sessionId' => $sessionId,
            'groupId' => $groupId,
            'message' => $message,
            'api_key' => $apiKey,
        ];

        $response = Http::acceptJson()
            ->withHeaders([
                $authHeader => $authPrefix.$apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($baseUrl.'/api/whatsapp/group/send-message', $payload);

        return $response->successful();
    }

    /**
     * إرسال ميديا (base64) نحو WhatsBridge.
     */
    public function sendMedia(
        string $sessionId,
        string $phoneNumber,
        string $base64Data,
        string $mimeType,
        string $type,
        ?string $caption = null
    ): bool {
        $baseUrl = rtrim(config('services.whatsbridge.base_url'), '/');
        $apiKey = config('services.whatsbridge.api_key');
        $authHeader = config('services.whatsbridge.auth_header', 'Authorization');
        $authPrefix = config('services.whatsbridge.auth_prefix', 'Bearer ');

        if (! $apiKey || ! $baseUrl) {
            return false;
        }

        $payload = [
            'sessionId' => $sessionId,
            'phoneNumber' => $phoneNumber,
            'type' => $type,
            'mimeType' => $mimeType,
            'data' => $base64Data,
            'caption' => $caption ?? '',
            'api_key' => $apiKey,
        ];

        try {
            $response = Http::acceptJson()
                ->timeout(120)
                ->withHeaders([
                    $authHeader => $authPrefix.$apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($baseUrl.'/api/whatsapp/send-media', $payload);
        } catch (\Throwable $e) {
            return false;
        }

        return $response->successful();
    }
}
