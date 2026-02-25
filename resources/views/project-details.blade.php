@extends('layouts.landing')

@section('content')
<!-- Breadcrumb -->
<section class="bg-gray-100 dark:bg-[#161615] pt-24 pb-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-orange-500 transition-colors">{{ __('Home') }}</a>
            <span>/</span>
            <a href="{{ route('listings') }}" class="hover:text-orange-500 transition-colors">{{ __('Listings') }}</a>
            <span>/</span>
            <a href="{{ route('site.projects', $site) }}" class="hover:text-orange-500 transition-colors">{{ $site->name }}</a>
            <span>/</span>
            <span class="text-gray-900 dark:text-white">{{ $project->title }}</span>
        </nav>
    </div>
</section>

<!-- Project Details Section -->
<section class="py-12 bg-gradient-to-b from-gray-50 to-white dark:from-[#0a0a0a] dark:to-[#161615]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid lg:grid-cols-2 gap-8 mb-12">
            <!-- Left Column - Project Info -->
            <div>
                <!-- Status Badge -->
                <div class="mb-4">
                    <span class="px-4 py-2 text-sm rounded-full {{ $project->type === 'current' ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }} font-medium">
                        {{ $project->type === 'current' ? __('Current Project') : __('Previous Project') }}
                    </span>
                </div>

                <!-- Title -->
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ $project->title }}</h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 mb-6">{{ $site->name }}</p>

                <!-- Project Info Box -->
                <div class="bg-gray-50 dark:bg-[#161615] rounded-xl p-6 mb-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Project Type') }}</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $project->type === 'current' ? __('Current') : __('Previous') }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Site') }}</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $site->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Images') }}</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ ($project->main_image_url ? 1 : 0) + ($project->layout_image_url ? 1 : 0) + $project->images->count() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($project->description)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ __('Description') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-line">{{ $project->description }}</p>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    <a href="{{ route('site.projects', $site) }}" class="px-6 py-3 border-2 border-orange-500 text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg font-semibold transition-all">
                        {{ __('Back to Projects') }}
                    </a>
                    <a href="{{ route('contact') }}" class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-3 rounded-lg text-center font-semibold transition-all transform hover:scale-105">
                        {{ __('Contact Us') }}
                    </a>
                </div>
            </div>

            <!-- Right Column - Image Gallery -->
            <div>
                @php
                $allImages = [];
                if ($project->main_image_url) {
                $allImages[] = ['url' => $project->main_image_url, 'type' => 'main'];
                }
                if ($project->layout_image_url) {
                $allImages[] = ['url' => $project->layout_image_url, 'type' => 'layout'];
                }
                foreach ($project->images as $image) {
                $allImages[] = ['url' => $image->image_url, 'type' => 'gallery'];
                }
                @endphp

                @if(count($allImages) > 0)
                <!-- Main Image -->
                <div class="mb-4">
                    <img src="{{ $allImages[0]['url'] }}" alt="{{ $project->title }}" class="w-full h-96 object-cover rounded-xl shadow-lg cursor-pointer hover:opacity-90 transition-opacity" id="mainImage" onclick="openImageModal(0)">
                </div>

                <!-- Thumbnail Images -->
                @if(count($allImages) > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach(array_slice($allImages, 0, 4) as $index => $imageData)
                    <img src="{{ $imageData['url'] }}" alt="{{ $project->title }}"
                        class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity border-2 border-transparent hover:border-orange-500"
                        onclick="changeMainImage({{ $index }})">
                    @endforeach
                </div>
                @endif
                @else
                <div class="w-full h-96 bg-gray-200 dark:bg-[#3E3E3A] rounded-xl flex items-center justify-center">
                    <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                @endif
            </div>
        </div>

        <!-- Layout Image Section -->
        @if($project->layout_image_url)
        <div class="mb-12 bg-white dark:bg-[#161615] rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-800">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                <span class="w-1 h-8 bg-orange-500 rounded-full"></span>
                {{ __('Layout Image') }}
            </h2>
            <div class="rounded-xl overflow-hidden shadow-lg cursor-pointer" onclick="openImageModal(-2)">
                <img src="{{ $project->layout_image_url }}" alt="{{ $project->title }} - {{ __('Layout') }}" class="w-full h-[500px] md:h-[600px] object-cover hover:opacity-90 transition-opacity">
            </div>
        </div>
        @endif



    </div>
</section>

@php
$allImages = [];
if ($project->main_image_url) {
$allImages[] = ['url' => $project->main_image_url, 'type' => 'main'];
}
if ($project->layout_image_url) {
$allImages[] = ['url' => $project->layout_image_url, 'type' => 'layout'];
}
foreach ($project->images as $image) {
$allImages[] = ['url' => $image->image_url, 'type' => 'gallery'];
}

$modalImages = [];
if ($project->main_image_url) {
$modalImages[] = $project->main_image_url;
}
if ($project->layout_image_url) {
$modalImages[] = $project->layout_image_url;
}
foreach ($project->images as $image) {
$modalImages[] = $image->image_url;
}
$mainImageIndex = $project->main_image_url ? 0 : -1;
$layoutImageIndex = $project->main_image_url ? 1 : ($project->layout_image_url ? 0 : -1);
$galleryStartIndex = ($project->main_image_url ? 1 : 0) + ($project->layout_image_url ? 1 : 0);
@endphp

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    }

    function toggleLanguageMenu() {
        const menu = document.getElementById('languageMenu');
        menu.classList.toggle('hidden');
    }

    function toggleMobileLanguageMenu() {
        const menu = document.getElementById('mobileLanguageMenu');
        menu.classList.toggle('hidden');
    }

    const projectImages = {
        !!json_encode($modalImages) !!
    };
    let currentImageIndex = 0;
    const mainImageIndex = {
        {
            $mainImageIndex
        }
    };
    const layoutImageIndex = {
        {
            $layoutImageIndex
        }
    };
    const galleryStartIndex = {
        {
            $galleryStartIndex
        }
    };

    function changeMainImage(index) {
        const mainImage = document.getElementById('mainImage');
        const allImagesArray = {
            !!json_encode($allImages) !!
        };
        if (mainImage && allImagesArray[index]) {
            mainImage.src = allImagesArray[index].url;
        }
    }

    function openImageModal(index) {
        // index = -1 for main image, -2 for layout image, 0+ for gallery images
        if (index === -1) {
            currentImageIndex = mainImageIndex; // Main image
        } else if (index === -2) {
            currentImageIndex = layoutImageIndex; // Layout image
        } else {
            currentImageIndex = galleryStartIndex + index; // Gallery images
        }

        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const imageCounter = document.getElementById('imageCounter');

        if (modal && modalImage) {
            modalImage.src = projectImages[currentImageIndex];
            imageCounter.textContent = `${currentImageIndex + 1} / ${projectImages.length}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }

    function changeModalImage(direction) {
        currentImageIndex += direction;

        if (currentImageIndex < 0) {
            currentImageIndex = projectImages.length - 1;
        } else if (currentImageIndex >= projectImages.length) {
            currentImageIndex = 0;
        }

        const modalImage = document.getElementById('modalImage');
        const imageCounter = document.getElementById('imageCounter');

        if (modalImage) {
            modalImage.src = projectImages[currentImageIndex];
            imageCounter.textContent = `${currentImageIndex + 1} / ${projectImages.length}`;
        }
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeImageModal();
        } else if (event.key === 'ArrowLeft') {
            changeModalImage(-1);
        } else if (event.key === 'ArrowRight') {
            changeModalImage(1);
        }
    });

    // Close modal when clicking outside image
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.addEventListener('click', function(event) {
                if (event.target === this) {
                    closeImageModal();
                }
            });
        }
    });

    // Close language menu when clicking outside
    document.addEventListener('click', function(event) {
        const languageButton = event.target.closest('[onclick="toggleLanguageMenu()"]');
        const mobileLanguageButton = event.target.closest('[onclick="toggleMobileLanguageMenu()"]');
        const languageMenu = document.getElementById('languageMenu');
        const mobileLanguageMenu = document.getElementById('mobileLanguageMenu');

        if (languageMenu && !languageButton && !languageMenu.contains(event.target)) {
            languageMenu.classList.add('hidden');
        }
        if (mobileLanguageMenu && !mobileLanguageButton && !mobileLanguageMenu.contains(event.target)) {
            mobileLanguageMenu.classList.add('hidden');
        }
    });
</script>
@endsection