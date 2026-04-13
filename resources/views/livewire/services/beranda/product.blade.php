<div class="space-y-4">
    {{-- Filter --}}
    {{-- PENTING: overflow-visible agar dropdown tidak terpotong, z-10 agar di atas grid produk --}}
    <div class="relative z-10 bg-white rounded-2xl p-4 shadow-sm border border-gray-100 space-y-3 overflow-visible">
        <x-select-search 
            placeholder="Cari jasa servis (misal: AC, Kulkas)..."
            model="search" 
            searchModel="searchJasa"
            :options="$nama_jasa"
        />

        <div class="flex gap-2">
            <div class="flex-1">
                <x-select-search 
                    placeholder="Pilih Wilayah..."
                    model="wilayah" 
                    searchModel="searchWilayah"
                    :options="$list_wilayah"
                />
            </div>

            <button 
                type="button"
                wire:click="resetFilter"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-gray-600 text-sm font-semibold rounded-xl transition whitespace-nowrap"
            >
                <i class="bi bi-arrow-counterclockwise text-base leading-none text-white"></i>
                <span class="text-white">Hapus</span>
            </button>
        </div>
    </div>

    {{-- Products --}}
    <div class="grid grid-cols-2 gap-3 px-1">
        @forelse ($produk as $item)
            <a 
                href="{{ route('detail-product', ['id' => $item->id]) }}"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200"
            >
                <div class="aspect-square bg-gray-50 overflow-hidden">
                    <img 
                        src="{{ asset('storage/' . ($item->first_thumbnail ?? 'default.jpg')) }}" 
                        class="w-full h-full object-cover"
                        loading="lazy"
                    >
                </div>
                <div class="p-3 space-y-1">
                    <h3 class="font-semibold text-sm text-gray-800 line-clamp-2 leading-snug">
                        {{ $item->nama_jasa }}
                    </h3>
                    <p class="text-indigo-600 font-bold text-sm">
                        Rp {{ number_format($item->harga_jasa, 0, ',', '.') }}
                    </p>
                </div>
            </a>
        @empty
            <div class="col-span-2 py-14 text-center text-gray-400 space-y-2">
                <i class="bi bi-search text-4xl block"></i>
                <p class="text-sm">Jasa tidak ditemukan.</p>
            </div>
        @endforelse
    </div>

    <div class="px-1 pb-10">
        {{ $produk->links() }}
    </div>
</div>