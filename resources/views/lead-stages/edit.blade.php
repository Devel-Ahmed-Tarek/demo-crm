@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Edit Stage') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                {{ __('Update the localized labels, colors, or order of this pipeline stage.') }}
            </p>
        </div>
        <a href="{{ route('lead-stages.index') }}" class="px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#1f1f1f]">
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

    <form method="POST" action="{{ route('lead-stages.update', $stage) }}" class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl shadow-sm p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Stage Key') }} *</label>
                <input type="text" name="key" value="{{ old('key', $stage->key) }}" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]" placeholder="e.g. nurturing">
                <p class="mt-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Use lowercase letters, numbers, or dashes.') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Sort Order') }} *</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $stage->sort_order) }}" min="0" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Stage Category') }} *</label>
                <select name="category" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
                    <option value="positive" {{ old('category', $stage->category) === 'positive' ? 'selected' : '' }}>{{ __('Positive Leads') }}</option>
                    <option value="negative" {{ old('category', $stage->category) === 'negative' ? 'selected' : '' }}>{{ __('Negative / Cold Leads') }}</option>
                </select>
                <p class="mt-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Choose how this stage is grouped for reporting and the pipeline UI.') }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_contract_stage" value="1" {{ old('is_contract_stage', $stage->is_contract_stage) ? 'checked' : '' }}
                        class="rounded text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Is Contract Stage') }}</span>
                </label>
                <p class="mt-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Mark this stage as a contract stage. Leads reaching this stage will automatically create a contract.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Label (English)') }} *</label>
                <input type="text" name="label_en" value="{{ old('label_en', $stage->label_en) }}" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Label (Arabic)') }} *</label>
                <input type="text" name="label_ar" value="{{ old('label_ar', $stage->label_ar) }}" dir="rtl" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Description (English)') }}</label>
                <textarea name="description_en" rows="3" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">{{ old('description_en', $stage->description_en) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" rows="3" dir="rtl" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">{{ old('description_ar', $stage->description_ar) }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Accent Gradient Classes') }}</label>
                <input type="text" name="accent" value="{{ old('accent', $stage->accent) }}" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]" placeholder="from-blue-500 to-blue-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Dot Color Classes') }}</label>
                <input type="text" name="dot" value="{{ old('dot', $stage->dot) }}" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]" placeholder="bg-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Border Color (rgba/hex)') }}</label>
                <input type="text" name="border" value="{{ old('border', $stage->border) }}" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]" placeholder="rgba(59, 130, 246, 0.2)">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Card Border Color') }}</label>
                <input type="text" name="card_border" value="{{ old('card_border', $stage->card_border) }}" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Shadow Color') }}</label>
                <input type="text" name="shadow" value="{{ old('shadow', $stage->shadow) }}" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Glow Color') }}</label>
                <input type="text" name="glow" value="{{ old('glow', $stage->glow) }}" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a]">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('lead-stages.index') }}" class="px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#1f1f1f]">
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="px-6 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium hover:-translate-y-0.5 transition">
                {{ __('Update Stage') }}
            </button>
        </div>
    </form>
</div>
@endsection