@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Sites') }}</h1>
        <a href="{{ route('sites.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
            {{ __('Add Site') }}
        </a>
    </div>

    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-[#3E3E3A]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Projects') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Description') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                    @forelse($sites as $site)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $site->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">
                                    {{ $site->projects->count() }} {{ __('Projects') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                {{ Str::limit($site->description, 100) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('sites.show', $site) }}" class="text-blue-500 hover:text-blue-700">{{ __('View') }}</a>
                                <a href="{{ route('sites.edit', $site) }}" class="text-green-500 hover:text-green-700">{{ __('Edit') }}</a>
                                <form action="{{ route('sites.destroy', $site) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this site?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-[#706f6c] dark:text-[#A1A09A]">
                            {{ __('No sites found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
            {{ $sites->links() }}
        </div>
    </div>
</div>
@endsection

