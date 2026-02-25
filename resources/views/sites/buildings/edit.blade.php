@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Edit Building') }}: {{ $building->title }}</h1>
        <a href="{{ route('sites.show', $site) }}" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
            {{ __('Back') }}
        </a>
    </div>

    <form method="POST" action="{{ route('sites.buildings.update', ['site' => $site, 'building' => $building]) }}" enctype="multipart/form-data" class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Building Title') }} *</label>
            <input type="text" name="title" value="{{ old('title', $building->title) }}" required
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            @error('title')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Building Type') }} *</label>
            <select name="type" required
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="current" {{ old('type', $building->type) == 'current' ? 'selected' : '' }}>{{ __('Current Building') }}</option>
                <option value="previous" {{ old('type', $building->type) == 'previous' ? 'selected' : '' }}>{{ __('Previous Building') }}</option>
            </select>
            @error('type')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Description') }}</label>
            <textarea name="description" rows="4"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('description', $building->description) }}</textarea>
            @error('description')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Main Image') }}</label>
            @if($building->main_image_url)
            <div class="mb-2">
                <img src="{{ $building->main_image_url }}" alt="{{ $building->title }}" class="w-32 h-32 object-cover rounded-lg">
            </div>
            @endif
            <input type="file" name="main_image" accept="image/*"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Main image for the building. Maximum file size: 10MB.') }}</p>
            @error('main_image')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Layout Image') }}</label>
            @if($building->layout_image_url)
            <div class="mb-2">
                <img src="{{ $building->layout_image_url }}" alt="{{ $building->title }}" class="w-32 h-32 object-cover rounded-lg">
            </div>
            @endif
            <input type="file" name="layout_image" accept="image/*"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Layout image that will be displayed in the listings page. Maximum file size: 10MB.') }}</p>
            @error('layout_image')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        @if($building->images->count() > 0)
        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Current Images') }}</label>
            <div class="grid grid-cols-4 gap-4 mb-4">
                @foreach($building->images as $image)
                <div class="relative">
                    <img src="{{ $image->image_url }}" alt="{{ $building->title }}" class="w-full h-24 object-cover rounded-lg">
                    <form action="{{ route('building-images.destroy', $image) }}" method="POST" class="absolute top-1 right-1" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white rounded-full p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Add More Images') }}</label>
            <input type="file" name="images[]" multiple accept="image/*"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('You can select multiple images. Maximum file size: 10MB per image.') }}</p>
            @error('images.*')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('sites.show', $site) }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Update Building') }}
            </button>
        </div>
    </form>
</div>
@endsection

