<div 
    class="space-y-4"
    x-data="{
        showLightbox: false,
        lightboxImage: '',
        lightboxName: '',
        openLightbox(url, name) {
            this.lightboxImage = url;
            this.lightboxName = name;
            this.showLightbox = true;
        }
    }"
>
    {{-- BAR KOLOM PENCARIAN --}}
    <div class="mb-4 max-w-md">
        <div class="relative rounded-xl shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <i class="bi bi-search text-xs sm:text-sm"></i>
            </div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama pelanggan atau nama jasa..."
                class="w-full text-xs sm:text-sm pl-9 pr-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
            >
        </div>
    </div>

    {{-- DATA TABLE RESPONSIVE --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50/70 dark:bg-slate-800/50 border-b border-gray-100 dark:border-gray-800 text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3.5 sm:px-6">Pelanggan / Order ID</th>
                        <th class="px-4 py-3.5 sm:px-6">Layanan Yang Dipesan</th>
                        <th class="px-4 py-3.5 sm:px-6">Total Biaya</th>
                        <th class="px-4 py-3.5 sm:px-6">Status Terakhir</th>
                        <th class="px-4 py-3.5 sm:px-6 text-center">Tanggal Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs sm:text-sm">
                    @forelse($data_transaksi as $item)
                        @php
                            $customerUser = $item->customer->user ?? null;
                            $customerName = $customerUser->name ?? 'Pelanggan';
                            $customerAvatar = $customerUser->avatar ?? null;
                            
                            $avatarUrl = ($customerAvatar && !str_starts_with($customerAvatar, 'default'))
                                ? (Str::startsWith($customerAvatar, ['http://', 'https://']) ? $customerAvatar : asset('storage/' . $customerAvatar))
                                : asset('assets/default_profile/default_profile_teknisi.webp'); // Fallback default

                            $statusNow = $item->latestStatus->status_order ?? 'menunggu_konfirmasi';
                        @endphp
                        <tr wire:key="transaction-row-{{ $item->id }}" class="hover:bg-slate-50/40 dark:hover:bg-slate-800/20 transition-colors">
                            
                            {{-- KOLOM 1: DETAIL CUSTOMER (DENGAN LIGHTBOX AVATAR) --}}
                            <td class="px-4 py-3 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div 
                                        @click="openLightbox('{{ $avatarUrl }}', '{{ $customerName }}')"
                                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl overflow-hidden bg-slate-100 border border-gray-100 dark:border-gray-800 flex-shrink-0 shadow-sm cursor-zoom-in hover:scale-105 hover:border-indigo-400 dark:hover:border-indigo-500 transition-all duration-200"
                                        title="Klik untuk memperbesar foto pelanggan"
                                    >
                                        <img src="{{ $avatarUrl }}" alt="Avatar {{ $customerName }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 truncate max-w-[140px] sm:max-w-[180px]">
                                            {{ $customerName }}
                                        </p>
                                        <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest">
                                            #{{ $item->id }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- KOLOM 2: NAMA JASA YANG DIPESAN --}}
                            <td class="px-4 py-3 sm:px-6 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg overflow-hidden bg-slate-50 border border-slate-100 shrink-0">
                                        <img src="{{ asset('storage/' . ($item->jasa->thumbnails[0] ?? 'default.jpg')) }}" class="w-full h-full object-cover">
                                    </div>
                                    <p class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-[160px] sm:max-w-[240px]" title="{{ $item->jasa->nama_jasa ?? 'Layanan' }}">
                                        {{ $item->jasa->nama_jasa ?? 'Layanan Servis' }}
                                    </p>
                                </div>
                            </td>

                            {{-- KOLOM 3: NOMINAL HARGA TOTAL --}}
                            <td class="px-4 py-3 sm:px-6 align-middle">
                                <p class="font-black text-slate-900 dark:text-white tabular-nums">
                                    Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                </p>
                            </td>

                            {{-- KOLOM 4: BADGE STATUS DINAMIS BERDASARKAN STATUS TERBARU --}}
                            <td class="px-4 py-3 sm:px-6 align-middle">
                                @php
                                    $statusConfig = match($statusNow) {
                                        'selesai_total'       => ['bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 border-green-100', 'check-circle-fill', 'Selesai & Lunas'],
                                        'ditolak', 'dibatalkan'=> ['bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400 border-red-100', 'x-circle-fill', 'Dibatalkan'],
                                        'selesai'             => ['bg-orange-50 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400 border-orange-100', 'clock-fill', 'Menunggu Pembayaran'],
                                        'pembayaran_ditolak'  => ['bg-rose-50 text-rose-700 dark:bg-rose-900/20 dark:text-rose-400 border-rose-100', 'exclamation-octagon-fill', 'Bukti Ditolak'],
                                        default               => ['bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 border-blue-100', 'hourglass-split', ucwords(str_replace('_', ' ', $statusNow))],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md border text-[10px] font-bold tracking-wide {{ $statusConfig[0] }} dark:border-transparent">
                                    <i class="bi bi-{{ $statusConfig[1] }} text-[9px]"></i>
                                    {{ $statusConfig[2] }}
                                </span>
                            </td>

                            {{-- KOLOM 5: TANGGAL JADWAL SERVIS --}}
                            <td class="px-4 py-3 sm:px-6 align-middle text-center text-[11px] sm:text-xs text-gray-400 dark:text-gray-500 font-medium">
                                {{ \Carbon\Carbon::parse($item->order_date)->translatedFormat('d F Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500 italic">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="bi bi-receipt text-3xl opacity-40"></i>
                                    <p class="text-xs sm:text-sm font-medium">Belum ada riwayat data transaksi pemesanan terekam di sistem.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION LINK --}}
    @if($data_transaksi->hasPages())
        <div class="pt-2">
            {{ $data_transaksi->links() }}
        </div>
    @endif

    {{-- GLOBAL LIGHTBOX AVATAR POP-UP ELEMENT --}}
    <template x-teleport="body">
        <div 
            x-show="showLightbox" 
            class="fixed inset-0 z-[99999] flex flex-col items-center justify-center bg-slate-950/95 p-4 backdrop-blur-sm" 
            style="display: none;"
            @keydown.escape.window="showLightbox = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <button @click="showLightbox = false" class="absolute top-5 right-5 text-white/70 hover:text-white p-2.5 rounded-full bg-white/5 hover:bg-white/10 transition-all duration-200">
                <i class="bi bi-x-lg text-xl sm:text-2xl"></i>
            </button>
            <div class="max-w-3xl w-full flex flex-col items-center justify-center" @click.away="showLightbox = false">
                <p x-text="lightboxName" class="text-white font-bold text-sm sm:text-base mb-4 tracking-wider uppercase bg-white/5 px-4 py-1.5 rounded-full border border-white/10"></p>
                <img 
                    :src="lightboxImage" 
                    :alt="lightboxName" 
                    class="max-w-full max-h-[70vh] sm:max-h-[75vh] rounded-2xl object-contain shadow-2xl border border-white/10"
                    x-show="showLightbox"
                    x-transition:enter="transition ease-out duration-300 transform scale-95"
                    x-transition:enter-start="scale-95"
                    x-transition:enter-end="scale-100"
                >
            </div>
        </div>
    </template>
</div>