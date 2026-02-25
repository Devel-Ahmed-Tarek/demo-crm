@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Edit Unit:') }} {{ $unit->code }}</h1>

    <form method="POST" action="{{ route('units.update', $unit) }}" enctype="multipart/form-data" class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Unit Code') }} *</label>
                <input type="text" name="code" value="{{ old('code', $unit->code) }}" required placeholder="e.g., UNIT-001"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Location') }} *</label>
                <input type="text" name="location" value="{{ old('location', $unit->location) }}" required placeholder="e.g., Downtown, Building A"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('location')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Area') }} (m²) *</label>
                <input type="number" name="area" value="{{ old('area', $unit->area) }}" required min="0" step="0.01" placeholder="e.g., 120.5"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('area')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Number of Rooms') }} *</label>
                <input type="number" name="rooms" value="{{ old('rooms', $unit->rooms) }}" required min="0" placeholder="e.g., 3"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('rooms')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Price') }} *</label>
                <input type="number" name="price" value="{{ old('price', $unit->price) }}" required min="0" step="0.01" placeholder="e.g., 150000"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('price')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Status') }} *</label>
                <select name="status" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="available" {{ old('status', $unit->status) == 'available' ? 'selected' : '' }}>{{ __('Available') }}</option>
                    <option value="reserved" {{ old('status', $unit->status) == 'reserved' ? 'selected' : '' }}>{{ __('Reserved') }}</option>
                    <option value="sold" {{ old('status', $unit->status) == 'sold' ? 'selected' : '' }}>{{ __('Sold') }}</option>
                    <option value="contracted" {{ old('status', $unit->status) == 'contracted' ? 'selected' : '' }}>{{ __('Contracted') }}</option>
                    <option value="owner" {{ old('status', $unit->status) == 'owner' ? 'selected' : '' }}>{{ __('Owner') }}</option>
                </select>
                @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Project') }}</label>
                <select name="project_id"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="">{{ __('No Project') }}</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ old('project_id', $unit->project_id) == $project->id ? 'selected' : '' }}>
                        {{ $project->site->name ?? '' }} - {{ $project->title }}
                    </option>
                    @endforeach
                </select>
                @error('project_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Floor') }}</label>
                <input type="number" name="floor" value="{{ old('floor', $unit->floor) }}" min="0" placeholder="{{ __('e.g., 0 for ground floor') }}"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('floor')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Required for Booking Plan Grid (0 = Ground Floor)') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Column') }}</label>
                <input type="number" name="column" value="{{ old('column', $unit->column) }}" min="1" placeholder="{{ __('e.g., 1, 2, 3...') }}"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('column')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Unit position in the grid (required for Booking Plan)') }}</p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Features') }}</label>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                @foreach($features as $feature)
                <label class="flex items-center">
                    <input type="checkbox" name="features[]" value="{{ $feature->id }}"
                        {{ (is_array(old('features')) && in_array($feature->id, old('features'))) || $unit->features->contains($feature->id) ? 'checked' : '' }}
                        class="rounded border-[#e3e3e0] dark:border-[#3E3E3A] text-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ $feature->name }}</span>
                </label>
                @endforeach
            </div>
            @error('features.*')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        @if($unit->images->count() > 0)
        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Current Images') }}</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($unit->images as $image)
                <div class="relative">
                    <img src="{{ $image->image_url }}" alt="{{ $unit->code }}" class="w-full h-32 object-cover rounded-lg">
                    @if($image->is_primary)
                    <span class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">{{ __('Primary') }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Add More Images') }}</label>
            <input type="file" name="images[]" multiple accept="image/*"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('You can select multiple images to add to existing ones. Maximum file size: 100MB per image.') }}</p>
            @error('images.*')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Description') }}</label>
            <textarea name="description" rows="4" placeholder="{{ __('Enter unit description...') }}"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('description', $unit->description) }}</textarea>
            @error('description')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('units.show', $unit) }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Update Unit') }}
            </button>
        </div>
    </form>
</div>
@endsection