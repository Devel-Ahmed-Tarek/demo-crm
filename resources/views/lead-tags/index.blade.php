@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Lead Tags') }}</h1>
        <button onclick="openCreateModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
            {{ __('Add Tag') }}
        </button>
    </div>

    <!-- Tags Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tags as $tag)
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full" style="background-color: {{ $tag->color }}"></div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $tag->name }}</h3>
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $tag->leads()->count() }} {{ __('leads') }}</p>
                        </div>
                    </div>
                    <form action="{{ route('lead-tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this tag?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-[#706f6c] dark:text-[#A1A09A]">
                {{ __('No tags found. Create your first tag!') }}
            </div>
        @endforelse
    </div>
</div>

<!-- Create Modal -->
<div id="tagModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Add Tag') }}</h2>
            <button onclick="closeModal()" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('lead-tags.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Tag Name') }} *</label>
                <input type="text" name="name" required placeholder="{{ __('e.g., Hot Lead, VIP') }}"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Color') }} *</label>
                <input type="color" name="color" value="#3b82f6" required
                    class="w-full h-12 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a]">
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-50 dark:hover:bg-[#3E3E3A]">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('Create Tag') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('tagModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('tagModal').classList.add('hidden');
}

document.getElementById('tagModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection
