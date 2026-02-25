@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Apartments') }} - {{ $unit->code }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $unit->location }}</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('units.show', $unit) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Back to Unit') }}
            </a>
            <a href="{{ route('units.apartments.create', $unit) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Add Apartment') }}
            </a>
        </div>
    </div>

    @if($unit->apartments->count() > 0)
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-[#e3e3e0] dark:border-[#3E3E3A] bg-gray-50 dark:bg-[#2a2a28]">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Code') }}</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Area') }}</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Rooms') }}</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Price') }}</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unit->apartments as $apartment)
                    <tr class="border-b border-[#e3e3e0] dark:border-[#3E3E3A] hover:bg-gray-50 dark:hover:bg-[#2a2a28]">
                        <td class="px-4 py-3 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                            <a href="{{ route('units.apartments.show', ['unit' => $unit, 'apartment' => $apartment]) }}" class="font-medium hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $apartment->code }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $apartment->area ? number_format($apartment->area, 0) . ' m²' : '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $apartment->rooms ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $apartment->price ? '$' . number_format($apartment->price, 0) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($apartment->status == 'reserved')
                            <span class="px-2 py-1 text-xs rounded-full bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300">{{ __('Reserved') }}</span>
                            @elseif($apartment->status == 'owner')
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">{{ __('Not for sale') }}</span>
                            @elseif($apartment->status == 'contracted')
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">{{ __('Contracted') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300">{{ __('Available') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('units.apartments.edit', ['unit' => $unit, 'apartment' => $apartment]) }}" class="text-blue-500 hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ __('Edit') }}
                                </a>
                                <form action="{{ route('units.apartments.destroy', ['unit' => $unit, 'apartment' => $apartment]) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-600 dark:hover:text-red-400">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-12 text-center">
        <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">{{ __('No apartments found.') }}</p>
        <a href="{{ route('units.apartments.create', $unit) }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
            {{ __('Add First Apartment') }}
        </a>
    </div>
    @endif
</div>
@endsection