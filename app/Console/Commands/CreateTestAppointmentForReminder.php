<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Console\Command;

class CreateTestAppointmentForReminder extends Command
{
    protected $signature = 'appointments:create-test-reminder
                            {--hours=1 : عدد الساعات من الآن لموعد الاختبار}
                            {--phone= : رقم واتساب للاختبار (اختياري، لو مش موجود يستخدم أول عميل له رقم}';

    protected $description = 'إنشاء موعد تجريبي لتجربة تذكير الواتساب (يظهر خلال نافذة التذكير)';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        if ($hours < 1) {
            $hours = 1;
        }

        $appointmentDate = now()->addHours($hours);

        $user = User::where('is_active', true)->first();
        if (!$user) {
            $this->error('لا يوجد مستخدم نشط. أنشئ مستخدماً أولاً.');
            return 1;
        }

        $phone = $this->option('phone');
        $customer = null;

        if ($phone !== null && $phone !== '') {
            $phone = preg_replace('/\s+/', '', $phone);
            $customer = Customer::where('phone', $phone)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => 'عميل تجربة تذكير',
                    'phone' => $phone,
                    'email' => 'test-reminder@test.local',
                ]);
                $this->info("تم إنشاء عميل تجريبي برقم: {$phone}");
            }
        }

        if (!$customer) {
            $customer = Customer::whereNotNull('phone')
                ->where('phone', '!=', '')
                ->first();
        }

        if (!$customer) {
            $this->error('لا يوجد عميل له رقم هاتف. استخدم الخيار --phone=201234567890 أو أضف رقماً لأحد العملاء.');
            return 1;
        }

        $appointment = Appointment::create([
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'appointment_date' => $appointmentDate,
            'status' => 'scheduled',
            'notes' => 'موعد تجريبي لتذكير الواتساب',
            'whatsapp_reminder_sent_at' => null,
        ]);

        $this->info('تم إنشاء الموعد التجريبي بنجاح.');
        $this->table(
            ['الحقل', 'القيمة'],
            [
                ['معرّف الموعد', $appointment->id],
                ['العميل', $customer->name . ' (' . ($customer->phone ?? '—') . ')'],
                ['تاريخ الموعد', $appointment->appointment_date->format('Y-m-d H:i')],
                ['بعد (ساعة)', $hours . ' ساعة من الآن'],
            ]
        );
        $this->newLine();
        $this->line('لتشغيل التذكيرات الآن (إرسال واتساب لهذا الموعد):');
        $this->line('  php artisan appointments:send-whatsapp-reminders');
        $this->newLine();

        return 0;
    }
}
