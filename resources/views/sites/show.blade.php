@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $site->name }}</h1>
            @if($site->description)
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $site->description }}</p>
            @endif
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('sites.edit', $site) }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Edit Site') }}
            </a>
            <a href="{{ route('sites.projects.create', $site) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Add Project') }}
            </a>
        </div>
    </div>

    <!-- Current Projects -->
    @if($site->currentProjects->count() > 0)
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Current Projects') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($site->currentProjects as $project)
            <div class="border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                @if($project->main_image_url)
                <img src="{{ $project->main_image_url }}" alt="{{ $project->title }}" class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gray-200 dark:bg-[#3E3E3A] flex items-center justify-center">
                    <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ __('No Image') }}</span>
                </div>
                @endif
                <div class="p-4">
                    <h3 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ $project->title }}</h3>
                    @if($project->description)
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">{{ Str::limit($project->description, 100) }}</p>
                    @endif
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('units.index') }}?project_id={{ $project->id }}" class="w-full text-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ __('View Units') }}
                        </a>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('sites.projects.edit', ['site' => $site, 'project' => $project]) }}" class="flex-1 text-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('sites.projects.destroy', ['site' => $site, 'project' => $project]) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Previous Projects -->
    @if($site->previousProjects->count() > 0)
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Previous Projects') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($site->previousProjects as $project)
            <div class="border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                @if($project->main_image_url)
                <img src="{{ $project->main_image_url }}" alt="{{ $project->title }}" class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gray-200 dark:bg-[#3E3E3A] flex items-center justify-center">
                    <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ __('No Image') }}</span>
                </div>
                @endif
                <div class="p-4">
                    <h3 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ $project->title }}</h3>
                    @if($project->description)
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">{{ Str::limit($project->description, 100) }}</p>
                    @endif
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('units.index') }}?project_id={{ $project->id }}" class="w-full text-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ __('View Units') }}
                        </a>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('sites.projects.edit', ['site' => $site, 'project' => $project]) }}" class="flex-1 text-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('sites.projects.destroy', ['site' => $site, 'project' => $project]) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($site->projects->count() === 0)
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-12 text-center">
        <p class="text-[#706f6c] dark:text-[#A1A09A]">{{ __('No projects found. Add your first project.') }}</p>
    </div>
    @endif
</div>
@endsection