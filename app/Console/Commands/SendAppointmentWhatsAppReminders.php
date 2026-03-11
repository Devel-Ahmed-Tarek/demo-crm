<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\WhatsBridgeService;
use Illuminate\Console\Command;

class SendAppointmentWhatsAppReminders extends Command
{
    protected $signature = 'appointments:send-whatsapp-reminders';

    protected $description = 'إرسال تذكير واتساب للعملاء قبل مواعيدهم حسب الإعداد (مثلاً 24 ساعة)';

    public function handle(WhatsBridgeService $whatsBridge): int
    {
        if (!config('services.whatsbridge.api_key')) {
            $this->warn('WHATSBRIDGE_API_KEY غير مُعد. تخطي التذكيرات.');
            return 0;
        }

        $hours = config('services.whatsbridge.appointment_reminder_hours', 24);
        $template = config('services.whatsbridge.appointment_reminder_message', 'مرحباً :name، نذكرك بموعدك لدينا في :date الساعة :time.');

        $windowStart = now();
        $windowEnd = now()->addHours($hours);

        $appointments = Appointment::with('customer')
            ->where('status', 'scheduled')
            ->whereNull('whatsapp_reminder_sent_at')
            ->whereBetween('appointment_date', [$windowStart, $windowEnd])
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($appointments as $appointment) {
            $customer = $appointment->customer;
            if (!$customer || empty(trim($customer->phone ?? ''))) {
                $skipped++;
                continue;
            }

            $name = $customer->name ?: 'عميلنا';
            $date = $appointment->appointment_date->locale('ar')->translatedFormat('l j F Y');
            $time = $appointment->appointment_date->format('H:i');

            $message = str_replace(
                [':name', ':date', ':time'],
                [$name, $date, $time],
                $template
            );

            if ($whatsBridge->sendMessage($customer->phone, $message)) {
                $appointment->update(['whatsapp_reminder_sent_at' => now()]);
                $sent++;
            }
        }

        if ($sent > 0 || $skipped > 0) {
            $this->info("تم إرسال {$sent} تذكير واتساب، وتخطي {$skipped} لعدم وجود رقم.");
        }

        return 0;
    }
}
