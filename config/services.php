<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'whatsbridge' => [
        'base_url' => env('WHATSBRIDGE_BASE_URL', 'https://api.whatsbridge.example'),
        'api_key' => env('WHATSBRIDGE_API_KEY'),
        'session_id' => env('WHATSBRIDGE_SESSION_ID'),
        'auth_header' => env('WHATSBRIDGE_AUTH_HEADER', 'Authorization'),
        'auth_prefix' => env('WHATSBRIDGE_AUTH_PREFIX', 'Bearer '),
        'media_path' => env('WHATSBRIDGE_MEDIA_PATH', '/message-media'),
        'media_base_url' => env('WHATSBRIDGE_MEDIA_BASE_URL'),
        'appointment_reminder_hours' => (int) env('WHATSAPP_APPOINTMENT_REMINDER_HOURS', 24),
        'appointment_reminder_message' => env('WHATSAPP_APPOINTMENT_REMINDER_MESSAGE', 'مرحباً :name، نذكرك بموعدك لدينا في :date الساعة :time. نتطلع لرؤيتك.'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Lead API (إنشاء ليد من تكامل خارجي)
    |--------------------------------------------------------------------------
    */
    'lead_api' => [
        'token' => env('LEAD_API_TOKEN'),
    ],

];
