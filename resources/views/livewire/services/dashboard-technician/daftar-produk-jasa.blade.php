<div class="space-y-6">
    <!-- Search Input dengan styling lebih modern -->
    <div class="relative max-w-lg mx-auto">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search" 
            placeholder="Cari nama jasa Anda..."
            class="block w-full pl-12 pr-4 py-3 border-0 rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all duration-200"
        >
    </div>

    <!-- Container Utama dengan Alpine.js -->
    <div 
        x-data="{ isLoading: true }"
        x-init="setTimeout(() => isLoading = false, 500)"
        class="relative"
    >
        <!-- SKELETON LOADING GRID - Perbaikan visual -->
        <div 
            x-show="isLoading" 
            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
        >
            @foreach(range(1, 8) as $i)
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 animate-pulse">
                    <!-- Image Placeholder dengan aspect ratio -->
                    <div class="aspect-[4/3] bg-gray-200 rounded-xl"></div>
                    
                    <!-- Title Placeholder -->
                    <div class="h-4 bg-gray-200 rounded-lg w-3/4 mt-4"></div>
                    
                    <!-- Price Placeholder -->
                    <div class="h-3 bg-gray-200 rounded-lg w-1/2 mt-3"></div>

                    <!-- Status Placeholder -->
                    <div class="h-8 bg-gray-100 rounded-lg w-full mt-4"></div>
                </div>
            @endforeach
        </div>

        <!-- Grid Produk yang Sebenarnya - Perbaikan visual -->
        <div 
            x-show="!isLoading" 
            x-cloak 
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
        >
            @forelse($jasa as $item)
                <a href="{{ route('detail_jasa.technician', ['id_jasa' => $item->id]) }}" 
                   class="group bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    
                    <!-- Container Gambar dengan aspect ratio -->
                    <div class="aspect-[4/3] overflow-hidden rounded-xl bg-gray-50 relative">
                        @if($item->first_thumbnail)
                            <img src="{{ asset('storage/' . $item->first_thumbnail) }}" 
                                alt="{{ $item->nama_jasa }}" 
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Informasi Produk -->
                    <div class="mt-4 space-y-2">
                        <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 leading-tight">{{ $item->nama_jasa }}</h3>
                        <p class="text-indigo-600 font-bold text-lg">Rp {{ number_format($item->harga_jasa, 0, ',', '.') }}</p>
                    </div>

                    <!-- Status Badge yang lebih modern -->
                    <div class="mt-3">
                        @if($item->ketersediaan_status === 'Ketersediaan perlu diperbarui')
                            <span class="inline-flex items-center justify-center w-full px-3 py-2 rounded-xl bg-red-50 text-red-600 text-xs font-medium border border-red-100">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $item->ketersediaan_status }}
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center w-full px-3 py-2 rounded-xl bg-green-50 text-green-600 text-xs font-medium border border-green-100">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                {{ $item->ketersediaan_status }}
                            </span>
                        @endif
                    </div>
                </a>
            @empty
                <!-- Empty State yang lebih menarik -->
                <div class="col-span-full py-16 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-lg font-medium">Belum ada jasa yang diposting</p>
                    <p class="text-gray-400 text-sm mt-1">Silakan tambahkan jasa baru untuk mulai berjualan</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination dengan styling yang lebih baik -->
    <div class="mt-8 flex justify-center">
        {{ $jasa->links() }}
    </div>
</div>