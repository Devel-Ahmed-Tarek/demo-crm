@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-wide text-[#706f6c] dark:text-[#A1A09A]">{{ __('Team Overview') }}</p>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $team->name }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                {{ $team->description ?: __('No description provided for this team.') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $team->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-[#2b2b2b] dark:text-[#cbd5f5]' }}">
                {{ $team->is_active ? __('Active team') : __('Inactive team') }}
            </span>
            @if(auth()->user()?->isAdmin())
            <a href="{{ route('teams.edit', $team) }}" class="px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Edit') }}
            </a>
            <form method="POST" action="{{ route('teams.destroy', $team) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this team? This action cannot be undone.') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-lg border border-red-200 text-sm text-red-600 hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10">
                    {{ __('Delete Team') }}
                </button>
            </form>
            @endif
            <a href="{{ route('teams.index') }}" class="px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('All Teams') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="team-stat">
            <p class="team-stat-label">{{ __('Leaders') }}</p>
            <p class="team-stat-value">{{ $team->leaders_count }}</p>
        </div>
        <div class="team-stat">
            <p class="team-stat-label">{{ __('Members') }}</p>
            <p class="team-stat-value">{{ $team->members_count }}</p>
        </div>
        <div class="team-stat">
            <p class="team-stat-label">{{ __('Leads') }}</p>
            <p class="team-stat-value">{{ $team->leads_count }}</p>
        </div>
        <div class="team-stat">
            <p class="team-stat-label">{{ __('Customers') }}</p>
            <p class="team-stat-value">{{ $team->customers_count }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Leaders') }}</h2>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Strategic owners of this team\'s pipeline') }}</p>
                </div>
            </div>
            <div class="space-y-3">
                @forelse($team->members->where('pivot.membership_type', 'leader') as $leader)
                <a href="{{ route('users.show', $leader) }}" class="flex items-center justify-between hover:bg-gray-50 dark:hover:bg-[#3E3E3A] p-2 rounded-lg transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 text-white flex items-center justify-center font-semibold">
                            {{ strtoupper(substr($leader->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $leader->name }}</p>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __(ucfirst(str_replace('_', ' ', $leader->role))) }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $leader->email }}</span>
                </a>
                @empty
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No leaders assigned yet.') }}</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark.bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Members') }}</h2>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Sales agents assigned to this team') }}</p>
                </div>
            </div>
            <div class="space-y-3 max-h-72 overflow-y-auto custom-scrollbar-thin">
                @forelse($team->members->where('pivot.membership_type', 'member') as $member)
                <a href="{{ route('users.show', $member) }}" class="flex items-center justify-between hover:bg-gray-50 dark:hover:bg-[#3E3E3A] p-2 rounded-lg transition-colors">
                    <div>
                        <p class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $member->name }}</p>
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $member->email }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A]">
                        {{ __(ucfirst(str_replace('_', ' ', $member->role))) }}
                    </span>
                </a>
                @empty
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No members added yet.') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Recent Leads') }}</h2>
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $team->leads_count }} {{ __('total') }}</span>
            </div>
            <div class="space-y-4 max-h-72 overflow-y-auto custom-scrollbar-thin">
                @forelse($team->leads as $lead)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $lead->name }}</p>
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $lead->stage }}</p>
                    </div>
                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ optional($lead->updated_at)->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No leads yet for this team.') }}</p>
                @endforelse
            </div>
        </div>
        <div class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Recent Customers') }}</h2>
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $team->customers_count }} {{ __('total') }}</span>
            </div>
            <div class="space-y-4 max-h-72 overflow-y-auto custom-scrollbar-thin">
                @forelse($team->customers as $customer)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $customer->name }}</p>
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $customer->email }}</p>
                    </div>
                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ optional($customer->updated_at)->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No customers yet for this team.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection