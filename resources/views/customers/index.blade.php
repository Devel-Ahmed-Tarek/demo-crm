@extends('layouts.app')

@section('content')
@php
$isAdmin = auth()->user()?->isAdmin();
@endphp
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Customers') }}</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('export.customers', request()->query()) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Export Excel') }}
            </a>
            <a href="{{ route('customers.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Add Customer') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-4">
        <form method="GET" action="{{ route('customers.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search') }}..." class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">

            <select name="assigned_to" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Users') }}</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    <!-- Customers Table -->
    @if($isAdmin)
    <form id="customerBulkDeleteForm" method="POST" action="{{ route('customers.bulk-delete') }}">
        @csrf
        @endif
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
            @if($isAdmin)
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between px-6 py-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                <label class="inline-flex items-center gap-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                    <input type="checkbox" id="customerSelectAll" class="rounded text-blue-600 focus:ring-blue-500">
                    <span>{{ __('Select all') }}</span>
                </label>
                <button type="submit" id="customerBulkDeleteBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    {{ __('Delete selected') }}
                </button>
            </div>
            @endif
            <div class="table-container">
                <table class="w-full min-w-max">
                    <thead class="bg-gray-50 dark:bg-[#3E3E3A]">
                        <tr>
                            @if($isAdmin)
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider w-12">{{ __('Select') }}</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Contact') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Address') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Assigned To') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                        @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                            @if($isAdmin)
                            <td class="px-6 py-4">
                                <input type="checkbox" name="ids[]" value="{{ $customer->id }}" class="customer-row-checkbox rounded text-blue-600 focus:ring-blue-500">
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('customers.show', $customer) }}" class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] hover:text-blue-500">
                                    {{ $customer->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                <div>{{ $customer->email }}</div>
                                <div>{{ $customer->phone }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                {{ $customer->address }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                {{ $customer->assignedUser?->name ?? __('Unassigned') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('customers.edit', $customer) }}" class="text-blue-500 hover:text-blue-700 mr-3">{{ __('Edit') }}</a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? '6' : '5' }}" class="px-6 py-4 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No customers found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                {{ $customers->links() }}
            </div>
        </div>
        @if($isAdmin)
    </form>
    @endif
</div>

@if($isAdmin)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('customerSelectAll');
        const bulkButton = document.getElementById('customerBulkDeleteBtn');
        const form = document.getElementById('customerBulkDeleteForm');

        function getCheckboxes() {
            return Array.from(document.querySelectorAll('.customer-row-checkbox'));
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
            if (event.target.classList.contains('customer-row-checkbox')) {
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

        const tableContainer = document.querySelector('tbody');
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
                if (!confirm("{{ __('Delete selected customers ? This action cannot be undone.') }}")) {
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
