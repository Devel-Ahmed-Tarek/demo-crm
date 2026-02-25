@extends('layouts.landing')

@section('content')
<!-- Navigation -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-black/90 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                @if($siteLogo)
                    <img src="{{ \App\Helpers\UploadHelper::url($siteLogo) }}" alt="{{ $siteName }}" class="h-10 w-auto">
                @endif
                <span class="text-white text-2xl font-bold">{{ $siteName }}</span>
            </a>
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
                <a href="{{ route('login') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors">{{ __('Login') }}</a>
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
            @auth
            <a href="{{ route('dashboard') }}" class="block bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-center mt-2">{{ __('Dashboard') }}</a>
            @else
            <a href="{{ route('login') }}" class="block bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-center mt-2">{{ __('Login') }}</a>
            @endauth
        </div>
    </div>
</nav>

<!-- Unit Details Section -->
<section class="pt-24 pb-12 bg-white dark:bg-[#0a0a0a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                <li><a href="{{ route('home') }}" class="hover:text-orange-500">{{ __('Home') }}</a></li>
                <li>/</li>
                <li><a href="{{ route('listings') }}" class="hover:text-orange-500">{{ __('Listings') }}</a></li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">{{ $unit->code }}</li>
            </ol>
        </nav>

        <div class="grid lg:grid-cols-2 gap-8 mb-12">
            <!-- Image Gallery -->
            <div>
                @if($unit->primaryImage && $unit->primaryImage->first())
                    <div class="mb-4">
                        <img src="{{ $unit->primaryImage->first()->image_url }}" alt="{{ $unit->code }}" class="w-full h-96 object-cover rounded-xl shadow-lg" id="mainImage">
                    </div>
                @elseif($unit->images->count() > 0)
                    <div class="mb-4">
                        <img src="{{ $unit->images->first()->image_url }}" alt="{{ $unit->code }}" class="w-full h-96 object-cover rounded-xl shadow-lg" id="mainImage">
                    </div>
                @else
                    <div class="w-full h-96 bg-gray-200 dark:bg-[#3E3E3A] rounded-xl flex items-center justify-center">
                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif

                @if($unit->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($unit->images->take(4) as $image)
                            <img src="{{ $image->image_url }}" alt="{{ $unit->code }}" 
                                class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity"
                                onclick="document.getElementById('mainImage').src = this.src">
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Unit Info -->
            <div>
                <div class="mb-4">
                    @if($unit->status == 'reserved')
                        <span class="px-4 py-2 text-sm rounded-full bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300 font-medium">{{ __('Reserved') }}</span>
                    @elseif($unit->status == 'sold')
                        <span class="px-4 py-2 text-sm rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 font-medium">{{ __('Sold') }}</span>
                    @else
                        <span class="px-4 py-2 text-sm rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 font-medium">{{ __('Available') }}</span>
                    @endif
                </div>

                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ $unit->code }}</h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 mb-6">{{ $unit->location }}</p>

                <div class="bg-gray-50 dark:bg-[#161615] rounded-xl p-6 mb-6">
                    <div class="text-3xl font-bold text-orange-500 mb-2">${{ number_format($unit->price, 0) }}</div>
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Area') }}</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $unit->area }} m²</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Rooms') }}</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $unit->rooms }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Status') }}</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ __(ucfirst($unit->status)) }}</div>
                        </div>
                    </div>
                </div>

                @if($unit->features->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ __('Features') }}</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($unit->features as $feature)
                                <span class="px-3 py-1 text-sm rounded-full bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300">{{ $feature->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($unit->description)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ __('Description') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $unit->description }}</p>
                    </div>
                @endif

                <div class="flex gap-4">
                    <a href="{{ route('contact') }}" class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-3 rounded-lg text-center font-semibold transition-all transform hover:scale-105">
                        {{ __('Contact Us') }}
                    </a>
                    <a href="{{ route('listings') }}" class="px-6 py-3 border-2 border-orange-500 text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg font-semibold transition-all">
                        {{ __('Back to Listings') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Units -->
        @if($relatedUnits->count() > 0)
        <div class="mt-16">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{{ __('Related Properties') }}</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedUnits as $relatedUnit)
                    <a href="{{ route('unit.details', $relatedUnit) }}" class="bg-white dark:bg-[#161615] rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        @if($relatedUnit->primaryImage && $relatedUnit->primaryImage->first())
                            <img src="{{ $relatedUnit->primaryImage->first()->image_url }}" alt="{{ $relatedUnit->code }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 dark:bg-[#3E3E3A] flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $relatedUnit->code }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $relatedUnit->location }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-orange-500">${{ number_format($relatedUnit->price, 0) }}</span>
                                @if($relatedUnit->status == 'reserved')
                                    <span class="px-2 py-1 text-xs rounded-full bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300">{{ __('Reserved') }}</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">{{ __('Available') }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

<script>
    function toggleLanguageMenu() {
        const menu = document.getElementById('languageMenu');
        menu.classList.toggle('hidden');
    }

    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    }

    document.addEventListener('click', function(event) {
        const languageButton = event.target.closest('[onclick="toggleLanguageMenu()"]');
        const languageMenu = document.getElementById('languageMenu');

        if (languageMenu && !languageButton && !languageMenu.contains(event.target)) {
            languageMenu.classList.add('hidden');
        }
    });
</script>
@endsection

