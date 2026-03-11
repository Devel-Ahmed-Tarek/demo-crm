@extends('layouts.app')

@section('content')
@php
$currentUser = auth()->user();
$isSalesAgent = $currentUser?->isSalesAgent();
$isAdmin = $currentUser?->isAdmin();
@endphp
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Appointments') }}</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('export.appointments', request()->query()) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Export Excel') }}
            </a>
            <button onclick="openCreateModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Add Appointment') }}
            </button>
        </div>
    </div>

    @unless($isSalesAgent)
    <!-- Filters -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-4">
        <form method="GET" action="{{ route('appointments.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <select name="status" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Status') }}</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
            </select>

            <select name="customer_id" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Customers') }}</option>
                @foreach($customers as $customer)
                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Filter') }}
            </button>
        </form>
    </div>
    @endunless

    @if($isAdmin)
    <form id="appointmentBulkDeleteForm" method="POST" action="{{ route('appointments.bulk-delete') }}">
        @csrf
    </form>
    @endif
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
        @if($isAdmin)
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between px-6 py-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
            <label class="inline-flex items-center gap-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                <input type="checkbox" id="appointmentSelectAll" class="rounded text-blue-600 focus:ring-blue-500">
                <span>{{ __('Select all') }}</span>
            </label>
            <button type="submit" form="appointmentBulkDeleteForm" id="appointmentBulkDeleteBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Customer') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Unit') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Date & Time') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Price') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Created By') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                    @forelse($appointments as $appointment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                        @if($isAdmin)
                        <td class="px-6 py-4">
                            <input type="checkbox" name="ids[]" value="{{ $appointment->id }}" form="appointmentBulkDeleteForm" class="appointment-row-checkbox rounded text-blue-600 focus:ring-blue-500">
                        </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($appointment->customer)
                            <a href="{{ route('customers.show', $appointment->customer) }}" class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] hover:text-blue-500">
                                {{ $appointment->customer->name }}
                            </a>
                            @else
                            <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('N/A') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            @if($appointment->unit)
                            <a href="{{ route('units.show', $appointment->unit) }}" class="hover:text-blue-500">
                                {{ $appointment->unit->code }}
                            </a>
                            @else
                            N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $appointment->appointment_date->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            @if($appointment->price)
                            ${{ number_format($appointment->price, 0) }}
                            @else
                            N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($appointment->status == 'scheduled')
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">{{ __('Scheduled') }}</span>
                            @elseif($appointment->status == 'completed')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">{{ __('Completed') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">{{ __('Cancelled') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $appointment->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="editAppointment({{ $appointment->id }})" class="text-blue-500 hover:text-blue-700 mr-3">{{ __('Edit') }}</button>
                            <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? '8' : '7' }}" class="px-6 py-4 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No appointments found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
            {{ $appointments->links() }}
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="appointmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-[#161615] rounded-xl shadow-lg p-4 sm:p-6 w-full max-w-lg max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 id="modalTitle" class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Add Appointment') }}</h2>
            <button onclick="closeModal()" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="appointmentForm" method="POST" action="{{ route('appointments.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" id="appointment_id" name="appointment_id" value="">
            <input type="hidden" id="form_method" name="_method" value="POST">

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Customer') }} *</label>
                <select name="customer_id" id="customer_id" required class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="">{{ __('Select Customer') }}</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Unit (Optional)') }}</label>
                <select name="unit_id" id="unit_id" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="">{{ __('Select Unit') }}</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->code }} - {{ $unit->location }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Appointment Date & Time') }} *</label>
                <input type="datetime-local" name="appointment_date" id="appointment_date" required class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Price') }}</label>
                <input type="number" name="price" id="price" step="0.01" min="0" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Status') }}</label>
                <select name="status" id="status" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="scheduled">{{ __('Scheduled') }}</option>
                    <option value="completed">{{ __('Completed') }}</option>
                    <option value="cancelled">{{ __('Cancelled') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Notes') }}</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]"></textarea>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                    {{ __('رسالة تذكير الواتساب') }}
                </label>
                <div class="flex flex-col sm:flex-row gap-3 text-xs sm:text-sm">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="use_custom_whatsapp_message" value="0" checked
                            onchange="toggleWhatsappMessageField(false)">
                        <span>{{ __('استخدم الرسالة الافتراضية') }}</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="use_custom_whatsapp_message" value="1"
                            onchange="toggleWhatsappMessageField(true)">
                        <span>{{ __('استخدم رسالة خاصة لهذا الموعد') }}</span>
                    </label>
                </div>
                <textarea name="whatsapp_reminder_message" id="whatsapp_reminder_message" rows="3"
                    placeholder="{{ __('اكتب رسالة خاصة أو اتركها فارغة لاستخدام الافتراضية. المتغيرات: :name :date :time') }}"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] disabled:bg-gray-100 disabled:text-[#A1A09A]"
                    disabled></textarea>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@if($isAdmin)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('appointmentSelectAll');
        const bulkButton = document.getElementById('appointmentBulkDeleteBtn');
        const form = document.getElementById('appointmentBulkDeleteForm');

        function getCheckboxes() {
            return Array.from(document.querySelectorAll('.appointment-row-checkbox'));
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
            if (event.target.classList.contains('appointment-row-checkbox')) {
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
                if (!confirm("{{ __('Delete selected appointments ? This action cannot be undone.') }}")) {
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

<script>
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = '{{ __('Add Appointment') }}';

        const route = '{{ route("appointments.store") }}';
        document.getElementById('appointmentForm').action = route;
        document.getElementById('form_method').value = 'POST';
        document.getElementById('appointmentForm').reset();
        document.getElementById('appointment_id').value = '';
        document.getElementById('appointmentModal').classList.remove('hidden');
    }

    function editAppointment(id) {
        window.location.href = '/appointments/' + id + '/edit';
    }

    function closeModal() {
        document.getElementById('appointmentModal').classList.add('hidden');
    }

    function toggleWhatsappMessageField(useCustom) {
        const field = document.getElementById('whatsapp_reminder_message');
        if (!field) return;

        if (useCustom) {
            field.disabled = false;
            field.focus();
        } else {
            field.value = '';
            field.disabled = true;
        }
    }

    // Close modal on outside click
    document.getElementById('appointmentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

</script>
@endsection
