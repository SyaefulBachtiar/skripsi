<div class="space-y-4">
    {{-- Filter Section --}}
    {{-- Menggunakan grid pada filter agar rapi di layar besar dan bertumpuk di mobile --}}
    <div class="relative z-20 bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-slate-700 overflow-visible">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            
            {{-- Search Jasa --}}
            <div class="md:col-span-6">
                <x-select-search 
                    placeholder="Cari jasa servis (misal: AC, Kulkas)..."
                    model="search" 
                    searchModel="searchJasa"
                    :options="$nama_jasa"
                />
            </div>

            {{-- Filter Wilayah --}}
            <div class="md:col-span-4">
                <x-select-search 
                    placeholder="Pilih Wilayah..."
                    model="wilayah" 
                    searchModel="searchWilayah"
                    :options="$list_wilayah"
                />
            </div>

            {{-- Button Reset --}}
            <div class="md:col-span-2">
                <button 
                    type="button"
                    wire:click="resetFilter"
                    class="w-full h-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-xl transition duration-200 shadow-sm"
                >
                    <i class="bi bi-arrow-counterclockwise text-lg"></i>
                    <span class="md:hidden lg:inline">Hapus</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Products Grid --}}
    {{-- Mobile: 2 kolom, Tablet: 3 kolom, Desktop: 4 kolom, Large Desktop: 5 kolom --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
        @forelse ($produk as $item)
            <a 
                href="{{ route('detail-product', ['id' => $item->id]) }}"
                class="group bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
            >
                {{-- Container Image dengan Aspect Ratio tetap --}}
                <div class="aspect-square bg-gray-50 dark:bg-slate-900 overflow-hidden relative">
                    <img 
                        src="{{ asset('storage/' . ($item->thumbnails[0] ?? 'default.jpg')) }}" 
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        loading="lazy"
                        alt="{{ $item->nama_jasa }}"
                    >
                    {{-- Overlay Tipis saat hover --}}
                    <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>

                {{-- Konten Teks --}}
                <div class="p-3 space-y-2">

                    <span class="w-max px-2 py-0.5 rounded text-[9px] sm:text-[10px] font-bold uppercase tracking-wider 
                        {{ $item->tipe_layanan === 'panggilan' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ $item->tipe_layanan === 'panggilan' ? 'Panggilan' : 'Bengkel' }}
                    </span>
                    
                    {{-- Rating Section --}}
                    <div class="flex items-center gap-1">
                        {{-- Icon Bintang dari Bootstrap Icons --}}
                        <i class="bi bi-star-fill text-amber-400 text-xs sm:text-sm"></i>
                        
                        {{-- Menampilkan Rata-rata Rating, Default 0 jika null --}}
                        <span class="font-bold text-xs sm:text-sm text-gray-700 dark:text-gray-200">
                            {{ number_format($item->rata_rata_rating ?? 0, 1) }}
                        </span>

                        {{-- Opsional: Menampilkan Total Ulasan --}}
                        <span class="text-[10px] sm:text-xs text-gray-400 font-medium">
                            ({{ $item->review_count ?? 0 }} Pelanggan)
                        </span>
                    </div>

                    {{-- Nama Jasa --}}
                    <h3 class="font-bold text-xs sm:text-sm text-gray-800 dark:text-white line-clamp-2 leading-snug group-hover:text-blue-700 transition-colors uppercase">
                        {{ $item->nama_jasa }}
                    </h3>
                </div>
            </a>
        @empty
            {{-- Empty State yang lebih cantik --}}
            <div class="col-span-full py-20 text-center bg-gray-50 dark:bg-slate-900/50 rounded-3xl border-2 border-dashed border-gray-200 dark:border-slate-800">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full mb-4">
                    <i class="bi bi-search text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-gray-800 dark:text-white font-bold">Jasa Tidak Ditemukan</h3>
                <p class="text-sm text-gray-500">Coba gunakan kata kunci lain atau hapus filter.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="pt-6 pb-12">
        {{ $produk->links() }}
    </div>
</div>