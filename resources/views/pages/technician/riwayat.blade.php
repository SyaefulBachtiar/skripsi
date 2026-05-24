<x-app-layout>

    <x-slot name="title">
        {{ 'Riwayat Pesanan' }}
    </x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-2 py-4 px-4 bg-gray-50 fixed top-0 w-full z-50 shadow-sm">
            <a 
                href="{{ url()->previous() }}"
                onclick="if(document.referrer.indexOf(window.location.host) !== -1) { history.back(); return false; }"
                class="leading-none"
            >
                <i class="bi bi-chevron-left font-bold"></i>
            </a>
            <h1 class="font-semibold">Riwayat Pesanan</h1>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- <livewire:services.beranda.konten-rincian-pesanan :id_order="$id_order"/> --}}
            <livewire:services.riwayat-pesanan-technician/>
            
        </div>
    </div>
</x-app-layout>
