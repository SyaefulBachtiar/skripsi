<div
    x-data="{
        isLoading: true
    }"
    x-init="setTimeout(() => isLoading = false, 800)"
    class="w-full"
>

    {{-- SKELETON LOADING --}}
    <div x-show="isLoading" class="animate-pulse py-3 px-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="flex justify-between items-center">
            <div class="space-y-3 w-full">
                {{-- Label Skeleton --}}
                <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded w-1/4"></div>
                
                {{-- Address Body Skeleton --}}
                <div class="flex items-center gap-2">
                    <div class="h-4 w-4 bg-blue-200 dark:bg-blue-900/30 rounded-full"></div>
                    <div class="h-3 bg-slate-100 dark:bg-slate-700 rounded w-3/4"></div>
                </div>
            </div>
            {{-- Arrow Icon Skeleton --}}
            <div class="h-5 w-5 bg-slate-100 dark:bg-slate-700 rounded-full ml-4"></div>
        </div>
    </div>

    <div 
        x-show="!isLoading" x-cloak x-transition.opacity.duration.500ms
        class=""
    >
        <a 
            href="{{ route('atur_alamat') }}" 
            class="group py-3 px-4 bg-white dark:bg-slate-800 rounded-xl block shadow-sm border border-slate-100 dark:border-slate-700 transition-all duration-300 hover:shadow-md hover:border-blue-400 dark:hover:border-blue-500">
            
            <div class="flex justify-between items-start">
                <div class="space-y-1 w-full overflow-hidden">
                    {{-- Label --}}
                    <h4 class="font-bold text-slate-800 dark:text-slate-200 text-sm sm:text-base group-hover:text-blue-600 transition-colors">
                        {{ auth()->user()->role === 'customer' ? 'Alamat Anda' : 'Alamat Bengkel' }}
                    </h4>
                    
                    {{-- Alamat Body --}}
                    <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                        <i class="bi bi-geo-alt-fill text-blue-500"></i>
                        <span class="text-xs sm:text-sm truncate pr-4">
                            {{ $alamat ?? 'Alamat belum ditambahkan, klik untuk mengatur' }}
                        </span>
                    </div>
                </div>

                {{-- Icon Panah (Indikator bisa diklik) --}}
                <div class="text-slate-300 group-hover:text-blue-500 transition-colors self-center">
                    <i class="bi bi-chevron-right text-lg"></i>
                </div>
            </div>

            {{-- Garis dekoratif tipis di bawah saat hover --}}
            {{-- <div class="absolute bottom-0 left-0 h-1 bg-blue-500 transition-all duration-300 w-0 group-hover:w-full rounded-b-xl"></div> --}}
        </a>
    </div>

</div>