<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsBridgeService
{
    public function sendMessage(string $phoneNumber, string $message): bool
    {
        $baseUrl = rtrim(config('services.whatsbridge.base_url'), '/');
        $apiKey = config('services.whatsbridge.api_key');
        $authHeader = config('services.whatsbridge.auth_header', 'Authorization');
        $authPrefix = config('services.whatsbridge.auth_prefix', 'Bearer ');

        if (! $apiKey || ! $baseUrl) {
            return false;
        }

        $payload = [
            'phoneNumber' => $this->normalizePhone($phoneNumber),
            'message' => $message,
            'api_key' => $apiKey,
        ];

        $response = Http::acceptJson()
            ->withHeaders([
                $authHeader => $authPrefix.$apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($baseUrl.'/api/whatsapp/send-message', $payload);

        return $response->successful();
    }

    /**
     * إرسال لمعرف محادثة كامل (JID) مثل @lid أو أي صيغة لا تُستخرج منها أرقام لـ phoneNumber.
     * يطابق سلوك Baileys: sendMessage(jid, ...). جرّب أكثر من شكل للبادي حسب إصدارات WhatsBridge.
     */
    public function sendMessageToChatJid(string $sessionId, string $chatJid, string $message): bool
    {
        $baseUrl = rtrim(config('services.whatsbridge.base_url'), '/');
        $apiKey = config('services.whatsbridge.api_key');
        $authHeader = config('services.whatsbridge.auth_header', 'Authorization');
        $authPrefix = config('services.whatsbridge.auth_prefix', 'Bearer ');

        if (! $apiKey || ! $baseUrl) {
            return false;
        }

        $headers = [
            $authHeader => $authPrefix.$apiKey,
            'Content-Type' => 'application/json',
        ];

        $url = $baseUrl.'/api/whatsapp/send-message';

        $variants = [
            [
                'sessionId' => $sessionId,
                'chatId' => $chatJid,
                'message' => $message,
                'api_key' => $apiKey,
            ],
            [
                'sessionId' => $sessionId,
                'jid' => $chatJid,
                'message' => $message,
                'api_key' => $apiKey,
            ],
        ];

        foreach ($variants as $payload) {
            try {
                $response = Http::acceptJson()
                    ->timeout(60)
                    ->withHeaders($headers)
                    ->post($url, $payload);
            } catch (\Throwable $e) {
                continue;
            }

            if ($response->successful()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Session used for chats/messages APIs (query param sessionId).
     * 1) WHATSBRIDGE_SESSION_ID إن وُجد
     * 2) استنتاج من مفتاح wb_<base64>.<sig> (يُفك غالباً إلى sess_<uuid>)
     * 3) أول جلسة من GET /api/whatsapp/sessions
     */
    public function resolveSessionId(): ?string
    {
        $configured = config('services.whatsbridge.session_id');
        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        $apiKey = config('services.whatsbridge.api_key');
        $fromKey = is_string($apiKey) ? $this->parseSessionIdFromApiKey($apiKey) : null;
        if ($fromKey !== null && $fromKey !== '') {
            return $fromKey;
        }

        $sessions = $this->getSessionsRaw();
        if ($sessions === null) {
            return null;
        }

        $list = $sessions['sessions'] ?? $sessions['data'] ?? $sessions;
        if (! is_array($list)) {
            return null;
        }

        if (isset($list[0]) && is_array($list[0])) {
            $first = $list[0];

            return $first['sessionId'] ?? $first['id'] ?? $first['name'] ?? null;
        }

        return null;
    }

    /**
     * مفاتيح WhatsBridge من الشكل: wb_<حمولة_base64>.<توقيع>
     * الحمولة تُفك غالباً إلى sess_<uuid> — نُرجع النص كاملاً أو الـ UUID حسب التنسيق.
     */
    protected function parseSessionIdFromApiKey(string $apiKey): ?string
    {
        if (! str_starts_with($apiKey, 'wb_')) {
            return null;
        }

        $rest = substr($apiKey, 3);
        $dotPos = strpos($rest, '.');
        if ($dotPos === false) {
            return null;
        }

        $payloadB64 = substr($rest, 0, $dotPos);
        $payloadB64 = strtr($payloadB64, '-_', '+/');
        $pad = strlen($payloadB64) % 4;
        if ($pad > 0) {
            $payloadB64 .= str_repeat('=', 4 - $pad);
        }

        $decoded = base64_decode($payloadB64, true);
        if ($decoded === false || $decoded === '') {
            return null;
        }

        $decoded = trim($decoded);

        if (preg_match('/^sess_[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $decoded)) {
            return $decoded;
        }

        if (preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $decoded)) {
            return $decoded;
        }

        if (str_starts_with($decoded, 'sess_')) {
            return $decoded;
        }

        return null;
    }

    /**
     * @return array{ok: bool, status: int|null, body: mixed, error?: string}
     */
    public function getChats(string $sessionId, int $limit = 50, int $offset = 0, bool $refresh = false): array
    {
        return $this->getJson('/api/whatsapp/chats', [
            'sessionId' => $sessionId,
            'limit' => $limit,
            'offset' => $offset,
            'refresh' => $refresh ? 'true' : 'false',
        ]);
    }

    /**
     * @return array{ok: bool, status: int|null, body: mixed, error?: string}
     */
    public function getMessages(string $sessionId, string $chatId, int $limit = 50, int $offset = 0, string $order = 'desc'): array
    {
        return $this->getJson('/api/whatsapp/messages', [
            'sessionId' => $sessionId,
            'chatId' => $chatId,
            'limit' => $limit,
            'offset' => $offset,
            'order' => $order,
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getSessionsRaw(): ?array
    {
        $baseUrl = rtrim(config('services.whatsbridge.base_url'), '/');
        $apiKey = config('services.whatsbridge.api_key');
        if (! $apiKey || ! $baseUrl) {
            return null;
        }

        $authHeader = config('services.whatsbridge.auth_header', 'Authorization');
        $authPrefix = config('services.whatsbridge.auth_prefix', 'Bearer ');

        $response = Http::acceptJson()
            ->withHeaders([
                $authHeader => $authPrefix.$apiKey,
            ])
            ->get($baseUrl.'/api/whatsapp/sessions');

        if (! $response->successful()) {
            return null;
        }

        $json = $response->json();

        return is_array($json) ? $json : null;
    }

    /**
     * @return array{ok: bool, status: int|null, body: mixed, error?: string}
     */
    protected function getJson(string $path, array $query): array
    {
        $baseUrl = rtrim(config('services.whatsbridge.base_url'), '/');
        $apiKey = config('services.whatsbridge.api_key');
        if (! $apiKey || ! $baseUrl) {
            return ['ok' => false, 'status' => null, 'body' => null, 'error' => 'missing_config'];
        }

        $authHeader = config('services.whatsbridge.auth_header', 'Authorization');
        $authPrefix = config('services.whatsbridge.auth_prefix', 'Bearer ');

        try {
            $response = Http::acceptJson()
                ->timeout(60)
                ->withHeaders([
                    $authHeader => $authPrefix.$apiKey,
                ])
                ->get($baseUrl.$path, $query);
        } catch (\Throwable $e) {
            return ['ok' => false, 'status' => null, 'body' => null, 'error' => 'request_failed'];
        }

        return [
            'ok' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->json(),
        ];
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        $phone = ltrim($phone, '+0');

        return $phone;
    }
}
