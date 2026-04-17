<div class="space-y-4">

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
                        wire:click="hapusPesanan({{ $item->id }})"
                        wire:confirm="Apakah Anda yakin ingin menghapus pesanan ini?"
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
</div>