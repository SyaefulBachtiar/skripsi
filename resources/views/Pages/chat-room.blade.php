<x-app-layout>

    <x-slot name="title">
        {{ 'Chat' }}
    </x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between py-2 px-4 bg-white/90 backdrop-blur-md fixed top-0 w-full z-50 shadow-sm border-b border-gray-100">
            {{-- Left Side: Back & Profile --}}
            <div class="flex items-center gap-3">
                {{-- Back Button --}}
                <a 
                    href="{{ url()->previous() }}"
                    onclick="if(document.referrer.indexOf(window.location.host) !== -1) { history.back(); return false; }"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors"
                >
                     <i class="bi bi-chevron-left font-bold text-gray-600"></i>
                </a>

                {{-- Technician Info --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full overflow-hidden ring-2 ring-gray-100 shadow-sm bg-gray-200">
                        <img 
                            src="{{ asset('storage/' . $data->technician->foto_wajah) }}" 
                            alt="{{ $data->technician->nama_asli }}"
                            class="w-full h-full object-cover" {{-- Diubah ke object-cover agar pas di lingkaran --}}
                        >
                    </div>
                    <div>
                        <h1 class="text-sm font-bold text-gray-900 leading-none">{{ $data->technician->nama_asli }}</h1>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Teknisi Online</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Shop/Profile Link --}}
            <a
                href="{{ route('technician.profile', ['id' => $data->technician_id]) }}"
                class="flex items-center justify-center w-10 h-10 bg-gray-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all duration-300 border border-gray-100"
                title="Lihat Toko"
            >
                <i class="bi bi-shop text-lg"></i>
            </a>
        </div>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <livewire:services.konten-chat-message :roomChatId='$data->id'/>
            
        </div>
    </div>
</x-app-layout>
