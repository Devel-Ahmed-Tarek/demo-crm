@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Reports & Analytics') }}</h1>
        <a href="{{ route('export.reports', request()->query()) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{ __('Export Excel') }}
        </a>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-4">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    <!-- Lead Status Report -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Leads by Status') }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-300">{{ $leadStatusReport->get('new', 0) }}</p>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('New') }}</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-300">{{ $leadStatusReport->get('contacted', 0) + $leadStatusReport->get('follow-up', 0) }}</p>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Active') }}</p>
            </div>
            <div class="text-center p-4 bg-red-50 dark:bg-red-900 rounded-lg">
                <p class="text-2xl font-bold text-red-600 dark:text-red-300">{{ $leadStatusReport->get('lost', 0) }}</p>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Lost') }}</p>
            </div>
            <div class="text-center p-4 bg-green-50 dark:bg-green-900 rounded-lg">
                <p class="text-2xl font-bold text-green-600 dark:text-green-300">{{ $leadStatusReport->get('won', 0) }}</p>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Won') }}</p>
            </div>
        </div>
    </div>

    <!-- Conversion Rate -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Conversion Rate') }}</h2>
        <div class="text-center">
            <p class="text-4xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($conversionRate, 2) }}%</p>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ $wonLeads }} {{ __('won out of') }} {{ $wonLeads + $lostLeads }} {{ __('closed leads') }}</p>
        </div>
    </div>

    <!-- Dead Leads Report -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Dead Leads') }}</h2>
        <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $deadLeads }}</p>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ __('Leads not contacted in last 30 days') }}</p>
    </div>

    <!-- Sales Performance -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Sales Performance by User') }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-[#3E3E3A]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase">{{ __('User') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase">{{ __('Total Leads') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase">{{ __('Won Leads') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase">{{ __('Conversion Rate') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                    @foreach($salesPerformance as $user)
                    <tr>
                        <td class="px-6 py-4 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $user->total_leads }}</td>
                        <td class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $user->won_leads }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($user->conversion_rate, 2) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection