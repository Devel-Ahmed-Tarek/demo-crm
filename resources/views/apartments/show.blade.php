@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $apartment->code }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Unit') }}: {{ $unit->code }} - {{ $unit->location }}</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('units.apartments.index', $unit) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Back') }}
            </a>
            <a href="{{ route('units.apartments.edit', ['unit' => $unit, 'apartment' => $apartment]) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Edit') }}
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Status') }}:</span>
                <p class="mt-1">
                    @if($apartment->status == 'reserved')
                        <span class="px-2 py-1 text-xs rounded-full bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300">{{ __('Reserved') }}</span>
                    @elseif($apartment->status == 'owner')
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">{{ __('Not for sale') }}</span>
                    @elseif($apartment->status == 'contracted')
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">{{ __('Contracted') }}</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300">{{ __('Available') }}</span>
                    @endif
                </p>
            </div>

            @if($apartment->area)
            <div>
                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Area') }}:</span>
                <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ number_format($apartment->area, 0) }} m²</p>
            </div>
            @endif

            @if($apartment->rooms)
            <div>
                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Rooms') }}:</span>
                <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $apartment->rooms }}</p>
            </div>
            @endif

            @if($apartment->price)
            <div>
                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Price') }}:</span>
                <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">${{ number_format($apartment->price, 0) }}</p>
            </div>
            @endif
        </div>

        @if($apartment->description)
        <div class="mt-6 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
            <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Description') }}:</span>
            <p class="text-[#1b1b18] dark:text-[#EDEDEC] mt-2">{{ $apartment->description }}</p>
        </div>
        @endif
    </div>
</div>
@endsection

