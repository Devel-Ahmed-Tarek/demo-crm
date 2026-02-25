@extends('layouts.landing')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-orange-800 to-orange-300 text-white pt-24 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold mb-4 text-center">
            @if(app()->getLocale() === 'ar' && isset($siteSettings['about_hero_title_ar']) && $siteSettings['about_hero_title_ar']->value)
            {{ $siteSettings['about_hero_title_ar']->value }}
            @elseif(app()->getLocale() === 'en' && isset($siteSettings['about_hero_title_en']) && $siteSettings['about_hero_title_en']->value)
            {{ $siteSettings['about_hero_title_en']->value }}
            @else
            {{ __('About WE SOLD') }}
            @endif
        </h1>
        <p class="text-xl text-center text-orange-100">
            @if(app()->getLocale() === 'ar' && isset($siteSettings['about_hero_subtitle_ar']) && $siteSettings['about_hero_subtitle_ar']->value)
            {{ $siteSettings['about_hero_subtitle_ar']->value }}
            @elseif(app()->getLocale() === 'en' && isset($siteSettings['about_hero_subtitle_en']) && $siteSettings['about_hero_subtitle_en']->value)
            {{ $siteSettings['about_hero_subtitle_en']->value }}
            @else
            {{ __('Your trusted partner in real estate success') }}
            @endif
        </p>
    </div>
</section>

