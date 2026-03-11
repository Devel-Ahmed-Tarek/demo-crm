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
        $defaultTemplate = config('services.whatsbridge.appointment_reminder_message', 'مرحباً :name، نذكرك بموعدك لدينا في :date الساعة :time.');

        // نافذة التذكير: أي موعد يبعد عن الآن أقل من X ساعة (قبله أو بعده)
        $windowPast = now()->subHours($hours);
        $windowFuture = now()->addHours($hours);

        $appointments = Appointment::with(['customer.leads'])
            ->where('status', 'scheduled')
            ->whereNull('whatsapp_reminder_sent_at')
            ->whereBetween('appointment_date', [$windowPast, $windowFuture])
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($appointments as $appointment) {
            $customer = $appointment->customer;
            $phone = null;

            if ($customer) {
                // أولاً جرّب رقم العميل نفسه
                if (!empty(trim($customer->phone ?? ''))) {
                    $phone = $customer->phone;
                } else {
                    // لو مفيش رقم على العميل، جرّب أول ليد مرتبط بيه له رقم
                    $leadWithPhone = $customer->leads
                        ->filter(fn ($lead) => !empty(trim($lead->phone ?? '')))
                        ->sortByDesc('created_at')
                        ->first();

                    if ($leadWithPhone) {
                        $phone = $leadWithPhone->phone;
                    }
                }
            }

            if (!$customer || !$phone) {
                $skipped++;
                continue;
            }

            $name = $customer->name ?: 'عميلنا';
            $date = $appointment->appointment_date->locale('ar')->translatedFormat('l j F Y');
            $time = $appointment->appointment_date->format('H:i');

            // لو الميعاد له رسالة خاصة، نستخدمها؛ لو فاضية نستخدم الرسالة الافتراضية
            $template = !empty(trim($appointment->whatsapp_reminder_message ?? ''))
                ? $appointment->whatsapp_reminder_message
                : $defaultTemplate;

            $message = str_replace(
                [':name', ':date', ':time'],
                [$name, $date, $time],
                $template
            );

            if ($whatsBridge->sendMessage($phone, $message)) {
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
