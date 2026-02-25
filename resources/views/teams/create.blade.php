@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Create Team') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                {{ __('Define a new team, assign leaders and invite members to collaborate on the same pipeline.') }}
            </p>
        </div>
        <a href="{{ route('teams.index') }}" class="px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
            {{ __('Back') }}
        </a>
    </div>

    <form method="POST" action="{{ route('teams.store') }}" class="bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-6">
        @csrf
        @include('teams._form', [
        'leaders' => $leaders,
        'members' => $members,
        'selectedLeaderIds' => [],
        'selectedMemberIds' => [],
        'submitLabel' => __('Create Team'),
        'cancelUrl' => route('teams.index'),
        ])
    </form>
</div>
@endsection