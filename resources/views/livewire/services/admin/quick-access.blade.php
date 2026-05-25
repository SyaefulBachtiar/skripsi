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
        {{-- Semua User --}}
        <a href="{{ route('users.view') }}" class="group flex flex-col items-center justify-center gap-2 p-3 sm:p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-blue-50 dark:bg-blue-900/30 rounded-lg group-hover:bg-blue-600 transition-colors">
                <i class="bi bi-people-fill text-lg sm:text-xl text-blue-600 dark:text-blue-400 group-hover:text-white transition-colors"></i>
            </div>
            <span class="text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-300 text-center leading-tight">Users</span>
        </a>

        {{-- Ditolak --}}
        <a href="{{ route('ditolak.view') }}" class="group flex flex-col items-center justify-center gap-2 p-3 sm:p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-red-50 dark:bg-red-900/30 rounded-lg group-hover:bg-red-600 transition-colors">
                <i class="bi bi-x-circle-fill text-lg sm:text-xl text-red-600 dark:text-red-400 group-hover:text-white transition-colors"></i>
            </div>
            <span class="text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-300 text-center leading-tight">Ditolak</span>
        </a>

        {{-- Jasa --}}
        <a href="{{ route('beranda') }}" class="group flex flex-col items-center justify-center gap-2 p-3 sm:p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/30 rounded-lg group-hover:bg-emerald-600 transition-colors">
                <i class="bi bi-wrench-adjustable-circle text-lg sm:text-xl text-emerald-600 dark:text-emerald-400 group-hover:text-white transition-colors"></i>
            </div>
            <span class="text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-300 text-center leading-tight">Jasa</span>
        </a>

        {{-- Laporan --}}
        <a href="{{ route('transaksi.view') }}" class="group flex flex-col items-center justify-center gap-2 p-3 sm:p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-violet-50 dark:bg-violet-900/30 rounded-lg group-hover:bg-violet-600 transition-colors">
                <i class="bi bi-clipboard-data-fill text-lg sm:text-xl text-violet-600 dark:text-violet-400 group-hover:text-white transition-colors"></i>
            </div>
            <span class="text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-300 text-center leading-tight">Transaksi</span>
        </a>
    </div>
</div>