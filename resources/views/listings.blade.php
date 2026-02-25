@extends('layouts.landing')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-orange-800 to-orange-300 text-white pt-24 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold mb-4 text-center">{{ __('Explore Our Sites & Projects') }}</h1>
        <p class="text-xl text-center text-orange-100">{{ __('Discover our premium real estate sites and projects') }}</p>
    </div>
</section>

<!-- Search and Filters Section -->
<section class="py-8 bg-gray-50 dark:bg-[#161615] -mt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-[#0a0a0a] rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-800">
            <form method="GET" action="{{ route('listings') }}" class="space-y-4">
                <!-- Search Bar -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by site name, project title, or description...') }}"
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-[#161615] text-gray-900 dark:text-white focus:ring-orange-500 focus:border-orange-500">
                </div>

                <!-- Filters Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Project Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Project Type') }}</label>
                        <select name="project_type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-[#161615] text-gray-900 dark:text-white focus:ring-orange-500 focus:border-orange-500">
                            <option value="">{{ __('All Projects') }}</option>
                            <option value="current" {{ request('project_type') == 'current' ? 'selected' : '' }}>{{ __('Current Projects') }}</option>
                            <option value="previous" {{ request('project_type') == 'previous' ? 'selected' : '' }}>{{ __('Previous Projects') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        {{ __('Search') }}
                    </button>
                    @if(request()->hasAny(['search', 'project_type']))
                    <a href="{{ route('listings') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                        {{ __('Clear Filters') }}
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Listings Section -->
<section class="py-12 bg-white dark:bg-[#0a0a0a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($sites->count() > 0)
        <div class="mb-6 flex items-center justify-between">
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('Showing') }} {{ $sites->firstItem() }} - {{ $sites->lastItem() }} {{ __('of') }} {{ $sites->total() }} {{ __('sites') }}
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($sites as $site)
            <a href="{{ route('site.projects', $site) }}" class="group relative rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-800 hover:shadow-2xl transition-all duration-500 cursor-pointer block" style="height: 320px; min-height: 320px;">
                <!-- Site Card with Background Image -->
                @if($site->image_url)
                <div class="absolute inset-0 transition-all duration-500 group-hover:scale-110 site-image-blur" style="background-image: url('{{ $site->image_url }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent group-hover:from-black/80 group-hover:via-black/50 transition-all duration-500"></div>
                </div>
                @else
                <div class="absolute inset-0 bg-gradient-to-br from-orange-800 to-orange-300"></div>
                @endif

                <!-- Content Overlay -->
                <div class="absolute inset-0 flex flex-col justify-center items-center p-6 text-white z-10 text-center transition-all duration-500 group-hover:scale-105">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4 drop-shadow-lg group-hover:drop-shadow-2xl transition-all duration-500">{{ $site->name }}</h2>
                    @if($site->projects && $site->projects->count() > 0)
                    <div class="flex items-center gap-2 transition-all duration-500 group-hover:scale-110">
                        <span class="bg-white/30 backdrop-blur-md px-4 py-2 rounded-full text-sm font-medium group-hover:bg-white/40 transition-all duration-500">
                            {{ $site->projects->count() }} {{ __('Projects') }}
                        </span>
                    </div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>


        <!-- Pagination -->
        <div class="mt-8">
            {{ $sites->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ __('No Sites Found') }}</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('Try adjusting your search filters to find more sites.') }}</p>
            @if(request()->hasAny(['search', 'project_type']))
            <a href="{{ route('listings') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                {{ __('Clear Filters') }}
            </a>
            @endif
        </div>
        @endif
    </div>
</section>
<style>
    .site-image-blur {
        filter: blur(4px);
        -webkit-filter: blur(4px);
    }

    .group:hover .site-image-blur {
        filter: blur(6px);
        -webkit-filter: blur(6px);
    }
</style>
@endsection