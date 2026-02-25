<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
    $siteSettings = \App\Models\SiteSetting::all()->keyBy('key');
    $siteName = $siteSettings['site_name']->value ?? 'WE SOLD';
    $siteLogo = $siteSettings['site_logo']->value ?? null;
    $siteFavicon = $siteSettings['site_favicon']->value ?? null;
    @endphp
    <!-- Primary Meta Tags -->
    <title>{{ $siteName }} - {{ __('Where Your Real Estate Dreams Become Reality') }}</title>
    <meta name="title" content="{{ $siteName }} - {{ __('Where Your Real Estate Dreams Become Reality') }}">
    <meta name="description" content="{{ $siteName }} {{ __('helps you buy, sell & invest with confidence, ease, and proven results. Over 500+ homes sold with 98% client satisfaction. Expert real estate services since 2013.') }}">
    <meta name="keywords" content="real estate, property, homes for sale, real estate agent, property management, investment properties, {{ __('buying homes') }}, {{ __('selling homes') }}">
    <meta name="author" content="{{ $siteName }}">
    <meta name="robots" content="index, follow">
    <meta name="language" content="{{ app()->getLocale() }}">
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ $siteName }} - {{ __('Where Your Real Estate Dreams Become Reality') }}">
    <meta property="og:description" content="{{ $siteName }} {{ __('helps you buy, sell & invest with confidence, ease, and proven results. Over 500+ homes sold with 98% client satisfaction.') }}">
    <meta property="og:image" content="{{ $siteSettings['hero_image']->value ? \App\Helpers\UploadHelper::url($siteSettings['hero_image']->value) : asset('images/og-image.jpg') }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_SA' : 'en_US' }}">
    <meta property="og:site_name" content="{{ $siteName }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="{{ $siteName }} - {{ __('Where Your Real Estate Dreams Become Reality') }}">
    <meta name="twitter:description" content="{{ $siteName }} {{ __('helps you buy, sell & invest with confidence, ease, and proven results.') }}">
    <meta name="twitter:image" content="{{ $siteSettings['hero_image']->value ? \App\Helpers\UploadHelper::url($siteSettings['hero_image']->value) : asset('images/og-image.jpg') }}">

    <!-- Performance Optimizations -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kiro:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="dns-prefetch" href="https://images.unsplash.com">

    <!-- Favicon -->
    @if($siteFavicon)
    <link rel="icon" type="image/png" href="{{ \App\Helpers\UploadHelper::url($siteFavicon) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if(file_exists(public_path('build/manifest.json')))
    @php
    try {
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    if (isset($manifest['resources/css/app.css']['file'])) {
    $cssFile = $manifest['resources/css/app.css']['file'];
    }
    if (isset($manifest['resources/js/app.js']['file'])) {
    $jsFile = $manifest['resources/js/app.js']['file'];
    }
    } catch (\Exception $e) {
    // Manifest file exists but couldn't be read
    }
    @endphp
    @isset($cssFile)
    <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}" integrity="" crossorigin="anonymous">
    @endisset
    @isset($jsFile)
    <script type="module" src="{{ asset('build/' . $jsFile) }}" crossorigin="anonymous"></script>
    @endisset
    @endif
    <style>
        body {
            font-family: 'Kiro', ui-sans-serif, system-ui, sans-serif;
        }

        .hero-bg {
            @if( !isset($siteSettings['hero_image']) || !$siteSettings['hero_image']->value) background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1920');
            @endif background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* Performance: Optimize background attachment on mobile */
        @media (max-width: 768px) {
            .hero-bg {
                background-attachment: scroll;
            }
        }

        /* RTL Support */
        [dir="rtl"] .space-x-8>*+* {
            margin-left: 0;
            margin-right: 2rem;
        }

        [dir="rtl"] .space-y-2>*+* {
            margin-top: 0.5rem;
            margin-bottom: 0;
        }

        [dir="rtl"] .gap-4 {
            gap: 1rem;
        }

        [dir="rtl"] .flex-row {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .text-left {
            text-align: right;
        }

        [dir="rtl"] .text-right {
            text-align: left;
        }

        [dir="rtl"] .ml-2 {
            margin-left: 0;
            margin-right: 0.5rem;
        }

        [dir="rtl"] .mr-2 {
            margin-right: 0;
            margin-left: 0.5rem;
        }

        [dir="rtl"] .mr-4 {
            margin-right: 0;
            margin-left: 1rem;
        }

        [dir="rtl"] .ml-4 {
            margin-left: 0;
            margin-right: 1rem;
        }

        /* Hide scrollbar for partners slider */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Section Animations */
        .section-animate {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .section-animate.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        /* Animate from left */
        .animate-from-left {
            opacity: 0;
            transform: translateX(-50px);
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-from-left.animate-in {
            opacity: 1;
            transform: translateX(0);
        }

        /* Animate from right */
        .animate-from-right {
            opacity: 0;
            transform: translateX(50px);
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-from-right.animate-in {
            opacity: 1;
            transform: translateX(0);
        }

        /* Fallback: Show sections if JS is disabled or not loaded */
        .no-js .section-animate,
        .no-js .animate-from-left,
        .no-js .animate-from-right {
            opacity: 1;
            transform: translateX(0) translateY(0);
        }

        /* Show sections after a delay if JS fails to load */
        @media (prefers-reduced-motion: no-preference) {
            .section-animate:not(.animate-in) {
                animation: fadeInFallback 0.1s ease-out 1s forwards;
            }
        }

        @keyframes fadeInFallback {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Fade In Animation */
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Slide In From Left */
        .slide-in-left {
            animation: slideInLeft 0.8s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Slide In From Right */
        .slide-in-right {
            animation: slideInRight 0.8s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Scale In Animation */
        .scale-in {
            animation: scaleIn 0.6s ease-out;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Hero Section Animation */
        .hero-content {
            animation: heroFadeIn 1.2s ease-out;
        }

        @keyframes heroFadeIn {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stagger Animation for Children */
        .stagger-children>* {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .stagger-children>*:nth-child(1) {
            animation-delay: 0.1s;
        }

        .stagger-children>*:nth-child(2) {
            animation-delay: 0.2s;
        }

        .stagger-children>*:nth-child(3) {
            animation-delay: 0.3s;
        }

        .stagger-children>*:nth-child(4) {
            animation-delay: 0.4s;
        }

        .stagger-children>*:nth-child(5) {
            animation-delay: 0.5s;
        }

        .stagger-children>*:nth-child(6) {
            animation-delay: 0.6s;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hover Effects */
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        /* Smooth Transitions */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
</head>

<body class="bg-white dark:bg-[#0a0a0a] no-js">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-black/90 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    @if($siteLogo)
                    <img src="{{ \App\Helpers\UploadHelper::url($siteLogo) }}" alt="{{ $siteName }}" class="h-10 w-auto" width="40" height="40" loading="eager" fetchpriority="high">
                    @endif
                    <a href="{{ route('home') }}" class="text-white text-2xl font-bold">{{ $siteName }}</a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-white hover:text-orange-400 transition-colors">{{ __('Home') }}</a>
                    <a href="{{ route('about') }}" class="text-white hover:text-orange-400 transition-colors">{{ __('About') }}</a>
                    <a href="{{ route('listings') }}" class="text-white hover:text-orange-400 transition-colors">{{ __('Listings') }}</a>
                    <a href="{{ route('services') }}" class="text-white hover:text-orange-400 transition-colors">{{ __('Services') }}</a>
                    <a href="{{ route('contact') }}" class="text-white hover:text-orange-400 transition-colors">{{ __('Contact') }}</a>

                    <!-- Language Switcher -->
                    <div class="relative">
                        <button onclick="toggleLanguageMenu()" class="text-white hover:text-orange-400 transition-colors flex items-center gap-1" title="{{ __('Select Language') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm">{{ strtoupper(app()->getLocale()) }}</span>
                        </button>
                        <div id="languageMenu" class="hidden absolute right-0 mt-2 w-32 bg-white dark:bg-[#161615] border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg z-50">
                            <a href="{{ route('language.switch', 'ar') }}" class="block px-4 py-2 text-sm text-gray-800 dark:text-white hover:bg-gray-50 dark:hover:bg-[#3E3E3A] {{ app()->getLocale() === 'ar' ? 'bg-orange-50 dark:bg-orange-900/30' : '' }}">
                                {{ __('Arabic') }}
                            </a>
                            <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-800 dark:text-white hover:bg-gray-50 dark:hover:bg-[#3E3E3A] {{ app()->getLocale() === 'en' ? 'bg-orange-50 dark:bg-orange-900/30' : '' }}">
                                {{ __('English') }}
                            </a>
                        </div>
                    </div>

                    @auth
                    <a href="{{ route('dashboard') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors">{{ __('Dashboard') }}</a>
                    @else
                    <a href="{{ route('login') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">{{ __('Login') }}</a>
                    @endauth
                </div>
                <button class="md:hidden text-white" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-black/95 backdrop-blur-sm">
            <div class="px-4 pt-2 pb-4 space-y-2">
                <a href="{{ route('home') }}" class="block text-white hover:text-orange-400 py-2">{{ __('Home') }}</a>
                <a href="{{ route('about') }}" class="block text-white hover:text-orange-400 py-2">{{ __('About') }}</a>
                <a href="{{ route('listings') }}" class="block text-white hover:text-orange-400 py-2">{{ __('Listings') }}</a>
                <a href="{{ route('services') }}" class="block text-white hover:text-orange-400 py-2">{{ __('Services') }}</a>
                <a href="{{ route('contact') }}" class="block text-white hover:text-orange-400 py-2">{{ __('Contact') }}</a>

                <!-- Language Switcher Mobile -->
                <div class="border-t border-gray-700 pt-2 mt-2">
                    <div class="relative">
                        <button onclick="toggleMobileLanguageMenu()" class="text-white hover:text-orange-400 transition-colors flex items-center gap-1 py-2 w-full justify-center" title="{{ __('Select Language') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm">{{ strtoupper(app()->getLocale()) }}</span>
                        </button>
                        <div id="mobileLanguageMenu" class="hidden absolute left-1/2 transform -translate-x-1/2 mt-2 w-32 bg-white dark:bg-[#161615] border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg z-50">
                            <a href="{{ route('language.switch', 'ar') }}" class="block px-4 py-2 text-sm text-gray-800 dark:text-white hover:bg-gray-50 dark:hover:bg-[#3E3E3A] {{ app()->getLocale() === 'ar' ? 'bg-orange-50 dark:bg-orange-900/30' : '' }}">
                                {{ __('Arabic') }}
                            </a>
                            <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-800 dark:text-white hover:bg-gray-50 dark:hover:bg-[#3E3E3A] {{ app()->getLocale() === 'en' ? 'bg-orange-50 dark:bg-orange-900/30' : '' }}">
                                {{ __('English') }}
                            </a>
                        </div>
                    </div>
                </div>
                @auth
                <a href="{{ route('dashboard') }}" class="block bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-center">{{ __('Dashboard') }}</a>
                @else
                <a href="{{ route('login') }}" class="block bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-center">{{ __('Login') }}</a>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        @if($siteLogo)
                        <img src="{{ \App\Helpers\UploadHelper::url($siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto" width="32" height="32" loading="lazy">
                        @endif
                        <h2 class="text-2xl font-bold">{{ $siteName }}</h2>
                    </div>
                    <p class="text-gray-400">{{ __('Your trusted partner in real estate. Helping you find your perfect home since 2013.') }}</p>
                    <div class="flex gap-4 mt-4">
                        @if(isset($siteSettings['site_facebook']) && $siteSettings['site_facebook']->value)
                        <a href="{{ $siteSettings['site_facebook']->value }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Visit our Facebook page') }}" class="text-gray-400 hover:text-orange-400 transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        @endif
                        @if(isset($siteSettings['site_twitter']) && $siteSettings['site_twitter']->value)
                        <a href="{{ $siteSettings['site_twitter']->value }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Visit our Twitter page') }}" class="text-gray-400 hover:text-orange-400 transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                        @endif
                        @if(isset($siteSettings['site_instagram']) && $siteSettings['site_instagram']->value)
                        <a href="{{ $siteSettings['site_instagram']->value }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Visit our Instagram page') }}" class="text-gray-400 hover:text-orange-400 transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                        @endif
                        @if(isset($siteSettings['site_linkedin']) && $siteSettings['site_linkedin']->value)
                        <a href="{{ $siteSettings['site_linkedin']->value }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Visit our LinkedIn page') }}" class="text-gray-400 hover:text-orange-400 transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">{{ __('Quick Links') }}</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('about') }}" class="hover:text-orange-400 transition-colors">{{ __('About Us') }}</a></li>
                        <li><a href="{{ route('listings') }}" class="hover:text-orange-400 transition-colors">{{ __('Listings') }}</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-orange-400 transition-colors">{{ __('Services') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-orange-400 transition-colors">{{ __('Contact') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">{{ __('Services') }}</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-orange-400 transition-colors">{{ __('Home Buying') }}</a></li>
                        <li><a href="#" class="hover:text-orange-400 transition-colors">{{ __('Home Selling') }}</a></li>
                        <li><a href="#" class="hover:text-orange-400 transition-colors">{{ __('Property Management') }}</a></li>
                        <li><a href="#" class="hover:text-orange-400 transition-colors">{{ __('Investment Consulting') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">{{ __('Contact Info') }}</h4>
                    <ul class="space-y-3 text-gray-400">
                        <!-- Phone Numbers -->
                        @if((isset($siteSettings['site_phone']) && $siteSettings['site_phone']->value) || (isset($siteSettings['site_phone2']) && $siteSettings['site_phone2']->value))
                        <li>
                            <h5 class="text-white text-sm font-medium mb-2">{{ __('Phone Numbers') }}</h5>
                            <ul class="space-y-2">
                                @if(isset($siteSettings['site_phone']) && $siteSettings['site_phone']->value)
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span>{{ $siteSettings['site_phone']->value }}</span>
                                </li>
                                @endif
                                @if(isset($siteSettings['site_phone2']) && $siteSettings['site_phone2']->value)
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span>{{ $siteSettings['site_phone2']->value }}</span>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        <!-- Addresses -->
                        @if((isset($siteSettings['site_address']) && $siteSettings['site_address']->value) || (isset($siteSettings['site_address2']) && $siteSettings['site_address2']->value))
                        <li>
                            <h5 class="text-white text-sm font-medium mb-2">{{ __('Addresses') }}</h5>
                            <ul class="space-y-2">
                                @if(isset($siteSettings['site_address']) && $siteSettings['site_address']->value)
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ $siteSettings['site_address']->value }}</span>
                                </li>
                                @endif
                                @if(isset($siteSettings['site_address2']) && $siteSettings['site_address2']->value)
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ $siteSettings['site_address2']->value }}</span>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        <!-- Email -->
                        @if(isset($siteSettings['site_email']) && $siteSettings['site_email']->value)
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $siteSettings['site_email']->value }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        function toggleLanguageMenu() {
            const menu = document.getElementById('languageMenu');
            menu.classList.toggle('hidden');
        }

        function toggleMobileLanguageMenu() {
            const menu = document.getElementById('mobileLanguageMenu');
            menu.classList.toggle('hidden');
        }

        // Close language menu when clicking outside
        document.addEventListener('click', function(event) {
            const languageButton = event.target.closest('[onclick="toggleLanguageMenu()"]');
            const mobileLanguageButton = event.target.closest('[onclick="toggleMobileLanguageMenu()"]');
            const languageMenu = document.getElementById('languageMenu');
            const mobileLanguageMenu = document.getElementById('mobileLanguageMenu');

            if (languageMenu && !languageButton && !languageMenu.contains(event.target)) {
                languageMenu.classList.add('hidden');
            }
            if (mobileLanguageMenu && !mobileLanguageButton && !mobileLanguageMenu.contains(event.target)) {
                mobileLanguageMenu.classList.add('hidden');
            }
        });
    </script>

    <script defer>
        // Smooth scroll for anchor links
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });

        // FAQ Accordion - Deferred
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFAQ);
        } else {
            initFAQ();
        }

        function initFAQ() {
            document.querySelectorAll('.faq-item').forEach(item => {
                const question = item.querySelector('.faq-question');
                const answer = item.querySelector('.faq-answer');
                const icon = question.querySelector('svg');

                question.addEventListener('click', () => {
                    const isOpen = item.classList.contains('open');

                    // Close all items
                    document.querySelectorAll('.faq-item').forEach(i => {
                        i.classList.remove('open');
                        i.querySelector('.faq-answer').classList.add('hidden');
                        const svg = i.querySelector('.faq-question svg');
                        if (svg) svg.classList.remove('rotate-180');
                    });

                    // Open clicked item if it wasn't open
                    if (!isOpen) {
                        item.classList.add('open');
                        answer.classList.remove('hidden');
                        if (icon) icon.classList.add('rotate-180');
                    }
                });
            });
        }
    </script>
</body>

</html>