<a href="{{ route('atur_alamat') }}" 
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