@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-4 space-y-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('إرسال رسالة واتساب لرقم معيّن') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('اكتب رقم العميل والرسالة، وسيتم إرسالها من خلال خدمة WhatsBridge.') }}
            </p>
        </div>

        {{-- Tabs header --}}
        <div class="inline-flex rounded-xl bg-gray-100 dark:bg-[#222220] p-1 text-xs sm:text-sm mb-5">
            <button type="button" id="whatsapp-tab-single"
                class="px-3 sm:px-4 py-1.5 rounded-lg font-medium text-gray-900 dark:text-gray-100 bg-white dark:bg-[#161615] shadow-sm">
                {{ __('رسالة لرقم واحد') }}
            </button>
            <button type="button" id="whatsapp-tab-leads"
                class="px-3 sm:px-4 py-1.5 rounded-lg font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                {{ __('رسالة لعدة ليدز') }}
            </button>
        </div>

        @if (session('success'))
            <div
                class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-100">
                {{ session('success') }}
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
                {{ __('يمكنك استخدام الكارد التالي لإرسال نفس الرسالة لعدّة ليدز دفعة واحدة.') }}
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
                                        <td class="px-3 py-2 align-top.hidden md:table-cell">
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
            const panelSingle = document.getElementById('whatsapp-tab-single-panel');
            const panelLeads = document.getElementById('whatsapp-tab-leads-panel');

            if (!tabSingle || !tabLeads || !panelSingle || !panelLeads) {
                return;
            }

            const setActiveTab = (active) => {
                const activeClasses =
                    'px-3 sm:px-4 py-1.5 rounded-lg font-medium text-gray-900 dark:text-gray-100 bg-white dark:bg-[#161615] shadow-sm';
                const inactiveClasses =
                    'px-3 sm:px-4 py-1.5 rounded-lg font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100';

                if (active === 'single') {
                    tabSingle.className = activeClasses;
                    tabLeads.className = inactiveClasses;
                    panelSingle.classList.remove('hidden');
                    panelLeads.classList.add('hidden');
                } else {
                    tabLeads.className = activeClasses;
                    tabSingle.className = inactiveClasses;
                    panelLeads.classList.remove('hidden');
                    panelSingle.classList.add('hidden');
                }
            };

            tabSingle.addEventListener('click', () => setActiveTab('single'));
            tabLeads.addEventListener('click', () => setActiveTab('leads'));

            // التبويب يتبع الـ URL: لو فيه tab=leads أو فلتر (stage/source) نفتح التاني، وإلاّ نفتح الأول
            const params = new URLSearchParams(window.location.search);
            const openLeads = params.get('tab') === 'leads' || params.has('stage') || params.has('source');
            setActiveTab(openLeads ? 'leads' : 'single');
        });
    </script>
@endpush

