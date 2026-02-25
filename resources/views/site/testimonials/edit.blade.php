@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Edit Testimonial') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Update testimonial information') }}</p>
        </div>
        <a href="{{ route('site.testimonials') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('Back') }}
        </a>
    </div>

    <div class="bg-white dark:bg-[#161615] rounded-xl shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <form action="{{ route('site.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Name') }} *
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $testimonial->name) }}" required
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Position') }}
                        </label>
                        <input type="text" id="position" name="position" value="{{ old('position', $testimonial->position) }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]"
                            placeholder="{{ __('e.g., Home Buyer, Property Owner') }}">
                    </div>
                </div>

                <div>
                    <label for="testimonial" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Testimonial') }} *
                    </label>
                    <textarea id="testimonial" name="testimonial" rows="4" required
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('testimonial', $testimonial->testimonial) }}</textarea>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="photo" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Photo') }}
                        </label>
                        @if($testimonial->photo_url)
                            <div class="mb-2">
                                <img src="{{ $testimonial->photo_url }}" alt="{{ $testimonial->name }}" class="w-20 h-20 rounded-full object-cover">
                            </div>
                        @endif
                        <input type="file" id="photo" name="photo" accept="image/*"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ __('Recommended size: 200x200px. Max file size: 2MB') }}</p>
                    </div>

                    <div>
                        <label for="rating" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Rating') }}
                        </label>
                        <select id="rating" name="rating"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                            <option value="5" {{ old('rating', $testimonial->rating) == 5 ? 'selected' : '' }}>5 {{ __('Stars') }}</option>
                            <option value="4" {{ old('rating', $testimonial->rating) == 4 ? 'selected' : '' }}>4 {{ __('Stars') }}</option>
                            <option value="3" {{ old('rating', $testimonial->rating) == 3 ? 'selected' : '' }}>3 {{ __('Stars') }}</option>
                            <option value="2" {{ old('rating', $testimonial->rating) == 2 ? 'selected' : '' }}>2 {{ __('Stars') }}</option>
                            <option value="1" {{ old('rating', $testimonial->rating) == 1 ? 'selected' : '' }}>1 {{ __('Star') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="property_sold" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Property Sold') }}
                        </label>
                        <input type="text" id="property_sold" name="property_sold" value="{{ old('property_sold', $testimonial->property_sold) }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]"
                            placeholder="{{ __('e.g., Luxury Villa in Downtown') }}">
                    </div>

                    <div>
                        <label for="order" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ __('Display Order') }}
                        </label>
                        <input type="number" id="order" name="order" value="{{ old('order', $testimonial->order) }}" min="0"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $testimonial->is_active) ? 'checked' : '' }}
                            class="rounded border-[#e3e3e0] dark:border-[#3E3E3A] text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Is Active') }}</span>
                    </label>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        {{ __('Update Testimonial') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

