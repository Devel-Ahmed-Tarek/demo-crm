@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Teams') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                {{ __('Organize your sales force into collaborative teams and monitor their pipelines at a glance.') }}
            </p>
        </div>
        @if(auth()->user()?->isAdmin())
        <a href="{{ route('teams.create') }}" class="btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>{{ __('Create Team') }}</span>
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="p-4 rounded-xl bg-green-50 text-green-800 border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-4">
        @forelse($teams as $team)
        @php
        $accent = $team->color ?? '#6366f1';
        @endphp
        <div class="team-card" style="--team-accent: {{ $accent }}">
            <div class="team-card__halo"></div>
            <div class="team-card__body">
                <div class="team-card__header">
                    <div class="team-card__identity">
                        <span class="team-card__avatar" style="background: {{ $accent }}">{{ strtoupper(substr($team->name, 0, 1)) }}</span>
                        <div>
                            <p class="team-card__title">{{ $team->name }}</p>
                            <span class="team-card__status {{ $team->is_active ? 'team-card__status--active' : 'team-card__status--inactive' }}">
                                {{ $team->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </div>
                    </div>
                    <span class="team-card__spark" style="color: {{ $accent }}">●</span>
                </div>

                <p class="team-card__description">
                    {{ Str::limit($team->description ?: __('No description provided for this team.'), 110) }}
                </p>

                <div class="team-card__metrics">
                    <div class="team-metric">
                        <span class="team-metric__label">{{ __('Members') }}</span>
                        <span class="team-metric__value">{{ $team->members_count }}</span>
                    </div>
                    <div class="team-metric">
                        <span class="team-metric__label">{{ __('Leads') }}</span>
                        <span class="team-metric__value">{{ $team->leads_count }}</span>
                    </div>
                    <div class="team-metric">
                        <span class="team-metric__label">{{ __('Customers') }}</span>
                        <span class="team-metric__value">{{ $team->customers_count }}</span>
                    </div>
                </div>

                <div class="team-card__footer">
                    <div class="team-card__avatars">
                        @foreach($team->leaders->take(3) as $leader)
                        <div class="team-card__avatar-pill" title="{{ $leader->name }}">
                            {{ strtoupper(Str::substr($leader->name, 0, 1)) }}
                        </div>
                        @endforeach
                        @if($team->leaders_count > 3)
                        <div class="team-card__avatar-pill team-card__avatar-pill--more">
                            +{{ $team->leaders_count - 3 }}
                        </div>
                        @endif
                    </div>
                    <div class="team-card__actions">
                        <a href="{{ route('teams.show', $team) }}" class="team-action team-action--ghost">
                            {{ __('View') }}
                        </a>
                        @if(auth()->user()?->isAdmin())
                        <a href="{{ route('teams.edit', $team) }}" class="team-action">
                            {{ __('Edit') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="p-8 border border-dashed border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl text-center">
                <p class="text-[#1b1b18] dark:text-[#EDEDEC] font-semibold">{{ __('No teams found') }}</p>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">
                    {{ __('Create your first team to start grouping your sales reps and leads.') }}
                </p>
            </div>
        </div>
        @endforelse
    </div>

    {{ $teams->links() }}
</div>
@endsection