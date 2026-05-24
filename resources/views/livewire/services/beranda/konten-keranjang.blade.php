<div 
    class="space-y-4"
    x-data="{ 
        openDeleteModal: false, 
        selectedItemId: null,
        confirmDelete(id) {
            this.selectedItemId = id;
            this.openDeleteModal = true;
        },
        executeDelete() {
            // Panggil fungsi backend Livewire menggunakan API $wire
            $wire.hapusPesanan(this.selectedItemId);
            this.openDeleteModal = false;
            this.selectedItemId = null;
        }
    }"
>

    {{-- Header --}}
    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Keranjang Pesanan</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola pesanan yang Tersimpan</p>
        </div>
        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
            {{ $data->total() }} item
        </span>
    </div>

    {{-- List Pesanan --}}
    <div class="space-y-3">
        @forelse($data as $item)
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex gap-4 hover:shadow-sm transition-shadow">

            {{-- Thumbnail (Link ke Detail Produk) --}}
            <a 
                href="{{ route('detail-product', ['id' => $item->id_jasa]) }}" 
                wire:navigate
                class="relative flex-shrink-0 w-20 h-20 cursor-pointer"
            >
                <img
                    src="{{ asset('storage/' . ($item->jasa->first_thumbnail ?? 'default.jpg')) }}"
                    alt="{{ $item->jasa->nama_jasa }}"
                    class="w-full h-full object-cover rounded-lg border border-gray-100 hover:opacity-90 transition-opacity"
                >
            </a>

            {{-- Body --}}
            <div class="flex-1 min-w-0 flex flex-col justify-between">

                {{-- Top Row: Nama & Hapus --}}
                <div class="flex items-start justify-between gap-3">
                    {{-- Nama Jasa (Link ke Detail Produk) --}}
                    <a 
                        href="{{ route('detail-product', ['id' => $item->id_jasa]) }}" 
                        wire:navigate
                        class="min-w-0 flex-1 cursor-pointer"
                    >
                        <p class="text-sm font-semibold text-gray-900 truncate hover:text-blue-600 transition-colors" title="{{ $item->jasa->nama_jasa }}">
                            {{ $item->jasa->nama_jasa }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            <i class="bi bi-calendar3 text-gray-400"></i>
                            {{ \Carbon\Carbon::parse($item->order_date)->translatedFormat('l, d F Y') }}
                        </p>
                    </a>
                    
                    {{-- Tombol Hapus --}}
                    <button 
                        type="button"
                        @click="confirmDelete({{ $item->id }})"
                        class="flex-shrink-0 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all z-10"
                        title="Hapus pesanan"
                        onclick="event.stopPropagation();"
                    >
                        <i class="bi bi-trash3 text-base"></i>
                    </button>
                </div>

                {{-- Tags Keluhan --}}
                <div class="flex flex-wrap gap-1.5 mt-2">
                    @foreach(array_slice($item->keluhan, 0, 2) as $keluhan)
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-[11px] font-medium rounded-md">
                            #{{ $keluhan }}
                        </span>
                    @endforeach
                    @if(count($item->keluhan) > 2)
                        <span class="px-2 py-1 bg-gray-50 text-gray-400 text-[11px] rounded-md">
                            +{{ count($item->keluhan) - 2 }}
                        </span>
                    @endif
                </div>

                {{-- Footer: Harga & Detail --}}
                <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-2">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-medium">Total Harga</p>
                        <p class="text-base font-bold text-blue-600">
                            Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                        </p>
                    </div>
                    
                    {{-- Tombol Detail --}}
                    <button 
                        wire:click="goToDetail('{{ $item->id }}')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors z-10"
                        onclick="event.stopPropagation();"
                    >
                        <span>Detail</span>
                    </button>
                </div>
            </div>
        </div>
        @empty
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-16 border border-dashed border-gray-300 rounded-xl bg-gray-50 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="bi bi-cart-x text-3xl text-gray-400"></i>
            </div>
            <p class="text-base font-semibold text-gray-800">Keranjang Kosong</p>
            <p class="text-sm text-gray-500 mt-1 mb-5">Anda belum memiliki pesanan aktif</p>
            <a 
                href="{{ route('beranda') }}" 
                wire:navigate 
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
            >
                <i class="bi bi-search"></i>
                <span>Cari Jasa Sekarang</span>
            </a>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($data->hasPages())
    <div class="pt-4 border-t border-gray-200">
        {{ $data->links() }}
    </div>
    @endif

    <template x-teleport="body">
        <div 
            x-show="openDeleteModal" 
            class="fixed inset-0 z-[9999] flex items-center justify-center px-4 overflow-hidden"
            style="display: none;"
        >
            {{-- Backdrop Blur Latar Belakang --}}
            <div 
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                @click="openDeleteModal = false"
                x-show="openDeleteModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            ></div>

            {{-- Kotak Dialog Card Modal --}}
            <div 
                class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl border border-gray-100 relative z-10 text-center"
                x-show="openDeleteModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                @keydown.escape.window="openDeleteModal = false"
            >
                {{-- Icon Trash Animatif --}}
                <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center text-red-500 mx-auto mb-3.5 shadow-inner">
                    <i class="bi bi-trash3-fill text-xl"></i>
                </div>

                {{-- Judul dan Deskripsi Kalimat --}}
                <h3 class="text-sm font-bold text-gray-900">Hapus dari Keranjang?</h3>
                <p class="text-xs text-gray-500 mt-1.5 leading-relaxed px-2">
                    Tindakan ini tidak bisa dibatalkan. Pesanan Anda akan dihapus secara permanen dari daftar penyimpanan keranjang.
                </p>

                {{-- Grid Tombol Aksi --}}
                <div class="grid grid-cols-2 gap-3 mt-5">
                    <button 
                        type="button" 
                        @click="openDeleteModal = false"
                        class="w-full py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition active:scale-95 shadow-sm"
                    >
                        Kembali
                    </button>
                    <button 
                        type="button" 
                        @click="executeDelete()"
                        class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl transition active:scale-95 shadow-md shadow-red-100"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>