<!-- About Section -->
<section class="py-20 bg-white dark:bg-[#0a0a0a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-stretch">
            <div class="rounded-2xl overflow-hidden shadow-xl flex">
                @if(isset($siteSettings['about_video']) && $siteSettings['about_video']->value)
                <video controls class="w-full h-full object-cover rounded-2xl" preload="metadata">
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
                    loading="lazy">
                @else
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800"
                    alt="{{ __('WE SOLD team meeting and collaboration') }}"
                    class="w-full h-full object-cover"
                    loading="lazy"
                    width="800"
                    height="600">
                @endif
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                    @if(app()->getLocale() === 'ar' && isset($siteSettings['about_story_title_ar']) && $siteSettings['about_story_title_ar']->value)
                    {{ $siteSettings['about_story_title_ar']->value }}
                    @elseif(app()->getLocale() === 'en' && isset($siteSettings['about_story_title_en']) && $siteSettings['about_story_title_en']->value)
                    {{ $siteSettings['about_story_title_en']->value }}
                    @else
                    {{ __('Our Story') }}
                    @endif
                </h2>
                <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                    @if(app()->getLocale() === 'ar' && isset($siteSettings['about_story_text_ar']) && $siteSettings['about_story_text_ar']->value)
                    {{ $siteSettings['about_story_text_ar']->value }}
                    @elseif(app()->getLocale() === 'en' && isset($siteSettings['about_story_text_en']) && $siteSettings['about_story_text_en']->value)
                    {{ $siteSettings['about_story_text_en']->value }}
                    @else
                    {{ __('At WE SOLD, we build lasting relationships through trust, expertise, and unwavering commitment to your real estate success. With over a decade of experience in the market, we\'ve helped countless families and investors achieve their property dreams with personalized service and proven results.') }}
                    @endif
                </p>
                <p class="text-lg text-gray-700 dark:text-gray-300 mb-8">
                    @if(app()->getLocale() === 'ar' && isset($siteSettings['about_mission_text_ar']) && $siteSettings['about_mission_text_ar']->value)
                    {{ $siteSettings['about_mission_text_ar']->value }}
                    @elseif(app()->getLocale() === 'en' && isset($siteSettings['about_mission_text_en']) && $siteSettings['about_mission_text_en']->value)
                    {{ $siteSettings['about_mission_text_en']->value }}
                    @else
                    {{ __('Our mission is to make real estate transactions seamless, transparent, and rewarding for everyone involved. We combine cutting-edge technology with personalized service to deliver exceptional results.') }}
                    @endif
                </p>
                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-[#161615] rounded-lg p-6 shadow-md text-center">
                        <div class="text-orange-500 text-3xl mb-2">🕐</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $siteSettings['about_stats_years']->value ?? '10+' }}</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ __('Years Experience') }}</div>
                    </div>
                    <div class="bg-white dark:bg-[#161615] rounded-lg p-6 shadow-md text-center">
                        <div class="text-orange-500 text-3xl mb-2">🏠</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $siteSettings['about_stats_homes']->value ?? '500+' }}</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ __('Homes Sold') }}</div>
                    </div>
                    <div class="bg-white dark:bg-[#161615] rounded-lg p-6 shadow-md text-center">
                        <div class="text-orange-500 text-3xl mb-2">⭐</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $siteSettings['about_stats_satisfaction']->value ?? '98%' }}</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ __('Client Satisfaction') }}</div>
                    </div>
                    <div class="bg-white dark:bg-[#161615] rounded-lg p-6 shadow-md text-center">
                        <div class="text-orange-500 text-3xl mb-2">🏆</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $siteSettings['about_stats_award']->value ?? __('Award') }}</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ __('Winning Service') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="py-20 bg-gray-50 dark:bg-[#161615]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-12">{{ __('Our Core Values') }}</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-[#0a0a0a] rounded-xl p-8 shadow-lg text-center">
                <div class="text-orange-500 text-5xl mb-4">🤝</div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    @if(app()->getLocale() === 'ar' && isset($siteSettings['about_value_trust_title_ar']) && $siteSettings['about_value_trust_title_ar']->value)
                    {{ $siteSettings['about_value_trust_title_ar']->value }}
                    @elseif(app()->getLocale() === 'en' && isset($siteSettings['about_value_trust_title_en']) && $siteSettings['about_value_trust_title_en']->value)
                    {{ $siteSettings['about_value_trust_title_en']->value }}
                    @else
                    {{ __('Trust') }}
                    @endif
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    @if(app()->getLocale() === 'ar' && isset($siteSettings['about_value_trust_text_ar']) && $siteSettings['about_value_trust_text_ar']->value)
                    {{ $siteSettings['about_value_trust_text_ar']->value }}
                    @elseif(app()->getLocale() === 'en' && isset($siteSettings['about_value_trust_text_en']) && $siteSettings['about_value_trust_text_en']->value)
                    {{ $siteSettings['about_value_trust_text_en']->value }}
                    @else
                    {{ __('We believe in building long-term relationships based on honesty, transparency, and integrity in every transaction.') }}
                    @endif
                </p>
            </div>
            <div class="bg-white dark:bg-[#0a0a0a] rounded-xl p-8 shadow-lg text-center">
                <div class="text-orange-500 text-5xl mb-4">🎯</div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    @if(app()->getLocale() === 'ar' && isset($siteSettings['about_value_excellence_title_ar']) && $siteSettings['about_value_excellence_title_ar']->value)
                    {{ $siteSettings['about_value_excellence_title_ar']->value }}
                    @elseif(app()->getLocale() === 'en' && isset($siteSettings['about_value_excellence_title_en']) && $siteSettings['about_value_excellence_title_en']->value)
                    {{ $siteSettings['about_value_excellence_title_en']->value }}
                    @else
                    {{ __('Excellence') }}
                    @endif
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    @if(app()->getLocale() === 'ar' && isset($siteSettings['about_value_excellence_text_ar']) && $siteSettings['about_value_excellence_text_ar']->value)
                    {{ $siteSettings['about_value_excellence_text_ar']->value }}
                    @elseif(app()->getLocale() === 'en' && isset($siteSettings['about_value_excellence_text_en']) && $siteSettings['about_value_excellence_text_en']->value)
                    {{ $siteSettings['about_value_excellence_text_en']->value }}
                    @else
                    {{ __('We strive for excellence in every aspect of our service, from property search to closing the deal.') }}
                    @endif
                </p>
            </div>
            <div class="bg-white dark:bg-[#0a0a0a] rounded-xl p-8 shadow-lg text-center">
                <div class="text-orange-500 text-5xl mb-4">💡</div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    @if(app()->getLocale() === 'ar' && isset($siteSettings['about_value_innovation_title_ar']) && $siteSettings['about_value_innovation_title_ar']->value)
                    {{ $siteSettings['about_value_innovation_title_ar']->value }}
                    @elseif(app()->getLocale() === 'en' && isset($siteSettings['about_value_innovation_title_en']) && $siteSettings['about_value_innovation_title_en']->value)
                    {{ $siteSettings['about_value_innovation_title_en']->value }}
                    @else
                    {{ __('Innovation') }}
                    @endif
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    @if(app()->getLocale() === 'ar' && isset($siteSettings['about_value_innovation_text_ar']) && $siteSettings['about_value_innovation_text_ar']->value)
                    {{ $siteSettings['about_value_innovation_text_ar']->value }}
                    @elseif(app()->getLocale() === 'en' && isset($siteSettings['about_value_innovation_text_en']) && $siteSettings['about_value_innovation_text_en']->value)
                    {{ $siteSettings['about_value_innovation_text_en']->value }}
                    @else
                    {{ __('We leverage the latest technology and market insights to provide you with the best possible service.') }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-20 bg-white dark:bg-[#0a0a0a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-12">{{ __('How It Works') }}</h2>
        <div class="grid md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">1</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Search') }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ __('Browse our extensive listings to find properties that match your criteria.') }}</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">2</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Schedule') }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ __('Book viewings and consultations with our expert agents.') }}</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">3</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Offer') }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ __('Make competitive offers with our guidance and negotiation support.') }}</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">4</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Close') }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ __('Complete the transaction smoothly with our full closing support.') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-20 bg-gray-50 dark:bg-[#161615]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-12">{{ __('What Our Clients Say') }}</h2>
        @if(isset($testimonials) && $testimonials->count() > 0)
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
            <div class="bg-white dark:bg-[#0a0a0a] rounded-xl p-6 shadow-lg">
                <div class="flex items-center mb-4">
                    @if($testimonial->photo_url)
                    <img src="{{ $testimonial->photo_url }}" alt="{{ $testimonial->name }}" class="w-12 h-12 rounded-full object-cover mr-4">
                    @else
                    <div class="w-12 h-12 rounded-full bg-gray-300 dark:bg-gray-700 mr-4"></div>
                    @endif
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $testimonial->name }}</div>
                        @if($testimonial->position)
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $testimonial->position }}</div>
                        @endif
                        <div class="flex text-yellow-400">
                            @for($i = 0; $i < $testimonial->rating; $i++)
                                ★
                                @endfor
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 italic">"{{ $testimonial->testimonial }}"</p>
                @if($testimonial->property_sold)
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Property Sold') }}: {{ $testimonial->property_sold }}</p>
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

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-orange-800 to-orange-300 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold mb-6">{{ __('Ready to Start Your Real Estate Journey?') }}</h2>
        <p class="text-xl mb-8 text-orange-100">{{ __('Let us help you find your perfect property or sell your current one.') }}</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('listings') }}" class="bg-white text-orange-500 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-orange-50 transition-colors shadow-lg">
                {{ __('Browse Properties') }}
            </a>
            <a href="{{ route('home') }}#contact" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-white/10 transition-colors shadow-lg">
                {{ __('Contact Us') }}
            </a>
        </div>
    </div>
</section>
@endsection