@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Contract') }} #{{ $contract->contract_number ?? $contract->id }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Contract Details') }}</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('contracts.edit', $contract) }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Edit') }}
            </a>
            <a href="{{ route('contracts.index') }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Contract Information') }}</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Contract Number') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->contract_number ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Status') }}:</span>
                        @php
                        $statusColors = [
                            'draft' => 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200',
                            'active' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                            'completed' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
                            'cancelled' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                        ];
                        @endphp
                        <p class="mt-1">
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$contract->status] ?? $statusColors['draft'] }}">
                                {{ __(ucfirst($contract->status)) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Contract Date') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->contract_date ? $contract->contract_date->format('M d, Y') : __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Start Date') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->start_date ? $contract->start_date->format('M d, Y') : __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('End Date') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->end_date ? $contract->end_date->format('M d, Y') : __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Total Amount') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->total_amount ? '$' . number_format($contract->total_amount, 2) : __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Down Payment') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->down_payment ? '$' . number_format($contract->down_payment, 2) : __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Remaining Amount') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->remaining_amount ? '$' . number_format($contract->remaining_amount, 2) : __('N/A') }}</p>
                    </div>
                </div>

                @if($contract->notes)
                <div class="mt-4">
                    <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Notes') }}:</span>
                    <p class="text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->notes }}</p>
                </div>
                @endif
            </div>

            @if($contract->lead)
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Lead Information') }}</h2>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Name') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $contract->lead->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Email') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $contract->lead->email ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Phone') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $contract->lead->phone ?? __('N/A') }}</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('leads.show', $contract->lead) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                            {{ __('View Lead') }} →
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            @if($contract->customer)
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Customer Info') }}</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Name') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->customer->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Email') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->customer->email ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Phone') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->customer->phone ?? __('N/A') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if($contract->unit)
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Unit Information') }}</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Unit Code') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->unit->code }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Location') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->unit->location }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Area') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $contract->unit->area }} m²</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

