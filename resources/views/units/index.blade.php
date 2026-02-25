@extends('layouts.app')

@section('content')
@php
$currentUser = auth()->user();
$isSalesAgent = $currentUser?->isSalesAgent();
$isAdmin = $currentUser?->isAdmin();
@endphp
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Units Catalog') }}</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('export.units', request()->query()) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Export Excel') }}
            </a>
            <a href="{{ route('units.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Add Unit') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-4">
        <form method="GET" action="{{ route('units.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <select name="status" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Status') }}</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>{{ __('Available') }}</option>
                <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>{{ __('Reserved') }}</option>
                <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>{{ __('Sold') }}</option>
            </select>

            <input type="text" name="location" value="{{ request('location') }}" placeholder="{{ __('Location') }}..." class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">

            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="{{ __('Min Price') }}..." class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">

            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ __('Max Price') }}..." class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">

            <input type="number" name="min_area" value="{{ request('min_area') }}" placeholder="{{ __('Min Area') }}..." class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    @if($isAdmin)
    <form id="unitBulkDeleteForm" method="POST" action="{{ route('units.bulk-delete') }}">
        @csrf
        @endif

        @if($isAdmin)
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-4">
            <label class="inline-flex items-center gap-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                <input type="checkbox" id="unitSelectAll" class="rounded text-blue-600 focus:ring-blue-500">
                <span>{{ __('Select all units') }}</span>
            </label>
            <button type="submit" id="unitBulkDeleteBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                {{ __('Delete selected') }}
            </button>
        </div>
        @endif

        <!-- Units Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($units as $unit)
            <div class="rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden transition-shadow relative
                @if($unit->status == 'reserved')
                    shadow-xl shadow-orange-200/70 dark:shadow-orange-500/40 hover:shadow-orange-300/80
                @elseif($unit->status == 'available')
                    shadow-lg shadow-emerald-100/60 dark:shadow-emerald-500/25 hover:shadow-emerald-200/80 bg-[#f3fff6] dark:bg-[#0f1f17]
                @else
                    bg-white dark:bg-[#161615] hover:shadow-md
                @endif" @if($unit->status == 'reserved')
                style="background: linear-gradient(180deg, rgba(254, 249, 195, 0.8), rgba(254, 243, 199, 0.6));"
                @elseif($unit->status == 'available')
                style="background: linear-gradient(180deg, rgba(236, 253, 245, 0.95), rgba(209, 250, 229, 0.7));"
                @endif>
                @if($unit->primaryImage->first())
                <img src="{{ $unit->primaryImage->first()->image_url }}" alt="{{ $unit->code }}" class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gray-200 dark:bg-[#3E3E3A] flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                @endif

                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $unit->code }}</h3>
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $unit->location }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($isAdmin)
                            <input type="checkbox" name="ids[]" value="{{ $unit->id }}" class="unit-row-checkbox rounded text-blue-600 focus:ring-blue-500">
                            @endif
                            @if($unit->status == 'reserved')
                            <span class="px-2 py-1 text-xs rounded-full bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300 shadow-lg shadow-orange-200/60 dark:shadow-orange-500/40">{{ __('Reserved') }}</span>
                            @elseif($unit->status == 'sold')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">{{ __('Sold') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">{{ __('Available') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
                        <div>
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ __('Area') }}:</span>
                            <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] ml-1">{{ $unit->area }} m²</span>
                        </div>
                        <div>
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ __('Rooms') }}:</span>
                            <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] ml-1">{{ $unit->rooms }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ __('Price') }}:</span>
                            <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] ml-1">${{ number_format($unit->price, 0) }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between gap-2">
                            <a href="{{ route('units.show', $unit) }}" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-center transition-colors text-sm">
                                {{ __('View Details') }}
                            </a>
                            @if($unit->status == 'available')
                            <form action="{{ route('units.reserve', $unit) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                    {{ __('Reserve') }}
                                </button>
                            </form>
                            @endif
                        </div>
                        @unless($isSalesAgent)
                        <form action="{{ route('units.destroy', $unit) }}" method="POST" onsubmit="return confirm('{{ __('Delete this unit? This cannot be undone.') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                                {{ __('Delete Unit') }}
                            </button>
                        </form>
                        @endunless
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12 text-[#706f6c] dark:text-[#A1A09A]">
                {{ __('No units found') }}
            </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $units->links() }}
        </div>

        @if($isAdmin)
    </form>
    @endif
</div>

@if($isAdmin)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('unitSelectAll');
        const bulkButton = document.getElementById('unitBulkDeleteBtn');
        const form = document.getElementById('unitBulkDeleteForm');

        function getCheckboxes() {
            return Array.from(document.querySelectorAll('.unit-row-checkbox'));
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
                cb.removeEventListener('change', updateButtonState);
                cb.addEventListener('change', updateButtonState);
            });
        }

        // Use event delegation for dynamically added checkboxes
        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('unit-row-checkbox')) {
                updateButtonState();
            }
        });

        // Watch for DOM changes
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

        const tableContainer = document.querySelector('tbody') || document.querySelector('.grid');
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
                if (!confirm("{{ __('Delete selected units ? This action cannot be undone.') }}")) {
                    event.preventDefault();
                    return;
                }
                form.method = 'POST';
                const methodField = form.querySelector('input[name="_method"]');
                if (methodField) {
                    methodField.remove();
                }
            });
        }

        attachCheckboxListeners();
        updateButtonState();
    });

</script>
@endif
@endsection
