@extends('layouts.landing')

@section('content')
<div class="bg-gray-50 dark:bg-[#0a0a0a] py-12 pt-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm font-medium text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ route('home') }}" class="text-orange-500 hover:text-orange-600">{{ __('Home') }}</a>
                    <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568 0 33.941z"/></svg>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('services') }}" class="text-orange-500 hover:text-orange-600">{{ __('Services') }}</a>
                    <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568 0 33.941z"/></svg>
                </li>
                <li>
                    <span class="text-gray-700 dark:text-gray-300">{{ $service->title }}</span>
                </li>
            </ol>
        </nav>

        <div class="bg-white dark:bg-[#161615] rounded-xl shadow-lg overflow-hidden">
            <!-- Service Header -->
            <div class="p-8 md:p-12 bg-gradient-to-r from-orange-500 to-red-500 text-white">
                <div class="flex items-center gap-6 mb-6">
                    @if($service->icon_type === 'image' && $service->icon_image_url)
                        <div class="w-20 h-20 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <img src="{{ $service->icon_image_url }}" alt="{{ $service->translated_title }}" class="w-16 h-16 object-contain">
                        </div>
                    @elseif($service->icon)
                        <div class="text-6xl">{{ $service->icon }}</div>
                    @endif
                    <div>
                        <h1 class="text-4xl font-bold mb-2">{{ $service->translated_title }}</h1>
                        <p class="text-xl text-orange-100">{{ __('Professional Real Estate Service') }}</p>
                    </div>
                </div>
            </div>

            <!-- Service Content -->
            <div class="p-8 md:p-12">
                <div class="prose prose-lg dark:prose-invert max-w-none">
                    <div class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed whitespace-pre-line">
                        {{ $service->translated_description }}
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="mt-12 p-8 bg-gray-50 dark:bg-[#0a0a0a] rounded-xl border border-gray-200 dark:border-gray-800">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Interested in This Service?') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('Contact us today to learn more about how we can help you with your real estate needs.') }}</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('contact') }}" class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-3 rounded-lg font-semibold transition-all transform hover:scale-105 text-center">
                            {{ __('Get Started Today') }}
                        </a>
                        @if($service->link)
                        <a href="{{ $service->link }}" target="_blank" rel="noopener noreferrer" class="bg-white dark:bg-[#161615] border-2 border-orange-500 text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20 px-8 py-3 rounded-lg font-semibold transition-all text-center">
                            {{ $service->translated_link_text ?? __('Learn More') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Services -->
        @if($relatedServices->count() > 0)
        <div class="mt-12">
            <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-8">{{ __('Other Services') }}</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($relatedServices as $relatedService)
                <div class="bg-white dark:bg-[#161615] rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                    @if($relatedService->icon_type === 'image' && $relatedService->icon_image_url)
                        <div class="mb-4 flex justify-center">
                            <img src="{{ $relatedService->icon_image_url }}" alt="{{ $relatedService->translated_title }}" class="w-16 h-16 object-contain">
                        </div>
                    @elseif($relatedService->icon)
                        <div class="text-orange-500 text-5xl mb-4 text-center">{{ $relatedService->icon }}</div>
                    @endif
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 text-center">{{ $relatedService->translated_title }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 text-center">{{ Str::limit($relatedService->translated_description, 100) }}</p>
                    <a href="{{ route('service.details', $relatedService) }}" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center py-2 rounded-lg font-semibold transition-colors">
                        {{ $relatedService->translated_link_text ?? __('Learn More') }}
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Back to Services -->
        <div class="mt-8 text-center">
            <a href="{{ route('services') }}" class="inline-flex items-center text-orange-500 hover:text-orange-600 font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Back to All Services') }}
            </a>
        </div>
    </div>
</div>
@endsection

