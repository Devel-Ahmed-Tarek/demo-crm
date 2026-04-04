@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="mb-4 space-y-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('إدارة واتساب') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('إرسال رسائل، أو لعدة ليدز، أو متابعة محادثاتك المرتبطة بحساب WhatsBridge.') }}
            </p>
        </div>

        <div id="whatsapp-api-routes" class="hidden"
            data-chats-url="{{ route('whatsapp.services.api.chats') }}"
            data-messages-url="{{ route('whatsapp.services.api.messages') }}"
            data-media-url="{{ route('whatsapp.services.media') }}"
            data-send-chat-url="{{ route('whatsapp.services.send-chat') }}">
        </div>

        {{-- Tabs header --}}
        <div class="inline-flex flex-wrap gap-1 rounded-xl bg-gray-100 dark:bg-[#222220] p-1 text-xs sm:text-sm mb-5">
            <button type="button" id="whatsapp-tab-single"
                class="px-3 sm:px-4 py-1.5 rounded-lg font-medium text-gray-900 dark:text-gray-100 bg-white dark:bg-[#161615] shadow-sm">
                {{ __('رسالة لرقم واحد') }}
            </button>
            <button type="button" id="whatsapp-tab-leads"
                class="px-3 sm:px-4 py-1.5 rounded-lg font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                {{ __('رسالة لعدة ليدز') }}
            </button>
            <button type="button" id="whatsapp-tab-chats"
                class="px-3 sm:px-4 py-1.5 rounded-lg font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                {{ __('محادثات الواتساب') }}
            </button>
        </div>

        @if (session('success'))
            <div
                class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-100">
                {{ session('success') }}
            </div>
        @endif

        @if (session('success_chats'))
            <div
                class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-100">
                {{ session('success_chats') }}
            </div>
        @endif

        @if ($errors->any())
            <div
                class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-800 dark:text-red-100">
                <ul class="list-disc ps-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Tab: single number --}}
        <div id="whatsapp-tab-single-panel"
            class="bg-white dark:bg-[#161615] border border-gray-200 dark:border-[#3E3E3A] rounded-xl shadow-sm p-6 space-y-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                <div>
                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ __('حالة الإعداد') }}:</span>
                    @if ($baseUrl && config('services.whatsbridge.api_key'))
                        <span class="inline-flex items-center gap-1 ms-2 px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            {{ __('مُعد') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 ms-2 px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">
                            <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                            {{ __('بحاجة إلى إعداد البيئة (ENV)') }}
                        </span>
                    @endif
                </div>
                <div>
                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ __('عنوان الخدمة (Base URL)') }}:</span>
                    <span class="ms-1">{{ $baseUrl ?: '—' }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('whatsapp.services.send') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="tab" value="single">

                <div class="space-y-2">
                    <label for="phone_number"
                        class="block text-sm font-semibold text-gray-800 dark:text-gray-100">
                        {{ __('رقم الواتساب (بدون +)') }}
                    </label>
                    <div class="relative flex items-stretch">
                        <div
                            class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-300 dark:border-[#3E3E3A] bg-gray-50 dark:bg-[#222220] text-xs font-medium text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-1 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3l2 3h4l2-3h3a2 2 0 012 2v3l-3 2v4l3 2v3a2 2 0 01-2 2h-3l-2-3h-4l-2 3H5a2 2 0 01-2-2v-3l3-2v-4L3 8V5z" />
                            </svg>
                            {{ __('WhatsApp') }}
                        </div>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                            class="flex-1 min-w-0 rounded-r-xl border border-gray-300 dark:border-[#3E3E3A] border-l-0 dark:bg-[#161615] dark:text-gray-100 text-sm px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500"
                            placeholder="مثال: 201234567890" />
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('اكتب الرقم بالمفتاح الدولي مباشرة بدون 0 في البداية وبدون علامة +.') }}
                    </p>
                </div>

                <div class="space-y-2">
                    <label for="message"
                        class="block text-sm font-semibold text-gray-800 dark:text-gray-100">
                        {{ __('الرسالة') }}
                    </label>
                    <textarea name="message" id="message" rows="4"
                        class="block w-full rounded-xl border border-gray-300 dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-gray-100 text-sm px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500"
                        placeholder="{{ __('اكتب نص الرسالة التي تريد إرسالها للعميل عبر الواتساب...') }}">{{ old('message') }}</textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('يمكنك كتابة نص عادي أو لصق رسالة جاهزة، وسيتم إرسالها كما هي.') }}
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('إرسال الرسالة عبر WhatsBridge') }}
                    </button>
                </div>
            </form>

            <div class="pt-4 border-t border-gray-200 dark:border-[#3E3E3A] text-xs text-gray-500 dark:text-gray-400">
                {{ __('يمكنك استخدام التبويبات الأخرى لإرسال لعدة ليدز أو متابعة المحادثات.') }}
            </div>
        </div>

        {{-- Tab: leads bulk --}}
        <div id="whatsapp-tab-leads-panel"
            class="bg-white dark:bg-[#161615] border border-gray-200 dark:border-[#3E3E3A] rounded-xl shadow-sm p-6 space-y-5 hidden">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('إرسال رسالة لعدة ليدز') }}
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                        {{ __('فلتر الليدز حسب المرحلة أو المصدر، ثم اختر الليدز التي تريد إرسال نفس الرسالة لها دفعة واحدة.') }}
                    </p>
                </div>
                <form method="GET" action="{{ route('whatsapp.services.index') }}"
                    class="flex flex-wrap items-center gap-2 sm:justify-end">
                    <input type="hidden" name="tab" value="leads">
                    <select name="stage"
                        class="rounded-lg border border-gray-300 dark:border-[#3E3E3A] bg-white dark:bg-[#161615] text-xs sm:text-sm text-gray-700 dark:text-gray-100 px-2 py-1 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">{{ __('كل المراحل') }}</option>
                        @foreach ($stages as $stage)
                            <option value="{{ $stage }}" @selected($currentStage === $stage)>
                                {{ $stage }}
                            </option>
                        @endforeach
                    </select>
                    <select name="source"
                        class="rounded-lg border border-gray-300 dark:border-[#3E3E3A] bg-white dark:bg-[#161615] text-xs sm:text-sm text-gray-700 dark:text-gray-100 px-2 py-1 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">{{ __('كل المصادر') }}</option>
                        @foreach ($sources as $source)
                            <option value="{{ $source }}" @selected($currentSource === $source)>
                                {{ $source }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 4h13M8 9h13M8 14h13M8 19h13M3 4h.01M3 9h.01M3 14h.01M3 19h.01" />
                        </svg>
                        <span>{{ __('تطبيق الفلتر') }}</span>
                    </button>
                </form>
            </div>

            @if (session('success_leads'))
                <div
                    class="mb-3 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-4 py-3 text-xs sm:text-sm text-green-800 dark:text-green-100">
                    {{ session('success_leads') }}
                </div>
            @endif

            @error('whatsapp_leads')
                <div
                    class="mb-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 px-4 py-3 text-xs sm:text-sm text-red-800 dark:text-red-100">
                    {{ $message }}
                </div>
            @enderror

            <form method="POST" action="{{ route('whatsapp.services.send-leads') }}" class="space-y-4">
                @csrf

                <div class="border border-gray-200 dark:border-[#3E3E3A] rounded-lg overflow-hidden">
                    <div class="max-h-64 overflow-y-auto">
                        <table class="min-w-full text-xs sm:text-sm">
                            <thead class="bg-gray-50 dark:bg-[#222220] text-gray-600 dark:text-gray-300">
                                <tr>
                                    <th class="w-10 px-3 py-2 text-start">
                                        <input type="checkbox" onclick="toggleAllLeads(this)"
                                            class="rounded border-gray-300 dark:border-[#3E3E3A] text-emerald-600 focus:ring-emerald-500">
                                    </th>
                                    <th class="px-3 py-2 text-start font-medium">
                                        {{ __('الليد') }}
                                    </th>
                                    <th class="px-3 py-2 text-start font-medium hidden sm:table-cell">
                                        {{ __('رقم الهاتف') }}
                                    </th>
                                    <th class="px-3 py-2 text-start font-medium hidden md:table-cell">
                                        {{ __('المرحلة') }}
                                    </th>
                                    <th class="px-3 py-2 text-start font-medium hidden md:table-cell">
                                        {{ __('المصدر') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-[#3E3E3A]">
                                @forelse ($leads as $lead)
                                    <tr class="{{ $loop->even ? 'bg-gray-50/40 dark:bg-[#181818]' : 'bg-white dark:bg-[#161615]' }}">
                                        <td class="px-3 py-2 align-top">
                                            @if ($lead->phone)
                                                <input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}"
                                                    class="lead-checkbox rounded border-gray-300 dark:border-[#3E3E3A] text-emerald-600 focus:ring-emerald-500">
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-500 dark:bg-[#222220] dark:text-gray-400">
                                                    {{ __('بدون رقم') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $lead->name ?? __('بدون اسم') }}
                                                </span>
                                                <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                                    #{{ $lead->id }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 align-top hidden sm:table-cell">
                                            <span class="text-gray-800 dark:text-gray-100 text-xs">
                                                {{ $lead->phone ?: '—' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 align-top hidden md:table-cell">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-200">
                                                {{ $lead->stage ?: __('غير محدد') }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 align-top hidden md:table-cell">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200">
                                                {{ $lead->source ?: __('غير محدد') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('لا توجد ليدز لعرضها حالياً.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="message_leads"
                        class="block text-sm font-semibold text-gray-800 dark:text-gray-100">
                        {{ __('نص الرسالة المرسلة لجميع الليدز المختارة') }}
                    </label>
                    <textarea name="message" id="message_leads" rows="3"
                        class="block w-full rounded-xl border border-gray-300 dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-gray-100 text-sm px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500"
                        placeholder="{{ __('اكتب الرسالة التي سيتم إرسالها لكل الليدز المختارة...') }}">{{ old('message') }}</textarea>
                </div>

                <div class="flex items-center justify-between gap-3 pt-1">
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">
                        {{ __('سيتم إرسال نفس الرسالة لكل رقم ليد يحتوي على رقم هاتف.') }}
                    </p>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs sm:text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('إرسال الرسالة لليدز المختارة') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab: WhatsApp chats (WhatsBridge) --}}
        <div id="whatsapp-tab-chats-panel"
            class="bg-white dark:bg-[#161615] border border-gray-200 dark:border-[#3E3E3A] rounded-xl shadow-sm overflow-hidden mb-6 hidden">
            <div class="p-4 sm:p-5 border-b border-gray-200 dark:border-[#3E3E3A] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('محادثات الواتساب') }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ __('لا يوجد تحديث تلقائي للشات — استخدم «تحديث القائمة» أو «تحديث الرسائل» عند الحاجة. بعد إرسالك لرد يُحدَّث المحادثة مرة واحدة تلقائياً.') }}
                    </p>
                    <p id="whatsapp-live-status" class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-1 hidden"
                        aria-live="polite"></p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="whatsapp-chats-refresh"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ __('تحديث القائمة') }}
                    </button>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row min-h-[min(70vh,560px)] max-h-[min(85vh,720px)]">
                <aside
                    class="w-full lg:w-[min(100%,320px)] lg:flex-shrink-0 border-b lg:border-b-0 lg:border-e border-gray-200 dark:border-[#3E3E3A] flex flex-col max-h-[40vh] lg:max-h-none">
                    <div id="whatsapp-chats-list" class="flex-1 overflow-y-auto p-2 space-y-1 text-sm">
                        <p class="text-xs text-gray-500 dark:text-gray-400 p-3">
                            {{ __('اضغط «تحديث القائمة» لتحميل المحادثات.') }}
                        </p>
                    </div>
                </aside>
                <section class="flex-1 flex flex-col min-h-0 bg-gray-50/50 dark:bg-[#0f0f0e]">
                    <div id="whatsapp-chat-header"
                        class="px-4 py-3 border-b border-gray-200 dark:border-[#3E3E3A] flex flex-wrap items-center justify-between gap-2">
                        <span id="whatsapp-chat-header-title"
                            class="text-sm font-medium text-gray-800 dark:text-gray-100 flex-1 min-w-0 truncate">
                            {{ __('اختر محادثة من القائمة') }}
                        </span>
                        <button type="button" id="whatsapp-messages-refresh" disabled
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-emerald-100 dark:hover:bg-emerald-900/50">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ __('تحديث الرسائل') }}
                        </button>
                    </div>
                    <div id="whatsapp-messages-wrap" class="flex-1 overflow-y-auto p-3 space-y-2 min-h-[200px]">
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center py-8">
                            {{ __('لم تُحدد محادثة بعد.') }}
                        </p>
                    </div>
                    <form id="whatsapp-reply-form" method="POST" action="{{ route('whatsapp.services.send-chat') }}"
                        class="border-t border-gray-200 dark:border-[#3E3E3A] p-3 space-y-2 bg-white dark:bg-[#161615]">
                        @csrf
                        <input type="hidden" name="tab" value="chats">
                        <input type="hidden" name="chat_id" id="whatsapp-reply-chat-id" value="">
                        <label for="whatsapp-reply-message" class="sr-only">{{ __('رسالة') }}</label>
                        <textarea name="message" id="whatsapp-reply-message" rows="2" disabled
                            class="block w-full rounded-xl border border-gray-300 dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-gray-100 text-sm px-3 py-2 focus:ring-2 focus:ring-emerald-500 disabled:opacity-50"
                            placeholder="{{ __('اكتب ردك هنا…') }}">{{ old('message') }}</textarea>
                        <div class="flex justify-end">
                            <button type="submit" id="whatsapp-reply-submit" disabled
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ __('إرسال') }}
                            </button>
                        </div>
                    </form>
                </section>
            </div>
            <p class="text-[11px] text-gray-500 dark:text-gray-500 px-4 pb-4">
                {{ __('لاستقبال رسائل الواردة لحظياً دون ضغط «تحديث» يلزم ربط Webhook من WhatsBridge بسيرفر الـ CRM (أو WebSocket). الاستطلاع التلقائي المتكرر أُلغي لتخفيف الحمل على الخادم.') }}
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleAllLeads(source) {
            const checkboxes = document.querySelectorAll('.lead-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = source.checked;
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const tabSingle = document.getElementById('whatsapp-tab-single');
            const tabLeads = document.getElementById('whatsapp-tab-leads');
            const tabChats = document.getElementById('whatsapp-tab-chats');
            const panelSingle = document.getElementById('whatsapp-tab-single-panel');
            const panelLeads = document.getElementById('whatsapp-tab-leads-panel');
            const panelChats = document.getElementById('whatsapp-tab-chats-panel');
            const routesEl = document.getElementById('whatsapp-api-routes');

            if (!tabSingle || !tabLeads || !tabChats || !panelSingle || !panelLeads || !panelChats || !routesEl) {
                return;
            }

            const chatsUrl = routesEl.dataset.chatsUrl;
            const messagesUrl = routesEl.dataset.messagesUrl;
            const mediaProxyUrl = routesEl.dataset.mediaUrl || '';
            const sendChatUrl = routesEl.dataset.sendChatUrl || '';
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const activeClasses =
                'px-3 sm:px-4 py-1.5 rounded-lg font-medium text-gray-900 dark:text-gray-100 bg-white dark:bg-[#161615] shadow-sm';
            const inactiveClasses =
                'px-3 sm:px-4 py-1.5 rounded-lg font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100';

            let selectedChatId = null;
            let selectedChatLabel = '';

            const chatListEl = document.getElementById('whatsapp-chats-list');
            const liveStatusEl = document.getElementById('whatsapp-live-status');
            const chatHeaderTitleEl = document.getElementById('whatsapp-chat-header-title');
            const messagesWrap = document.getElementById('whatsapp-messages-wrap');
            const replyChatId = document.getElementById('whatsapp-reply-chat-id');
            const replyMessage = document.getElementById('whatsapp-reply-message');
            const replySubmit = document.getElementById('whatsapp-reply-submit');
            const replyForm = document.getElementById('whatsapp-reply-form');
            const btnRefreshChats = document.getElementById('whatsapp-chats-refresh');
            const btnMessagesRefresh = document.getElementById('whatsapp-messages-refresh');

            function extractChats(payload) {
                if (!payload || !payload.ok) return [];
                const d = payload.data;
                if (Array.isArray(d)) return d;
                if (d && Array.isArray(d.chats)) return d.chats;
                if (d && d.data && Array.isArray(d.data.chats)) return d.data.chats;
                if (d && d.data && d.data.data && Array.isArray(d.data.data.chats)) return d.data.data.chats;
                if (d && Array.isArray(d.data)) return d.data;
                if (d && Array.isArray(d.items)) return d.items;
                return [];
            }

            function chatIdOf(c) {
                return c.id || c.chatId || c._serialized || '';
            }

            function chatLabelOf(c) {
                return c.name || c.pushName || c.formattedTitle || chatIdOf(c) || '—';
            }

            function extractMessages(payload) {
                if (!payload || !payload.ok) return [];
                const d = payload.data;
                if (Array.isArray(d)) return d;
                if (d && Array.isArray(d.messages)) return d.messages;
                if (d && d.data && Array.isArray(d.data.messages)) return d.data.messages;
                if (d && d.data && d.data.data && Array.isArray(d.data.data.messages)) return d.data.data.messages;
                if (d && Array.isArray(d.data)) return d.data;
                return [];
            }

            function messageText(m) {
                if (typeof m === 'string') return m;
                const b = m.body;
                if (typeof b === 'string') return b;
                if (b && typeof b === 'object' && b.text) return b.text;
                return m.caption || m.message || m.text || (m.type ? ('[' + m.type + ']') : '');
            }

            function safeMediaUrl(u) {
                if (!u || typeof u !== 'string') return null;
                const t = u.trim();
                if (t.startsWith('https://') || t.startsWith('http://') || t.startsWith('blob:') || t.startsWith(
                        'data:audio/')) return t;
                return null;
            }

            function getAudioMessageUrl(m) {
                if (!m || typeof m !== 'object') return null;
                const tryList = [
                    m.mediaUrl, m.media_url, m.url, m.downloadUrl, m.download_url,
                    m.audioUrl, m.audio_url, m.voiceUrl,
                    m.audio && m.audio.url,
                    m.voice && m.voice.url,
                    m.message && m.message.audioMessage && m.message.audioMessage.url,
                    m.message && m.message.pttMessage && m.message.pttMessage.url,
                    m.body && typeof m.body === 'object' && m.body.audioMessage && m.body.audioMessage.url,
                    m._data && m._data.url,
                    m.raw && m.raw.message && m.raw.message.audioMessage && m.raw.message.audioMessage.url
                ];
                for (let i = 0; i < tryList.length; i++) {
                    const x = tryList[i];
                    const s = safeMediaUrl(x);
                    if (s) return s;
                }
                if (typeof m.audioBase64 === 'string' && m.audioMime) {
                    return 'data:' + m.audioMime + ';base64,' + m.audioBase64;
                }
                return null;
            }

            function getWhatsappMessageId(m) {
                if (!m || typeof m !== 'object') return null;
                if (m.key && typeof m.key === 'object' && typeof m.key.id === 'string' && m.key.id.length) {
                    return m.key.id;
                }
                if (typeof m.key === 'string' && m.key.length) {
                    try {
                        const parsed = JSON.parse(m.key);
                        if (parsed && typeof parsed.id === 'string' && parsed.id.length) {
                            return parsed.id;
                        }
                    } catch (e) { /* ignore */ }
                }
                if (m.msg && m.msg.key && typeof m.msg.key.id === 'string') return m.msg.key.id;
                if (m.message && m.message.key && typeof m.message.key.id === 'string') {
                    return m.message.key.id;
                }
                if (typeof m.messageId === 'string' && m.messageId.length) return m.messageId;
                if (typeof m.msgId === 'string' && m.msgId.length) return m.msgId;
                if (typeof m.id === 'string' && m.id.length && m.id.indexOf('_') !== -1) return m.id;
                return null;
            }

            function hydrateVoiceAudio(audioEl, playbackUrl, directUrl) {
                audioEl.preload = 'auto';
                audioEl.crossOrigin = 'anonymous';
                const tryBlobFromUrl = async (u) => {
                    try {
                        const r = await fetch(u, {
                            credentials: 'same-origin'
                        });
                        if (!r.ok) return false;
                        const buf = await r.arrayBuffer();
                        if (buf.byteLength < 16) return false;
                        const ct = (r.headers.get('content-type') || '').split(';')[0].trim();
                        const mime = ct && ct.indexOf('audio') !== -1 ? ct : 'audio/ogg';
                        const blob = new Blob([buf], {
                            type: mime
                        });
                        const objUrl = URL.createObjectURL(blob);
                        audioEl.src = objUrl;
                        audioEl.addEventListener('ended', () => URL.revokeObjectURL(objUrl), {
                            once: true
                        });
                        return true;
                    } catch (e) {
                        return false;
                    }
                };
                audioEl.addEventListener('error', async function onVoiceErr() {
                    audioEl.removeEventListener('error', onVoiceErr);
                    if (directUrl && directUrl !== playbackUrl) {
                        audioEl.src = directUrl;
                        audioEl.addEventListener('error', async function onVoiceErr2() {
                            audioEl.removeEventListener('error', onVoiceErr2);
                            await tryBlobFromUrl(playbackUrl);
                        }, {
                            once: true
                        });
                        return;
                    }
                    await tryBlobFromUrl(playbackUrl);
                }, {
                    once: true
                });
            }

            /**
             * يفضّل البروكسي (نفس أصل الموقع) عند توفر message id لتفادي CORS ومدة 0:00.
             * chatJid = معرف المحادثة الحالية (مهم لـ @lid) يُمرّر كـ chat_id للخادم.
             */
            function getVoicePlaybackUrl(m, chatJid) {
                const mid = getWhatsappMessageId(m);
                if (mid && mediaProxyUrl) {
                    const u = new URL(mediaProxyUrl, window.location.origin);
                    u.searchParams.set('message_id', mid);
                    if (chatJid) {
                        u.searchParams.set('chat_id', chatJid);
                    }
                    return u.toString();
                }
                return getAudioMessageUrl(m);
            }

            function isVoiceOrAudioMessage(m) {
                if (!m || typeof m !== 'object') return false;
                const t = String(m.type || m.messageType || m.msgType || '').toLowerCase();
                if (['ptt', 'audio', 'voice', 'audio_message', 'ptt_message'].indexOf(t) !== -1) return true;
                if (m.ptt === true) return true;
                if (m.message && (m.message.audioMessage || m.message.pttMessage)) return true;
                if (m.body && typeof m.body === 'object' && m.body.audioMessage) return true;
                if (getAudioMessageUrl(m)) return true;
                const mime = (m.mimetype || m.mimeType || (m.message && m.message.audioMessage && m.message
                    .audioMessage.mimetype) || '').toLowerCase();
                if (mime.indexOf('audio') !== -1) return true;
                return false;
            }

            function voiceDurationLabel(m) {
                const s = m.seconds || m.duration || (m.message && m.message.audioMessage && m.message.audioMessage
                    .seconds);
                if (typeof s === 'number' && s > 0) return Math.round(s) + 's';
                return '';
            }

            function isFromMe(m) {
                return m.fromMe === true || m.fromMe === 1 || m.from_me === true;
            }

            function formatTs(m) {
                const t = m.timestamp || m.t;
                if (!t) return '';
                const ms = t < 1e12 ? t * 1000 : t;
                try {
                    return new Date(ms).toLocaleString();
                } catch (e) {
                    return '';
                }
            }

            function touchLiveStatus() {
                if (!liveStatusEl) return;
                liveStatusEl.classList.remove('hidden');
                liveStatusEl.textContent = '{{ __('آخر تحديث:') }} ' + new Date().toLocaleTimeString();
            }

            function renderChatListItems(items, applyDeepLink) {
                chatListEl.innerHTML = '';
                items.forEach((c) => {
                    const id = chatIdOf(c);
                    if (!id) return;
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.setAttribute('data-chat-id', id);
                    const unread = (typeof c.unreadCount === 'number' && c.unreadCount > 0) ?
                        ' · ' + c.unreadCount :
                        '';
                    btn.className =
                        'w-full text-start px-3 py-2 rounded-lg text-xs sm:text-sm transition ' +
                        (id === selectedChatId ?
                            'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-900 dark:text-emerald-100' :
                            'hover:bg-gray-100 dark:hover:bg-[#222220] text-gray-800 dark:text-gray-100');
                    btn.textContent = chatLabelOf(c) + unread;
                    btn.addEventListener('click', () => selectChat(id, chatLabelOf(c)));
                    chatListEl.appendChild(btn);
                });
                if (applyDeepLink) {
                    const params = new URLSearchParams(window.location.search);
                    const deep = params.get('chat');
                    if (deep) {
                        const found = items.find((x) => chatIdOf(x) === deep);
                        if (found) {
                            selectChat(deep, chatLabelOf(found));
                        } else {
                            selectChat(deep, deep);
                        }
                    }
                }
            }

            async function loadChats(refresh) {
                chatListEl.innerHTML =
                    '<p class="text-xs text-gray-500 p-3">{{ __('جاري التحميل…') }}</p>';
                try {
                    const u = new URL(chatsUrl, window.location.origin);
                    if (refresh) u.searchParams.set('refresh', '1');
                    const res = await fetch(u.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        credentials: 'same-origin'
                    });
                    const json = await res.json();
                    const items = extractChats(json);
                    if (!json.ok) {
                        chatListEl.innerHTML = '<p class="text-xs text-red-600 dark:text-red-400 p-3">' + (json
                            .message || '{{ __('تعذر التحميل') }}') + '</p>';
                        return;
                    }
                    if (items.length === 0) {
                        chatListEl.innerHTML =
                            '<p class="text-xs text-gray-500 p-3">{{ __('لا توجد محادثات أو القائمة فارغة.') }}</p>';
                        const paramsEmpty = new URLSearchParams(window.location.search);
                        const deepOnly = paramsEmpty.get('chat');
                        if (deepOnly) {
                            selectChat(deepOnly, deepOnly);
                        }
                        return;
                    }
                    renderChatListItems(items, true);
                    touchLiveStatus();
                } catch (e) {
                    chatListEl.innerHTML =
                        '<p class="text-xs text-red-600 p-3">{{ __('فشل الاتصال بالخادم.') }}</p>';
                }
            }

            async function loadMessages(chatId, scrollBottom) {
                if (!chatId) return;
                try {
                    const u = new URL(messagesUrl, window.location.origin);
                    u.searchParams.set('chat_id', chatId);
                    u.searchParams.set('limit', '80');
                    u.searchParams.set('order', 'asc');
                    const res = await fetch(u.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        credentials: 'same-origin'
                    });
                    const json = await res.json();
                    const msgs = extractMessages(json);
                    if (!json.ok) {
                        messagesWrap.innerHTML = '<p class="text-xs text-red-600 p-3">' + (json.message ||
                            '{{ __('تعذر جلب الرسائل') }}') + '</p>';
                        return;
                    }
                    messagesWrap.innerHTML = '';
                    if (msgs.length === 0) {
                        messagesWrap.innerHTML =
                            '<p class="text-xs text-gray-500 text-center py-6">{{ __('لا توجد رسائل في هذه الدفعة.') }}</p>';
                    } else {
                        msgs.forEach((m) => {
                            const row = document.createElement('div');
                            const mine = isFromMe(m);
                            row.className = 'flex ' + (mine ? 'justify-end' : 'justify-start');
                            const bubble = document.createElement('div');
                            bubble.className = 'max-w-[85%] rounded-2xl px-3 py-2 text-xs sm:text-sm ' +
                                (mine ?
                                    'bg-emerald-600 text-white' :
                                    'bg-white dark:bg-[#222220] text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-[#3E3E3A]');
                            const chatJidForMedia = selectedChatId ||
                                (m.key && m.key.remoteJid) || '';
                            const directAudioUrl = getAudioMessageUrl(m);
                            const playbackUrl = getVoicePlaybackUrl(m, chatJidForMedia);
                            const voice = isVoiceOrAudioMessage(m);

                            if (voice && playbackUrl) {
                                const voiceWrap = document.createElement('div');
                                voiceWrap.className = 'space-y-2';
                                const voiceHead = document.createElement('div');
                                voiceHead.className =
                                    'text-[11px] opacity-90 flex flex-wrap items-center gap-2';
                                voiceHead.appendChild(document.createTextNode('🎤 '));
                                const voiceTitle = document.createElement('span');
                                voiceTitle.textContent = @json(__('رسالة صوتية'));
                                voiceHead.appendChild(voiceTitle);
                                const durStr = voiceDurationLabel(m);
                                if (durStr) {
                                    const durEl = document.createElement('span');
                                    durEl.className = 'opacity-75 tabular-nums';
                                    durEl.textContent = durStr;
                                    voiceHead.appendChild(durEl);
                                }
                                const audioEl = document.createElement('audio');
                                audioEl.controls = true;
                                audioEl.className = 'block w-full min-w-[200px] max-w-[min(100%,280px)] h-9';
                                audioEl.setAttribute('src', playbackUrl);
                                hydrateVoiceAudio(audioEl, playbackUrl, directAudioUrl);
                                voiceWrap.appendChild(voiceHead);
                                voiceWrap.appendChild(audioEl);
                                bubble.appendChild(voiceWrap);
                            } else if (voice && !playbackUrl) {
                                const hint = document.createElement('div');
                                hint.className = 'whitespace-pre-wrap break-words text-[11px]';
                                hint.textContent = '🎤 ' + @json(__('رسالة صوتية — لا يوجد رابط تشغيل في بيانات الـ API.'));
                                bubble.appendChild(hint);
                            } else {
                                const text = document.createElement('div');
                                text.className = 'whitespace-pre-wrap break-words';
                                text.textContent = messageText(m) || '—';
                                bubble.appendChild(text);
                            }
                            const meta = document.createElement('div');
                            meta.className = 'text-[10px] mt-1 opacity-80';
                            meta.textContent = formatTs(m);
                            if (formatTs(m)) bubble.appendChild(meta);
                            row.appendChild(bubble);
                            messagesWrap.appendChild(row);
                        });
                    }
                    if (scrollBottom !== false) {
                        messagesWrap.scrollTop = messagesWrap.scrollHeight;
                    }
                    touchLiveStatus();
                } catch (e) {
                    messagesWrap.innerHTML =
                        '<p class="text-xs text-red-600 p-3">{{ __('فشل تحميل الرسائل.') }}</p>';
                }
            }

            function selectChat(id, label) {
                selectedChatId = id;
                selectedChatLabel = label || id;
                if (chatHeaderTitleEl) {
                    chatHeaderTitleEl.textContent = selectedChatLabel;
                }
                replyChatId.value = id;
                replyMessage.disabled = false;
                replySubmit.disabled = false;
                if (btnMessagesRefresh) {
                    btnMessagesRefresh.disabled = false;
                }
                chatListEl.querySelectorAll('button[data-chat-id]').forEach((btn) => {
                    const bid = btn.getAttribute('data-chat-id');
                    const isSel = bid === id;
                    btn.className =
                        'w-full text-start px-3 py-2 rounded-lg text-xs sm:text-sm transition ' +
                        (isSel ?
                            'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-900 dark:text-emerald-100' :
                            'hover:bg-gray-100 dark:hover:bg-[#222220] text-gray-800 dark:text-gray-100');
                });
                loadMessages(id, true);
            }

            btnRefreshChats.addEventListener('click', () => loadChats(true));

            if (btnMessagesRefresh) {
                btnMessagesRefresh.addEventListener('click', () => {
                    if (!selectedChatId) return;
                    loadMessages(selectedChatId, true);
                });
            }

            if (replyForm && sendChatUrl) {
                replyForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    if (!selectedChatId || !replyMessage.value.trim()) return;
                    replySubmit.disabled = true;
                    const fd = new FormData(replyForm);
                    try {
                        const res = await fetch(sendChatUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: fd,
                            credentials: 'same-origin',
                        });
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            let msg = data.message || '';
                            if (data.errors && typeof data.errors === 'object') {
                                const k = Object.keys(data.errors)[0];
                                if (k && data.errors[k] && data.errors[k][0]) {
                                    msg = data.errors[k][0];
                                }
                            }
                            if (!msg) {
                                msg = @json(__('فشل إرسال الرسالة.'));
                            }
                            alert(msg);
                            replySubmit.disabled = false;
                            return;
                        }
                        if (data.ok) {
                            replyMessage.value = '';
                            touchLiveStatus();
                            await loadMessages(selectedChatId, true);
                        }
                    } catch (err) {
                        alert(@json(__('فشل الاتصال بالخادم.')));
                    }
                    replySubmit.disabled = false;
                });
            }

            const setActiveTab = (active) => {
                tabSingle.className = active === 'single' ? activeClasses : inactiveClasses;
                tabLeads.className = active === 'leads' ? activeClasses : inactiveClasses;
                tabChats.className = active === 'chats' ? activeClasses : inactiveClasses;

                panelSingle.classList.toggle('hidden', active !== 'single');
                panelLeads.classList.toggle('hidden', active !== 'leads');
                panelChats.classList.toggle('hidden', active !== 'chats');

                if (active === 'chats') {
                    void loadChats(false);
                }
            };

            tabSingle.addEventListener('click', () => setActiveTab('single'));
            tabLeads.addEventListener('click', () => setActiveTab('leads'));
            tabChats.addEventListener('click', () => setActiveTab('chats'));

            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            const openLeads = tab === 'leads' || (tab !== 'chats' && (params.has('stage') || params.has('source')));
            const openChats = tab === 'chats';

            if (openChats) {
                setActiveTab('chats');
            } else if (openLeads) {
                setActiveTab('leads');
            } else {
                setActiveTab('single');
            }
        });
    </script>
@endpush
