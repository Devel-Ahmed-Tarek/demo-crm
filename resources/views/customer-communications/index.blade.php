@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Customer Communications') }}</h1>
        <button onclick="openCreateModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
            {{ __('Add Communication') }}
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-4">
        <form method="GET" action="{{ route('customer-communications.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <select name="customer_id" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Customers') }}</option>
                @foreach($customers as $customer)
                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                @endforeach
            </select>

            <select name="type" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <option value="">{{ __('All Types') }}</option>
                <option value="whatsapp" {{ request('type') == 'whatsapp' ? 'selected' : '' }}>{{ __('WhatsApp') }}</option>
                <option value="email" {{ request('type') == 'email' ? 'selected' : '' }}>{{ __('Email') }}</option>
                <option value="visit" {{ request('type') == 'visit' ? 'selected' : '' }}>{{ __('Visit') }}</option>
                <option value="call" {{ request('type') == 'call' ? 'selected' : '' }}>{{ __('Call') }}</option>
            </select>

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    <!-- Communications Table -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
        <div class="table-container">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 dark:bg-[#3E3E3A]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Customer') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Notes') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Scheduled') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Completed') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Created By') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                    @forelse($communications as $communication)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($communication->customer)
                            <a href="{{ route('customers.show', $communication->customer) }}" class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] hover:text-blue-500">
                                {{ $communication->customer->name }}
                            </a>
                            @else
                            <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('N/A') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">
                                {{ __(ucfirst($communication->type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ Str::limit($communication->notes, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $communication->scheduled_at ? $communication->scheduled_at->format('M d, Y H:i') : __('N/A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $communication->completed_at ? $communication->completed_at->format('M d, Y H:i') : __('N/A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $communication->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="editCommunication({{ $communication->id }})" class="text-blue-500 hover:text-blue-700 mr-3">{{ __('Edit') }}</button>
                            <form action="{{ route('customer-communications.destroy', $communication) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('No communications found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
            {{ $communications->links() }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="communicationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Add Communication') }}</h2>
            <button onclick="closeModal()" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('customer-communications.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Customer') }} *</label>
                <select name="customer_id" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="">{{ __('Select Customer') }}</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Type') }} *</label>
                <select name="type" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="whatsapp">{{ __('WhatsApp') }}</option>
                    <option value="email">{{ __('Email') }}</option>
                    <option value="visit">{{ __('Visit') }}</option>
                    <option value="call">{{ __('Call') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Scheduled Date & Time') }}</label>
                <input type="datetime-local" name="scheduled_at"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Completed Date & Time') }}</label>
                <input type="datetime-local" name="completed_at"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Notes') }}</label>
                <textarea name="notes" rows="4"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]"></textarea>
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

<script>
    function openCreateModal() {
        document.getElementById('communicationModal').classList.remove('hidden');
    }

    function editCommunication(id) {
        window.location.href = '/customer-communications/' + id + '/edit';
    }

    function closeModal() {
        document.getElementById('communicationModal').classList.add('hidden');
    }

    document.getElementById('communicationModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection