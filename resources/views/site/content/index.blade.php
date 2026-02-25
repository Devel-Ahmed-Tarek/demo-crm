@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Site Content') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Manage translations and site content files (en.json & ar.json)') }}</p>
        </div>
        <a href="{{ route('site.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('Back') }}
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Tabs -->
    <div class="bg-white dark:bg-[#161615] rounded-xl shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A]">
        <div class="border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
            <nav class="flex -mb-px">
                <a href="{{ route('site.content', ['locale' => 'en']) }}" id="tab-en" class="tab-button flex-1 px-6 py-4 text-sm font-medium text-center border-b-2 {{ ($locale ?? 'en') === 'en' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                    English (en.json)
                </a>
                <a href="{{ route('site.content', ['locale' => 'ar']) }}" id="tab-ar" class="tab-button flex-1 px-6 py-4 text-sm font-medium text-center border-b-2 {{ ($locale ?? 'en') === 'ar' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
                    Arabic (ar.json)
                </a>
            </nav>
        </div>

        <!-- English Content -->
        <div id="content-en" class="tab-content p-6 {{ ($locale ?? 'en') === 'en' ? '' : 'hidden' }}">
            <div class="mb-4">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                        {{ __('English Content (en.json)') }}
                    </label>
                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('site.content') }}" class="flex items-center gap-2">
                            <input type="hidden" name="locale" value="en">
                            <input type="text" name="search" value="{{ request('search', '') }}" placeholder="{{ __('Search by key or value...') }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] text-sm w-64">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                {{ __('Search') }}
                            </button>
                            @if(request('search'))
                            <a href="{{ route('site.content', ['locale' => 'en']) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                {{ __('Clear') }}
                            </a>
                            @endif
                        </form>
                        <button type="button" onclick="addNewRow('en')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add New') }}
                        </button>
                    </div>
                </div>
            </div>
            <form action="{{ route('site.content.update') }}" method="POST" id="form-en">
                @csrf
                <input type="hidden" name="locale" value="en">
                <div class="mb-4">
                    <div class="overflow-x-auto border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg shadow-sm">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-[#0a0a0a] dark:to-[#1a1a1a]">
                                <tr>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-[#1b1b18] dark:text-[#EDEDEC] uppercase tracking-wider border-b-2 border-[#e3e3e0] dark:border-[#3E3E3A] w-1/4">{{ __('Key') }}</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-[#1b1b18] dark:text-[#EDEDEC] uppercase tracking-wider border-b-2 border-[#e3e3e0] dark:border-[#3E3E3A]">{{ __('Value') }}</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-[#1b1b18] dark:text-[#EDEDEC] uppercase tracking-wider border-b-2 border-[#e3e3e0] dark:border-[#3E3E3A] w-24">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-en" class="bg-white dark:bg-[#161615] divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                                @if($locale === 'en')
                                @if(isset($itemsArray) && count($itemsArray) > 0)
                                @foreach($itemsArray as $index => $item)
                                <tr class="content-row hover:bg-gray-50 dark:hover:bg-[#1f1f1d] transition-colors duration-150">
                                    <td class="px-4 py-4 w-1/4">
                                        <input type="hidden" name="keys[]" value="{{ $item['key'] }}">
                                        <div class="px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-gray-50 dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] text-xs font-medium font-mono shadow-sm">
                                            {{ $item['key'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <textarea name="values[]" rows="5" class="value-input w-full px-4 py-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-y min-h-[120px]" required>{{ $item['value'] }}</textarea>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button type="button" onclick="removeRow(this)" class="inline-flex items-center justify-center p-2 text-red-500 hover:text-white hover:bg-red-500 dark:hover:bg-red-600 rounded-lg transition-all duration-200 hover:shadow-md" title="{{ __('Delete') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                                        @if(request('search'))
                                        {{ __('No results found for') }} "{{ request('search') }}"
                                        @else
                                        {{ __('No data found') }}
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <textarea name="content" id="hidden-content-en" class="hidden">{{ $enContent }}</textarea>
                </div>
                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        {{ __('Save English Content') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Arabic Content -->
        <div id="content-ar" class="tab-content p-6 {{ ($locale ?? 'en') === 'ar' ? '' : 'hidden' }}">
            <div class="mb-4">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                        {{ __('Arabic Content (ar.json)') }}
                    </label>
                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('site.content') }}" class="flex items-center gap-2">
                            <input type="hidden" name="locale" value="ar">
                            <input type="text" name="search" value="{{ request('search', '') }}" placeholder="{{ __('Search by key or value...') }}" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] text-sm w-64">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                {{ __('Search') }}
                            </button>
                            @if(request('search'))
                            <a href="{{ route('site.content', ['locale' => 'ar']) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                {{ __('Clear') }}
                            </a>
                            @endif
                        </form>
                        <button type="button" onclick="addNewRow('ar')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add New') }}
                        </button>
                    </div>
                </div>
            </div>
            <form action="{{ route('site.content.update') }}" method="POST" id="form-ar">
                @csrf
                <input type="hidden" name="locale" value="ar">
                <div class="mb-4">
                    <div class="overflow-x-auto border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg shadow-sm">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-[#0a0a0a] dark:to-[#1a1a1a]">
                                <tr>
                                    <th class="px-4 py-4 text-right text-xs font-semibold text-[#1b1b18] dark:text-[#EDEDEC] uppercase tracking-wider border-b-2 border-[#e3e3e0] dark:border-[#3E3E3A] w-1/4">{{ __('Key') }}</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-[#1b1b18] dark:text-[#EDEDEC] uppercase tracking-wider border-b-2 border-[#e3e3e0] dark:border-[#3E3E3A]">{{ __('Value') }}</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-[#1b1b18] dark:text-[#EDEDEC] uppercase tracking-wider border-b-2 border-[#e3e3e0] dark:border-[#3E3E3A] w-24">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-ar" class="bg-white dark:bg-[#161615] divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                                @if(($locale ?? 'en') === 'ar')
                                @if(isset($itemsArray) && count($itemsArray) > 0)
                                @foreach($itemsArray as $index => $item)
                                <tr class="content-row hover:bg-gray-50 dark:hover:bg-[#1f1f1d] transition-colors duration-150">
                                    <td class="px-4 py-4 w-1/4">
                                        <input type="hidden" name="keys[]" value="{{ $item['key'] }}">
                                        <div class="px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-gray-50 dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] text-xs font-medium font-mono shadow-sm text-right">
                                            {{ $item['key'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <textarea name="values[]" rows="5" class="value-input w-full px-4 py-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-y min-h-[120px] text-right" required>{{ $item['value'] }}</textarea>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button type="button" onclick="removeRow(this)" class="inline-flex items-center justify-center p-2 text-red-500 hover:text-white hover:bg-red-500 dark:hover:bg-red-600 rounded-lg transition-all duration-200 hover:shadow-md" title="{{ __('Delete') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300 dark:text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-[#706f6c] dark:text-[#A1A09A] text-base font-medium">
                                                @if(request('search'))
                                                {{ __('No results found for') }} "{{ request('search') }}"
                                                @else
                                                {{ __('No data found') }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <textarea name="content" id="hidden-content-ar" class="hidden">{{ $arContent }}</textarea>
                </div>
                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        {{ __('Save Arabic Content') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Tab switching is now handled by links, no need for JavaScript

    function addNewRow(locale) {
        const key = prompt('{{ __("Enter new key:") }}');
        if (!key || key.trim() === '') {
            return;
        }

        const tbody = document.getElementById(`table-body-${locale}`);
        const row = document.createElement('tr');
        row.className = 'content-row';
        row.innerHTML = `
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="hidden" name="keys[]" value="${key.trim()}">
                <div class="px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded bg-gray-50 dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-medium">${key.trim()}</div>
            </td>
            <td class="px-4 py-3">
                <textarea name="values[]" rows="1" class="value-input w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] text-sm" required></textarea>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-right">
                <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 dark:hover:text-red-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    }

    function removeRow(button) {
        if (confirm('{{ __("Are you sure you want to delete this row?") }}')) {
            button.closest('tr').remove();
        }
    }


    // Convert table data to JSON before submit
    document.getElementById('form-en').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const keys = Array.from(form.querySelectorAll('.key-input')).map(input => input.value.trim());
        const values = Array.from(form.querySelectorAll('.value-input')).map(input => input.value.trim());

        const json = {};
        keys.forEach((key, index) => {
            if (key && values[index] !== undefined) {
                json[key] = values[index];
            }
        });

        const hiddenContent = document.getElementById('hidden-content-en');
        hiddenContent.value = JSON.stringify(json, null, 4);

        // Create a new form data with JSON content
        const formData = new FormData();
        formData.append('_token', form.querySelector('[name="_token"]').value);
        formData.append('locale', 'en');
        formData.append('content', hiddenContent.value);

        // Submit via fetch
        fetch(form.action, {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                alert('{{ __("Error saving content.") }}');
            }
        });
    });

    document.getElementById('form-ar').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const keys = Array.from(form.querySelectorAll('.key-input')).map(input => input.value.trim());
        const values = Array.from(form.querySelectorAll('.value-input')).map(input => input.value.trim());

        const json = {};
        keys.forEach((key, index) => {
            if (key && values[index] !== undefined) {
                json[key] = values[index];
            }
        });

        const hiddenContent = document.getElementById('hidden-content-ar');
        hiddenContent.value = JSON.stringify(json, null, 4);

        // Create a new form data with JSON content
        const formData = new FormData();
        formData.append('_token', form.querySelector('[name="_token"]').value);
        formData.append('locale', 'ar');
        formData.append('content', hiddenContent.value);

        // Submit via fetch
        fetch(form.action, {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                alert('{{ __("Error saving content.") }}');
            }
        });
    });
</script>
@endsection