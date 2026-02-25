@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $customer->name }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $customer->email }} | {{ $customer->phone }}</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('customers.edit', $customer) }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Edit') }}
            </a>
            <a href="{{ route('customers.index') }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Customer Details') }}</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Name') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Email') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $customer->email ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Phone') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $customer->phone ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Assigned To') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $customer->assignedUser?->name ?? __('Unassigned') }}</p>
                    </div>
                    @if($customer->address)
                        <div class="col-span-2">
                            <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Address') }}:</span>
                            <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $customer->address }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($customer->leads->count() > 0)
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                    <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Leads') }}</h2>
                    <div class="space-y-2">
                        @foreach($customer->leads as $lead)
                            <a href="{{ route('leads.show', $lead) }}" class="block p-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $lead->name }}</span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A]">
                                        {{ __(ucfirst($lead->stage)) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($customer->communications->count() > 0)
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                    <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Communications') }}</h2>
                    <div class="space-y-4">
                        @foreach($customer->communications as $communication)
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

            @if($customer->appointments->count() > 0)
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                    <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Appointments') }}</h2>
                    <div class="space-y-2">
                        @foreach($customer->appointments as $appointment)
                            <div class="p-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $appointment->appointment_date->format('M d, Y H:i') }}</span>
                                        @if($appointment->unit)
                                            <span class="text-sm text-[#706f6c] dark:text-[#A1A09A] ml-2">- {{ $appointment->unit->code }}</span>
                                        @endif
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A]">
                                        {{ __(ucfirst($appointment->status)) }}
                                    </span>
                                </div>
                                @if($appointment->notes)
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $appointment->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($customer->reservedUnits->count() > 0 || $customer->purchasedUnits->count() > 0)
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                    <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Units') }}</h2>
                    @if($customer->reservedUnits->count() > 0)
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">{{ __('Reserved Units') }}</h3>
                            <div class="space-y-2">
                                @foreach($customer->reservedUnits as $unit)
                                    <a href="{{ route('units.show', $unit) }}" class="block p-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                                        <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $unit->code }}</span>
                                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A] ml-2">- {{ $unit->location }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($customer->purchasedUnits->count() > 0)
                        <div>
                            <h3 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">{{ __('Purchased Units') }}</h3>
                            <div class="space-y-2">
                                @foreach($customer->purchasedUnits as $unit)
                                    <a href="{{ route('units.show', $unit) }}" class="block p-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                                        <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $unit->code }}</span>
                                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A] ml-2">- {{ $unit->location }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
