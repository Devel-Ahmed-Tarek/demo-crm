<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Note: Appointment notifications are now sent immediately when appointments are created/updated
// No scheduled job needed - notifications are real-time

// Schedule the expired reservations check to run daily
Schedule::command('units:check-expired-reservations')
    ->daily()
    ->at('02:00')
    ->timezone('Africa/Cairo');

// تذكير المواعيد عبر واتساب
// في التطوير نخليه كل دقيقة عشان يسهل التست، وفي الإنتاج يفضل يكون hourly
Schedule::command('appointments:send-whatsapp-reminders')
    ->everyMinute()
    ->timezone('Africa/Cairo');
