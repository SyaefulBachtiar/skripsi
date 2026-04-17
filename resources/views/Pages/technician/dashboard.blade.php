<x-app-layout>

    <x-slot name="title">
        {{ 'Dashboard' }}
    </x-slot>

    <x-slot name="header">
        <livewire:layout.header-mobile/>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <livewire:services.address-card/>

            @if(!empty($data->spesialisasi))

                @if(!empty($data->foto_wajah && $data->foto_kegiatan))

                    @if($data->verifikasi === 'diverifikasi')

                        <livewire:services.dashboard-technician.quick-access/>

                        {{-- List Antrian --}}
                        <livewire:services.dashboard-technician.list-antrian/>

                    @else

                        {{-- Status Verifikasi --}}
                        <div class="bg-white rounded-2xl border border-gray-100 p-6 overflow-hidden relative">

                            @if($data->verifikasi === 'ditolak')

                                {{-- Ditolak --}}
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 flex-shrink-0 rounded-xl bg-red-50 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm font-medium text-gray-800">Akun ditolak</span>
                                            <span class="text-xs font-medium bg-red-50 text-red-600 px-2.5 py-0.5 rounded-full">Ditolak</span>
                                        </div>
                                        <p class="text-sm text-gray-500 leading-relaxed mt-1">{{ $data->alasan_ditolak }}</p>
                                        <a href="{{ route('profile.technician') }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                            Perbarui dokumen
                                        </a>
                                    </div>
                                </div>

                            @else

                                {{-- Menunggu Verifikasi --}}
                                <div class="flex justify-center">
                                    <div class="relative w-16 h-16">
                                        <div class="absolute inset-0 bg-amber-300 rounded-full animate-ping opacity-20"></div>
                                        <div class="relative w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center border-2 border-white">
                                            <svg class="w-7 h-7 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-5 space-y-2">
                                    <h3 class="text-base font-medium text-gray-800">Akun sedang diverifikasi</h3>
                                    <p class="text-sm text-gray-500 leading-relaxed max-w-sm mx-auto">
                                        Terima kasih, <span class="font-medium text-gray-700">{{ auth()->user()->name }}</span>.
                                        Admin sedang memeriksa dokumen Anda. Proses ini biasanya selesai dalam 1×24 jam.
                                    </p>
                                </div>

                                {{-- Progress Steps --}}
                                <div class="mt-6 flex items-center justify-center">
                                    {{-- Step 1 --}}
                                    <div class="flex flex-col items-center gap-1.5">
                                        <div class="w-7 h-7 rounded-full bg-green-500 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                        </div>
                                        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Terkirim</span>
                                    </div>

                                    <div class="w-10 h-px bg-green-200 mb-4 mx-1"></div>

                                    {{-- Step 2 --}}
                                    <div class="flex flex-col items-center gap-1.5">
                                        <div class="w-7 h-7 rounded-full bg-amber-500 flex items-center justify-center animate-pulse">
                                            <span class="w-2 h-2 bg-white rounded-full"></span>
                                        </div>
                                        <span class="text-[10px] font-medium text-amber-600 uppercase tracking-wider">Ditinjau</span>
                                    </div>

                                    <div class="w-10 h-px bg-gray-100 mb-4 mx-1"></div>

                                    {{-- Step 3 --}}
                                    <div class="flex flex-col items-center gap-1.5 opacity-40">
                                        <div class="w-7 h-7 rounded-full bg-gray-100 border border-gray-200"></div>
                                        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Selesai</span>
                                    </div>
                                </div>

                            @endif
                        </div>

                    @endif

                @else

                    <livewire:services.dashboard-technician.form-data-diri/>

                @endif

            @else

                {{-- Form Registrasi Teknisi --}}
                <div class="pb-12 sm:pb-0">
                    <livewire:services.dashboard-technician.form-technician/>
                </div>

            @endif

        </div>
    </div>

</x-app-layout>