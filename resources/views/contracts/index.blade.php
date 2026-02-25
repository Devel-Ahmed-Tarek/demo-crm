@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Contracts') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Final Contracts') }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="{{ route('contracts.index') }}" class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-4">
        <div class="flex flex-wrap gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search') }}..."
                class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">

            <select name="status" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                {{ __('Filter') }}
            </button>
        </div>
    </form>

    <!-- Contracts Table -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
        <div class="table-container">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 dark:bg-[#0a0a0a] border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Contract Number') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Lead') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Customer') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Total Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Contract Date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                    @forelse($contracts as $contract)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#0a0a0a]">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                {{ $contract->contract_number ?? __('N/A') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                {{ $contract->lead->name }}
                            </div>
                            <div class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                {{ $contract->lead->email ?? $contract->lead->phone }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                {{ $contract->customer->name ?? __('N/A') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                {{ $contract->total_amount ? '$' . number_format($contract->total_amount, 2) : __('N/A') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $statusColors = [
                            'draft' => 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200',
                            'active' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                            'completed' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
                            'cancelled' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                            ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$contract->status] ?? $statusColors['draft'] }}">
                                {{ __(ucfirst($contract->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $contract->contract_date ? $contract->contract_date->format('M d, Y') : __('N/A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('contracts.show', $contract) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3">
                                {{ __('View') }}
                            </a>
                            <a href="{{ route('contracts.edit', $contract) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-3">
                                {{ __('Edit') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                            {{ __('No contracts found.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
        <div class="px-6 py-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
            {{ $contracts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection