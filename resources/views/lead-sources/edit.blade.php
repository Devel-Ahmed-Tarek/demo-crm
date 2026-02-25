@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Edit Source') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                {{ __('Update the label, status, or order for this source.') }}
            </p>
        </div>
        <a href="{{ route('lead-sources.index') }}" class="px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#1f1f1f]">
            {{ __('Back') }}
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 rounded-xl bg-red-50 text-red-800 border border-red-200">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('lead-sources.update', $source) }}" class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl shadow-sm p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Source Key') }} *</label>
                <input type="text" name="key" value="{{ old('key', $source->key) }}" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
                <p class="mt-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Use lowercase letters, numbers, or dashes.') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Sort Order') }} *</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $source->sort_order) }}" min="0"
                    class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Status') }}</label>
                <select name="is_active" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
                    <option value="1" {{ old('is_active', $source->is_active) ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="0" {{ old('is_active', $source->is_active) ? '' : 'selected' }}>{{ __('Inactive') }}</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Label (English)') }} *</label>
                <input type="text" name="label_en" value="{{ old('label_en', $source->label_en) }}" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Label (Arabic)') }}</label>
                <input type="text" name="label_ar" value="{{ old('label_ar', $source->label_ar) }}" dir="rtl" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Description (English)') }}</label>
                <textarea name="description_en" rows="3" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">{{ old('description_en', $source->description_en) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" rows="3" dir="rtl" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">{{ old('description_ar', $source->description_ar) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('lead-sources.index') }}" class="px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#1f1f1f]">
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="px-6 py-2 rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 text-white font-medium hover:-translate-y-0.5 transition">
                {{ __('Update Source') }}
            </button>
        </div>
    </form>
</div>
@endsection

