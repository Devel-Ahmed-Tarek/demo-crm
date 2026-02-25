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
$sourceLabels = $sourceLabels ?? $leadSourceCollection->mapWithKeys(fn ($source) => [$source->key => $source->label])->all();
@endphp
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $lead->name }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $lead->email }} | {{ $lead->phone }}</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('leads.edit', $lead) }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Edit') }}
            </a>
            <a href="{{ route('leads.index') }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lead Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Lead Details') }}</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Name') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $lead->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Email') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $lead->email ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Phone') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $lead->phone ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Source') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $sourceLabels[$lead->source] ?? __(ucfirst(str_replace('_', ' ', $lead->source))) }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Stage') }}:</span>
                        <select onchange="updateStage({{ $lead->id }}, this.value)" 
                            class="mt-1 px-3 py-1 text-sm rounded border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                            @foreach($stageGroups as $category => $stages)
                            <optgroup label="{{ $categoryLabels[$category] ?? ucfirst($category) }}">
                                @foreach($stages as $stage)
                                <option value="{{ $stage->key }}" {{ $lead->stage == $stage->key ? 'selected' : '' }}>{{ $stage->label }}</option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Assigned To') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $lead->assignedUser?->name ?? __('Unassigned') }}</p>
                    </div>
                </div>

                @if($lead->tags->count() > 0)
                    <div class="mt-4">
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Tags') }}:</span>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($lead->tags as $tag)
                                <span class="px-2 py-1 text-xs rounded-full text-white" style="background-color: {{ $tag->color }}">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($lead->notes)
                    <div class="mt-4">
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Notes') }}:</span>
                        <p class="text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $lead->notes }}</p>
                    </div>
                @endif

                @if($lead->last_contacted_at)
                    <div class="mt-4">
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Last Contacted:</span>
                        <p class="text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $lead->last_contacted_at->format('M d, Y H:i') }}</p>
                    </div>
                @endif

                @if($lead->next_followup_at)
                    <div class="mt-4">
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Next Follow-up:</span>
                        <p class="text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $lead->next_followup_at->format('M d, Y H:i') }}</p>
                    </div>
                @endif
            </div>

            <!-- Comments Section -->
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Comments') }}</h2>
                
                <!-- Add Comment Form -->
                <form action="{{ route('leads.comments.store', $lead) }}" method="POST" class="mb-6">
                    @csrf
                    <div class="flex gap-3">
                        <textarea name="comment" rows="3" required
                            class="flex-1 px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="{{ __('Add a comment...') }}"></textarea>
                        <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors self-start">
                            {{ __('Add') }}
                        </button>
                    </div>
                </form>

                <!-- Comments List -->
                <div class="space-y-4">
                    @if($lead->comments->count() > 0)
                        @foreach($lead->comments as $comment)
                            <div class="border-l-2 border-blue-500 pl-4 py-2 bg-gray-50 dark:bg-[#0a0a0a] rounded-r-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                        {{ $comment->user->name ?? __('Unknown User') }}
                                    </span>
                                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                        {{ $comment->created_at->format('M d, Y H:i') }}
                                    </span>
                                </div>
                                <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC] whitespace-pre-wrap">{{ $comment->comment }}</p>
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] text-center py-4">{{ __('No comments yet.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Events Section -->
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Events') }}</h2>
                
                <!-- Add Event Form -->
                <form action="{{ route('leads.events.store', $lead) }}" method="POST" class="mb-6">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <input type="text" name="title" required
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="{{ __('Event Title') }}">
                        </div>
                        <div>
                            <select name="activity_type" required
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="call">{{ __('Call') }}</option>
                                <option value="email">{{ __('Email') }}</option>
                                <option value="meeting">{{ __('Meeting') }}</option>
                                <option value="site_visit">{{ __('Site Visit') }}</option>
                                <option value="note">{{ __('Note') }}</option>
                                <option value="task">{{ __('Task') }}</option>
                                <option value="event" selected>{{ __('Event') }}</option>
                            </select>
                        </div>
                        <div>
                            <textarea name="details" rows="2"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="{{ __('Event Details (optional)') }}"></textarea>
                        </div>
                        <div class="flex gap-3">
                            <input type="datetime-local" name="scheduled_at" required
                                class="flex-1 px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="submit" class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                                {{ __('Add Event') }}
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Events List -->
                <div class="space-y-3">
                    @if($lead->events->count() > 0)
                        @foreach($lead->events as $event)
                            <div class="border-l-2 border-green-500 pl-4 py-3 bg-gray-50 dark:bg-[#0a0a0a] rounded-r-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                            {{ $event->title }}
                                        </span>
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                            {{ __(ucfirst(str_replace('_', ' ', $event->activity_type))) }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                        {{ $event->scheduled_at->format('M d, Y H:i') }}
                                    </span>
                                </div>
                                @if($event->details)
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-1">{{ $event->details }}</p>
                                @endif
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ __('By:') }} {{ $event->user->name ?? __('Unknown User') }}
                                </p>
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] text-center py-4">{{ __('No events scheduled.') }}</p>
                    @endif
                </div>
            </div>

            @if($lead->customer && $lead->customer->communications->count() > 0)
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                    <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Communications') }}</h2>
                    <div class="space-y-4">
                        @foreach($lead->customer->communications as $communication)
                            <div class="border-l-2 border-blue-500 pl-4 py-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ __(ucfirst($communication->type)) }}</span>
                                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $communication->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                @if($communication->notes)
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $communication->notes }}</p>
                                @endif
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('By:') }} {{ $communication->user->name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Customer Info Sidebar -->
        @if($lead->customer)
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Customer Info') }}</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Name') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $lead->customer->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Email') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $lead->customer->email ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Phone') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $lead->customer->phone ?? __('N/A') }}</p>
                    </div>
                    <div class="pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <a href="{{ route('customers.show', $lead->customer) }}" class="block w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-center transition-colors text-sm">
                            {{ __('View Customer') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function updateStage(leadId, stage) {
    fetch(`/leads/${leadId}/update-stage`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ stage })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Optionally show success message
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
