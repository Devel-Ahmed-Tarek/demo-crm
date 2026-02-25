@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Lead Statistics') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                @if($isAdmin)
                {{ __('Showing pipeline distribution for the entire team.') }}
                @else
                {{ __('These insights reflect only your assigned leads.') }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}"
                class="px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#1f1f1f]">
                {{ __('Back to Dashboard') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lead-stats-card p-6">
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Total Visible Leads') }}</p>
            <p class="mt-2 text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $totalVisibleLeads }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        @foreach($stageGroups as $category => $stages)
        @php
        $categoryMeta = $category === 'negative'
        ? ['label' => __('Negative / Cold Leads'), 'theme' => 'negative']
        : ['label' => __('Positive Leads'), 'theme' => 'positive'];
        @endphp
        <div class="lead-stats-category lead-stats-category--{{ $categoryMeta['theme'] }}">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <p class="text-xs uppercase tracking-wide category-label">{{ __('Category') }}</p>
                    <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $categoryMeta['label'] }}</h2>
                </div>
                <span class="lead-stats-pill">
                    {{ $categoryTotals[$category] ?? 0 }}
                </span>
            </div>

            <div class="space-y-4">
                @foreach($stages as $stage)
                <a href="{{ route('dashboard.leads-by-stage', $stage->key) }}" class="block">
                    <div class="lead-stats-stage cursor-pointer hover:bg-gray-50 dark:hover:bg-[#1f1f1f] transition-colors">
                        <div>
                            <p class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $stage->label }}</p>
                            @if($stage->description)
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $stage->description }}</p>
                            @endif
                        </div>
                        <div class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                            {{ $leadCounts[$stage->key] ?? 0 }}
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection