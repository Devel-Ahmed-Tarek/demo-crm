@extends('layouts.app')

@section('content')
@php
$viewType = $viewType ?? 'list';
$queryParams = request()->except(['page', 'view']);
$stageLabels = collect($stageMeta ?? [])->mapWithKeys(fn ($meta, $key) => [$key => $meta['label']])->all();
$stageCollection = $leadStages ?? collect();
$stageGroups = $stageCollection->groupBy(fn ($stage) => $stage->category ?? 'positive');
$categoryLabels = [
'positive' => __('Positive Leads'),
'negative' => __('Negative / Cold Leads'),
];
$stageOptions = $stageCollection->mapWithKeys(fn ($stage) => [$stage->key => $stage->label])->all();
$leadSourceCollection = ($leadSources ?? collect());
$activeLeadSources = $leadSourceCollection->filter(fn ($source) => $source->is_active ?? true);
$sourceOptions = $sourceOptions ?? $activeLeadSources->mapWithKeys(fn ($source) => [$source->key => $source->label])->all();
$sourceLabels = $sourceLabels ?? $leadSourceCollection->mapWithKeys(fn ($source) => [$source->key => $source->label])->all();
$isAdmin = auth()->user()?->isAdmin();
$teamCollection = $teams ?? collect();
$teamOptions = $teamCollection->pluck('name', 'id')->all();
$sourceMeta = [
'facebook' => ['label' => __('Facebook'), 'class' => 'source-chip source-chip--facebook'],
'website' => ['label' => __('Website'), 'class' => 'source-chip source-chip--website'],
'referral' => ['label' => __('Referral'), 'class' => 'source-chip source-chip--referral'],
'other' => ['label' => __('Other'), 'class' => 'source-chip source-chip--other'],
];
@endphp
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Leads') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Switch between classic table and the sales board to manage leads the way your team prefers.') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="bg-white dark:bg-[#0f0f0f] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-full p-1 flex">
                <a href="{{ route('leads.index', array_merge($queryParams, ['view' => 'list'])) }}" class="px-4 py-2 rounded-full text-sm font-medium transition {{ $viewType === 'list' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-[#706f6c] dark:text-[#A1A09A]' }}">
                    {{ __('Pipeline') }}
                </a>
                <a href="{{ route('leads.index', array_merge($queryParams, ['view' => 'board'])) }}" class="px-4 py-2 rounded-full text-sm font-medium transition {{ $viewType === 'board' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-md shadow-purple-500/30' : 'text-[#706f6c] dark:text-[#A1A09A]' }}">
                    {{ __('Sales Board') }}
                </a>
            </div>
            <a href="{{ route('export.leads', request()->query()) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Export Excel') }}
            </a>

            <a href="{{ route('leads.create') }}" class="group bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl transition-all duration-200 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 hover:-translate-y-0.5 flex items-center gap-2 font-medium">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Add Lead') }}
            </a>
            @if($isAdmin)
            <a href="{{ route('lead-sources.create') }}" class="action-chip flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Add Source') }}
            </a>
            <a href="{{ route('lead-stages.create') }}" class="action-chip flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Add Stage') }}
            </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-4">
        <form method="GET" action="{{ route('leads.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <input type="hidden" name="view" value="{{ $viewType }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search') }}..." class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">

            <select name="source" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Sources') }}</option>
                @foreach($sourceOptions as $sourceKey => $sourceLabel)
                <option value="{{ $sourceKey }}" {{ request('source') == $sourceKey ? 'selected' : '' }}>{{ $sourceLabel }}</option>
                @endforeach
            </select>

            <select name="stage" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Stages') }}</option>
                @foreach($stageGroups as $category => $stages)
                <optgroup label="{{ $categoryLabels[$category] ?? ucfirst($category) }}">
                    @foreach($stages as $stage)
                    <option value="{{ $stage->key }}" {{ request('stage') == $stage->key ? 'selected' : '' }}>{{ $stage->label }}</option>
                    @endforeach
                </optgroup>
                @endforeach
            </select>

            <select name="assigned_to" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Users') }}</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>

            <select name="team_id" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Teams') }}</option>
                @foreach($teamOptions as $teamId => $teamName)
                <option value="{{ $teamId }}" {{ request('team_id', $selectedTeamId ?? null) == $teamId ? 'selected' : '' }}>{{ $teamName }}</option>
                @endforeach
            </select>

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    @if($viewType === 'list')
    @if($isAdmin)
    <form id="leadBulkDeleteForm" method="POST" action="{{ route('leads.bulk-delete') }}">
        @csrf
        @endif
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
            @if($isAdmin)
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between px-6 py-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                <label class="inline-flex items-center gap-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                    <input type="checkbox" id="leadSelectAll" class="rounded text-blue-600 focus:ring-blue-500">
                    <span>{{ __('Select all') }}</span>
                </label>
                <button type="submit" id="leadBulkDeleteBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    {{ __('Delete selected') }}
                </button>
            </div>
            @endif
            <div class="table-container">
                <table class="w-full min-w-max">
                    <thead class="bg-gray-50 dark:bg-[#3E3E3A]">
                        <tr>
                            @if($isAdmin)
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider w-12">
                                {{ __('Select') }}
                            </th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Contact') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Source') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Stage') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Assigned To') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Tags') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Team') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                        @forelse($leads as $lead)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                            @if($isAdmin)
                            <td class="px-6 py-4">
                                <input type="checkbox" name="ids[]" value="{{ $lead->id }}" class="lead-row-checkbox rounded text-blue-600 focus:ring-blue-500">
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('leads.show', $lead) }}" class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] hover:text-blue-500">
                                    {{ $lead->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                <div>{{ $lead->email }}</div>
                                <div>{{ $lead->phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                $rawSourceKey = $lead->source ?: 'other';
                                $normalizedSource = strtolower(trim($rawSourceKey));
                                $sourceBadge = $sourceMeta[$normalizedSource] ?? [
                                'label' => $sourceLabels[$rawSourceKey] ?? __(ucfirst(str_replace('_', ' ', $rawSourceKey))),
                                'class' => 'source-chip',
                                ];
                                @endphp
                                <span class="{{ $sourceBadge['class'] }}">
                                    <span class="source-chip-dot"></span>
                                    {{ $sourceBadge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select onchange="updateStage({{ $lead->id }}, this.value)" class="text-sm px-2 py-1 rounded border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                                    @foreach($stageOptions as $stageKey => $label)
                                    <option value="{{ $stageKey }}" {{ $lead->stage == $stageKey ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                {{ $lead->assignedUser?->name ?? __('Unassigned') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($lead->tags as $tag)
                                    <span class="px-2 py-1 text-xs rounded-full text-white" style="background-color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($lead->team)
                                @php
                                $teamColor = $lead->team->color ?: '#7c3aed';
                                @endphp
                                <span class="team-chip" style="--team-chip-color: {{ $teamColor }}">
                                    <span class="team-chip-dot" style="background-color: {{ $teamColor }}"></span>
                                    {{ $lead->team->name }}
                                </span>
                                @else
                                <span class="team-chip team-chip--unassigned">
                                    <span class="team-chip-dot"></span>
                                    {{ __('No Team') }}
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('leads.edit', $lead) }}" class="text-blue-500 hover:text-blue-700 mr-3">{{ __('Edit') }}</a>
                                <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? '9' : '8' }}" class="px-6 py-4 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No leads found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leads)
            <div class="px-6 py-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                {{ $leads->links() }}
            </div>
            @endif
        </div>
        @if($isAdmin)
    </form>
    @endif
    @else
    <div class="flex flex-col gap-4">
        <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-600 text-xs font-semibold">i</span>
            {{ __('Drag the timeline headers horizontally on mobile to explore every stage. Use the quick action buttons on each card to log events or jump into lead details.') }}
        </div>
        <div class="flex gap-4 overflow-x-auto pb-4">
            @foreach($stageCollection as $stage)
            @php
            $key = $stage->key;
            $meta = $stageMeta[$key] ?? [
            'label' => $stage->label,
            'description' => $stage->description ?? '',
            'accent' => 'from-slate-500 to-slate-600',
            'dot' => 'bg-slate-400',
            'border' => 'rgba(227, 227, 224, 0.6)',
            'card_border' => 'rgba(227, 227, 224, 0.6)',
            'shadow' => 'rgba(15, 15, 15, 0.15)',
            'glow' => 'rgba(59, 130, 246, 0.12)',
            ];
            $stageLeads = $boardLeads[$key] ?? collect();
            $stageLeadCount = $stageLeads instanceof \Illuminate\Support\Collection ? $stageLeads->count() : collect($stageLeads)->count();
            $category = $stage->category ?? 'positive';
            $glowColor = $meta['glow'] ?? 'rgba(59, 130, 246, 0.18)';
            $borderColor = $meta['border'] ?? 'rgba(227, 227, 224, 0.6)';
            $cardBorderColor = $meta['card_border'] ?? '#e3e3e0';
            $shadowColor = $meta['shadow'] ?? 'rgba(15, 15, 15, 0.12)';
            $columnStyle = sprintf('--kanban-accent:%s; --kanban-border:%s; --kanban-shadow:%s;', $glowColor, $borderColor, $shadowColor);
            $cardStyle = sprintf('--kanban-card-border:%s; --kanban-card-shadow:%s;', $cardBorderColor, $shadowColor);
            @endphp
            <div class="min-w-[272px] w-full md:w-1/2 xl:w-1/3 rounded-2xl flex-shrink-0 kanban-column" style="{{ $columnStyle }}" data-stage-column="{{ $key }}">
                <div class="kanban-category-banner">
                    <span class="kanban-category-chip {{ $category === 'positive' ? 'kanban-chip-positive' : 'kanban-chip-negative' }}">
                        {{ $categoryLabels[$category] ?? ucfirst($category) }}
                    </span>
                </div>
                <div class="p-4 kanban-column-header">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 kanban-stage-meta">
                            <span class="w-2.5 h-2.5 rounded-full {{ $meta['dot'] }}"></span>
                            <h3 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $meta['label'] }}</h3>
                        </div>
                        <span class="kanban-stage-count">{{ $stageLeadCount }}</span>
                    </div>
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $meta['description'] }}</p>
                </div>

                <div class="p-4 space-y-3 max-h-[75vh] overflow-y-auto custom-scrollbar-thin kanban-dropzone" data-stage-dropzone="{{ $key }}">
                    @forelse($stageLeads as $lead)
                    <div class="kanban-card rounded-2xl p-4 transition" style="{{ $cardStyle }}" draggable="true" data-lead-card="true" data-lead-id="{{ $lead->id }}" data-current-stage="{{ $lead->stage }}" id="lead-card-{{ $lead->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <a href="{{ route('leads.show', $lead) }}" class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC] hover:text-blue-500">
                                    {{ $lead->name }}
                                </a>
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ $sourceLabels[$lead->source] ?? __(ucfirst(str_replace('_', ' ', $lead->source))) }} · {{ $lead->email ?: __('No email') }}
                                </p>
                            </div>
                            <button onclick='openLeadActivityModal({{ $lead->id }}, @json($lead->name), {{ $lead->customer_id ? 'true' : 'false' }})' class="text-xs px-3 py-1 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 text-white font-medium hover:shadow-lg hover:-translate-y-0.5 transition">
                                {{ __('Add Event') }}
                            </button>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                            <div>
                                <p class="text-[11px] uppercase tracking-wide">{{ __('Owner') }}</p>
                                <p class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $lead->assignedUser?->name ?? __('Unassigned') }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wide">{{ __('Next Follow-up') }}</p>
                                <p class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">
                                    {{ optional($lead->next_followup_at)->format('M d, H:i') ?? __('Not scheduled') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="text-[11px] uppercase tracking-wide text-[#706f6c] dark:text-[#A1A09A]">{{ __('Quick Stage Update') }}</label>
                            <select onchange="updateStage({{ $lead->id }}, this.value)" class="mt-1 w-full text-sm px-3 py-2 rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-blue-500/40">
                                @foreach($stageOptions as $stageKey => $label)
                                <option value="{{ $stageKey }}" {{ $lead->stage == $stageKey ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($lead->activities?->count())
                        <div class="mt-4 border-t border-dashed border-[#e3e3e0] dark:border-[#3E3E3A] pt-3 space-y-2">
                            @foreach($lead->activities as $activity)
                            <div class="flex items-start justify-between text-xs gap-3">
                                <div>
                                    <p class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">
                                        {{ __(ucfirst(str_replace('_', ' ', $activity->activity_type))) }} · {{ $activity->title }}
                                    </p>
                                    @if($activity->details)
                                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{{ Str::limit($activity->details, 80) }}</p>
                                    @endif
                                </div>
                                <span class="text-[11px] text-[#706f6c] dark:text-[#A1A09A] whitespace-nowrap">
                                    {{ optional($activity->scheduled_at)->format('M d, H:i') ?? $activity->created_at->shortRelativeDiffForHumans() }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="mt-4 flex flex-wrap gap-2 text-xs">
                            <a href="{{ route('leads.show', $lead) }}" class="px-3 py-1.5 rounded-full border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#1f1f1f]">
                                {{ __('View Lead') }}
                            </a>
                            <a href="{{ route('leads.edit', $lead) }}" class="px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-200">
                                {{ __('Edit') }}
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="border border-dashed border-[#e3e3e0] dark:border-[#3E3E3A] rounded-xl p-4 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        {{ __('No leads in this stage yet.') }}
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Lead Activity Modal -->
    <div id="leadActivityModal" class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center px-4">
        <div class="bg-white dark:bg-[#161615] rounded-2xl shadow-xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Add Lead Event') }}</h3>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Log calls, follow-ups, visits or quick notes for your sales team.') }}</p>
                </div>
                <button onclick="closeLeadActivityModal()" class="text-[#706f6c] hover:text-[#1b1b18] dark:hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="leadActivityForm" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Lead') }}</label>
                    <p id="leadActivityName" class="text-sm text-[#706f6c] dark:text-[#A1A09A] font-semibold"></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Event Type') }} *</label>
                        <select name="activity_type" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" required>
                            <option value="call">{{ __('Call') }}</option>
                            <option value="email">{{ __('Email') }}</option>
                            <option value="meeting">{{ __('Meeting') }}</option>
                            <option value="site_visit">{{ __('Site Visit') }}</option>
                            <option value="task">{{ __('Task') }}</option>
                            <option value="note">{{ __('Note') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Schedule') }}</label>
                        <input type="datetime-local" name="scheduled_at" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Title') }} *</label>
                    <input type="text" name="title" required class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" placeholder="{{ __('Ex: Follow-up call, Demo visit') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Notes') }}</label>
                    <textarea name="details" rows="4" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]" placeholder="{{ __('Important talking points, commitments, context for the next touchpoint.') }}"></textarea>
                </div>

                <div class="space-y-2 border-t border-dashed border-[#e3e3e0] dark:border-[#3E3E3A] pt-4">
                    <label class="flex items-start gap-3 text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                        <input type="checkbox" name="log_communication" id="log_communication" value="1" class="mt-1 rounded text-blue-600 focus:ring-blue-500">
                        <span>
                            {{ __('Also log this under Customer Communications') }}
                            <span id="logCommunicationHint" class="block text-xs font-normal text-[#706f6c] dark:text-[#A1A09A]">{{ __('If this lead is linked to a customer, we will create a communication record with the same details.') }}</span>
                        </span>
                    </label>

                    <div id="communicationFields" class="grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Communication Type') }} *</label>
                            <select name="communication_type" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                                <option value="call">{{ __('Call') }}</option>
                                <option value="email">{{ __('Email') }}</option>
                                <option value="visit">{{ __('Visit / Meeting') }}</option>
                                <option value="whatsapp">{{ __('WhatsApp') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Completed At') }}</label>
                            <input type="datetime-local" name="communication_completed_at" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="closeLeadActivityModal()" class="px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#1f1f1f]">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="px-6 py-2 rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 text-white font-medium hover:shadow-lg hover:-translate-y-0.5 transition">
                        {{ __('Save Event') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="leadBoardToast" class="fixed bottom-4 right-4 z-40 hidden px-4 py-3 rounded-xl bg-[#1b1b18] text-white text-sm shadow-xl dark:bg-[#0f0f0f] opacity-0 translate-y-3 transition-all duration-200">
        {{ __('Lead updated') }}
    </div>
    @endif
</div>

@if($isAdmin && $viewType === 'list')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('leadSelectAll');
        const bulkButton = document.getElementById('leadBulkDeleteBtn');
        const form = document.getElementById('leadBulkDeleteForm');

        function getCheckboxes() {
            return Array.from(document.querySelectorAll('.lead-row-checkbox'));
        }

        function updateButtonState() {
            const checkboxes = getCheckboxes();
            const anyChecked = checkboxes.some(cb => cb.checked);
            if (bulkButton) {
                bulkButton.disabled = !anyChecked;
            }
            if (selectAll) {
                selectAll.checked = anyChecked && checkboxes.length > 0 && checkboxes.every(cb => cb.checked);
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', (event) => {
                const checkboxes = getCheckboxes();
                checkboxes.forEach(cb => cb.checked = event.target.checked);
                updateButtonState();
            });
        }

        // Function to attach event listeners to checkboxes
        function attachCheckboxListeners() {
            const checkboxes = getCheckboxes();
            checkboxes.forEach(cb => {
                // Remove existing listener if any, then add new one
                cb.removeEventListener('change', updateButtonState);
                cb.addEventListener('change', updateButtonState);
            });
        }

        // Use event delegation for dynamically added checkboxes
        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('lead-row-checkbox')) {
                updateButtonState();
            }
        });

        // Watch for DOM changes (e.g., after pagination or filtering)
        const observer = new MutationObserver(function(mutations) {
            let shouldUpdate = false;
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0 || mutation.removedNodes.length > 0) {
                    shouldUpdate = true;
                }
            });
            if (shouldUpdate) {
                attachCheckboxListeners();
                updateButtonState();
            }
        });

        // Start observing the table container
        const tableContainer = document.querySelector('.table-container') || document.querySelector('tbody');
        if (tableContainer) {
            observer.observe(tableContainer, {
                childList: true
                , subtree: true
            });
        }

        if (form) {
            form.addEventListener('submit', (event) => {
                const checkboxes = getCheckboxes();
                if (!checkboxes.some(cb => cb.checked)) {
                    event.preventDefault();
                    return;
                }
                if (!confirm("{{ __('Delete selected leads ? This action cannot be undone.') }}")) {
                    event.preventDefault();
                    return;
                }
                // Force method to be POST and remove any _method hidden fields
                form.method = 'POST';
                const methodField = form.querySelector('input[name="_method"]');
                if (methodField) {
                    methodField.remove();
                }
            });
        }

        // Attach listeners and update button state after page load
        attachCheckboxListeners();
        updateButtonState();
    });

</script>
@endif

<script>
    function updateStage(leadId, stage, options = {}) {
        return fetch(`/leads/${leadId}/update-stage`, {
                method: 'POST'
                , headers: {
                    'Content-Type': 'application/json'
                    , 'Accept': 'application/json'
                    , 'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
                , body: JSON.stringify({
                    stage
                })
            })
            .then(async (response) => {
                const payload = await response.text();
                let data = {};
                if (payload) {
                    try {
                        data = JSON.parse(payload);
                    } catch (error) {
                        throw new Error('Invalid server response');
                    }
                }
                if (!response.ok || !data.success) {
                    const message = data.message || "{{ __('Stage update failed') }}";
                    const error = new Error(message);
                    error.details = data;
                    throw error;
                }
                if (options.onSuccess) {
                    options.onSuccess(data);
                }
                return data;
            })
            .catch(error => {
                console.error('Error:', error);
                if (options.onError) {
                    options.onError(error);
                }
                throw error;
            });
    }

    const leadActivityModal = document.getElementById('leadActivityModal');
    const leadActivityForm = document.getElementById('leadActivityForm');
    const leadActivityName = document.getElementById('leadActivityName');
    const logCommunicationCheckbox = document.getElementById('log_communication');
    const communicationFields = document.getElementById('communicationFields');
    const logCommunicationHint = document.getElementById('logCommunicationHint');

    function openLeadActivityModal(leadId, leadName, hasCustomer = true) {
        if (!leadActivityModal || !leadActivityForm || !leadActivityName) return;
        leadActivityForm.action = `/leads/${leadId}/activities`;
        leadActivityName.textContent = leadName;
        if (logCommunicationCheckbox) {
            logCommunicationCheckbox.checked = false;
            logCommunicationCheckbox.disabled = !hasCustomer;
            logCommunicationCheckbox.dataset.allowed = hasCustomer ? '1' : '0';
        }
        if (communicationFields) {
            communicationFields.classList.add('hidden');
        }
        if (logCommunicationHint) {
            logCommunicationHint.textContent = hasCustomer ?
                "{{ __('If this lead is linked to a customer, we will create a communication record with the same details.') }}" :
                "{{ __('Link this lead to a customer to enable logging under Customer Communications.') }}";
        }
        leadActivityModal.classList.remove('hidden');
    }

    function closeLeadActivityModal() {
        if (!leadActivityModal || !leadActivityForm) return;
        leadActivityModal.classList.add('hidden');
        leadActivityForm.reset();
        if (communicationFields) {
            communicationFields.classList.add('hidden');
        }
    }

    if (leadActivityModal) {
        leadActivityModal.addEventListener('click', function(event) {
            if (event.target === leadActivityModal) {
                closeLeadActivityModal();
            }
        });
    }

    if (logCommunicationCheckbox && communicationFields) {
        logCommunicationCheckbox.addEventListener('change', () => {
            if (logCommunicationCheckbox.disabled || logCommunicationCheckbox.dataset.allowed === '0') {
                logCommunicationCheckbox.checked = false;
                return;
            }
            communicationFields.classList.toggle('hidden', !logCommunicationCheckbox.checked);
        });
    }

    const kanbanStageLabels = @json($stageLabels);
    const leadBoardToast = document.getElementById('leadBoardToast');
    let dragState = {
        card: null
        , fromZone: null
    };

    function showLeadBoardToast(message, isError = false) {
        if (!leadBoardToast) return;
        leadBoardToast.textContent = message;
        leadBoardToast.classList.toggle('bg-red-600', isError);
        leadBoardToast.classList.toggle('dark:bg-red-500', isError);
        leadBoardToast.classList.toggle('bg-[#1b1b18]', !isError);
        leadBoardToast.classList.toggle('dark:bg-[#0f0f0f]', !isError);
        leadBoardToast.classList.remove('hidden');
        requestAnimationFrame(() => {
            leadBoardToast.classList.remove('opacity-0', 'translate-y-3');
            leadBoardToast.classList.add('opacity-100', 'translate-y-0');
        });
        clearTimeout(leadBoardToast.dataset.timeoutId);
        const timeoutId = setTimeout(() => {
            leadBoardToast.classList.remove('opacity-100', 'translate-y-0');
            leadBoardToast.classList.add('opacity-0', 'translate-y-3');
            setTimeout(() => leadBoardToast.classList.add('hidden'), 200);
        }, 2200);
        leadBoardToast.dataset.timeoutId = timeoutId;
    }

    function initLeadBoardDragAndDrop() {
        const cards = document.querySelectorAll('[data-lead-card="true"]');
        const dropzones = document.querySelectorAll('.kanban-dropzone');

        cards.forEach(card => {
            card.addEventListener('dragstart', (event) => {
                dragState.card = card;
                dragState.fromZone = card.closest('.kanban-dropzone');
                event.dataTransfer.effectAllowed = 'move';
                card.classList.add('kanban-card-dragging');
            });

            card.addEventListener('dragend', () => {
                card.classList.remove('kanban-card-dragging');
                dragState.card = null;
                dragState.fromZone = null;
                dropzones.forEach(zone => zone.classList.remove('kanban-drop-hover'));
            });
        });

        dropzones.forEach(zone => {
            zone.addEventListener('dragover', (event) => {
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
                zone.classList.add('kanban-drop-hover');
            });

            zone.addEventListener('dragleave', () => {
                zone.classList.remove('kanban-drop-hover');
            });

            zone.addEventListener('drop', (event) => {
                event.preventDefault();
                zone.classList.remove('kanban-drop-hover');

                if (!dragState.card) return;

                const newStage = zone.dataset.stageDropzone;
                const previousStage = dragState.card.dataset.currentStage;
                const leadId = dragState.card.dataset.leadId;

                if (newStage === previousStage) {
                    zone.appendChild(dragState.card);
                    return;
                }

                const originZone = dragState.fromZone;
                const movingCard = dragState.card;
                zone.appendChild(movingCard);
                movingCard.classList.add('opacity-60');

                updateStage(leadId, newStage, {
                    onSuccess: () => {
                        if (movingCard) {
                            movingCard.dataset.currentStage = newStage;
                            movingCard.classList.remove('opacity-60');
                        }
                        showLeadBoardToast(`{{ __('Moved to') }} ${kanbanStageLabels[newStage] || newStage}`);
                    }
                    , onError: () => {
                        if (movingCard) {
                            movingCard.classList.remove('opacity-60');
                        }
                        if (originZone && movingCard) {
                            originZone.appendChild(movingCard);
                        }
                        showLeadBoardToast("{{ __('Failed to update stage. Please try again.') }}", true);
                    }
                });
            });
        });
    }

    if ('{{ $viewType }}' === 'board') {
        initLeadBoardDragAndDrop();
    }

</script>
@endsection
