@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Dashboard') }}</h1>
            <nav class="text-xs lg:text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1 hidden lg:block">
                <a href="{{ route('dashboard') }}" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">{{ __('Home') }}</a>
                <span class="mx-2">></span>
                <span class="text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Dashboard') }}</span>
            </nav>
        </div>
        <div class="flex items-center gap-2 lg:gap-4">
            <div class="flex items-center gap-2 px-3 lg:px-4 py-2 bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-xs lg:text-sm text-[#706f6c] dark:text-[#A1A09A]">
                <span class="hidden sm:inline">OCT 18, 25 - NOV 16, 25</span>
                <span class="sm:hidden">OCT - NOV</span>
            </div>
            <button class="flex items-center gap-2 px-3 lg:px-4 py-2 bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-xs lg:text-sm text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span class="hidden sm:inline">{{ __('Filter') }}</span>
            </button>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Leads -->
        <a href="{{ route('leads.index') }}" class="group bg-white dark:bg-[#161615] rounded-xl shadow-md hover:shadow-xl border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 lg:p-6 transition-all duration-300 hover:-translate-y-1 animate-fade-in block focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            <div class="flex items-start justify-between mb-3 lg:mb-4">
                <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="mb-2">
                <p class="text-2xl lg:text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $totalLeads }}</p>
                <p class="text-xs lg:text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Total Leads') }}</p>
            </div>
            <div class="mt-3 lg:mt-4">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('All Leads') }}</span>
                    <span class="text-xs font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $totalLeads }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-[#3E3E3A] rounded-full h-2.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2.5 rounded-full transition-all duration-500 shadow-sm" style="width: 100%"></div>
                </div>
                <p class="mt-2 text-xs text-blue-500 flex items-center gap-1">
                    <span>{{ __('View all leads') }}</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </p>
            </div>
        </a>

        <!-- Today's Leads -->
        <a href="{{ route('leads.index', ['created_today' => '1']) }}" class="group bg-white dark:bg-[#161615] rounded-xl shadow-md hover:shadow-xl border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 lg:p-6 transition-all duration-300 hover:-translate-y-1 animate-fade-in block focus:outline-none focus:ring-2 focus:ring-orange-500/50" style="animation-delay: 0.1s">
            <div class="flex items-start justify-between mb-3 lg:mb-4">
                <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mb-2">
                <p class="text-2xl lg:text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $todayLeads }}</p>
                <p class="text-xs lg:text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Today\'s Leads') }}</p>
            </div>
            <div class="mt-3 lg:mt-4">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Leads created today') }}</span>
                    <span class="text-xs font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ now()->format('M d') }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-[#3E3E3A] rounded-full h-2.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2.5 rounded-full transition-all duration-500 shadow-sm" style="width: {{ $totalLeads > 0 ? min(($todayLeads / $totalLeads) * 100, 100) : 0 }}%"></div>
                </div>
            </div>
        </a>

        <!-- Total Users -->
        <a href="{{ route('users.index') }}" class="group bg-white dark:bg-[#161615] rounded-xl shadow-md hover:shadow-xl border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 lg:p-6 transition-all duration-300 hover:-translate-y-1 animate-fade-in block focus:outline-none focus:ring-2 focus:ring-green-500/50" style="animation-delay: 0.2s">
            <div class="flex items-start justify-between mb-3 lg:mb-4">
                <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="mb-2">
                <p class="text-2xl lg:text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $totalUsers }}</p>
                <p class="text-xs lg:text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Total Users') }}</p>
            </div>
            <div class="mt-3 lg:mt-4">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Active Users') }}</span>
                    <span class="text-xs font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('View all') }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-[#3E3E3A] rounded-full h-2.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-2.5 rounded-full transition-all duration-500 shadow-sm" style="width: 100%"></div>
                </div>
                <p class="mt-2 text-xs text-green-500 flex items-center gap-1">
                    <span>{{ __('View all users') }}</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </p>
            </div>
        </a>

        <!-- Total Contracts -->
        <a href="{{ route('contracts.index') }}" class="group bg-white dark:bg-[#161615] rounded-xl shadow-md hover:shadow-xl border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 lg:p-6 transition-all duration-300 hover:-translate-y-1 animate-fade-in block focus:outline-none focus:ring-2 focus:ring-purple-500/50" style="animation-delay: 0.3s">
            <div class="flex items-start justify-between mb-3 lg:mb-4">
                <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <div class="mb-2">
                <p class="text-2xl lg:text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $totalContracts }}</p>
                <p class="text-xs lg:text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Total Contracts') }}</p>
            </div>
            <div class="mt-3 lg:mt-4">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Active Contracts') }}</span>
                    <span class="text-xs font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('View all') }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-[#3E3E3A] rounded-full h-2.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2.5 rounded-full transition-all duration-500 shadow-sm" style="width: 100%"></div>
                </div>
                <p class="mt-2 text-xs text-purple-500 flex items-center gap-1">
                    <span>{{ __('View all contracts') }}</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </p>
            </div>
        </a>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mt-4 lg:mt-6">
        <!-- Daily Events -->
        <a href="{{ route('leads.index') }}" class="bg-white dark:bg-[#161615] rounded-xl shadow-md border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 animate-fade-in block focus:outline-none focus:ring-2 focus:ring-blue-500/50 hover:shadow-xl transition-shadow duration-300" style="animation-delay: 0.4s">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Daily Events') }}</h2>
                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @if(isset($todayAppointments) && $todayAppointments->count() > 0)
                @foreach($todayAppointments as $event)
                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-[#0a0a0a] rounded-lg hover:bg-gray-100 dark:hover:bg-[#161615] transition-colors">
                    <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] truncate">
                                @if($event->lead)
                                {{ $event->lead->name }}
                                @else
                                {{ __('Unknown Lead') }}
                                @endif
                            </p>
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] ml-2">
                                {{ $event->scheduled_at->format('H:i') }}
                            </span>
                        </div>
                        @if($event->title)
                        <p class="text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] truncate">
                            {{ $event->title }}
                        </p>
                        @endif
                        @if($event->details)
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1 line-clamp-2">
                            {{ $event->details }}
                        </p>
                        @endif
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-[#706f6c] dark:text-[#A1A09A] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No events scheduled for today') }}</p>
                </div>
                @endif
            </div>
        </a>

        <!-- Total Sales -->
        <a href="{{ route('units.index', ['status' => 'sold']) }}" class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-xl p-6 text-white relative overflow-hidden animate-fade-in hover:shadow-2xl transition-shadow duration-300 block focus:outline-none focus:ring-2 focus:ring-blue-400/50" style="animation-delay: 0.5s">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600 rounded-full -mr-16 -mt-16 opacity-20 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-600 rounded-full -ml-24 -mb-24 opacity-20 animate-pulse" style="animation-delay: 1s"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">{{ __('Total Sales') }}</h2>
                    <div class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-medium">{{ $salesGrowth }}%</div>
                </div>
                <p class="text-4xl font-bold mb-4">${{ number_format($totalSales, 0) }}</p>
                <div class="h-32 mt-4">
                    <canvas id="salesChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Floating Action Button -->
<div class="fixed bottom-20 lg:bottom-8 right-4 lg:right-8 z-40 animate-fade-in">
    <button class="group w-12 h-12 lg:w-14 lg:h-14 bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-full shadow-2xl shadow-blue-500/40 flex items-center justify-center text-white transition-all duration-300 hover:scale-110 hover:shadow-blue-500/50">
        <svg class="w-5 h-5 lg:w-6 lg:h-6 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
    </button>
</div>
@endsection