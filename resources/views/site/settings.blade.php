@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Site Settings') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Manage site information, logo, favicon, and social media links') }}</p>
        </div>
        <a href="{{ route('site.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('Back') }}
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white dark:bg-[#161615] rounded-xl shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <form action="{{ route('site.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">
                <!-- Site Name -->
                <div>
                    <label for="site_name" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Site Name') }}
                    </label>
                    <input type="text" id="site_name" name="site_name" value="{{ $settings['site_name']->value ?? '' }}"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                </div>

                <!-- Logo -->
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Site Logo') }}
                    </label>
                    @if(isset($settings['site_logo']) && $settings['site_logo']->value)
                    <div class="mb-4">
                        <img src="{{ \App\Helpers\UploadHelper::url($settings['site_logo']->value) }}" alt="Logo" class="h-20 w-auto rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    </div>
                    @endif
                    <input type="file" name="site_logo" accept="image/*"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ __('Recommended size: 200x60px. Max file size: 2MB') }}</p>
                </div>

                <!-- Favicon -->
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Favicon') }}
                    </label>
                    @if(isset($settings['site_favicon']) && $settings['site_favicon']->value)
                    <div class="mb-4">
                        <img src="{{ \App\Helpers\UploadHelper::url($settings['site_favicon']->value) }}" alt="Favicon" class="h-16 w-16 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    </div>
                    @endif
                    <input type="file" name="site_favicon" accept="image/*"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ __('Recommended size: 32x32px or 64x64px. Max file size: 512KB') }}</p>
                </div>

                <!-- Contact Information -->
                <div class="border-t border-[#e3e3e0] dark:border-[#3E3E3A] pt-6">
                    <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Contact Information') }}</h3>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="site_address" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                {{ __('Address') }} 1
                            </label>
                            <textarea id="site_address" name="site_address" rows="3"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ $settings['site_address']->value ?? '' }}</textarea>
                        </div>

                        <div>
                            <label for="site_address2" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                {{ __('Address') }} 2
                            </label>
                            <textarea id="site_address2" name="site_address2" rows="3"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ $settings['site_address2']->value ?? '' }}</textarea>
                        </div>

                        <div>
                            <label for="site_phone" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                {{ __('Phone') }} 1
                            </label>
                            <input type="text" id="site_phone" name="site_phone" value="{{ $settings['site_phone']->value ?? '' }}"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        </div>

                        <div>
                            <label for="site_phone2" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                {{ __('Phone') }} 2
                            </label>
                            <input type="text" id="site_phone2" name="site_phone2" value="{{ $settings['site_phone2']->value ?? '' }}"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        </div>

                        <div>
                            <label for="site_email" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                {{ __('Email') }}
                            </label>
                            <input type="email" id="site_email" name="site_email" value="{{ $settings['site_email']->value ?? '' }}"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="border-t border-[#e3e3e0] dark:border-[#3E3E3A] pt-6">
                    <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Social Media Links') }}</h3>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="site_facebook" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                {{ __('Facebook URL') }}
                            </label>
                            <input type="url" id="site_facebook" name="site_facebook" value="{{ $settings['site_facebook']->value ?? '' }}"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]"
                                placeholder="https://facebook.com/...">
                        </div>

                        <div>
                            <label for="site_twitter" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                {{ __('Twitter URL') }}
                            </label>
                            <input type="url" id="site_twitter" name="site_twitter" value="{{ $settings['site_twitter']->value ?? '' }}"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]"
                                placeholder="https://twitter.com/...">
                        </div>

                        <div>
                            <label for="site_instagram" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                {{ __('Instagram URL') }}
                            </label>
                            <input type="url" id="site_instagram" name="site_instagram" value="{{ $settings['site_instagram']->value ?? '' }}"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]"
                                placeholder="https://instagram.com/...">
                        </div>

                        <div>
                            <label for="site_linkedin" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                {{ __('LinkedIn URL') }}
                            </label>
                            <input type="url" id="site_linkedin" name="site_linkedin" value="{{ $settings['site_linkedin']->value ?? '' }}"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]"
                                placeholder="https://linkedin.com/...">
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        {{ __('Save Settings') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

