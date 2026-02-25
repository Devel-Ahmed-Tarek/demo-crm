@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Lead Sources') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                {{ __('Manage the list of sources that appears in the leads pipeline, filters, and reports.') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('lead-sources.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Add Source') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 rounded-xl bg-green-50 text-green-800 border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-xl bg-red-50 text-red-800 border border-red-200">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-[#222] text-xs uppercase tracking-wide text-[#706f6c] dark:text-[#A1A09A]">
                <tr>
                    <th class="px-6 py-3 text-left">{{ __('Key') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('Label (English)') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('Label (Arabic)') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('Sort Order') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A] text-sm">
                @forelse($sources as $source)
                <tr>
                    <td class="px-6 py-4 font-mono text-xs uppercase text-[#706f6c] dark:text-[#A1A09A]">{{ $source->key }}</td>
                    <td class="px-6 py-4 font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $source->label_en }}</td>
                    <td class="px-6 py-4 text-[#1b1b18] dark:text-[#EDEDEC]">{{ $source->label_ar ?? '—' }}</td>
                    <td class="px-6 py-4 text-[#706f6c] dark:text-[#A1A09A]">{{ $source->sort_order }}</td>
                    <td class="px-6 py-4">
                        @if($source->is_active)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200">{{ __('Active') }}</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-3">
                        <a href="{{ route('lead-sources.edit', $source) }}" class="text-blue-500 hover:text-blue-700 font-medium text-sm">
                            {{ __('Edit') }}
                        </a>
                        <form action="{{ route('lead-sources.destroy', $source) }}" method="POST" class="inline"
                            onsubmit="return confirm('{{ __('Delete this source? This action cannot be undone.') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-sm">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                        {{ __('No sources found') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection