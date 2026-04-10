<x-app-layout>

    <x-slot name="title">
        {{ 'Dashboard' }}
    </x-slot>

    <x-slot name="header">
        <livewire:layout.header-mobile/>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Address --}}
            <livewire:services.address-card/>

            @if(!empty($data->spesialisasi))
                {{-- Fitur Quick Access --}}
                <div
                    x-data="{ isLoading: true }"
                    x-init="setTimeout(() => isLoading = false, 800)"
                    class="w-full"
                >

                    {{-- SKELETON LOADING GRID --}}
                    <div x-show="isLoading" class="grid grid-cols-4 sm:grid-cols-4 lg:grid-cols-8 gap-2 sm:gap-4 animate-pulse">
                        @for ($i = 0; $i < 4; $i++) {{-- Menampilkan 4 kotak skeleton (bisa ditambah sesuai kebutuhan) --}}
                            <div class="flex flex-col items-center justify-center gap-2 p-2 sm:p-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
                                {{-- Box Icon Skeleton --}}
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-slate-100 dark:bg-slate-700 rounded-xl"></div>
                                {{-- Text Skeleton --}}
                                <div class="h-2 w-10 bg-slate-100 dark:bg-slate-700 rounded"></div>
                            </div>
                        @endfor
                    </div>

                    <div 
                        x-show="!isLoading" 
                        x-cloak x-transition.opacity.duration.500ms
                        class="grid grid-cols-4 sm:grid-cols-4 lg:grid-cols-8 gap-2 sm:gap-4"
                    >
                        
                        {{-- Posting --}}
                        <a href="{{ route('posting.jasa') }}" class="group flex flex-col items-center justify-center gap-2 p-2 sm:p-3 bg-white dark:bg-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 border border-slate-100 dark:border-slate-700">
                            <div class="py-1 px-2 sm:py-2 sm:px-2 leading-none bg-blue-50 dark:bg-blue-900/30 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <i class="bi bi-megaphone text-xl text-blue-600 dark:text-blue-400 group-hover:text-white"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-semibold text-slate-600 dark:text-slate-300">Posting</span>
                        </a>

                        {{-- Riwayat --}}
                        <a href="{{ route('riwayat.technician') }}" class="group flex flex-col items-center justify-center gap-2 p-2 sm:p-3 bg-white dark:bg-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 border border-slate-100 dark:border-slate-700">
                            <div class="py-1 px-2 sm:py-2 sm:px-2 bg-amber-50 dark:bg-amber-900/30 rounded-xl group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                <i class="bi bi-clock-history text-xl text-amber-600 dark:text-amber-400 group-hover:text-white"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-semibold text-slate-600 dark:text-slate-300">Riwayat</span>
                        </a>

                        {{-- Jasa --}}
                        <a href="{{ route('jasa.technician') }}" class="group flex flex-col items-center justify-center gap-2 p-2 sm:p-3 bg-white dark:bg-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 border border-slate-100 dark:border-slate-700">
                            <div class="py-1 px-2 sm:py-2 sm:px-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                                <i class="bi bi-wrench-adjustable-circle text-xl text-emerald-600 dark:text-emerald-400 group-hover:text-white"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-semibold text-slate-600 dark:text-slate-300">Jasa</span>
                        </a>

                        {{-- Laporan --}}
                        <a href="{{ route('laporan.technician') }}" class="group flex flex-col items-center justify-center gap-2 p-2 sm:p-3 bg-white dark:bg-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 border border-slate-100 dark:border-slate-700">
                            <div class="py-1 px-2 sm:py-2 sm:px-2 bg-rose-50 dark:bg-rose-900/30 rounded-xl group-hover:bg-rose-500 group-hover:text-white transition-colors">
                                {{-- Menggunakan bi-clipboard-data untuk laporan data/statistik --}}
                                <i class="bi bi-clipboard-data text-xl text-rose-600 dark:text-rose-400 group-hover:text-white"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-semibold text-slate-600 dark:text-slate-300">Laporan</span>
                        </a>

                    </div>

                </div>

            

                {{-- Pekerjaan --}}
                {{-- <livewire:services.dashboard-technician.pekerjaan/> --}}

                {{-- List Antrian --}}
                <livewire:services.dashboard-technician.list-antrian/>

            @else
                <div class="pb-12 sm:pb-0">
                    <livewire:services.dashboard-technician.form-technician/>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>
