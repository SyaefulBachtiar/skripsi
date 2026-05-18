<div>
    {{-- Header Section --}}
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-2 shadow-sm border border-gray-100 dark:border-slate-700">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 p-2 text-center sm:text-left">
            {{-- Foto Profile --}}
            <div class="w-20 h-20 sm:w-16 sm:h-16 rounded-full overflow-hidden border-2 border-indigo-100 shadow-sm flex-shrink-0">
                <img 
                    src="{{ $data_technician->user->profile_photo_url }}" 
                    alt="Profile"
                    class="w-full h-full object-cover"
                >
            </div>

            <div class="flex flex-col justify-center">
                <div class="flex items-center justify-center sm:justify-start gap-1 group cursor-pointer">
                    <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ $data_technician->user->name }}</h1>
                    {{-- <i class="bi bi-chevron-right text-gray-400 group-hover:text-indigo-600 transition-colors"></i> --}}
                </div>
                
                <div class="flex items-center justify-center sm:justify-start gap-3 mt-1 text-sm text-gray-500 dark:text-slate-400">
                    <div class="flex items-center gap-1">
                        <span class="font-semibold text-gray-800 dark:text-white text-base">4.8</span>
                        <i class="bi bi-star-fill text-yellow-400"></i>
                    </div>
                    <span class="text-gray-300">|</span>
                    <div class="flex items-center gap-1">
                        <span class="font-semibold text-gray-800 dark:text-white text-base">10</span>
                        <span>Pengguna Jasa</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Profile Section --}}
        <div 
            x-data="{ 
                showModal: false, 
                modalImage: '',
                openModal(url) {
                    this.modalImage = url;
                    this.showModal = true;
                }
            }"
            class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4"
        >
            {{-- Deskripsi --}}
            <div class="bg-gray-50 dark:bg-slate-900/50 rounded-2xl p-4 border border-gray-100 dark:border-slate-700">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Deskripsi</p>
                <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $data_technician->deskripsi }}</p>
            </div>

            <div class="space-y-4">
                {{-- Spesialisasi --}}
                <div class="bg-gray-50 dark:bg-slate-900/50 rounded-2xl p-4 border border-gray-100 dark:border-slate-700">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Spesialisasi</p>
                    <div class="flex flex-wrap gap-2">
                        @forelse ($data_technician->spesialisasi as $item)
                            <span class="text-xs font-medium bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-3 py-1.5 rounded-full">
                                {{ $item }}
                            </span>
                        @empty
                            <p class="text-sm text-gray-400">Belum ada spesialisasi.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Pengalaman --}}
                <div class="bg-gray-50 dark:bg-slate-900/50 rounded-2xl p-4 border border-gray-100 dark:border-slate-700">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Pengalaman</p>
                    @forelse($data_technician->pengalaman as $item)
                        <div class="flex items-start gap-2 mb-2 last:mb-0">
                            <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></span>
                            <span class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">{{ $item }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Belum ada pengalaman.</p>
                    @endforelse
                </div>
            </div>

            {{-- Sertifikat --}}
            @if(!empty($data_technician->sertifikat))
            <div class="md:col-span-2 bg-gray-50 dark:bg-slate-900/50 rounded-2xl p-4 border border-gray-100 dark:border-slate-700">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Sertifikat Keahlian</p>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                    @foreach($data_technician->sertifikat as $item)
                        <div 
                            @click="openModal('{{ asset('storage/' . $item) }}')"
                            class="aspect-[4/3] rounded-xl overflow-hidden border border-gray-200 dark:border-slate-600 bg-white cursor-pointer hover:ring-2 hover:ring-indigo-500 transition-all"
                        >
                            <img src="{{ asset('storage/' . $item) }}" alt="Sertifikat" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Lightbox Modal --}}
            <template x-teleport="body">
                <div x-show="showModal" class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/90 p-4" style="display: none;" @keydown.escape.window="showModal = false">
                    <button @click="showModal = false" class="absolute top-5 right-5 text-white/70 hover:text-white p-2"><i class="bi bi-x-lg text-3xl"></i></button>
                    <div class="max-w-5xl w-full h-full flex items-center justify-center" @click.away="showModal = false">
                        <img :src="modalImage" class="max-w-full max-h-full rounded-lg object-contain border border-white/10 shadow-2xl transition-transform duration-300">
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Filter & Product Section --}}
    <div class="mt-8 space-y-6">
        {{-- Sticky Filter --}}
        <div class="sticky top-20 z-10 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md rounded-2xl p-4 shadow-md border border-gray-100 dark:border-slate-700 overflow-visible">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                <div class="lg:col-span-6">
                    <x-select-search placeholder="Cari jasa..." model="search" searchModel="searchJasa" :options="$options_nama_jasa" />
                </div>
                <div class="lg:col-span-4">
                    <x-select-search placeholder="Kategori" model="kategori" searchModel="searchKategori" :options="$options_kategori" />
                </div>
                <div class="lg:col-span-2">
                    <button wire:click="resetFilter" class="w-full h-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-xl transition shadow-md">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Reset</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse ($data_jasa as $item)
                <a href="{{ route('detail-product', ['id' => $item->id]) }}" class="group bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="aspect-square bg-gray-50 dark:bg-slate-900 overflow-hidden relative">
                        <img src="{{ asset('storage/' . ($item->thumbnails[0] ?? 'default.jpg')) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                        <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition-colors"></div>
                    </div>
                    <div class="p-3 space-y-1">
                        <h3 class="font-bold text-sm text-gray-800 dark:text-white line-clamp-2 h-10 leading-snug group-hover:text-indigo-600 transition-colors uppercase">
                            {{ $item->nama_jasa }}
                        </h3>
                        <p class="text-indigo-600 dark:text-indigo-400 font-extrabold text-base">
                            Rp {{ number_format($item->harga_jasa, 0, ',', '.') }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-20 text-center text-gray-400 bg-white dark:bg-slate-800 rounded-3xl border-2 border-dashed border-gray-100 dark:border-slate-700">
                    <i class="bi bi-search text-5xl block mb-4 opacity-20"></i>
                    <p class="text-lg font-medium">Wah, jasa yang kamu cari belum ada.</p>
                    <p class="text-sm">Coba kata kunci lain atau hapus filter.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $data_jasa->links() }}
        </div>
    </div>
</div>