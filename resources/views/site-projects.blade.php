@extends('layouts.landing')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-orange-800 to-orange-300 text-white pt-24 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('listings') }}" class="text-white hover:text-orange-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-4xl md:text-5xl font-bold">{{ $site->name }}</h1>
        </div>
        @if($site->description)
        <p class="text-xl text-orange-100">{{ $site->description }}</p>
        @endif
    </div>
</section>

<!-- Projects Section -->
<section class="py-12 bg-white dark:bg-[#0a0a0a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Current Projects -->
        @if($site->currentProjects->count() > 0)
        <div class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Current Projects') }}</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($site->currentProjects as $project)
                <a href="{{ route('project.details', ['site' => $site, 'project' => $project]) }}" class="bg-white dark:bg-[#161615] rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-800 hover:shadow-2xl transition-all duration-[3000ms] block group">
                    <div class="relative h-64 bg-gray-200 dark:bg-gray-800 overflow-hidden">
                        @if($project->layout_image_url)
                        <img src="{{ $project->layout_image_url }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-[3000ms] group-hover:scale-110" loading="lazy">
                        @elseif($project->main_image_url)
                        <img src="{{ $project->main_image_url }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-[3000ms] group-hover:scale-110" loading="lazy">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        @endif
                        <span class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                            {{ __('Current') }}
                        </span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $project->title }}</h3>
                        @if($project->description)
                        <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">{{ Str::limit($project->description, 120) }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Previous Projects -->
        @if($site->previousProjects->count() > 0)
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Previous Projects') }}</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($site->previousProjects as $project)
                <a href="{{ route('project.details', ['site' => $site, 'project' => $project]) }}" class="bg-white dark:bg-[#161615] rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-800 hover:shadow-2xl transition-all duration-[3000ms] block group">
                    <div class="relative h-64 bg-gray-200 dark:bg-gray-800 overflow-hidden">
                        @if($project->layout_image_url)
                        <img src="{{ $project->layout_image_url }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-[3000ms] group-hover:scale-110" loading="lazy">
                        @elseif($project->main_image_url)
                        <img src="{{ $project->main_image_url }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-[3000ms] group-hover:scale-110" loading="lazy">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        @endif
                        <span class="absolute top-4 right-4 bg-gray-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                            {{ __('Previous') }}
                        </span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $project->title }}</h3>
                        @if($project->description)
                        <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">{{ Str::limit($project->description, 120) }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($site->projects->count() === 0)
        <div class="text-center py-12">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ __('No Projects Found') }}</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ __('No projects available for this site.') }}</p>
        </div>
        @endif
    </div>
</section>
@endsection