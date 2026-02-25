@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Edit Team') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                {{ __('Update team settings, manage leaders and control membership from here.') }}
            </p>
        </div>
        <a href="{{ route('teams.show', $team) }}" class="px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
            {{ __('Back to Team') }}
        </a>
    </div>

    <form method="POST" action="{{ route('teams.update', $team) }}" class="bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-6">
        @csrf
        @method('PUT')
        @include('teams._form', [
        'team' => $team,
        'leaders' => $leaders,
        'members' => $members,
        'selectedLeaderIds' => $selectedLeaderIds ?? [],
        'selectedMemberIds' => $selectedMemberIds ?? [],
        'submitLabel' => __('Update Team'),
        'cancelUrl' => route('teams.show', $team),
        ])
    </form>
</div>
@endsection