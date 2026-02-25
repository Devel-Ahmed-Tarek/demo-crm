@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Edit Step') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Update step information') }}</p>
        </div>
        <a href="{{ route('site.how-it-works') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('Back') }}
        </a>
    </div>

    <div class="bg-white dark:bg-[#161615] rounded-xl shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <form action="{{ route('site.how-it-works.update', $howItWorksStep) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="step_number" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Step Number') }} *
                        </label>
                        <input type="number" id="step_number" name="step_number" value="{{ old('step_number', $howItWorksStep->step_number) }}" min="1" required
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ __('The number displayed in the circle (1, 2, 3, etc.)') }}</p>
                    </div>

                    <div>
                        <label for="order" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Display Order') }}
                        </label>
                        <input type="number" id="order" name="order" value="{{ old('order', $howItWorksStep->order) }}" min="0"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Title') }} ({{ __('Default') }}) *
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $howItWorksStep->title) }}" required
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="title_en" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Title') }} ({{ __('English') }})
                        </label>
                        <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $howItWorksStep->title_en) }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>

                    <div>
                        <label for="title_ar" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Title') }} ({{ __('Arabic') }})
                        </label>
                        <input type="text" id="title_ar" name="title_ar" value="{{ old('title_ar', $howItWorksStep->title_ar) }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Description') }} ({{ __('Default') }}) *
                    </label>
                    <textarea id="description" name="description" rows="3" required
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('description', $howItWorksStep->description) }}</textarea>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="description_en" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Description') }} ({{ __('English') }})
                        </label>
                        <textarea id="description_en" name="description_en" rows="3"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('description_en', $howItWorksStep->description_en) }}</textarea>
                    </div>

                    <div>
                        <label for="description_ar" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Description') }} ({{ __('Arabic') }})
                        </label>
                        <textarea id="description_ar" name="description_ar" rows="3"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" dir="rtl">{{ old('description_ar', $howItWorksStep->description_ar) }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="icon_type" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Icon Type') }} *
                    </label>
                    <select id="icon_type" name="icon_type" onchange="toggleIconInput()"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        <option value="emoji" {{ old('icon_type', $howItWorksStep->icon_type) == 'emoji' ? 'selected' : '' }}>{{ __('Emoji') }}</option>
                        <option value="image" {{ old('icon_type', $howItWorksStep->icon_type) == 'image' ? 'selected' : '' }}>{{ __('Image') }}</option>
                    </select>
                </div>

                <div id="emoji_input">
                    <label for="icon" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Emoji Icon') }}
                    </label>
                    <input type="text" id="icon" name="icon" value="{{ old('icon', $howItWorksStep->icon) }}" placeholder="💰"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ __('Enter an emoji (optional - step number will be displayed by default)') }}</p>
                </div>

                <div id="image_input" style="display: none;">
                    <label for="icon_image" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Icon Image') }}
                    </label>
                    @if($howItWorksStep->icon_image_url)
                        <div class="mb-2">
                            <img src="{{ $howItWorksStep->icon_image_url }}" alt="{{ $howItWorksStep->title }}" class="w-16 h-16 object-contain">
                        </div>
                    @endif
                    <input type="file" id="icon_image" name="icon_image" accept="image/*"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ __('Recommended size: 64x64px. Max file size: 2MB') }}</p>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $howItWorksStep->is_active) ? 'checked' : '' }}
                            class="rounded border-[#e3e3e0] dark:border-[#3E3E3A] text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Is Active') }}</span>
                    </label>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        {{ __('Update Step') }}
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

