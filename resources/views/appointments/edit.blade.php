@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Edit Appointment') }}</h1>

    <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Customer') }}</label>
                <input type="text" value="{{ $appointment->customer->name }}" disabled
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-gray-100 dark:bg-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Unit') }}</label>
                <input type="text" value="{{ $appointment->unit ? $appointment->unit->code : __('N/A') }}" disabled
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-gray-100 dark:bg-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Appointment Date & Time') }} *</label>
                <input type="datetime-local" name="appointment_date" value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d\TH:i')) }}" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('appointment_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Price') }}</label>
                <input type="number" name="price" value="{{ old('price', $appointment->price) }}" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Status') }} *</label>
                <select name="status" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="scheduled" {{ old('status', $appointment->status) == 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                    <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                    <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Notes') }}</label>
            <textarea name="notes" rows="4"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">{{ old('notes', $appointment->notes) }}</textarea>
            @error('notes')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                {{ __('رسالة تذكير الواتساب') }}
            </label>
            <div class="flex flex-col sm:flex-row gap-3 text-xs sm:text-sm">
                @php
                    $hasCustomMessage = filled(old('whatsapp_reminder_message', $appointment->whatsapp_reminder_message));
                @endphp
                <label class="inline-flex items-center gap-2">
                    <input type="radio" name="use_custom_whatsapp_message" value="0"
                        onchange="toggleWhatsappMessageField(false)"
                        {{ $hasCustomMessage ? '' : 'checked' }}>
                    <span>{{ __('استخدم الرسالة الافتراضية') }}</span>
                </label>
                <label class="inline-flex.items-center gap-2">
                    <input type="radio" name="use_custom_whatsapp_message" value="1"
                        onchange="toggleWhatsappMessageField(true)"
                        {{ $hasCustomMessage ? 'checked' : '' }}>
                    <span>{{ __('استخدم رسالة خاصة لهذا الموعد') }}</span>
                </label>
            </div>
            <textarea name="whatsapp_reminder_message" id="whatsapp_reminder_message" rows="3"
                placeholder="{{ __('اكتب رسالة خاصة أو اتركها فارغة لاستخدام الافتراضية. المتغيرات: :name :date :time') }}"
                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] disabled:bg-gray-100 disabled:text-[#A1A09A]"
                {{ $hasCustomMessage ? '' : 'disabled' }}>{{ old('whatsapp_reminder_message', $appointment->whatsapp_reminder_message) }}</textarea>
            @error('whatsapp_reminder_message')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('appointments.index') }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Update Appointment') }}
            </button>
        </div>
    </form>
</div>
@endsection
