@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Users Management') }}</h1>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
            {{ __('Add User') }}
        </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-4">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by name or email...') }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">

            <select name="role" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Roles') }}</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                <option value="sales_supervisor" {{ request('role') == 'sales_supervisor' ? 'selected' : '' }}>{{ __('Sales Supervisor') }}</option>
                <option value="sales_agent" {{ request('role') == 'sales_agent' ? 'selected' : '' }}>{{ __('Sales Agent') }}</option>
                <option value="units_manager" {{ request('role') == 'units_manager' ? 'selected' : '' }}>{{ __('Units Manager') }}</option>
            </select>

            <select name="status" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Status') }}</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    @php
    $roleMeta = [
    'admin' => [
    'label' => __('Admin'),
    'class' => 'role-chip role-chip--admin',
    ],
    'sales_supervisor' => [
    'label' => __('Sales Supervisor'),
    'class' => 'role-chip role-chip--supervisor',
    ],
    'sales_agent' => [
    'label' => __('Sales Agent'),
    'class' => 'role-chip role-chip--agent',
    ],
    'units_manager' => [
    'label' => __('Units Manager'),
    'class' => 'role-chip role-chip--units',
    ],
    ];
    @endphp

    <!-- Users Table -->
    @if(auth()->user()->isAdmin())
    <form id="userBulkDeleteForm" method="POST" action="{{ route('users.bulk-delete') }}">
        @csrf
        @endif
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
            @if(auth()->user()->isAdmin())
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between px-6 py-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                <label class="inline-flex items-center gap-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                    <input type="checkbox" id="userSelectAll" class="rounded text-blue-600 focus:ring-blue-500">
                    <span>{{ __('Select all') }}</span>
                </label>
                <button type="submit" id="userBulkDeleteBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    {{ __('Delete selected') }}
                </button>
            </div>
            @endif
            <div class="table-container">
                <table class="w-full min-w-max">
                    <thead class="bg-gray-50 dark:bg-[#3E3E3A]">
                        <tr>
                            @if(auth()->user()->isAdmin())
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider w-12">{{ __('Select') }}</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Email') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Role') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Leads') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Customers') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                            @if(auth()->user()->isAdmin())
                            <td class="px-6 py-4">
                                <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="user-row-checkbox rounded text-blue-600 focus:ring-blue-500">
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('users.show', $user) }}" class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold mr-3">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] hover:text-blue-500">
                                        {{ $user->name }}
                                    </span>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                $roleKey = $user->role;
                                $roleBadge = $roleMeta[$roleKey] ?? [
                                'label' => __(ucfirst(str_replace('_', ' ', $roleKey))),
                                'class' => 'role-chip',
                                ];
                                @endphp
                                <span class="{{ $roleBadge['class'] }}">
                                    <span class="role-chip-dot"></span>
                                    {{ $roleBadge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">{{ __('Active') }}</span>
                                @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                <div class="flex flex-col">
                                    <span>{{ $user->total_leads ?? 0 }} total</span>
                                    <span class="text-xs">{{ $user->active_leads ?? 0 }} active</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                {{ $user->total_customers ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('users.show', $user) }}" class="text-blue-500 hover:text-blue-700" title="{{ __('View') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('users.edit', $user) }}" class="text-green-500 hover:text-green-700" title="{{ __('Edit') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-500 hover:text-yellow-700" title="{{ $user->is_active ? __('Deactivate') : __('Activate') }}">
                                            @if($user->is_active)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                            @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            @endif
                                        </button>
                                    </form>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="{{ __('Delete') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? '8' : '7' }}" class="px-6 py-4 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No users found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                {{ $users->links() }}
            </div>
        </div>
        @if(auth()->user()->isAdmin())
    </form>
    @endif
</div>

@if(auth()->user()->isAdmin())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('userSelectAll');
        const bulkButton = document.getElementById('userBulkDeleteBtn');
        const form = document.getElementById('userBulkDeleteForm');

        function getCheckboxes() {
            return Array.from(document.querySelectorAll('.user-row-checkbox'));
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
            if (event.target.classList.contains('user-row-checkbox')) {
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
                if (!confirm("{{ __('Delete selected users ? This action cannot be undone.') }}")) {
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
