@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="w-16 h-16 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-2xl mr-4">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $user->name }}</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $user->email }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @if($user->is_active)
            <span class="px-4 py-2 text-sm rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 font-medium">{{ __('Active') }}</span>
            @else
            <span class="px-4 py-2 text-sm rounded-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 font-medium">{{ __('Inactive') }}</span>
            @endif
            <span class="px-4 py-2 text-sm rounded-full bg-gray-100 dark:bg-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A] font-medium">
                {{ __(ucfirst(str_replace('_', ' ', $user->role))) }}
            </span>
            <!-- Export Button -->
            <button onclick="showExportModal()" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Export to Excel') }}
            </button>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Edit') }}
            </a>
            <a href="{{ route('users.index') }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Back') }}
            </a>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">{{ __('Total Leads') }}</div>
            <div class="text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $stats['total_leads'] }}</div>
            <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $stats['active_leads'] }} {{ __('active') }}</div>
        </div>
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">{{ __('Won Leads') }}</div>
            <div class="text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $stats['won_leads'] }}</div>
            <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Conversion') }}: {{ number_format($stats['conversion_rate'], 2) }}%</div>
        </div>
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">{{ __('Total Customers') }}</div>
            <div class="text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $stats['total_customers'] }}</div>
        </div>
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">{{ __('Appointments') }}</div>
            <div class="text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $stats['total_appointments'] }}</div>
        </div>
    </div>

    <!-- Recent Leads -->
    @if($user->assignedLeads->count() > 0)
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Recent Leads') }}</h2>
        <div class="space-y-2">
            @foreach($user->assignedLeads as $lead)
            <a href="{{ route('leads.show', $lead) }}" class="block p-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg hover:bg-gray-50 dark:hover:bg-[#3E3E3A] transition-colors cursor-pointer">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] hover:text-blue-500 dark:hover:text-blue-400 transition-colors">{{ $lead->name }}</span>
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A]">
                        {{ __(ucfirst($lead->stage)) }}
                    </span>
                </div>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $lead->created_at->format('M d, Y') }}</p>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Customers -->
    @if($user->assignedCustomers->count() > 0)
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Recent Customers') }}</h2>
        <div class="space-y-2">
            @foreach($user->assignedCustomers as $customer)
            <a href="{{ route('customers.show', $customer) }}" class="block p-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $customer->name }}</span>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $customer->email ?? $customer->phone }}</p>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Export to Excel') }}</h3>
        <form id="exportForm" action="{{ route('export.user', $user) }}" method="GET">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('Start Date') }}
                    </label>
                    <input type="date" name="start_date" id="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}" class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ __('End Date') }}
                    </label>
                    <input type="date" name="end_date" id="end_date" value="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="hideExportModal()" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                    {{ __('Export') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showExportModal() {
        document.getElementById('exportModal').classList.remove('hidden');
    }

    function hideExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('exportModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideExportModal();
        }
    });
</script>
@endsection