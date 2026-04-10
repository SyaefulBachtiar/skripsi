<div>
    {{-- Search Input --}}
    <div class="mb-6">
        <div class="relative max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="bi bi-search"></i>
            </span>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Cari nama jasa Anda..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
        </div>
    </div>

    {{-- Container Utama --}}
    <div 
        x-data="{ isLoading: true }"
        x-init="setTimeout(() => isLoading = false, 500)"
        class="relative"
    >
        {{-- SKELETON LOADING GRID --}}
        {{-- Muncul saat Alpine isLoading (load pertama) ATAU saat Livewire sedang request (pencarian/pagination) --}}
        <div 
            x-show="isLoading" 
            class="grid grid-cols-2 md:grid-cols-4 gap-2"
        >
            @foreach(range(1, 4) as $i)
                <div class="p-4 border rounded-xl bg-white animate-pulse">
                    {{-- Image Placeholder --}}
                    <div class="w-full h-40 bg-gray-200 rounded-lg"></div>
                    
                    {{-- Title Placeholder --}}
                    <div class="h-4 bg-gray-200 rounded w-3/4 mt-4"></div>
                    
                    {{-- Price Placeholder --}}
                    <div class="h-3 bg-gray-200 rounded w-1/2 mt-2"></div>

                    {{-- Status Placeholder --}}
                    <div class="h-6 bg-gray-100 rounded-md w-full mt-3"></div>
                </div>
            @endforeach
        </div>

        <div 
            x-show="!isLoading" 
            x-cloak x-transition.opacity.duration.500ms
            class="grid grid-cols-2 md:grid-cols-4 gap-2"
        >
            @forelse($jasa as $item)
                <a href="{{ route('detail_jasa.technician', ['id_jasa' => $item->id]) }}" class="group card p-4 border rounded-xl hover:shadow-md transition-all">
                    <div class="overflow-hidden rounded-lg">
                        <img src="{{ asset('storage/' . $item->first_thumbnail) }}" 
                             alt="{{ $item->nama_jasa }}" 
                             class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    
                    <h3 class="font-bold mt-2 truncate">{{ $item->nama_jasa }}</h3>
                    <p class="text-indigo-600 font-semibold">Rp {{ number_format($item->harga_jasa, 0, ',', '.') }}</p>

                    <div class="mt-3">
                        @if($item->ketersediaan_status === 'Ketersediaan perlu diperbarui')
                            <span class="text-[10px] bg-red-50 text-red-600 px-2 py-1 rounded-md border border-red-100 block text-center font-medium">
                                <i class="bi bi-exclamation-triangle-fill mr-1"></i> {{ $item->ketersediaan_status }}
                            </span>
                        @else
                            <span class="text-[10px] bg-green-50 text-green-600 px-2 py-1 rounded-md border border-green-100 block text-center font-medium">
                                <i class="bi bi-calendar-check mr-1"></i> {{ $item->ketersediaan_status }}
                            </span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="col-span-full py-10 text-center">
                    <p class="text-gray-500">Belum ada jasa yang diposting.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">
        {{ $jasa->links() }}
    </div>
</div>