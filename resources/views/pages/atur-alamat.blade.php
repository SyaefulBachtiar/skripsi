<x-app-layout>

    <x-slot name="title">
        {{ 'Alamat' }}
    </x-slot>

    <x-slot name="header">
        <div class=" flex items-center gap-2 py-4 px-4 bg-gray-100 fixed top-0 w-full z-50 shadow-sm">
            <a 
                href="{{ url()->previous() }}"
                onclick="if(document.referrer.indexOf(window.location.host) !== -1) { history.back(); return false; }"
                class="leading-none"
            >
                <i class="bi bi-chevron-left font-bold"></i>
            </a>
            <h1 class="font-semibold">Alamat</h1>
        </div>
    </x-slot>

    <div 
        class="pt-4"
        x-data="{ isLoading: true }" x-init="setTimeout(() => isLoading = false, 600)"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 pb-20 sm:pb-0">

            {{-- SKELETON LOADING --}}
            <div x-show="isLoading" class="space-y-6 p-4 mt-2 rounded-xl bg-white border border-gray-100 animate-pulse">
                {{-- Skeleton Header --}}
                <div class="h-6 bg-gray-200 rounded w-1/4 mb-8"></div>

                {{-- Skeleton Dropdowns (Grid 2x2) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach(range(1, 4) as $i)
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                            <div class="h-10 bg-gray-100 rounded-xl w-full"></div>
                        </div>
                    @endforeach
                </div>

                {{-- Skeleton Textarea --}}
                <div class="space-y-2">
                    <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                    <div class="h-24 bg-gray-100 rounded-xl w-full"></div>
                </div>

                {{-- Skeleton Map --}}
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                    </div>
                    <div class="h-48 bg-gray-100 rounded-xl w-full"></div>
                </div>

                {{-- Skeleton Button --}}
                <div class="h-12 bg-gray-200 rounded-xl w-full mt-4"></div>
            </div>
            <div 
                x-show="!isLoading" 
                x-cloak x-transition.opacity.duration.500ms
            >
                <livewire:services.alamat.form-alamat/>
            </div>
        </div>
    </div>
</x-app-layout>