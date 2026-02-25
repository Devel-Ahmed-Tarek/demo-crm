@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('units.index') }}{{ $unit->project_id ? '?project_id=' . $unit->project_id : '' }}" class="inline-flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            {{ __('Back to Units') }}
        </a>
        @if($unit->project)
        <span class="mx-2 text-[#706f6c] dark:text-[#A1A09A]">•</span>
        <a href="{{ route('projects.units.index', $unit->project) }}" class="inline-flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
            {{ __('Booking Plan') }}
        </a>
        @endif
    </div>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $unit->code ?? 'N/A' }}</h1>
            @if($unit->location)
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $unit->location }}</p>
            @endif
            @if($unit->project)
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Project') }}: {{ $unit->project->title }}</p>
            @endif
        </div>
        <div class="flex items-center gap-4">
            @if($unit->status == 'reserved')
            <span class="px-4 py-2 text-sm rounded-full bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300 font-medium">{{ __('Reserved') }}</span>
            @elseif($unit->status == 'sold')
            <span class="px-4 py-2 text-sm rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 font-medium">{{ __('Sold') }}</span>
            @else
            <span class="px-4 py-2 text-sm rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 font-medium">{{ __('Available') }}</span>
            @endif
            <a href="{{ route('units.edit', $unit) }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Edit') }}
            </a>
            <form action="{{ route('units.destroy', $unit) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to permanently delete this unit? This action cannot be undone.') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white shadow-sm text-sm">
                    {{ __('Delete') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Apartments Booking Plan Grid -->
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Booking Plan') }} - {{ $unit->code }}</h2>
            <a href="{{ route('units.apartments.create', $unit) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                {{ __('Add Apartment') }}
            </a>
        </div>

        @if(isset($grid) && $grid)
        <div class="overflow-x-auto">
            <table class="w-full border-collapse" style="min-width: 600px;">
                <thead>
                    <tr>
                        <th class="border border-[#e3e3e0] dark:border-[#3E3E3A] px-3 py-2 text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC] bg-gray-50 dark:bg-[#2a2a28]"></th>
                        @for($col = 1; $col <= $maxColumns; $col++)
                            <th class="border border-[#e3e3e0] dark:border-[#3E3E3A] px-3 py-2 text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC] bg-gray-50 dark:bg-[#2a2a28] text-center">{{ $col }}</th>
                            @endfor
                    </tr>
                </thead>
                <tbody>
                    @for($floor = $maxFloors; $floor >= 0; $floor--)
                    <tr>
                        <td class="border border-[#e3e3e0] dark:border-[#3E3E3A] px-3 py-2 text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] bg-gray-50 dark:bg-[#2a2a28] whitespace-nowrap">
                            @if($floor == 0)
                            {{ __('Ground Floor') }}
                            @else
                            {{ __('Floor') }} {{ $floor }}
                            @endif
                        </td>
                        @for($col = 1; $col <= $maxColumns; $col++)
                            @php
                            $apartment=$grid[$floor][$col] ?? null;
                            @endphp
                            <td class="border border-[#e3e3e0] dark:border-[#3E3E3A] px-2 py-3 text-center align-middle min-w-[100px]
                                @if($apartment)
                                    @if($apartment->status == 'available') bg-white dark:bg-[#161615]
                                    @elseif($apartment->status == 'reserved') bg-orange-100 dark:bg-orange-900/30
                                    @elseif($apartment->status == 'contracted') bg-red-100 dark:bg-red-900/30
                                    @elseif($apartment->status == 'owner') bg-blue-100 dark:bg-blue-900/30
                                    @else bg-gray-50 dark:bg-[#2a2a28]
                                    @endif
                                    cursor-pointer hover:opacity-80 transition-opacity
                                @else
                                    bg-gray-100 dark:bg-[#2a2a28]
                                @endif
                            ">
                            @if($apartment)
                            @if($apartment->status == 'owner')
                            <div class="block cursor-not-allowed opacity-75" title="{{ __('Not for sale') }}">
                                <div class="text-xs font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $apartment->code }}</div>
                                @if($apartment->area)
                                <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">({{ number_format($apartment->area, 0) }}M)</div>
                                @endif
                                <div class="mt-1 text-xs font-medium text-blue-700 dark:text-blue-300">{{ __('Not for sale') }}</div>
                            </div>
                            @else
                            <a href="{{ route('units.apartments.show', ['unit' => $unit, 'apartment' => $apartment]) }}" class="block">
                                <div class="text-xs font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $apartment->code }}</div>
                                @if($apartment->area)
                                <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">({{ number_format($apartment->area, 0) }}M)</div>
                                @endif
                                @if($apartment->status == 'contracted')
                                <div class="mt-1 text-xs font-medium text-red-700 dark:text-red-300">{{ __('Contracted') }}</div>
                                @endif
                            </a>
                            @endif
                            @else
                            <button
                                type="button"
                                onclick="openAddApartmentModal({{ $floor }}, {{ $col }})"
                                class="w-full h-full min-h-[60px] flex items-center justify-center text-xs text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-200 dark:hover:bg-[#3E3E3A] transition-colors rounded cursor-pointer"
                                title="{{ __('Click to add apartment') }}">
                                <span>+</span>
                            </button>
                            @endif
                            </td>
                            @endfor
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex flex-wrap gap-4 text-xs">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A]"></div>
                <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ __('Available') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-orange-100 dark:bg-orange-900/30"></div>
                <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ __('Reserved') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-red-100 dark:bg-red-900/30"></div>
                <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ __('Contracted') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-blue-100 dark:bg-blue-900/30"></div>
                <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ __('Not for sale') }}</span>
            </div>
        </div>
        @else
        <div class="text-center py-12 text-[#706f6c] dark:text-[#A1A09A]">
            <p class="mb-4">{{ __('No apartments found.') }}</p>
            <a href="{{ route('units.apartments.create', $unit) }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                {{ __('Add First Apartment') }}
            </a>
        </div>
        @endif
    </div>

    <!-- Add Apartment Modal -->
    <div id="addApartmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center" style="display: none;">
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Add New Apartment') }}</h3>
                    <button onclick="closeAddApartmentModal()" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('units.apartments.store', $unit) }}" method="POST" class="space-y-4" id="apartmentForm">
                    @csrf

                    <input type="hidden" name="floor" id="modal_floor">
                    <input type="hidden" name="column" id="modal_column">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Floor') }}</label>
                            <input type="text" id="modal_floor_display" readonly
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-gray-100 dark:bg-[#2a2a28] text-[#1b1b18] dark:text-[#EDEDEC]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Column') }}</label>
                            <input type="text" id="modal_column_display" readonly
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-gray-100 dark:bg-[#2a2a28] text-[#1b1b18] dark:text-[#EDEDEC]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Apartment Code') }} *</label>
                            <input type="text" name="code" required placeholder="e.g., A-001"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Status') }} *</label>
                            <select name="status" required
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                                <option value="available">{{ __('Available') }}</option>
                                <option value="reserved">{{ __('Reserved') }}</option>
                                <option value="contracted">{{ __('Contracted') }}</option>
                                <option value="owner">{{ __('Not for sale') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Area') }} (m²)</label>
                            <input type="number" name="area" min="0" step="0.01" placeholder="e.g., 80"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Rooms') }}</label>
                            <input type="number" name="rooms" min="0" placeholder="e.g., 3"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Price') }}</label>
                            <input type="number" name="price" min="0" step="0.01" placeholder="e.g., 150000"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Description') }}</label>
                        <textarea name="description" rows="3" placeholder="{{ __('Enter apartment description...') }}"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <button type="button" onclick="closeAddApartmentModal()" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                            {{ __('Create Apartment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddApartmentModal(floor, column) {
            const modal = document.getElementById('addApartmentModal');
            const floorInput = document.getElementById('modal_floor');
            const columnInput = document.getElementById('modal_column');
            const floorDisplay = document.getElementById('modal_floor_display');
            const columnDisplay = document.getElementById('modal_column_display');

            if (modal && floorInput && columnInput && floorDisplay && columnDisplay) {
                floorInput.value = floor;
                columnInput.value = column;
                floorDisplay.value = floor == 0 ? '{{ __("Ground Floor") }}' : '{{ __("Floor") }} ' + floor;
                columnDisplay.value = column;
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
            } else {
                console.error('Modal elements not found');
            }
        }

        function closeAddApartmentModal() {
            const modal = document.getElementById('addApartmentModal');
            const form = document.getElementById('apartmentForm');

            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }

            if (form) {
                form.reset();
            }
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('addApartmentModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeAddApartmentModal();
                    }
                });
            }
        });
    </script>

    <!-- Gallery -->
    @if($unit->images->count() > 0)
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
        <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Gallery') }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($unit->images as $image)
            <img src="{{ $image->image_url }}" alt="{{ $unit->code }}" class="w-full h-32 object-cover rounded-lg">
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Unit Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Unit Details') }}</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Location') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $unit->location }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Area') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $unit->area }} m²</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Rooms') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $unit->rooms }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Price') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">${{ number_format($unit->price, 0) }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Status') }}:</span>
                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ __(ucfirst($unit->status)) }}</p>
                    </div>
                </div>

                @if($unit->features->count() > 0)
                <div class="mt-4">
                    <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Features') }}:</span>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($unit->features as $feature)
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">{{ $feature->name }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($unit->description)
                <div class="mt-4">
                    <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Description') }}:</span>
                    <p class="text-[#1b1b18] dark:text-[#EDEDEC] mt-1">{{ $unit->description }}</p>
                </div>
                @endif
            </div>

            <!-- Activity Log -->
            @if($unit->activityLogs->count() > 0)
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Activity Log') }}</h2>
                <div class="space-y-4">
                    @foreach($unit->activityLogs as $log)
                    <div class="border-l-2 border-blue-500 pl-4 py-2">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $log->action }}</span>
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $log->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        @if($log->description)
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $log->description }}</p>
                        @endif
                        @if($log->user)
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('By:') }} {{ $log->user->name }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-6">
            @if($unit->status == 'available')
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Reserve Unit') }}</h3>
                <form action="{{ route('units.reserve', $unit) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Customer') }}</label>
                        <select name="customer_id" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                            <option value="">{{ __('Select Customer') }}</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Customer Name (Optional)') }}</label>
                        <input type="text" name="customer_name" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Reserved Date') }}</label>
                        <input type="date" name="reserved_date" value="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors">
                        {{ __('Reserve') }}
                    </button>
                </form>
            </div>
            @endif

            @if($unit->status == 'reserved')
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Sell Unit') }}</h3>
                <form action="{{ route('units.sell', $unit) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Customer') }} *</label>
                        <select name="customer_id" required class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                            <option value="">{{ __('Select Customer') }}</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $unit->reserved_by == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Sold Date') }}</label>
                        <input type="date" name="sold_date" value="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    </div>
                    @if(auth()->user()->isSalesSupervisor() || auth()->user()->isAdmin())
                    <div>
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Sales Comment') }}</label>
                        <textarea name="sales_comment" rows="3" placeholder="{{ __('Add comment about this sale...') }}" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('sales_comment', $unit->sales_comment) }}</textarea>
                    </div>
                    @endif
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                        {{ __('Mark as Sold') }}
                    </button>
                </form>
            </div>
            @endif

            @if($unit->reservedBy)
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Reserved By') }}</h3>
                <p class="text-[#1b1b18] dark:text-[#EDEDEC]">{{ $unit->reservedBy->name }}</p>
                @if($unit->reserved_at)
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('On:') }} {{ $unit->reserved_at->format('M d, Y') }}</p>
                @endif
            </div>
            @endif

            @if($unit->status == 'sold' && $unit->soldTo)
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Sold To') }}</h3>
                <p class="text-[#1b1b18] dark:text-[#EDEDEC]">{{ $unit->soldTo->name }}</p>
                @if($unit->sold_at)
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('On:') }} {{ $unit->sold_at->format('M d, Y') }}</p>
                @endif
                @if($unit->sales_comment)
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Sales Comment') }}</h4>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] bg-gray-50 dark:bg-[#3E3E3A] p-3 rounded-lg">{{ $unit->sales_comment }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection