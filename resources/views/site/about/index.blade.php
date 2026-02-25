@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('About Page Management') }}</h1>
        <a href="{{ route('site.index') }}" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
            {{ __('Back') }}
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('site.about.update') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-[#161615] rounded-xl shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-8">
        @csrf

        <!-- Hero Section -->
        <div class="border-b border-[#e3e3e0] dark:border-[#3E3E3A] pb-6">
            <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Hero Section') }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Hero Title') }} ({{ __('English') }})
                    </label>
                    <input type="text" name="about_hero_title_en" value="{{ $settings['about_hero_title_en']->value ?? '' }}"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Hero Title') }} ({{ __('Arabic') }})
                    </label>
                    <input type="text" name="about_hero_title_ar" value="{{ $settings['about_hero_title_ar']->value ?? '' }}"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Hero Subtitle') }} ({{ __('English') }})
                    </label>
                    <input type="text" name="about_hero_subtitle_en" value="{{ $settings['about_hero_subtitle_en']->value ?? '' }}"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Hero Subtitle') }} ({{ __('Arabic') }})
                    </label>
                    <input type="text" name="about_hero_subtitle_ar" value="{{ $settings['about_hero_subtitle_ar']->value ?? '' }}"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">
                </div>
            </div>
        </div>

        <!-- Story Section -->
        <div class="border-b border-[#e3e3e0] dark:border-[#3E3E3A] pb-6">
            <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Our Story Section') }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Video File') }}
                    </label>
                    @if(isset($settings['about_video']) && $settings['about_video']->value)
                        <div class="mb-2">
                            <video controls class="w-full max-w-md rounded-lg" style="max-height: 300px;">
                                <source src="{{ \App\Helpers\UploadHelper::url($settings['about_video']->value) }}" type="video/mp4">
                                {{ __('Your browser does not support the video tag.') }}
                            </video>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Current video file') }}</p>
                        </div>
                    @endif
                    <input type="file" name="about_video" accept="video/*"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Upload a video file (MP4, WebM, etc.). Max file size: 50MB. Video will be displayed instead of the image if provided.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Story Image') }}
                    </label>
                    @if(isset($settings['about_image']) && $settings['about_image']->value)
                        <div class="mb-2">
                            <img src="{{ \App\Helpers\UploadHelper::url($settings['about_image']->value) }}" alt="About Image" class="w-48 h-48 object-cover rounded-lg">
                        </div>
                    @endif
                    <input type="file" name="about_image" accept="image/*"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Image will be displayed if no video URL is provided.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Story Title') }} ({{ __('English') }})
                    </label>
                    <input type="text" name="about_story_title_en" value="{{ $settings['about_story_title_en']->value ?? '' }}"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Story Title') }} ({{ __('Arabic') }})
                    </label>
                    <input type="text" name="about_story_title_ar" value="{{ $settings['about_story_title_ar']->value ?? '' }}"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Story Text') }} ({{ __('English') }})
                    </label>
                    <textarea name="about_story_text_en" rows="4"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ $settings['about_story_text_en']->value ?? '' }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Story Text') }} ({{ __('Arabic') }})
                    </label>
                    <textarea name="about_story_text_ar" rows="4"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">{{ $settings['about_story_text_ar']->value ?? '' }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Mission Text') }} ({{ __('English') }})
                    </label>
                    <textarea name="about_mission_text_en" rows="3"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ $settings['about_mission_text_en']->value ?? '' }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Mission Text') }} ({{ __('Arabic') }})
                    </label>
                    <textarea name="about_mission_text_ar" rows="3"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">{{ $settings['about_mission_text_ar']->value ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="border-b border-[#e3e3e0] dark:border-[#3E3E3A] pb-6">
            <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Statistics Section') }}</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Years Experience') }} (e.g., 10+)
                    </label>
                    <input type="text" name="about_stats_years" value="{{ $settings['about_stats_years']->value ?? '' }}"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Homes Sold') }} (e.g., 500+)
                    </label>
                    <input type="text" name="about_stats_homes" value="{{ $settings['about_stats_homes']->value ?? '' }}"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Client Satisfaction') }} (e.g., 98%)
                    </label>
                    <input type="text" name="about_stats_satisfaction" value="{{ $settings['about_stats_satisfaction']->value ?? '' }}"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Award') }}
                    </label>
                    <input type="text" name="about_stats_award" value="{{ $settings['about_stats_award']->value ?? '' }}"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                </div>
            </div>
        </div>

        <!-- Core Values Section -->
        <div class="border-b border-[#e3e3e0] dark:border-[#3E3E3A] pb-6">
            <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Core Values Section') }}</h2>
            
            <!-- Trust Value -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-[#0a0a0a] rounded-lg">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Trust') }}</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Title') }} ({{ __('English') }})
                        </label>
                        <input type="text" name="about_value_trust_title_en" value="{{ $settings['about_value_trust_title_en']->value ?? '' }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Title') }} ({{ __('Arabic') }})
                        </label>
                        <input type="text" name="about_value_trust_title_ar" value="{{ $settings['about_value_trust_title_ar']->value ?? '' }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Description') }} ({{ __('English') }})
                        </label>
                        <textarea name="about_value_trust_text_en" rows="2"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ $settings['about_value_trust_text_en']->value ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Description') }} ({{ __('Arabic') }})
                        </label>
                        <textarea name="about_value_trust_text_ar" rows="2"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">{{ $settings['about_value_trust_text_ar']->value ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Excellence Value -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-[#0a0a0a] rounded-lg">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Excellence') }}</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Title') }} ({{ __('English') }})
                        </label>
                        <input type="text" name="about_value_excellence_title_en" value="{{ $settings['about_value_excellence_title_en']->value ?? '' }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Title') }} ({{ __('Arabic') }})
                        </label>
                        <input type="text" name="about_value_excellence_title_ar" value="{{ $settings['about_value_excellence_title_ar']->value ?? '' }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Description') }} ({{ __('English') }})
                        </label>
                        <textarea name="about_value_excellence_text_en" rows="2"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ $settings['about_value_excellence_text_en']->value ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Description') }} ({{ __('Arabic') }})
                        </label>
                        <textarea name="about_value_excellence_text_ar" rows="2"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">{{ $settings['about_value_excellence_text_ar']->value ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Innovation Value -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-[#0a0a0a] rounded-lg">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Innovation') }}</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Title') }} ({{ __('English') }})
                        </label>
                        <input type="text" name="about_value_innovation_title_en" value="{{ $settings['about_value_innovation_title_en']->value ?? '' }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Title') }} ({{ __('Arabic') }})
                        </label>
                        <input type="text" name="about_value_innovation_title_ar" value="{{ $settings['about_value_innovation_title_ar']->value ?? '' }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Description') }} ({{ __('English') }})
                        </label>
                        <textarea name="about_value_innovation_text_en" rows="2"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ $settings['about_value_innovation_text_en']->value ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Description') }} ({{ __('Arabic') }})
                        </label>
                        <textarea name="about_value_innovation_text_ar" rows="2"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">{{ $settings['about_value_innovation_text_ar']->value ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</div>
@endsection

