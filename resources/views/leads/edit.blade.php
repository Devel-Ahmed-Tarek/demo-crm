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
$leadSourceCollection = ($leadSources ?? collect());
$activeSources = $leadSourceCollection->filter(fn ($source) => $source->is_active ?? true);
$sourceOptions = $activeSources->mapWithKeys(fn ($source) => [$source->key => $source->label])->all();
$teamCollection = $teams ?? collect();
$teamOptions = $teamCollection->pluck('name', 'id')->all();
$selectedTeamId = old('team_id', $lead->team_id);
@endphp
<div class="max-w-4xl mx-auto space-y-6">
    <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Edit Lead') }}</h1>

    <form method="POST" action="{{ route('leads.update', $lead) }}" class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Name') }} *</label>
                <input type="text" name="name" value="{{ old('name', $lead->name) }}" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Email') }}</label>
                <input type="email" name="email" value="{{ old('email', $lead->email) }}"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Source') }} *</label>
                <select name="source" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    @foreach($sourceOptions as $sourceKey => $sourceLabel)
                    <option value="{{ $sourceKey }}" {{ old('source', $lead->source) == $sourceKey ? 'selected' : '' }}>{{ $sourceLabel }}</option>
                    @endforeach
                </select>
                @error('source')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Stage') }} *</label>
                <select name="stage" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    @foreach($stageGroups as $category => $stages)
                    <optgroup label="{{ $categoryLabels[$category] ?? ucfirst($category) }}">
                        @foreach($stages as $stage)
                        <option value="{{ $stage->key }}" {{ old('stage', $lead->stage) == $stage->key ? 'selected' : '' }}>{{ $stage->label }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
                @error('stage')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @unless($currentUser->isSalesAgent())
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Assigned To') }}</label>
                <select name="assigned_to"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="">{{ __('Unassigned') }}</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('assigned_to')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @else
            <input type="hidden" name="assigned_to" value="{{ $lead->assigned_to }}">
            @endunless

            @unless($currentUser->isSalesAgent())
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Team') }}</label>
                <select name="team_id"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="">{{ __('Auto assign by member team') }}</option>
                    @foreach($teamOptions as $teamId => $teamName)
                    <option value="{{ $teamId }}" {{ (string)$selectedTeamId === (string)$teamId ? 'selected' : '' }}>{{ $teamName }}</option>
                    @endforeach
                </select>
                @error('team_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @else
            <input type="hidden" name="team_id" value="{{ $lead->team_id }}">
            @endunless
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Tags') }}</label>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                <label class="flex items-center">
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                        {{ (is_array(old('tags')) && in_array($tag->id, old('tags'))) || $lead->tags->contains($tag->id) ? 'checked' : '' }}
                        class="rounded border-[#e3e3e0] dark:border-[#3E3E3A] text-blue-500">
                    <span class="ml-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ $tag->name }}</span>
                </label>
                @endforeach
            </div>
            @error('tags.*')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Notes') }}</label>
            <textarea name="notes" rows="4"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('notes', $lead->notes) }}</textarea>
            @error('notes')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('leads.show', $lead) }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Update Lead') }}
            </button>
        </div>
    </form>
</div>
@endsection