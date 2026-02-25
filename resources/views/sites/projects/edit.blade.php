@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Edit Project') }}: {{ $project->title }}</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.units.index', $project) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                {{ __('Booking Plan') }}
            </a>
            <a href="{{ route('sites.show', $site) }}" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('sites.projects.update', ['site' => $site, 'project' => $project]) }}" enctype="multipart/form-data" class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Project Title') }} *</label>
            <input type="text" name="title" value="{{ old('title', $project->title) }}" required
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            @error('title')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Project Type') }} *</label>
            <select name="type" required
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="current" {{ old('type', $project->type) == 'current' ? 'selected' : '' }}>{{ __('Current Project') }}</option>
                <option value="previous" {{ old('type', $project->type) == 'previous' ? 'selected' : '' }}>{{ __('Previous Project') }}</option>
            </select>
            @error('type')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Description') }}</label>
            <textarea name="description" rows="4"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('description', $project->description) }}</textarea>
            @error('description')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Main Image') }}</label>
            @if($project->main_image_url)
            <div class="mb-2">
                <img src="{{ $project->main_image_url }}" alt="{{ $project->title }}" class="w-32 h-32 object-cover rounded-lg">
            </div>
            @endif
            <input type="file" name="main_image" accept="image/*"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Main image for the project. Maximum file size: 10MB.') }}</p>
            @error('main_image')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Layout Image') }}</label>
            @if($project->layout_image_url)
            <div class="mb-2">
                <img src="{{ $project->layout_image_url }}" alt="{{ $project->title }}" class="w-32 h-32 object-cover rounded-lg">
            </div>
            @endif
            <input type="file" name="layout_image" accept="image/*"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Layout image that will be displayed in the listings page. Maximum file size: 10MB.') }}</p>
            @error('layout_image')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        @if($project->images->count() > 0)
        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Current Images') }}</label>
            <div class="grid grid-cols-4 gap-4 mb-4">
                @foreach($project->images as $image)
                <div class="relative">
                    <img src="{{ $image->image_url }}" alt="{{ $project->title }}" class="w-full h-24 object-cover rounded-lg">
                    <form action="{{ route('project-images.destroy', $image) }}" method="POST" class="absolute top-1 right-1" onsubmit="return confirm('{{ __('Are you sure?') }}');">
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
                {{ __('Update Project') }}
            </button>
        </div>
    </form>
</div>
@endsection

