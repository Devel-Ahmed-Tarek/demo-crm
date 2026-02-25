@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Add Service') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Add a new service') }}</p>
        </div>
        <a href="{{ route('site.services') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('Back') }}
        </a>
    </div>

    <div class="bg-white dark:bg-[#161615] rounded-xl shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <form action="{{ route('site.services.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Title') }} ({{ __('Default') }}) *
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="title_en" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Title') }} ({{ __('English') }})
                        </label>
                        <input type="text" id="title_en" name="title_en" value="{{ old('title_en') }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>

                    <div>
                        <label for="title_ar" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Title') }} ({{ __('Arabic') }})
                        </label>
                        <input type="text" id="title_ar" name="title_ar" value="{{ old('title_ar') }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Description') }} ({{ __('Default') }}) *
                    </label>
                    <textarea id="description" name="description" rows="4" required
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('description') }}</textarea>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="description_en" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Description') }} ({{ __('English') }})
                        </label>
                        <textarea id="description_en" name="description_en" rows="4"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('description_en') }}</textarea>
                    </div>

                    <div>
                        <label for="description_ar" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Description') }} ({{ __('Arabic') }})
                        </label>
                        <textarea id="description_ar" name="description_ar" rows="4"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">{{ old('description_ar') }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="icon_type" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Icon Type') }} *
                    </label>
                    <select id="icon_type" name="icon_type" onchange="toggleIconInput()"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        <option value="emoji" {{ old('icon_type', 'emoji') == 'emoji' ? 'selected' : '' }}>{{ __('Emoji') }}</option>
                        <option value="image" {{ old('icon_type') == 'image' ? 'selected' : '' }}>{{ __('Image') }}</option>
                    </select>
                </div>

                <div id="emoji_input">
                    <label for="icon" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Emoji Icon') }}
                    </label>
                    <input type="text" id="icon" name="icon" value="{{ old('icon') }}" placeholder="💰"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ __('Enter an emoji (e.g., 💰, 🏠, 📊)') }}</p>
                </div>

                <div id="image_input" style="display: none;">
                    <label for="icon_image" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Icon Image') }}
                    </label>
                    <input type="file" id="icon_image" name="icon_image" accept="image/*"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ __('Recommended size: 64x64px. Max file size: 2MB') }}</p>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="link" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Link URL') }}
                        </label>
                        <input type="url" id="link" name="link" value="{{ old('link') }}" placeholder="https://..."
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>

                    <div>
                        <label for="link_text" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Link Text') }} ({{ __('Default') }})
                        </label>
                        <input type="text" id="link_text" name="link_text" value="{{ old('link_text', __('Learn More')) }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="link_text_en" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Link Text') }} ({{ __('English') }})
                        </label>
                        <input type="text" id="link_text_en" name="link_text_en" value="{{ old('link_text_en') }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>

                    <div>
                        <label for="link_text_ar" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Link Text') }} ({{ __('Arabic') }})
                        </label>
                        <input type="text" id="link_text_ar" name="link_text_ar" value="{{ old('link_text_ar') }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">
                    </div>
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Display Order') }}
                    </label>
                    <input type="number" id="order" name="order" value="{{ old('order', 0) }}" min="0"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-[#e3e3e0] dark:border-[#3E3E3A] text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Is Active') }}</span>
                    </label>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        {{ __('Create Service') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function toggleIconInput() {
            const iconType = document.getElementById('icon_type').value;
            const emojiInput = document.getElementById('emoji_input');
            const imageInput = document.getElementById('image_input');

            if (iconType === 'emoji') {
                emojiInput.style.display = 'block';
                imageInput.style.display = 'none';
            } else {
                emojiInput.style.display = 'none';
                imageInput.style.display = 'block';
            }
        }

        // Initialize on page load
        toggleIconInput();
    </script>
</div>
@endsection