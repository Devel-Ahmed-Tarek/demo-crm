<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MessagesController extends Controller
{
    public function index(Request $request, WhatsAppService $whatsapp)
    {
        $sessionId = $whatsapp->resolveSessionId($request);
        $chatId = $request->query('chat_id');

        return view('customer.messages', [
            'resolvedSessionId' => $sessionId,
            'initialChatId' => is_string($chatId) ? $chatId : null,
            'pollIntervalMs' => max(500, (int) config('whatsapp.poll_interval_ms', 1000)),
            'markReadSupported' => (bool) config('whatsapp.mark_read_supported', false),
        ]);
    }

    public function send(Request $request, WhatsAppService $whatsapp): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $wantsJson = $request->wantsJson()
            || $request->ajax()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';

        $data = $request->validate([
            'session_id' => ['nullable', 'string', 'max:256'],
            'sessionId' => ['nullable', 'string', 'max:256'],
            'chat_id' => ['nullable', 'string', 'max:512'],
            'chatId' => ['nullable', 'string', 'max:512'],
            'message' => ['required', 'string', 'max:4096'],
        ]);

        $chatId = $data['chat_id'] ?? $data['chatId'] ?? '';
        if ($chatId === '') {
            return $this->sendError($wantsJson, __('معرّف المحادثة مطلوب (chat_id).'), 422);
        }
        $message = $data['message'];

        $apiKey = config('services.whatsbridge.api_key');
        if (! $apiKey) {
            return $this->sendError($wantsJson, __('WhatsBridge API Key غير مُعد.'), 422);
        }

        $sessionId = $whatsapp->resolveSessionId($request);
        if (! $sessionId) {
            return $this->sendError($wantsJson, __('لم تُحدد جلسة واتساب.'), 422);
        }

        try {
            $ok = $whatsapp->sendMessageToChat($sessionId, $chatId, $message);
        } catch (\Throwable $e) {
            return $this->sendError($wantsJson, __('فشل الاتصال بخدمة WhatsBridge.'), 502);
        }

        if (! $ok) {
            return $this->sendError($wantsJson, __('فشل إرسال الرسالة.'), 422);
        }

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => __('تم إرسال الرسالة.'),
                'data' => [],
            ]);
        }

        return redirect()
            ->route('customer.messages', ['chat_id' => $chatId])
            ->with('success', __('تم إرسال الرسالة.'));
    }

    public function sendMedia(Request $request, WhatsAppService $whatsapp): JsonResponse
    {
        $data = $request->validate([
            'session_id' => ['nullable', 'string', 'max:256'],
            'sessionId' => ['nullable', 'string', 'max:256'],
            'phone_number' => ['required', 'string', 'max:512'],
            'phoneNumber' => ['nullable', 'string', 'max:512'],
            'chat_id' => ['nullable', 'string', 'max:512'],
            'caption' => ['nullable', 'string', 'max:2000'],
            'media' => ['required', 'file', 'max:16384'],
        ]);

        $apiKey = config('services.whatsbridge.api_key');
        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => __('WhatsBridge API Key غير مُعد.'),
                'data' => null,
            ], 422);
        }

        $sessionId = $whatsapp->resolveSessionId($request);
        if (! $sessionId) {
            return response()->json([
                'success' => false,
                'message' => __('لم تُحدد جلسة واتساب.'),
                'data' => null,
            ], 422);
        }

        $phone = $data['phone_number'] ?? $data['phoneNumber'] ?? '';
        $file = $request->file('media');
        $mime = $file->getMimeType() ?: 'application/octet-stream';
        $type = $this->guessMediaType((string) $mime);
        $base64 = base64_encode((string) file_get_contents($file->getRealPath()));

        $ok = $whatsapp->sendMedia(
            $sessionId,
            $phone,
            $base64,
            $mime,
            $type,
            $data['caption'] ?? null
        );

        if (! $ok) {
            return response()->json([
                'success' => false,
                'message' => __('فشل إرسال الميديا.'),
                'data' => null,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => __('تم إرسال الملف.'),
            'data' => [],
        ]);
    }

    public function poll(Request $request, WhatsAppService $whatsapp): JsonResponse
    {
        $request->validate([
            'chat_id' => ['required', 'string', 'max:512'],
            'chatId' => ['nullable', 'string', 'max:512'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $chatId = $request->input('chat_id') ?? $request->input('chatId');
        $limit = min(100, max(1, (int) $request->input('limit', 40)));

        $sessionId = $whatsapp->resolveSessionId($request);
        if (! $sessionId) {
            return response()->json([
                'success' => false,
                'message' => __('لم تُحدد جلسة واتساب.'),
                'data' => null,
            ], 422);
        }

        $result = $whatsapp->getMessages($sessionId, (string) $chatId, $limit, 0, 'desc');
        if (! $result['ok']) {
            return response()->json([
                'success' => false,
                'message' => __('تعذر جلب الرسائل.'),
                'data' => ['status' => $result['status'], 'body' => $result['body']],
            ], 502);
        }

        $messages = $whatsapp->extractMessagesArray($result['body']);
        $messages = $whatsapp->sortMessagesOldestFirst($messages);

        return response()->json([
            'success' => true,
            'message' => 'ok',
            'data' => [
                'messages' => $messages,
            ],
        ]);
    }

    public function loadMore(Request $request, WhatsAppService $whatsapp): JsonResponse
    {
        $request->validate([
            'chat_id' => ['required', 'string', 'max:512'],
            'chatId' => ['nullable', 'string', 'max:512'],
            'offset' => ['nullable', 'integer', 'min:0'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $chatId = $request->input('chat_id') ?? $request->input('chatId');
        $offset = max(0, (int) $request->input('offset', 0));
        $limit = min(100, max(1, (int) $request->input('limit', 30)));

        $sessionId = $whatsapp->resolveSessionId($request);
        if (! $sessionId) {
            return response()->json([
                'success' => false,
                'message' => __('لم تُحدد جلسة واتساب.'),
                'data' => null,
            ], 422);
        }

        $result = $whatsapp->getMessages($sessionId, (string) $chatId, $limit, $offset, 'desc');
        if (! $result['ok']) {
            return response()->json([
                'success' => false,
                'message' => __('تعذر جلب الرسائل.'),
                'data' => null,
            ], 502);
        }

        $messages = $whatsapp->extractMessagesArray($result['body']);
        $messages = $whatsapp->sortMessagesOldestFirst($messages);

        $hasMore = count($messages) >= $limit;
        $nextOffset = $offset + count($messages);

        return response()->json([
            'success' => true,
            'message' => 'ok',
            'data' => [
                'messages' => $messages,
                'hasMore' => $hasMore,
                'nextOffset' => $nextOffset,
            ],
        ]);
    }

    public function media(Request $request, WhatsAppService $whatsapp): Response
    {
        return $whatsapp->proxyMessageMedia($request);
    }

    public function markSeen(Request $request): JsonResponse
    {
        if (! config('whatsapp.mark_read_supported')) {
            return response()->json([
                'success' => false,
                'message' => __('غير مفعّل في الإعدادات.'),
                'data' => null,
            ], 501);
        }

        return response()->json([
            'success' => true,
            'message' => 'noop',
            'data' => [],
        ]);
    }

    protected function guessMediaType(string $mime): string
    {
        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mime, 'audio/')) {
            return 'audio';
        }
        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }

        return 'document';
    }

    protected function sendError(bool $wantsJson, string $message, int $status): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if ($wantsJson) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
            ], $status);
        }

        return back()->withInput()->withErrors(['customer_messages' => $message]);
    }
}
