@extends('layouts.landing')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-orange-800 to-orange-300 text-white pt-24 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold mb-4 text-center">{{ __('Get In Touch') }}</h1>
        <p class="text-xl text-center text-orange-100">{{ __('We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.') }}</p>
    </div>
</section>

<!-- Contact Section -->
<section class="py-20 bg-white dark:bg-[#0a0a0a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12">
            <!-- Contact Video -->
            @if(isset($siteSettings['about_video']) && $siteSettings['about_video']->value)
            <div class="rounded-2xl overflow-hidden shadow-xl order-2 md:order-1">
                <video controls class="w-full h-full object-cover rounded-2xl" style="max-height: 500px;">
                    <source src="{{ \App\Helpers\UploadHelper::url($siteSettings['about_video']->value) }}" type="video/mp4">
                    <source src="{{ \App\Helpers\UploadHelper::url($siteSettings['about_video']->value) }}" type="video/webm">
                    <source src="{{ \App\Helpers\UploadHelper::url($siteSettings['about_video']->value) }}" type="video/ogg">
                    <track kind="captions" src="" srclang="{{ app()->getLocale() }}" label="{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}" default>
                    {{ __('Your browser does not support the video tag.') }}
                </video>
            </div>
            @endif

            <!-- Contact Form -->
            <div class="order-1 md:order-2">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Send Us a Message') }}</h2>
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
                            <label for="message" class="block text-gray-700 dark:text-gray-300 mb-2">{{ __('Message') }}</label>
                            <textarea name="message" id="message" rows="5"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none resize-none bg-white dark:bg-[#0a0a0a] text-gray-900 dark:text-white">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-4 rounded-lg font-semibold transition-all transform hover:scale-105">
                            {{ __('Send Message') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Contact Information') }}</h2>
                <div class="space-y-6">
                    <div class="bg-white dark:bg-[#161615] rounded-xl p-6 shadow-lg">
                        <div class="flex items-start">
                            <div class="text-orange-500 text-2xl mr-4">📍</div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('Address') }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">123 Real Estate Ave<br>City, State 12345</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-[#161615] rounded-xl p-6 shadow-lg">
                        <div class="flex items-start">
                            <div class="text-orange-500 text-2xl mr-4">📞</div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('Phone') }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">(555) 123-4567</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-[#161615] rounded-xl p-6 shadow-lg">
                        <div class="flex items-start">
                            <div class="text-orange-500 text-2xl mr-4">✉️</div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('Email') }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">info@wesold.com</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-[#161615] rounded-xl p-6 shadow-lg">
                        <div class="flex items-start">
                            <div class="text-orange-500 text-2xl mr-4">🕐</div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('Business Hours') }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">
                                    {{ __('Monday - Friday') }}: 9:00 AM - 6:00 PM<br>
                                    {{ __('Saturday') }}: 10:00 AM - 4:00 PM<br>
                                    {{ __('Sunday') }}: {{ __('Closed') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection