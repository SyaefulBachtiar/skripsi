{{-- Fitur Quick Access --}}
<div
    x-data="{ isLoading: true }"
    x-init="setTimeout(() => isLoading = false, 800)"
    class="w-full"
>
    {{-- SKELETON LOADING GRID --}}
    <div x-show="isLoading" class="grid grid-cols-4 gap-3 sm:gap-4 animate-pulse">
        @for ($i = 0; $i < 4; $i++)
            <div class="flex flex-col items-center justify-center gap-2 p-3 sm:p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
                <div class="h-2.5 w-12 bg-slate-200 dark:bg-slate-700 rounded"></div>
            </div>
        @endfor
    </div>

    {{-- ACTUAL CONTENT --}}
    <div 
        x-show="!isLoading" 
        x-cloak 
        x-transition.opacity.duration.500ms
        class="grid grid-cols-4 gap-3 sm:gap-4"
    >
        {{-- Posting --}}
        <a href="{{ route('posting.jasa') }}" class="group flex flex-col items-center justify-center gap-2 p-3 sm:p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-blue-50 dark:bg-blue-900/30 rounded-lg group-hover:bg-blue-600 transition-colors">
                <i class="bi bi-megaphone-fill text-lg sm:text-xl text-blue-600 dark:text-blue-400 group-hover:text-white transition-colors"></i>
            </div>
            <span class="text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-300 text-center leading-tight">Posting</span>
        </a>

        {{-- Riwayat --}}
        <a href="{{ route('riwayat.technician') }}" class="group flex flex-col items-center justify-center gap-2 p-3 sm:p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-amber-50 dark:bg-amber-900/30 rounded-lg group-hover:bg-amber-500 transition-colors">
                <i class="bi bi-clock-history text-lg sm:text-xl text-amber-600 dark:text-amber-400 group-hover:text-white transition-colors"></i>
            </div>
            <span class="text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-300 text-center leading-tight">Riwayat</span>
        </a>

        {{-- Jasa --}}
        <a href="{{ route('jasa.technician') }}" class="group flex flex-col items-center justify-center gap-2 p-3 sm:p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/30 rounded-lg group-hover:bg-emerald-500 transition-colors">
                <i class="bi bi-wrench-adjustable-circle text-lg sm:text-xl text-emerald-600 dark:text-emerald-400 group-hover:text-white transition-colors"></i>
            </div>
            <span class="text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-300 text-center leading-tight">Jasa</span>
        </a>

        {{-- Laporan --}}
        <a href="{{ route('pesanan.technician') }}" class="group flex flex-col items-center justify-center gap-2 p-3 sm:p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-rose-50 dark:bg-rose-900/30 rounded-lg group-hover:bg-rose-500 transition-colors">
                <i class="bi bi-journal-text text-lg sm:text-xl text-rose-600 dark:text-rose-400 group-hover:text-white transition-colors"></i>
            </div>
            <span class="text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-300 text-center leading-tight">Pesanan</span>
        </a>
    </div>
</div>