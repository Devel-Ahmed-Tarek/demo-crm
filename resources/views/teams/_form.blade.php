@php
$editing = isset($team);
$selectedLeaderIds = old('leaders', $selectedLeaderIds ?? []);
$selectedMemberIds = old('members', $selectedMemberIds ?? []);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Team Name') }} *</label>
        <input type="text" name="name" value="{{ old('name', $team->name ?? '') }}" required
            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
        @error('name')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Team Color') }}</label>
        <input type="color" name="color" value="{{ old('color', $team->color ?? '#6366f1') }}"
            class="w-full h-11 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
        @error('color')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Description') }}</label>
    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('description', $team->description ?? '') }}</textarea>
    @error('description')
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Leaders') }}</label>
        <select name="leaders[]" multiple size="6" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            @foreach($leaders as $leader)
            <option value="{{ $leader->id }}" {{ in_array($leader->id, $selectedLeaderIds) ? 'selected' : '' }}>
                {{ $leader->name }} ({{ __(ucfirst(str_replace('_', ' ', $leader->role))) }})
            </option>
            @endforeach
        </select>
        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Leaders can access every lead belonging to the team.') }}</p>
        @error('leaders.*')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Members') }}</label>
        <select name="members[]" multiple size="6" class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            @foreach($members as $member)
            <option value="{{ $member->id }}" {{ in_array($member->id, $selectedMemberIds) ? 'selected' : '' }}>
                {{ $member->name }} ({{ __(ucfirst(str_replace('_', ' ', $member->role))) }})
            </option>
            @endforeach
        </select>
        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Members inherit access to the team pipeline automatically.') }}</p>
        @error('members.*')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex items-center justify-between">
    <label class="inline-flex items-center gap-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $team->is_active ?? true) ? 'checked' : '' }} class="rounded text-indigo-500 focus:ring-indigo-500">
        <span>{{ __('Active team') }}</span>
    </label>

    <div class="flex items-center gap-3">
        <a href="{{ $cancelUrl ?? route('teams.index') }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
            {{ __('Cancel') }}
        </a>
        <button type="submit" class="btn-primary px-6 py-2">
            {{ $submitLabel ?? __('Save Team') }}
        </button>
    </div>
</div>