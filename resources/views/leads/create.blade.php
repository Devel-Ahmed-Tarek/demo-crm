@extends('layouts.app')

@section('content')
@php
$stageCollection = $leadStages ?? collect();
$stageGroups = $stageCollection->groupBy(fn ($stage) => $stage->category ?? 'positive');
$categoryLabels = [
'positive' => __('Positive Leads'),
'negative' => __('Negative / Cold Leads'),
];
$stageOptions = $stageCollection->mapWithKeys(fn ($stage) => [$stage->key => $stage->label])->all();
$defaultStageKey = array_key_first($stageOptions);
$leadSourceCollection = ($leadSources ?? collect());
$activeSources = $leadSourceCollection->filter(fn ($source) => $source->is_active ?? true);
$sourceOptions = $activeSources->mapWithKeys(fn ($source) => [$source->key => $source->label])->all();
$defaultSourceKey = array_key_first($sourceOptions) ?? 'website';
$teamCollection = $teams ?? collect();
$teamOptions = $teamCollection->pluck('name', 'id')->all();
$defaultTeamId = old('team_id', optional($currentUser->primaryTeam)->id);
@endphp
<div class="max-w-4xl mx-auto space-y-6">
    <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Create Lead') }}</h1>

    <form method="POST" action="{{ route('leads.store') }}" class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Name') }} *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Source') }} *</label>
                <select name="source" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    @foreach($sourceOptions as $sourceKey => $sourceLabel)
                    <option value="{{ $sourceKey }}" {{ old('source', $defaultSourceKey) == $sourceKey ? 'selected' : '' }}>{{ $sourceLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Stage') }} *</label>
                <select name="stage" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    @foreach($stageGroups as $category => $stages)
                    <optgroup label="{{ $categoryLabels[$category] ?? ucfirst($category) }}">
                        @foreach($stages as $stage)
                        <option value="{{ $stage->key }}" {{ old('stage', $defaultStageKey) == $stage->key ? 'selected' : '' }}>{{ $stage->label }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>

            @unless($currentUser->isSalesAgent())
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Assigned To') }}</label>
                <select name="assigned_to"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="">{{ __('Unassigned') }}</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <input type="hidden" name="assigned_to" value="{{ $currentUser->id }}">
            @endunless

            @unless($currentUser->isSalesAgent())
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Team') }}</label>
                <select name="team_id"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="">{{ __('Auto assign by member team') }}</option>
                    @foreach($teamOptions as $teamId => $teamName)
                    <option value="{{ $teamId }}" {{ (string)old('team_id', $defaultTeamId) === (string)$teamId ? 'selected' : '' }}>{{ $teamName }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <input type="hidden" name="team_id" value="{{ $defaultTeamId }}">
            @endunless
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Tags') }}</label>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                <label class="flex items-center">
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                        class="rounded border-[#e3e3e0] dark:border-[#3E3E3A] text-blue-500">
                    <span class="ml-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ $tag->name }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Notes') }}</label>
            <textarea name="notes" rows="4"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('leads.index') }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Create Lead') }}
            </button>
        </div>
    </form>
</div>
@endsection