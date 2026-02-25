@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Edit Communication') }}</h1>

    <form method="POST" action="{{ route('customer-communications.update', $customerCommunication) }}" class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Customer') }}</label>
                <input type="text" value="{{ $customerCommunication->customer->name }}" disabled
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-gray-100 dark:bg-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Type') }} *</label>
                <select name="type" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="whatsapp" {{ old('type', $customerCommunication->type) == 'whatsapp' ? 'selected' : '' }}>{{ __('WhatsApp') }}</option>
                    <option value="email" {{ old('type', $customerCommunication->type) == 'email' ? 'selected' : '' }}>{{ __('Email') }}</option>
                    <option value="visit" {{ old('type', $customerCommunication->type) == 'visit' ? 'selected' : '' }}>{{ __('Visit') }}</option>
                    <option value="call" {{ old('type', $customerCommunication->type) == 'call' ? 'selected' : '' }}>{{ __('Call') }}</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Scheduled Date & Time') }}</label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', $customerCommunication->scheduled_at ? $customerCommunication->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('scheduled_at')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Completed Date & Time') }}</label>
                <input type="datetime-local" name="completed_at" value="{{ old('completed_at', $customerCommunication->completed_at ? $customerCommunication->completed_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('completed_at')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Notes') }}</label>
            <textarea name="notes" rows="4"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('notes', $customerCommunication->notes) }}</textarea>
            @error('notes')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('customer-communications.index') }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Update Communication') }}
            </button>
        </div>
    </form>
</div>
@endsection
