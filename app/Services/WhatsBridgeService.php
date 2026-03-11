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

        if (!$apiKey || !$baseUrl) {
            return false;
        }

        $payload = [
            'phoneNumber' => $this->normalizePhone($phoneNumber),
            'message' => $message,
            'api_key' => $apiKey,
        ];

        $response = Http::acceptJson()
            ->withHeaders([
                $authHeader => $authPrefix . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($baseUrl . '/api/whatsapp/send-message', $payload);

        return $response->successful();
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        $phone = ltrim($phone, '+0');

        return $phone;
    }
}
