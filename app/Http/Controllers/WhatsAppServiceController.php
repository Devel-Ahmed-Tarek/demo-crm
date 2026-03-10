<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Lead;

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
                $authHeader => $authPrefix . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($baseUrl . '/api/whatsapp/send-message', $payload);

        if (!$response->successful()) {
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

        if (!$apiKey) {
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

        if (!$ok) {
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

        if (!$apiKey) {
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
            $message .= ' ' . __('فشل الإرسال إلى :failed ليد.', ['failed' => $failed]);
        }

        return redirect()->route('whatsapp.services.index', ['tab' => 'leads'])->with('success_leads', $message);
    }
}

