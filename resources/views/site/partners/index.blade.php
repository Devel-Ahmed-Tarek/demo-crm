@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Our Partners') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ __('Manage partners displayed on the landing page slider') }}</p>
        </div>
        <button onclick="openPartnerModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('Add Partner') }}
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <!-- Partners Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($partners as $partner)
        <div class="bg-white dark:bg-[#161615] rounded-xl shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    @if($partner->logo_url)
                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center overflow-hidden">
                        <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" class="w-full h-full object-contain">
                    </div>
                    @else
                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    @endif
                    <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] text-center mb-2">{{ $partner->name }}</h3>
                    @if($partner->description)
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] text-center mb-2">{{ Str::limit($partner->description, 50) }}</p>
                    @endif
                    <div class="flex items-center justify-center gap-4 text-xs text-[#706f6c] dark:text-[#A1A09A] mb-4">
                        <span>{{ __('Order') }}: {{ $partner->order }}</span>
                        <span class="px-2 py-1 rounded {{ $partner->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                            {{ $partner->is_active ? __('Active') : __('Inactive') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="editPartner({{ $partner->id }}, '{{ $partner->name }}', '{{ $partner->description ?? '' }}', '{{ $partner->website_url ?? '' }}', {{ $partner->order }}, {{ $partner->is_active ? 'true' : 'false' }}, '{{ $partner->logo_url ?? '' }}')" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                    {{ __('Edit') }}
                </button>
                <form action="{{ route('site.partners.destroy', $partner) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('No Partners Yet') }}</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('Get started by adding your first partner.') }}</p>
            <button onclick="openPartnerModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                {{ __('Add Partner') }}
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Partner Modal -->
<div id="partnerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#161615] rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-between">
            <h2 id="modalTitle" class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Add Partner') }}</h2>
            <button onclick="closePartnerModal()" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="partnerForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="partnerId" name="partner_id">
            <input type="hidden" id="formMethod" name="_method" value="POST">

            <div>
                <label for="name" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Name') }} *</label>
                <input type="text" id="name" name="name" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            </div>

            <div>
                <label for="logo" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Logo') }}</label>
                <input type="file" id="logo" name="logo" accept="image/*"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                <div id="logoPreview" class="mt-2 hidden">
                    <img id="logoPreviewImg" src="" alt="Logo Preview" class="w-20 h-20 object-contain rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A]">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Description') }}</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]"></textarea>
            </div>

            <div>
                <label for="website_url" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Website URL') }}</label>
                <input type="url" id="website_url" name="website_url"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="order" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Order') }}</label>
                    <input type="number" id="order" name="order" value="0" min="0"
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Status') }}</label>
                    <label class="flex items-center mt-2">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked
                            class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Active') }}</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('Save') }}
                </button>
                <button type="button" onclick="closePartnerModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('Cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentPartnerId = null;

    function openPartnerModal(partnerId = null) {
        document.getElementById('partnerModal').classList.remove('hidden');
        document.getElementById('partnerForm').reset();
        document.getElementById('logoPreview').classList.add('hidden');
        currentPartnerId = partnerId;

        if (partnerId) {
            document.getElementById('modalTitle').textContent = '{{ __('Edit Partner') }}';
            document.getElementById('partnerForm').action = '{{ route("site.partners.update", ":id") }}'.replace(':id', partnerId);
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        } else {
            document.getElementById('modalTitle').textContent = '{{ __('Add Partner') }}';
            document.getElementById('partnerForm').action = '{{ route("site.partners.store") }}';
            document.getElementById('methodField').innerHTML = '';
        }
    }

    function closePartnerModal() {
        document.getElementById('partnerModal').classList.add('hidden');
        currentPartnerId = null;
    }

    function editPartner(id, name, description, websiteUrl, order, isActive, logoUrl) {
        document.getElementById('partnerId').value = id;
        document.getElementById('name').value = name;
        document.getElementById('description').value = description;
        document.getElementById('website_url').value = websiteUrl;
        document.getElementById('order').value = order;
        document.getElementById('is_active').checked = isActive;

        if (logoUrl) {
            document.getElementById('logoPreviewImg').src = logoUrl;
            document.getElementById('logoPreview').classList.remove('hidden');
        }

        openPartnerModal(id);
    }

    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logoPreviewImg').src = e.target.result;
                document.getElementById('logoPreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Close modal when clicking outside
    document.getElementById('partnerModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePartnerModal();
        }
    });
</script>
@endsection

