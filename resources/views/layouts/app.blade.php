@php
    $currentUser = auth()->user();
    $isSalesAgent = $currentUser?->isSalesAgent();
    $currentLocale = app()->getLocale();
    $isRTL = $currentLocale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CRM System') }}</title>

    <script>
        (() => {
            try {
                const storedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = storedTheme ?? (prefersDark ? 'dark' : 'light');
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                document.documentElement.dataset.theme = theme;
            } catch (error) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="app-shell">
    <!-- Mobile Header -->
    <header
        class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white dark:bg-[#161615] border-b border-[#e3e3e0] dark:border-[#3E3E3A] shadow-sm">
        <div class="flex items-center justify-between px-4 py-3">

            <div class="flex items-center gap-2">

                <!-- Language Switcher -->
                <div class="relative">
                    <button onclick="toggleLanguageMenu()"
                        class="p-2 text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-100 dark:hover:bg-[#3E3E3A] rounded-lg transition-colors"
                        title="{{ __('Select Language') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </button>
                    <div id="languageMenu"
                        class="hidden absolute {{ $isRTL ? 'left-0' : 'right-0' }} mt-2 w-32 bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg shadow-lg z-50">
                        <a href="{{ route('language.switch', 'ar') }}"
                            class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] {{ $currentLocale === 'ar' ? 'bg-blue-50 dark:bg-blue-900/30' : '' }}">
                            {{ __('Arabic') }}
                        </a>
                        <a href="{{ route('language.switch', 'en') }}"
                            class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] {{ $currentLocale === 'en' ? 'bg-blue-50 dark:bg-blue-900/30' : '' }}">
                            {{ __('English') }}
                        </a>
                    </div>
                </div>
                <button type="button" class="theme-toggle" data-theme-toggle>
                    <span class="sr-only">{{ __('Toggle theme') }}</span>
                    <svg class="theme-icon theme-icon-sun" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4V2m0 20v-2m10-8h-2M4 12H2m15.364 6.364l-1.414-1.414M6.05 6.05 4.636 4.636m12.728 0-1.414 1.414M6.05 17.95l-1.414 1.414M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="theme-icon theme-icon-moon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg>
                </button>
                <div
                    class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                    {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                </div>
            </div>
        </div>
        <!-- Mobile Navigation Icons -->
        <div
            class="flex items-center justify-around px-2 py-3 border-t border-[#e3e3e0] dark:border-[#3E3E3A] overflow-x-auto">
            <a href="{{ route('dashboard') }}"
                class="flex flex-col items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]' }}"
                title="{{ __('Dashboard') }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-xs font-medium">{{ __('Home') }}</span>
            </a>
            <a href="{{ route('leads.index') }}"
                class="flex flex-col items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('leads.*') ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]' }}"
                title="{{ __('Leads') }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span class="text-xs font-medium">{{ __('Leads') }}</span>
            </a>
            <a href="{{ route('contracts.index') }}"
                class="flex flex-col items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('contracts.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]' }}"
                title="{{ __('Contracts') }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-xs font-medium">{{ __('Contracts') }}</span>
            </a>
            @unless ($isSalesAgent)
                <a href="{{ route('customers.index') }}"
                    class="flex flex-col items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('customers.*') ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]' }}"
                    title="{{ __('Customers') }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-xs font-medium">{{ __('Customers') }}</span>
                </a>
            @endunless
            <a href="{{ route('units.index') }}"
                class="flex flex-col items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('units.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]' }}"
                title="{{ __('Units') }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-xs font-medium">{{ __('Units') }}</span>
            </a>
            <a href="{{ route('appointments.index') }}"
                class="flex flex-col items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('appointments.*') ? 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]' }}"
                title="{{ __('Appointments') }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-xs font-medium">{{ __('Appointments') }}</span>
            </a>
            <a href="{{ route('calendar.index') }}"
                class="flex flex-col.items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('calendar.*') ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]' }}"
                title="{{ __('Calendar') }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-xs font-medium">{{ __('Calendar') }}</span>
            </a>
            <a href="{{ route('whatsapp.services.index') }}"
                class="flex flex-col items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('whatsapp.services.*') ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]' }}"
                title="{{ __('WhatsApp Services') }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v10a2 2 0 01-2 2z" />
                </svg>
                <span class="text-xs font-medium">{{ __('WhatsApp') }}</span>
            </a>
            @unless ($isSalesAgent)
                <a href="{{ route('teams.index') }}"
                    class="flex flex-col items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('teams.*') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]' }}"
                    title="{{ __('Teams') }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-xs font-medium">{{ __('Teams') }}</span>
                </a>
                <a href="{{ route('reports.index') }}"
                    class="flex flex-col items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]' }}"
                    title="{{ __('Reports') }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="text-xs font-medium">{{ __('Reports') }}</span>
                </a>
            @endunless
            @if ($currentUser->isAdmin())
                <a href="{{ route('users.index') }}"
                    class="flex flex-col items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]' }}"
                    title="{{ __('Users') }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-xs font-medium">{{ __('Users') }}</span>
                </a>
                <a href="{{ route('site.index') }}"
                    class="flex flex-col items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('site.*') ? 'bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]' }}"
                    title="{{ __('Site') }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                    <span class="text-xs font-medium">{{ __('Site') }}</span>
                </a>
            @endif
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div id="mobileMenu" class="lg:hidden fixed inset-0 z-40 bg-black bg-opacity-50 hidden"
        onclick="toggleMobileMenu()"></div>

    <!-- Mobile Sidebar Menu -->
    <aside id="mobileSidebar"
        class="lg:hidden fixed top-0 left-0 h-full w-64 bg-white dark:bg-[#161615] border-r border-[#e3e3e0] dark:border-[#3E3E3A] flex flex-col transform -translate-x-full transition-transform duration-300 z-50 pb-20">
        <div class="p-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-between">
            <button onclick="toggleMobileMenu()"
                class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-100 dark:hover:bg-[#3E3E3A] rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto p-4 pb-20">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('dashboard') ? '' : 'group-hover:scale-110' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="font-medium">{{ __('Dashboard') }}</span>
                    </a>
                    @if (request()->routeIs('dashboard'))
                        <ul class="mt-1 ml-4 space-y-1">
                            <li>
                                <a href="{{ route('dashboard') }}"
                                    class="flex items-center px-4 py-2 text-sm rounded-lg bg-gray-100 dark:bg-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC]">
                                    CRM
                                </a>
                            </li>
                        </ul>
                    @endif
                </li>
                <li>
                    <a href="{{ route('leads.index') }}"
                        class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('leads.*') ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg shadow-purple-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('leads.*') ? '' : 'group-hover:scale-110' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <span class="font-medium">{{ __('Leads') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('contracts.index') }}"
                        class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('contracts.*') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('contracts.*') ? '' : 'group-hover:scale-110' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="font-medium">{{ __('Contracts') }}</span>
                    </a>
                </li>
                @unless ($isSalesAgent)
                    <li>
                        <a href="{{ route('customers.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('customers.*') ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg shadow-green-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('customers.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="font-medium">{{ __('Customers') }}</span>
                        </a>
                    </li>
                @endunless
                <li>
                    <a href="{{ route('units.index') }}"
                        class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('units.*') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('units.*') ? '' : 'group-hover:scale-110' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="font-medium">{{ __('Units') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('appointments.index') }}"
                        class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('appointments.*') ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('appointments.*') ? '' : 'group-hover:scale-110' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="font-medium">{{ __('Appointments') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('calendar.index') }}"
                        class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('calendar.*') ? 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white shadow-lg shadow-yellow-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('calendar.*') ? '' : 'group-hover:scale-110' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="font-medium">{{ __('Calendar') }}</span>
                    </a>
                </li>
                @unless ($isSalesAgent)
                    <li>
                        <a href="{{ route('teams.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('teams.*') ? 'bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('teams.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="font-medium">{{ __('Teams') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-teal-500 to-teal-600 text-white shadow-lg shadow-teal-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('reports.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="font-medium">{{ __('Reports') }}</span>
                        </a>
                    </li>
                @endunless
                @if ($currentUser->isAdmin())
                    <li>
                        <a href="{{ route('lead-stages.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('lead-stages.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('lead-stages.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h10M4 18h7" />
                            </svg>
                            <span class="font-medium">{{ __('Lead Stages') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('lead-sources.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('lead-sources.*') ? 'bg-gradient-to-r from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('lead-sources.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h12m-12 5h18" />
                            </svg>
                            <span class="font-medium">{{ __('Lead Sources') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('users.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="font-medium">{{ __('Users') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('site.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('site.*') ? 'bg-gradient-to-r from-violet-500 to-purple-600 text-white shadow-lg shadow-violet-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('site.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                            <span class="font-medium">{{ __('Site') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
        <div class="p-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold shadow-lg shadow-blue-500/30 ring-2 ring-blue-500/20">
                        {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $currentUser->name }}
                        </p>
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                            {{ __(ucfirst(str_replace('_', ' ', $currentUser->role))) }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200 group">
                        <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Mobile Sidebar Menu Footer -->
    <div id="mobileSidebarFooter"
        class="lg:hidden fixed bottom-0 left-0 w-64 bg-white dark:bg-[#161615] border-t border-[#e3e3e0] dark:border-[#3E3E3A] p-4 transform translate-y-full transition-transform duration-300 z-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold shadow-lg shadow-blue-500/30">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                        {{ __(ucfirst(str_replace('_', ' ', auth()->user()->role))) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <div class="flex h-screen overflow-hidden">
        <!-- Desktop Sidebar -->
        <aside
            class="hidden lg:flex w-64 h-screen bg-white dark:bg-[#161615] border-r border-[#e3e3e0] dark:border-[#3E3E3A] flex flex-col">
            <div
                class="p-6 border-b border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-between overflow-visible">
                <div class="flex items-center gap-2 overflow-visible">
                    <!-- Missed Events Button -->
                    <button onclick="openMissedEventsModal()"
                        class="relative p-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] hover:bg-gray-100 dark:hover:bg-[#3E3E3A] rounded-lg transition-colors"
                        title="{{ __('Missed Events') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                    <!-- Notifications Bell -->
                    <div class="relative overflow-visible" id="notifications-container-desktop">
                        <button onclick="toggleNotificationsDesktop()"
                            class="relative p-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] hover:bg-gray-100 dark:hover:bg-[#3E3E3A] rounded-lg transition-colors"
                            title="{{ __('Notifications') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span id="notification-badge-desktop"
                                class="absolute top-0 {{ $isRTL ? 'left-0' : 'right-0' }} w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white dark:border-[#161615] hidden"></span>
                        </button>
                        <!-- Notifications Dropdown -->
                        <div id="notifications-dropdown-desktop"
                            class="hidden absolute {{ $isRTL ? 'right-full mr-2' : 'left-full ml-2' }} top-0 w-80 bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg shadow-xl z-[100] max-h-96 overflow-y-auto">
                            <div
                                class="p-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-between">
                                <h3 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">
                                    {{ __('Notifications') }}</h3>
                                <button onclick="markAllAsRead()"
                                    class="text-xs text-blue-500 hover:text-blue-600">{{ __('Mark all as read') }}</button>
                            </div>
                            <div id="notifications-list-desktop"
                                class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                                <div class="p-4 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ __('Loading...') }}</div>
                            </div>
                            <div class="p-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A] text-center">
                                <a href="{{ route('notifications.index') }}"
                                    class="text-sm text-blue-500 hover:text-blue-600">{{ __('View all notifications') }}</a>
                            </div>
                        </div>
                    </div>

                    <!-- Theme Toggle -->
                    <button type="button"
                        class="theme-toggle p-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] hover:bg-gray-100 dark:hover:bg-[#3E3E3A] rounded-lg transition-colors"
                        data-theme-toggle title="{{ __('Toggle theme') }}">
                        <svg class="theme-icon theme-icon-sun w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4V2m0 20v-2m10-8h-2M4 12H2m15.364 6.364l-1.414-1.414M6.05 6.05 4.636 4.636m12.728 0-1.414 1.414M6.05 17.95l-1.414 1.414M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg class="theme-icon theme-icon-moon w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                    </button>

                    <!-- Language Switcher -->
                    <div class="relative overflow-visible">
                        <button onclick="toggleLanguageMenuDesktop()"
                            class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] hover:bg-gray-100 dark:hover:bg-[#3E3E3A] rounded-lg transition-colors"
                            title="{{ __('Select Language') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                        </button>
                        <div id="languageMenuDesktop"
                            class="hidden absolute {{ $isRTL ? 'left-0' : 'right-0' }} mt-2 w-32 bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg shadow-lg z-[100]">
                            <a href="{{ route('language.switch', 'ar') }}"
                                class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] {{ $currentLocale === 'ar' ? 'bg-blue-50 dark:bg-blue-900/30' : '' }}">
                                {{ __('Arabic') }}
                            </a>
                            <a href="{{ route('language.switch', 'en') }}"
                                class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] {{ $currentLocale === 'en' ? 'bg-blue-50 dark:bg-blue-900/30' : '' }}">
                                {{ __('English') }}
                            </a>
                        </div>
                    </div>


                </div>
            </div>
            <nav class="flex-1 overflow-y-auto overflow-x-hidden p-4 min-h-0 overscroll-contain">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('dashboard') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="font-medium">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('leads.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('leads.*') ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg shadow-purple-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('leads.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span class="font-medium">{{ __('Leads') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contracts.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('contracts.*') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('contracts.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="font-medium">{{ __('Contracts') }}</span>
                        </a>
                    </li>
                    @unless ($isSalesAgent)
                        <li>
                            <a href="{{ route('customers.index') }}"
                                class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('customers.*') ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg shadow-green-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                                <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('customers.*') ? '' : 'group-hover:scale-110' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="font-medium">{{ __('Customers') }}</span>
                            </a>
                        </li>
                    @endunless
                    <li>
                        <a href="{{ route('units.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('units.*') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('units.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="font-medium">{{ __('Units') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('appointments.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('appointments.*') ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('appointments.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">{{ __('Appointments') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('calendar.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('calendar.*') ? 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white shadow-lg shadow-yellow-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('calendar.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">{{ __('Calendar') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('whatsapp.services.index') }}"
                            class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('whatsapp.services.*') ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('whatsapp.services.*') ? '' : 'group-hover:scale-110' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v10a2 2 0 01-2 2z" />
                            </svg>
                            <span class="font-medium">{{ __('WhatsApp Services') }}</span>
                        </a>
                    </li>
                    @unless ($isSalesAgent)
                        <li>
                            <a href="{{ route('teams.index') }}"
                                class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all.duration-200 {{ request()->routeIs('teams.*') ? 'bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                                <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('teams.*') ? '' : 'group-hover:scale-110' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="font-medium">{{ __('Teams') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.index') }}"
                                class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-teal-500 to-teal-600 text-white shadow-lg shadow-teal-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                                <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('reports.*') ? '' : 'group-hover:scale-110' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="font-medium">{{ __('Reports') }}</span>
                            </a>
                        </li>
                    @endunless
                    @if ($currentUser->isAdmin())
                        <li>
                            <a href="{{ route('lead-stages.index') }}"
                                class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('lead-stages.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                                <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('lead-stages.*') ? '' : 'group-hover:scale-110' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h10M4 18h7" />
                                </svg>
                                <span class="font-medium">{{ __('Lead Stages') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('lead-sources.index') }}"
                                class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('lead-sources.*') ? 'bg-gradient-to-r from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                                <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('lead-sources.*') ? '' : 'group-hover:scale-110' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7h18M3 12h12m-12 5h18" />
                                </svg>
                                <span class="font-medium">{{ __('Lead Sources') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('users.index') }}"
                                class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                                <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('users.*') ? '' : 'group-hover:scale-110' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span class="font-medium">{{ __('Users') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('site.index') }}"
                                class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-200 {{ request()->routeIs('site.*') ? 'bg-gradient-to-r from-violet-500 to-purple-600 text-white shadow-lg shadow-violet-500/30' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                                <svg class="w-5 h-5 mr-3 transition-transform duration-200 {{ request()->routeIs('site.*') ? '' : 'group-hover:scale-110' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                                <span class="font-medium">{{ __('Site') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>



            <div class="p-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold shadow-lg shadow-blue-500/30 ring-2 ring-blue-500/20">
                            {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                {{ $currentUser->name }}</p>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                {{ __(ucfirst(str_replace('_', ' ', $currentUser->role))) }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200 group">
                            <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="app-main flex-1 overflow-y-auto lg:pt-0 pt-32 lg:pr-14">
            <div class="p-4 lg:p-6">
                @if (session('success'))
                    <div
                        class="mb-6 animate-fade-in p-4 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 border-l-4 border-green-500 text-green-800 dark:text-green-200 rounded-lg shadow-md flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mb-6 animate-fade-in p-4 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900 dark:to-red-800 border-l-4 border-red-500 text-red-800 dark:text-red-200 rounded-lg shadow-md flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>


    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const sidebar = document.getElementById('mobileSidebar');
            const footer = document.getElementById('mobileSidebarFooter');

            const isOpen = !sidebar.classList.contains('-translate-x-full');

            if (isOpen) {
                // Close menu
                menu.classList.add('hidden');
                sidebar.classList.add('-translate-x-full');
                footer.classList.add('translate-y-full');
            } else {
                // Open menu
                menu.classList.remove('hidden');
                sidebar.classList.remove('-translate-x-full');
                footer.classList.remove('translate-y-full');
            }
        }

        function toggleLanguageMenu() {
            const mobileMenu = document.getElementById('languageMenu');
            const desktopMenu = document.getElementById('languageMenuDesktop');

            if (mobileMenu) {
                mobileMenu.classList.toggle('hidden');
            }
            if (desktopMenu) {
                desktopMenu.classList.toggle('hidden');
            }
        }

        function toggleLanguageMenuDesktop() {
            const desktopMenu = document.getElementById('languageMenuDesktop');
            if (desktopMenu) {
                desktopMenu.classList.toggle('hidden');
            }
        }

        // Close language menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('languageMenu');
            const desktopMenu = document.getElementById('languageMenuDesktop');
            const isClickInside = event.target.closest('.relative');

            if (!isClickInside) {
                if (mobileMenu) mobileMenu.classList.add('hidden');
                if (desktopMenu) desktopMenu.classList.add('hidden');
            }
        });

        // Notifications functionality
        let notificationsDropdownOpen = false;

        function toggleNotifications() {
            const dropdown = document.getElementById('notifications-dropdown');
            notificationsDropdownOpen = !notificationsDropdownOpen;

            if (notificationsDropdownOpen) {
                dropdown.classList.remove('hidden');
                loadNotifications('notifications-list');
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function toggleNotificationsDesktop() {
            const dropdown = document.getElementById('notifications-dropdown-desktop');
            notificationsDropdownOpen = !notificationsDropdownOpen;

            if (notificationsDropdownOpen) {
                dropdown.classList.remove('hidden');
                loadNotifications('notifications-list-desktop');
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function toggleNotificationsRight() {
            const dropdown = document.getElementById('notifications-dropdown-right');
            notificationsDropdownOpen = !notificationsDropdownOpen;

            if (notificationsDropdownOpen) {
                dropdown.classList.remove('hidden');
                loadNotifications('notifications-list-right');
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function toggleLanguageMenuRight() {
            const menu = document.getElementById('languageMenuRight');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        function loadNotifications(listId = 'notifications-list') {
            fetch('{{ route('notifications.recent') }}')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById(listId);
                    if (data.notifications && data.notifications.length > 0) {
                        list.innerHTML = data.notifications.map(notification => {
                            const data = notification.data || {};
                            const isRead = notification.read_at !== null;
                            return `
                                <a href="${data.url || '#'}" onclick="markNotificationAsRead('${notification.id}')" class="block p-4 hover:bg-gray-50 dark:hover:bg-[#3E3E3A] transition-colors ${!isRead ? 'bg-blue-50 dark:bg-blue-900/20' : ''}">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full ${!isRead ? 'bg-blue-500' : 'bg-transparent'}"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">${data.message || ''}</p>
                                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">${formatNotificationTime(notification.created_at)}</p>
                                        </div>
                                    </div>
                                </a>
                            `;
                        }).join('');
                    } else {
                        list.innerHTML =
                            '<div class="p-4 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No notifications') }}</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
        }

        function markNotificationAsRead(id) {
            fetch(`{{ route('notifications.mark-as-read', ':id') }}`.replace(':id', id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationCount();
                        loadNotifications('notifications-list');
                        loadNotifications('notifications-list-desktop');
                        loadNotifications('notifications-list-right');
                    }
                });
        }

        function markAllAsRead() {
            fetch('{{ route('notifications.mark-all-read') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationCount();
                        loadNotifications('notifications-list');
                        loadNotifications('notifications-list-desktop');
                        loadNotifications('notifications-list-right');
                    }
                });
        }

        function updateNotificationCount() {
            fetch('{{ route('notifications.unread-count') }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    const badgeDesktop = document.getElementById('notification-badge-desktop');
                    const badgeRight = document.getElementById('notification-badge-right');
                    if (data.count > 0) {
                        const countText = data.count > 99 ? '99+' : data.count;
                        if (badge) {
                            badge.textContent = countText;
                            badge.classList.remove('hidden');
                        }
                        if (badgeDesktop) {
                            badgeDesktop.textContent = countText;
                            badgeDesktop.classList.remove('hidden');
                        }
                        if (badgeRight) {
                            badgeRight.textContent = countText;
                            badgeRight.classList.remove('hidden');
                        }
                    } else {
                        if (badge) badge.classList.add('hidden');
                        if (badgeDesktop) badgeDesktop.classList.add('hidden');
                        if (badgeRight) badgeRight.classList.add('hidden');
                    }
                });
        }

        function formatNotificationTime(timeString) {
            const time = new Date(timeString);
            const now = new Date();
            const diff = now - time;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);

            if (minutes < 1) return '{{ __('Just now') }}';
            if (minutes < 60) return `${minutes} {{ __('minutes ago') }}`;
            if (hours < 24) return `${hours} {{ __('hours ago') }}`;
            return `${days} {{ __('days ago') }}`;
        }

        // Load notification count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationCount();
            // Update every 30 seconds
            setInterval(updateNotificationCount, 30000);
        });

        // Close notifications dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const container = document.getElementById('notifications-container');
            const containerDesktop = document.getElementById('notifications-container-desktop');
            const containerRight = document.getElementById('notifications-container-right');
            if (container && !container.contains(event.target)) {
                const dropdown = document.getElementById('notifications-dropdown');
                if (dropdown) {
                    dropdown.classList.add('hidden');
                    notificationsDropdownOpen = false;
                }
            }
            if (containerDesktop && !containerDesktop.contains(event.target)) {
                const dropdown = document.getElementById('notifications-dropdown-desktop');
                if (dropdown) {
                    dropdown.classList.add('hidden');
                    notificationsDropdownOpen = false;
                }
            }
            if (containerRight && !containerRight.contains(event.target)) {
                const dropdown = document.getElementById('notifications-dropdown-right');
                if (dropdown) {
                    dropdown.classList.add('hidden');
                    notificationsDropdownOpen = false;
                }
            }
        });

        // Close language menu right when clicking outside
        document.addEventListener('click', function(event) {
            const languageMenuRight = document.getElementById('languageMenuRight');
            if (languageMenuRight && !event.target.closest('#languageMenuRight') && !event.target.closest(
                    '[onclick="toggleLanguageMenuRight()"]')) {
                languageMenuRight.classList.add('hidden');
            }
        });

        // Close menu when clicking outside
        document.getElementById('mobileMenu')?.addEventListener('click', function() {
            toggleMobileMenu();
        });

        // Close menu when clicking on links
        document.querySelectorAll('#mobileSidebar a').forEach(link => {
            link.addEventListener('click', function() {
                toggleMobileMenu();
            });
        });

        // Missed Events Modal
        function openMissedEventsModal() {
            const modal = document.getElementById('missedEventsModal');
            const loading = document.getElementById('missedEventsLoading');
            const content = document.getElementById('missedEventsContent');
            const empty = document.getElementById('missedEventsEmpty');

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            content.classList.add('hidden');
            empty.classList.add('hidden');

            fetch('{{ route('dashboard.missed-events') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loading.classList.add('hidden');

                    if (data.events && data.events.length > 0) {
                        const eventsList = document.getElementById('missedEventsList');
                        eventsList.innerHTML = '';

                        data.events.forEach(event => {
                            const eventItem = document.createElement('div');
                            eventItem.className =
                                'p-4 bg-gray-50 dark:bg-[#0a0a0a] rounded-lg hover:bg-gray-100 dark:hover:bg-[#161615] transition-colors';
                            eventItem.innerHTML = `
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">${event.title || '{{ __('Event') }}'}</h4>
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">${event.details || ''}</p>
                                    <div class="flex items-center gap-4 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                        <span><strong>{{ __('Lead') }}:</strong> ${event.lead_name}</span>
                                        <span><strong>{{ __('Scheduled') }}:</strong> ${event.scheduled_at_formatted}</span>
                                    </div>
                                </div>
                                <a href="/leads/${event.lead_id}" class="ml-4 text-blue-500 hover:text-blue-700 text-sm font-medium">
                                    {{ __('View') }}
                                </a>
                            </div>
                        `;
                            eventsList.appendChild(eventItem);
                        });

                        content.classList.remove('hidden');
                    } else {
                        empty.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading missed events:', error);
                    loading.classList.add('hidden');
                    empty.classList.remove('hidden');
                });
        }

        function closeMissedEventsModal() {
            document.getElementById('missedEventsModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('missedEventsModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeMissedEventsModal();
            }
        });
    </script>

    <!-- Missed Events Modal -->
    <div id="missedEventsModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-[#161615] rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between p-6 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Missed Events') }}</h2>
                <button onclick="closeMissedEventsModal()"
                    class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <!-- Loading State -->
                <div id="missedEventsLoading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    <p class="mt-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Loading events...') }}</p>
                </div>

                <!-- Content -->
                <div id="missedEventsContent" class="hidden">
                    <div id="missedEventsList" class="space-y-3">
                        <!-- Events will be loaded here -->
                    </div>
                </div>

                <!-- Empty State -->
                <div id="missedEventsEmpty" class="hidden text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-[#706f6c] dark:text-[#A1A09A] mb-4" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No missed events found') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Missed Events Modal
        function openMissedEventsModal() {
            const modal = document.getElementById('missedEventsModal');
            const loading = document.getElementById('missedEventsLoading');
            const content = document.getElementById('missedEventsContent');
            const empty = document.getElementById('missedEventsEmpty');

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            content.classList.add('hidden');
            empty.classList.add('hidden');

            fetch('{{ route('dashboard.missed-events') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loading.classList.add('hidden');

                    if (data.events && data.events.length > 0) {
                        const eventsList = document.getElementById('missedEventsList');
                        eventsList.innerHTML = '';

                        data.events.forEach(event => {
                            const eventItem = document.createElement('div');
                            eventItem.className =
                                'p-4 bg-gray-50 dark:bg-[#0a0a0a] rounded-lg hover:bg-gray-100 dark:hover:bg-[#161615] transition-colors';
                            eventItem.innerHTML = `
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">${event.title || '{{ __('Event') }}'}</h4>
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">${event.details || ''}</p>
                                    <div class="flex items-center gap-4 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                        <span><strong>{{ __('Lead') }}:</strong> ${event.lead_name}</span>
                                        <span><strong>{{ __('Scheduled') }}:</strong> ${event.scheduled_at_formatted}</span>
                                    </div>
                                </div>
                                <a href="/leads/${event.lead_id}" class="ml-4 text-blue-500 hover:text-blue-700 text-sm font-medium">
                                    {{ __('View') }}
                                </a>
                            </div>
                        `;
                            eventsList.appendChild(eventItem);
                        });

                        content.classList.remove('hidden');
                    } else {
                        empty.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading missed events:', error);
                    loading.classList.add('hidden');
                    empty.classList.remove('hidden');
                });
        }

        function closeMissedEventsModal() {
            document.getElementById('missedEventsModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('missedEventsModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeMissedEventsModal();
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
