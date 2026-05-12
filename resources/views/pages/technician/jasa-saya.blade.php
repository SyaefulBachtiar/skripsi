<x-app-layout>

    <x-slot name="title">
        {{ 'Jasa Saya' }}
    </x-slot>

    <x-slot name="header">
        <div class=" flex items-center gap-2 py-4 px-4 bg-gray-50 fixed top-0 w-full z-50 shadow-sm">
            <a 
                href="{{ route('dashboard_technician') }}"
                class="leading-none"
            >
                <i class="bi bi-chevron-left font-bold"></i>
            </a>
            <h1 class="font-semibold">Jasa Saya</h1>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10 sm:pb-0">
            <livewire:services.dashboard-technician.daftar-produk-jasa/>
        </div>
    </div>
</x-app-layout>
