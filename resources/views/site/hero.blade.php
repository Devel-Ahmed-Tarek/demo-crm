@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Hero Image') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Manage the hero image displayed on the landing page') }}</p>
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
        <form action="{{ route('site.hero.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Hero Image') }}
                    </label>
                    <div class="mt-2">
                        @if($heroImage)
                        <div class="mb-4">
                            <img src="{{ \App\Helpers\UploadHelper::url($heroImage) }}" alt="Hero Image" class="max-w-full h-auto rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] max-h-96">
                        </div>
                        @endif
                        <input type="file" name="hero_image" accept="image/*" 
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ __('Recommended size: 1920x1080px or larger. Max file size: 5MB') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        {{ __('Save Hero Image') }}
                    </button>
                    @if($heroImage)
                    <form action="{{ route('site.hero.delete') }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete the hero image?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition-colors">
                            {{ __('Delete Hero Image') }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

