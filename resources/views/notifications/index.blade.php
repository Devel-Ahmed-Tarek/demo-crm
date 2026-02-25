@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Notifications') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('View and manage your notifications') }}</p>
        </div>
        @if($notifications->count() > 0)
        <button onclick="markAllAsRead()" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors text-sm">
            {{ __('Mark all as read') }}
        </button>
        @endif
    </div>

    @if($notifications->count() > 0)
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
        @foreach($notifications as $notification)
        @php
            $data = $notification->data ?? [];
            $isRead = $notification->read_at !== null;
        @endphp
        <a href="{{ $data['url'] ?? '#' }}" onclick="markNotificationAsRead('{{ $notification->id }}')" class="block p-4 hover:bg-gray-50 dark:hover:bg-[#3E3E3A] transition-colors {{ !$isRead ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full {{ !$isRead ? 'bg-blue-500' : 'bg-transparent' }}"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $data['message'] ?? '' }}</p>
                    @if(isset($data['appointment_date']))
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Date') }}: {{ $data['appointment_date'] }}</p>
                    @endif
                    @if(isset($data['customer_name']))
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Customer') }}: {{ $data['customer_name'] }}</p>
                    @endif
                    @if(isset($data['lead_name']))
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Lead') }}: {{ $data['lead_name'] }}</p>
                    @endif
                    @if(isset($data['assigned_by']))
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Assigned by') }}: {{ $data['assigned_by'] }}</p>
                    @endif
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
    @else
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-[#706f6c] dark:text-[#A1A09A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <p class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('No notifications') }}</p>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('You don't have any notifications yet') }}</p>
    </div>
    @endif
</div>

<script>
    function markNotificationAsRead(id) {
        fetch(`{{ route("notifications.mark-as-read", ":id") }}`.replace(':id', id), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to update read status
                    window.location.reload();
                }
            });
    }

    function markAllAsRead() {
        fetch('{{ route("notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
    }
</script>
@endsection

