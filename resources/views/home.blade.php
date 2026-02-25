@extends('layouts.landing')

@section('content')
<!-- Hero Section -->
<section id="home" class="hero-bg min-h-screen flex items-center justify-center text-white pt-16" @if($heroImage) style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ \App\Helpers\UploadHelper::url($heroImage) }}'); background-size: cover; background-position: center; background-attachment: fixed;" @endif>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center hero-content">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">{{ __('Where Your Real Estate Dreams Become Reality') }}</h1>
        <p class="text-xl md:text-2xl mb-8 text-gray-200">{{ __('WE SOLD helps you buy, sell & invest with confidence, ease, and proven results') }}</p>
        <div class="text-2xl md:text-3xl font-bold mb-8">
            <span>500+ {{ __('Homes Sold') }}</span>
            <span class="mx-4">|</span>
            <span>98% {{ __('Client Satisfaction') }}</span>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <a href="{{ route('contact') }}" class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all transform hover:scale-105">
                {{ __('Get Free Property Evaluation') }}
            </a>
            <a href="{{ route('listings') }}" class="bg-white text-orange-700 border-2 border-orange-700 hover:bg-orange-50 px-8 py-4 rounded-lg text-lg font-semibold transition-all">
                {{ __('Browse Available Homes') }}
            </a>
        </div>

        <!-- Search Bar -->
        <div class="max-w-3xl mx-auto mt-8">
            <form action="{{ route('listings') }}" method="GET" class="bg-white dark:bg-[#0a0a0a] rounded-xl shadow-2xl p-4 md:p-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" placeholder="{{ __('Search by location, property code...') }}"
                            value="{{ request('search') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none bg-white dark:bg-[#161615] text-gray-900 dark:text-white">
                    </div>
                    <div class="md:w-48">
                        <label for="project_type" class="sr-only">{{ __('Project Type') }}</label>
                        <select name="project_type" id="project_type" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none bg-white dark:bg-[#161615] text-gray-900 dark:text-white">
                            <option value="">{{ __('All Projects') }}</option>
                            <option value="current" {{ request('project_type') === 'current' ? 'selected' : '' }}>{{ __('Current Projects') }}</option>
                            <option value="previous" {{ request('project_type') === 'previous' ? 'selected' : '' }}>{{ __('Previous Projects') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-3 rounded-lg font-semibold transition-all transform hover:scale-105 whitespace-nowrap">
                        {{ __('Search') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-20 bg-white dark:bg-[#0a0a0a] section-animate">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-12">{{ __('About WE SOLD') }}</h2>

        <div class="grid md:grid-cols-2 gap-12 items-stretch">
            <!-- Video Section -->
            <div class="rounded-2xl overflow-hidden shadow-xl flex animate-from-left">
                @if(isset($siteSettings['about_video']) && $siteSettings['about_video']->value)
                <video controls class="w-full h-full object-cover rounded-2xl" preload="none" poster="{{ isset($siteSettings['about_image']) && $siteSettings['about_image']->value ? \App\Helpers\UploadHelper::url($siteSettings['about_image']->value) : '' }}" loading="lazy">
                    <source src="{{ \App\Helpers\UploadHelper::url($siteSettings['about_video']->value) }}" type="video/mp4">
                    <source src="{{ \App\Helpers\UploadHelper::url($siteSettings['about_video']->value) }}" type="video/webm">
                    <source src="{{ \App\Helpers\UploadHelper::url($siteSettings['about_video']->value) }}" type="video/ogg">
                    <track kind="captions" src="" srclang="{{ app()->getLocale() }}" label="{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}" default>
                    {{ __('Your browser does not support the video tag.') }}
                </video>
                @elseif(isset($siteSettings['about_image']) && $siteSettings['about_image']->value)
                <img src="{{ \App\Helpers\UploadHelper::url($siteSettings['about_image']->value) }}"
                    alt="{{ __('WE SOLD team meeting and collaboration') }}"
                    class="w-full h-full object-cover"
                    loading="lazy"
                    width="800"
                    height="600">
                @else
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800"
                    alt="{{ __('WE SOLD team meeting and collaboration') }}"
                    class="w-full h-full object-cover"
                    loading="lazy"
                    width="800"
                    height="600">
                @endif
            </div>

            <!-- Content Section -->
            <div class="flex flex-col justify-center animate-from-right">
                <p class="text-lg text-gray-700 dark:text-gray-300 mb-8">
                    {{ __('At WE SOLD, we build lasting relationships through trust, expertise, and unwavering commitment to your real estate success. With over a decade of experience in the market, we\'ve helped countless families and investors achieve their property dreams with personalized service and proven results.') }}
                </p>
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="bg-white dark:bg-[#161615] rounded-lg p-6 shadow-md text-center">
                        <div class="text-orange-500 text-3xl mb-2">🕐</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">10+</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ __('Years Experience') }}</div>
                    </div>
                    <div class="bg-white dark:bg-[#161615] rounded-lg p-6 shadow-md text-center">
                        <div class="text-orange-500 text-3xl mb-2">🏠</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">500+</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ __('Homes Sold') }}</div>
                    </div>
                    <div class="bg-white dark:bg-[#161615] rounded-lg p-6 shadow-md text-center">
                        <div class="text-orange-500 text-3xl mb-2">⭐</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">98%</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ __('Client Satisfaction') }}</div>
                    </div>
                    <div class="bg-white dark:bg-[#161615] rounded-lg p-6 shadow-md text-center">
                        <div class="text-orange-500 text-3xl mb-2">🏆</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ __('Award') }}</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ __('Winning Service') }}</div>
                    </div>
                </div>
                <a href="{{ route('contact') }}" class="inline-block bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-3 rounded-lg font-semibold transition-all">
                    {{ __('Meet Our Team') }}
                </a>
            </div>
        </div>
    </div>
</section>


<!-- How It Works Section -->
<section class="py-20 bg-white dark:bg-[#0a0a0a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-12">{{ __('How It Works') }}</h2>
        @if($howItWorksSteps->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            @foreach($howItWorksSteps as $step)
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                    @if($step->icon_type === 'image' && $step->icon_image_url)
                    <img src="{{ $step->icon_image_url }}" alt="{{ $step->translated_title }}" class="w-12 h-12 object-contain">
                    @elseif($step->icon)
                    {{ $step->icon }}
                    @else
                    {{ $step->step_number }}
                    @endif
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $step->translated_title }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $step->translated_description }}</p>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-600 dark:text-gray-400">{{ __('No steps available at the moment.') }}</p>
        </div>
        @endif
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-20 bg-gray-50 dark:bg-[#161615]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-12">{{ __('What Our Clients Say') }}</h2>
        @if($testimonials->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
            <div class="bg-white dark:bg-[#0a0a0a] rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                <div class="flex items-center mb-4">
                    @if($testimonial->photo_url)
                    <img src="{{ $testimonial->photo_url }}" alt="{{ $testimonial->name }}" class="w-12 h-12 rounded-full object-cover mr-4" width="48" height="48" loading="lazy">
                    @else
                    <div class="w-12 h-12 rounded-full bg-gray-300 dark:bg-gray-700 mr-4 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    @endif
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $testimonial->name }}</div>
                        @if($testimonial->position)
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $testimonial->position }}</div>
                        @endif
                        <div class="flex text-yellow-400 mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <=$testimonial->rating)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                </svg>
                                @else
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                </svg>
                                @endif
                                @endfor
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 italic">"{{ $testimonial->testimonial }}"</p>
                @if($testimonial->property_sold)
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-3">
                    <span class="font-medium">{{ __('Property Sold') }}:</span> {{ $testimonial->property_sold }}
                </p>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-600 dark:text-gray-400">{{ __('No testimonials available at the moment.') }}</p>
        </div>
        @endif
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-20 bg-white dark:bg-[#0a0a0a] section-animate">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-12">{{ __('Our Services') }}</h2>
        @if($services->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($services as $index => $service)
            <div class="bg-white dark:bg-[#161615] rounded-xl p-8 shadow-lg text-center hover:shadow-xl transition-all hover-lift {{ $index % 2 === 0 ? 'animate-from-left' : 'animate-from-right' }}">
                @if($service->icon_type === 'image' && $service->icon_image_url)
                <div class="mb-4 flex justify-center">
                    <img src="{{ $service->icon_image_url }}" alt="{{ $service->translated_title }}" class="w-16 h-16 object-contain" width="64" height="64" loading="lazy">
                </div>
                @elseif($service->icon)
                <div class="text-orange-500 text-5xl mb-4">{{ $service->icon }}</div>
                @endif
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $service->translated_title }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">{{ Str::limit($service->translated_description, 120) }}</p>
                <a href="{{ route('service.details', $service) }}" class="inline-block bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
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

<!-- Partners Section -->
<section class="py-20 bg-gray-50 dark:bg-[#161615]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-4">{{ __('Our Trusted Partners') }}</h2>
        <p class="text-center text-gray-600 dark:text-gray-400 mb-12 max-w-3xl mx-auto">
            {{ __('We\'re proud to work with industry leaders and trusted partners who share our commitment to excellence.') }}
        </p>

        <!-- Slider Container -->
        <div class="relative">
            <!-- Navigation Buttons -->
            <button id="partnersPrevBtn" aria-label="{{ __('Previous partners') }}" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white dark:bg-[#0a0a0a] rounded-full p-3 shadow-lg hover:bg-gray-50 dark:hover:bg-[#161615] transition-colors border border-gray-200 dark:border-gray-800 hidden md:flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button id="partnersNextBtn" aria-label="{{ __('Next partners') }}" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white dark:bg-[#0a0a0a] rounded-full p-3 shadow-lg hover:bg-gray-50 dark:hover:bg-[#161615] transition-colors border border-gray-200 dark:border-gray-800 hidden md:flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Slider with Scroll Snap -->
            <div id="partnersSlider" class="overflow-x-auto scrollbar-hide scroll-smooth" style="scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch;">
                <div id="partnersTrack" class="flex gap-6 px-4 md:px-0">
                    @forelse($partners as $partner)
                    <div class="partners-slide flex-shrink-0 bg-white dark:bg-[#0a0a0a] rounded-lg p-6 shadow-md hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-800" style="width: 200px; scroll-snap-align: start;" itemscope itemtype="https://schema.org/Organization">
                        <div class="text-center">
                            @if($partner->logo_url)
                            <div class="w-16 h-16 mx-auto mb-3 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center overflow-hidden">
                                <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" class="w-full h-full object-contain" loading="lazy" width="64" height="64">
                            </div>
                            @else
                            <div class="w-16 h-16 mx-auto mb-3 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center" aria-hidden="true">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            @endif
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300" itemprop="name">{{ $partner->name }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="partners-slide flex-shrink-0 bg-white dark:bg-[#0a0a0a] rounded-lg p-6 shadow-md border border-gray-200 dark:border-gray-800" style="width: 200px; scroll-snap-align: start;">
                        <div class="text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No partners available') }}</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-white dark:bg-[#0a0a0a]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-12">{{ __('Frequently Asked Questions') }}</h2>
        <div class="space-y-4">
            <div class="faq-item bg-white dark:bg-[#0a0a0a] rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="faq-question flex items-center justify-between p-6 cursor-pointer">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('How long does the home buying process typically take?') }}</h3>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="faq-answer hidden p-6 pt-0 text-gray-600 dark:text-gray-400">
                    {{ __('The home buying process typically takes 30-60 days from offer acceptance to closing, depending on various factors including financing, inspections, and appraisals.') }}
                </div>
            </div>
            <div class="faq-item bg-white dark:bg-[#0a0a0a] rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="faq-question flex items-center justify-between p-6 cursor-pointer">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('What documents do I need to buy a home?') }}</h3>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="faq-answer hidden p-6 pt-0 text-gray-600 dark:text-gray-400">
                    {{ __('You\'ll need proof of income, employment verification, credit reports, bank statements, and identification. Our team will guide you through the specific requirements.') }}
                </div>
            </div>
            <div class="faq-item bg-white dark:bg-[#0a0a0a] rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="faq-question flex items-center justify-between p-6 cursor-pointer">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('How much should I save for closing costs?') }}</h3>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="faq-answer hidden p-6 pt-0 text-gray-600 dark:text-gray-400">
                    {{ __('Closing costs typically range from 2% to 5% of the home\'s purchase price. We\'ll provide a detailed estimate during the buying process.') }}
                </div>
            </div>
            <div class="faq-item bg-white dark:bg-[#0a0a0a] rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="faq-question flex items-center justify-between p-6 cursor-pointer">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Can you help with investment properties?') }}</h3>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="faq-answer hidden p-6 pt-0 text-gray-600 dark:text-gray-400">
                    {{ __('Yes! We specialize in helping investors find profitable properties. Our team has extensive experience with investment real estate.') }}
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-white dark:bg-[#0a0a0a] section-animate">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-12">{{ __('Get In Touch') }}</h2>
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-[#161615] rounded-2xl shadow-xl p-8 md:p-12">
                @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('contact.submit') }}" class="space-y-6">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-gray-700 dark:text-gray-300 mb-2">{{ __('First Name') }} *</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none bg-white dark:bg-[#0a0a0a] text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label for="last_name" class="block text-gray-700 dark:text-gray-300 mb-2">{{ __('Last Name') }} *</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none bg-white dark:bg-[#0a0a0a] text-gray-900 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block text-gray-700 dark:text-gray-300 mb-2">{{ __('Email') }} *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none bg-white dark:bg-[#0a0a0a] text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label for="phone" class="block text-gray-700 dark:text-gray-300 mb-2">{{ __('Phone') }}</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none bg-white dark:bg-[#0a0a0a] text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label for="message" class="block text-gray-700 dark:text-gray-300 mb-2">{{ __('Message') }} / {{ __('Property Details') }}</label>
                        <textarea name="message" id="message" rows="5" placeholder="{{ __('Tell us about your property or inquiry...') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none resize-none bg-white dark:bg-[#0a0a0a] text-gray-900 dark:text-white">{{ old('message') }}</textarea>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-4 rounded-lg font-semibold transition-all transform hover:scale-105">
                            {{ __('Get Your Free Consultation') }}
                        </button>
                        @if(isset($siteSettings['site_phone']) && $siteSettings['site_phone']->value)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings['site_phone']->value) }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg font-semibold transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                            </svg>
                            {{ __('WhatsApp') }}
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
</section>
<script>
    // Partners Slider - New Simple System (Deferred)
    let autoSlideInterval;

    function slidePartners(direction) {
        const slider = document.getElementById('partnersSlider');
        if (!slider) return;

        const slideWidth = 200 + 24; // width + gap
        const scrollAmount = slideWidth * (window.innerWidth >= 1024 ? 6 : window.innerWidth >= 768 ? 4 : 2);

        if (direction === 'next') {
            slider.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        } else {
            slider.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        }
    }

    function startAutoSlide() {
        stopAutoSlide();
        autoSlideInterval = setInterval(() => {
            slidePartners('next');
        }, 4000);
    }

    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
    }

    // Initialize slider
    document.addEventListener('DOMContentLoaded', function() {
        const prevBtn = document.getElementById('partnersPrevBtn');
        const nextBtn = document.getElementById('partnersNextBtn');
        const slider = document.getElementById('partnersSlider');

        if (prevBtn) {
            prevBtn.addEventListener('click', () => slidePartners('prev'));
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => slidePartners('next'));
        }

        if (slider && slider.children[0] && slider.children[0].children.length > 0) {
            // Auto slide
            startAutoSlide();

            // Pause on hover
            slider.addEventListener('mouseenter', stopAutoSlide);
            slider.addEventListener('mouseleave', startAutoSlide);

            // Reset to start when reaching end
            slider.addEventListener('scroll', function() {
                if (slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 10) {
                    setTimeout(() => {
                        slider.scrollTo({
                            left: 0,
                            behavior: 'smooth'
                        });
                    }, 2000);
                }
            });
        }
    });

    // Scroll Animations
    document.addEventListener('DOMContentLoaded', function() {
        // Remove no-js class if JS is enabled
        document.documentElement.classList.remove('no-js');

        // Check if IntersectionObserver is supported
        if ('IntersectionObserver' in window) {
            const observerOptions = {
                threshold: 0.05, // Trigger when 5% is visible
                rootMargin: '0px 0px 150px 0px' // Start animation 150px before section enters viewport
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Add animate-in class with a small delay for smoother effect
                        setTimeout(() => {
                            entry.target.classList.add('animate-in');
                        }, 50);
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all sections with animation class
            document.querySelectorAll('.section-animate, .animate-from-left, .animate-from-right').forEach(section => {
                observer.observe(section);
            });
        } else {
            // Fallback: Show all sections immediately if IntersectionObserver is not supported
            document.querySelectorAll('.section-animate').forEach(section => {
                section.classList.add('animate-in');
            });
        }

        // Add stagger animation to services grid
        const servicesGrid = document.querySelector('#services .grid');
        if (servicesGrid) {
            servicesGrid.classList.add('stagger-children');
        }
    });
</script>
@endsection