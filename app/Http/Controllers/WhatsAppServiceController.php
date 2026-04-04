<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Services\WhatsAppService;
use App\Services\WhatsBridgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppServiceController extends Controller
{
    public function index(Request $request)
    {
        $stageFilter = $request->get('stage');
        $sourceFilter = $request->get('source');

        $leadQuery = Lead::query()
            ->select(['id', 'name', 'phone', 'stage', 'source'])
            ->latest();

        if ($stageFilter) {
            $leadQuery->where('stage', $stageFilter);
        }

        if ($sourceFilter) {
            $leadQuery->where('source', $sourceFilter);
        }

        return view('whatsapp.services', [
            'baseUrl' => config('services.whatsbridge.base_url'),
            'leads' => $leadQuery->take(50)->get(),
            'stages' => Lead::whereNotNull('stage')->distinct()->orderBy('stage')->pluck('stage'),
            'sources' => Lead::whereNotNull('source')->distinct()->orderBy('source')->pluck('source'),
            'currentStage' => $stageFilter,
            'currentSource' => $sourceFilter,
        ]);
    }

    protected function sendSingleMessage(string $phoneNumber, string $message, string $baseUrl, string $apiKey, string $authHeader, string $authPrefix): bool
    {
        $payload = [
            'phoneNumber' => $phoneNumber,
            'message' => $message,
            'api_key' => $apiKey,
        ];

        $response = Http::acceptJson()
            ->withHeaders([
                $authHeader => $authPrefix.$apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($baseUrl.'/api/whatsapp/send-message', $payload);

        if (! $response->successful()) {
            return false;
        }

        return true;
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'phone_number' => ['required', 'string'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $baseUrl = rtrim(config('services.whatsbridge.base_url'), '/');
        $apiKey = config('services.whatsbridge.api_key');
        $authHeader = config('services.whatsbridge.auth_header', 'Authorization');
        $authPrefix = config('services.whatsbridge.auth_prefix', 'Bearer ');

        $tab = $request->get('tab', 'single');

        if (! $apiKey) {
            return redirect()->route('whatsapp.services.index', ['tab' => $tab])
                ->withInput()
                ->withErrors(['whatsapp' => __('WhatsBridge API Key غير مُعد في البيئة (WHATSBRIDGE_API_KEY).')]);
        }

        try {
            $ok = $this->sendSingleMessage($data['phone_number'], $data['message'], $baseUrl, $apiKey, $authHeader, $authPrefix);
        } catch (\Throwable $e) {
            return redirect()->route('whatsapp.services.index', ['tab' => $tab])
                ->withInput()
                ->withErrors(['whatsapp' => __('فشل الاتصال بخدمة WhatsBridge. جرّب مرة أخرى.')]);
        }

        if (! $ok) {
            return redirect()->route('whatsapp.services.index', ['tab' => $tab])
                ->withInput()
                ->withErrors(['whatsapp' => __('فشل إرسال الرسالة. تحقق من الرقم أو حاول لاحقاً.')]);
        }

        return redirect()->route('whatsapp.services.index', ['tab' => $tab])->with('success', __('تم إرسال الرسالة بنجاح عبر WhatsBridge.'));
    }

    public function sendToLeads(Request $request)
    {
        $data = $request->validate([
            'lead_ids' => ['required', 'array'],
            'lead_ids.*' => ['integer', 'exists:leads,id'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $baseUrl = rtrim(config('services.whatsbridge.base_url'), '/');
        $apiKey = config('services.whatsbridge.api_key');
        $authHeader = config('services.whatsbridge.auth_header', 'Authorization');
        $authPrefix = config('services.whatsbridge.auth_prefix', 'Bearer ');

        if (! $apiKey) {
            return back()
                ->withInput()
                ->withErrors(['whatsapp_leads' => __('WhatsBridge API Key غير مُعد في البيئة (WHATSBRIDGE_API_KEY).')]);
        }

        $leads = Lead::whereIn('id', $data['lead_ids'])
            ->whereNotNull('phone')
            ->get(['id', 'name', 'phone']);

        if ($leads->isEmpty()) {
            return redirect()->route('whatsapp.services.index', ['tab' => 'leads'])
                ->withInput()
                ->withErrors(['whatsapp_leads' => __('لا يوجد أي ليد مختار لديه رقم هاتف صالح.')]);
        }

        $sent = 0;
        $failed = 0;

        foreach ($leads as $lead) {
            try {
                $ok = $this->sendSingleMessage($lead->phone, $data['message'], $baseUrl, $apiKey, $authHeader, $authPrefix);
                if ($ok) {
                    $sent++;
                } else {
                    $failed++;
                }
            } catch (\Throwable $e) {
                $failed++;
            }
        }

        if ($sent === 0) {
            return redirect()->route('whatsapp.services.index', ['tab' => 'leads'])
                ->withInput()
                ->withErrors(['whatsapp_leads' => __('فشل إرسال الرسائل لكل الليدز المختارة. حاول لاحقاً.')]);
        }

        $message = __('تم إرسال الرسالة بنجاح إلى :sent ليد.', ['sent' => $sent]);
        if ($failed > 0) {
            $message .= ' '.__('فشل الإرسال إلى :failed ليد.', ['failed' => $failed]);
        }

        return redirect()->route('whatsapp.services.index', ['tab' => 'leads'])->with('success_leads', $message);
    }

    public function apiChats(Request $request, WhatsBridgeService $bridge, WhatsAppService $whatsapp): JsonResponse
    {
        $apiKey = config('services.whatsbridge.api_key');
        if (! $apiKey) {
            return response()->json(['ok' => false, 'message' => __('WhatsBridge API Key غير مُعد.')], 422);
        }

        $sessionId = $whatsapp->resolveSessionId($request);
        if (! $sessionId) {
            return response()->json([
                'ok' => false,
                'message' => __('لم يُعثر على جلسة واتساب. عيّن WHATSBRIDGE_SESSION_ID أو تأكد من اتصال الحساب بـ WhatsBridge.'),
            ], 422);
        }

        $limit = min(100, max(1, (int) $request->get('limit', 50)));
        $offset = max(0, (int) $request->get('offset', 0));
        $refresh = filter_var($request->get('refresh', false), FILTER_VALIDATE_BOOLEAN);

        $result = $bridge->getChats($sessionId, $limit, $offset, $refresh);

        if (! $result['ok']) {
            return response()->json([
                'ok' => false,
                'message' => __('تعذر جلب قائمة المحادثات من WhatsBridge.'),
                'status' => $result['status'],
                'body' => $result['body'],
            ], 502);
        }

        $body = $result['body'];
        if (is_array($body)) {
            $body = $whatsapp->normalizeChatsPayload($body);
        }

        return response()->json([
            'ok' => true,
            'session_id' => $sessionId,
            'data' => $body,
        ]);
    }

    public function apiMessages(Request $request, WhatsBridgeService $bridge, WhatsAppService $whatsapp): JsonResponse
    {
        $apiKey = config('services.whatsbridge.api_key');
        if (! $apiKey) {
            return response()->json(['ok' => false, 'message' => __('WhatsBridge API Key غير مُعد.')], 422);
        }

        $request->validate([
            'chat_id' => ['required', 'string', 'max:512'],
        ]);

        $sessionId = $whatsapp->resolveSessionId($request);
        if (! $sessionId) {
            return response()->json([
                'ok' => false,
                'message' => __('لم يُعثر على جلسة واتساب.'),
            ], 422);
        }

        $limit = min(100, max(1, (int) $request->get('limit', 50)));
        $offset = max(0, (int) $request->get('offset', 0));
        $order = $request->get('order', 'desc') === 'asc' ? 'asc' : 'desc';

        $result = $bridge->getMessages($sessionId, $request->input('chat_id'), $limit, $offset, $order);

        if (! $result['ok']) {
            return response()->json([
                'ok' => false,
                'message' => __('تعذر جلب الرسائل من WhatsBridge.'),
                'status' => $result['status'],
                'body' => $result['body'],
            ], 502);
        }

        return response()->json([
            'ok' => true,
            'data' => $result['body'],
        ]);
    }

    /**
     * بروكسي لتشغيل الوسائط من نفس أصل الـ CRM (يتفادى CORS ويمرّر مفتاح WhatsBridge من السيرفر).
     */
    public function proxyMessageMedia(Request $request, WhatsAppService $whatsapp)
    {
        return $whatsapp->proxyMessageMedia($request);
    }

    /**
     * رد من تبويب المحادثات: محادثة فردية أو مجموعة.
     */
    public function sendChatReply(Request $request, WhatsAppService $whatsapp)
    {
        $wantsJson = $request->wantsJson()
            || $request->ajax()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';

        $data = $request->validate([
            'chat_id' => ['required', 'string', 'max:512'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $apiKey = config('services.whatsbridge.api_key');
        $baseUrl = rtrim(config('services.whatsbridge.base_url'), '/');

        if (! $apiKey || ! $baseUrl) {
            return $wantsJson
                ? response()->json(['ok' => false, 'message' => __('WhatsBridge غير مُعد في البيئة.')], 422)
                : redirect()->route('whatsapp.services.index', ['tab' => 'chats'])
                    ->withInput()
                    ->withErrors(['whatsapp_chats' => __('WhatsBridge غير مُعد في البيئة.')]);
        }

        $sessionId = $whatsapp->resolveSessionId($request);
        if (! $sessionId) {
            return $wantsJson
                ? response()->json(['ok' => false, 'message' => __('لم تُحدد جلسة واتساب.')], 422)
                : redirect()->route('whatsapp.services.index', ['tab' => 'chats'])
                    ->withInput()
                    ->withErrors(['whatsapp_chats' => __('لم تُحدد جلسة واتساب (WHATSBRIDGE_SESSION_ID).')]);
        }

        $chatId = $data['chat_id'];
        $message = $data['message'];

        try {
            $ok = $whatsapp->sendMessageToChat($sessionId, $chatId, $message);
        } catch (\Throwable $e) {
            return $wantsJson
                ? response()->json(['ok' => false, 'message' => __('فشل الاتصال بخدمة WhatsBridge.')], 502)
                : redirect()->route('whatsapp.services.index', ['tab' => 'chats'])
                    ->withInput()
                    ->withErrors(['whatsapp_chats' => __('فشل الاتصال بخدمة WhatsBridge.')]);
        }

        if (! $ok) {
            return $wantsJson
                ? response()->json(['ok' => false, 'message' => __('فشل إرسال الرسالة.')], 422)
                : redirect()->route('whatsapp.services.index', ['tab' => 'chats'])
                    ->withInput()
                    ->withErrors(['whatsapp_chats' => __('فشل إرسال الرسالة.')]);
        }

        return $wantsJson
            ? response()->json(['ok' => true, 'message' => __('تم إرسال الرسالة.')])
            : redirect()->route('whatsapp.services.index', ['tab' => 'chats', 'chat' => $chatId])
                ->with('success_chats', __('تم إرسال الرسالة.'));
    }
}
