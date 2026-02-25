@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 py-2 sm:py-4 lg:py-6">
    <div class="mb-3 sm:mb-4 lg:mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-lg sm:text-xl lg:text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Missed Events') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Events that passed 15 minutes or more') }}</p>
        </div>
        <a href="{{ route('calendar.index') }}" class="flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-[#3E3E3A] hover:bg-gray-200 dark:hover:bg-[#4E4E4A] text-[#1b1b18] dark:text-[#EDEDEC] rounded-lg transition-colors text-sm font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>{{ __('Back to Calendar') }}</span>
        </a>
    </div>

    <!-- Missed Events Table -->
    @if($missedEvents->count() > 0)
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
        <div class="table-container">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 dark:bg-[#3E3E3A]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Event Title') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Lead') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Scheduled Date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Created By') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                    @foreach($missedEvents as $event)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $event->title }}</div>
                            @if($event->details)
                            <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ Str::limit($event->details, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($event->lead)
                            <a href="{{ route('leads.show', $event->lead) }}" class="text-sm text-blue-500 hover:text-blue-700">
                                {{ $event->lead->name }}
                            </a>
                            @else
                            <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Unknown Lead') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $event->scheduled_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300">
                                {{ __(ucfirst($event->activity_type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $event->user->name ?? __('Unknown User') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($event->lead)
                            <a href="{{ route('leads.show', $event->lead) }}" class="text-blue-500 hover:text-blue-700">{{ __('View Lead') }}</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
            {{ $missedEvents->links() }}
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-[#706f6c] dark:text-[#A1A09A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No missed events found') }}</p>
    </div>
    @endif
</div>
@endsection