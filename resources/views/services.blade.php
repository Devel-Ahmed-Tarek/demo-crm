@extends('layouts.landing')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-orange-800 to-orange-300 text-white pt-24 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold mb-4 text-center">{{ __('Our Services') }}</h1>
        <p class="text-xl text-center text-orange-100">{{ __('Comprehensive real estate solutions tailored to your needs') }}</p>
    </div>
</section>

<!-- Main Services Section -->
<section class="py-20 bg-white dark:bg-[#0a0a0a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($services->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($services as $service)
            <div class="bg-white dark:bg-[#161615] rounded-xl p-8 shadow-lg hover:shadow-2xl transition-shadow border border-gray-200 dark:border-gray-800">
                @if($service->icon_type === 'image' && $service->icon_image_url)
                    <div class="mb-4 flex justify-center">
                        <img src="{{ $service->icon_image_url }}" alt="{{ $service->translated_title }}" class="w-16 h-16 object-contain">
                    </div>
                @elseif($service->icon)
                    <div class="text-orange-500 text-5xl mb-4 text-center">{{ $service->icon }}</div>
                @endif
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 text-center">{{ $service->translated_title }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6 text-center">{{ Str::limit($service->translated_description, 150) }}</p>
                <a href="{{ route('service.details', $service) }}" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center px-6 py-3 rounded-lg font-semibold transition-colors">
                    {{ $service->translated_link_text ?? __('Learn More') }}
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-600 dark:text-gray-400">{{ __('No services available at the moment.') }}</p>
        </div>
        @endif
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-20 bg-white dark:bg-[#0a0a0a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-12">{{ __('Why Choose WE SOLD?') }}</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">✓</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Expert Team') }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ __('Our experienced agents have deep market knowledge and proven track records.') }}</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">⚡</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Fast Results') }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ __('We streamline the process to get you results faster without compromising quality.') }}</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">💎</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Best Value') }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ __('We negotiate the best deals to maximize your investment returns.') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-orange-800 to-orange-300 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold mb-6">{{ __('Ready to Get Started?') }}</h2>
        <p class="text-xl mb-8 text-orange-100">{{ __('Contact us today to discuss your real estate needs and discover how we can help you achieve your goals.') }}</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('home') }}#contact" class="bg-white text-orange-500 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-orange-50 transition-colors shadow-lg">
                {{ __('Contact Us') }}
            </a>
            <a href="{{ route('listings') }}" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-white/10 transition-colors shadow-lg">
                {{ __('Browse Properties') }}
            </a>
        </div>
    </div>
</section>
@endsection

