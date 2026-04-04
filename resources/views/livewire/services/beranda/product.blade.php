<div class="space-y-4">
    {{-- Fillter --}}
    <div class="bg-white rounded p-3">
        <div class="grid grid-cols-4 text-sm gap-4">
            <input 
                type="text" 
                class="col-span-3 rounded"
                placeholder="Cari"
            >
            <button class="col-span-1 bg-blue-700 rounded text-white">Kirim</button>
        </div>
    </div>
    {{-- Products --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4">
        
        {{-- Product Card --}}
        <a href="{{ route('detail-product') }}" class="bg-white p-2 sm:p-4 rounded space-y-2 shadow-sm sm:shadow-md">
            {{-- Image --}}
            <div>
                <img 
                    src="{{ asset('assets/icons/empty_image.webp') }}" 
                    alt="Kosong"
                    class="object-contain"
                >
            </div>
            <div>
                <div class="flex items-center gap-1 text-sm sm:text-lg leading-none">
                    <h1 class="font-semibold">Jasa Servis Ac</h1>
                    <div class="flex items-center gap-0.5 text-yellow-500 border border-yellow-400 rounded p-0.5 leading-none">
                        <span class="text-[12px] sm:text-xs font-bold leading-none">4.8</span>
                        <i class="bi bi-star-fill text-[10px] sm:text-[12px] leading-none mb-[1px]"></i>
                    </div>
                </div>
                <p class="text-xs sm:text-md truncate">Servis sparepart ac pendingin, kipas dll</p>
            </div>
        </a>
    </div>
</div>
