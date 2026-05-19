<div class="space-y-6 pb-16"
     x-data="{
         showServiceModal: true,
         showCheckoutModal: false,
         tipe: '{{ $tipeLayanan ?? $order->tipe_layanan ?? $order->jasa->tipe_layanan ?? 'panggilan' }}'
     }">

    @php
        $orderDateTime = \Carbon\Carbon::parse($order->order_date->format('Y-m-d') . ' ' . $order->order_time);
        $now = \Carbon\Carbon::now();
        $isExpired = $orderDateTime->isPast();
    @endphp

    @if($isExpired)
        {{-- Alert Expired --}}
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                    <i class="bi bi-exclamation-triangle-fill text-red-600"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-red-800 mb-1">Waktu Pesanan Telah Lewat</h4>
                    <p class="text-xs text-red-600 mb-3">
                        Tanggal dan waktu service yang Anda pilih ({{ $orderDateTime->translatedFormat('l, d F Y') }} pukul {{ date('H:i', strtotime($order->order_time)) }}) sudah melewati waktu saat ini.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Info Jasa --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Detail Jasa</h3>
        </div>
        
        <div class="p-4 flex gap-4">
            {{-- Thumbnail --}}
            <div class="flex-shrink-0 w-24 h-24">
                <img 
                    src="{{ asset('storage/' . ($order->jasa->first_thumbnail?? 'default.jpg')) }}" 
                    alt="{{ $order->jasa->nama_jasa }}"
                    class="w-full h-full object-cover rounded-lg border border-gray-200"
                >
            </div>
            
            {{-- Info --}}
            <div class="flex-1 min-w-0 flex flex-col justify-center">
                <h4 class="text-base font-semibold text-gray-900 truncate">
                    {{ $order->jasa->nama_jasa }}
                </h4>

                <div class="mt-1.5">
                    @php
                        // Mengambil tipe layanan dari tabel order (jika ada) atau fallback ke tabel jasa
                        $tipeLayanan = $order->tipe_layanan ?? $order->jasa->tipe_layanan ?? 'panggilan';
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border 
                        {{ $tipeLayanan === 'panggilan' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-purple-50 text-purple-700 border-purple-200' }}">
                        <i class="bi {{ $tipeLayanan === 'panggilan' ? 'bi-house-door-fill' : 'bi-shop' }}"></i>
                        {{ $tipeLayanan === 'panggilan' ? 'Panggilan ke Rumah' : 'Bawa ke Bengkel' }}
                    </span>

                    @if($tipeLayanan === 'panggilan')
                        <div class="mt-2 flex items-start gap-2 px-3 py-2.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-lg">
                            <i class="bi bi-house-door-fill text-blue-500 dark:text-blue-400 text-sm flex-shrink-0 mt-0.5"></i>
                            <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                                Teknisi akan <span class="font-semibold">datang ke lokasi Anda</span> sesuai jadwal. Pastikan Anda berada di tempat saat teknisi tiba.
                            </p>
                        </div>
                    @else
                        <div class="mt-2 flex items-start gap-2 px-3 py-2.5 bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800 rounded-lg">
                            <i class="bi bi-shop text-purple-500 dark:text-purple-400 text-sm flex-shrink-0 mt-0.5"></i>
                            <p class="text-xs text-purple-700 dark:text-purple-300 leading-relaxed">
                                Layanan ini dilakukan di bengkel teknisi. <span class="font-semibold">Bawa perangkat Anda</span> ke lokasi bengkel sesuai jadwal yang dipilih.
                            </p>
                        </div>
                    @endif
                </div>

                
                <p class="text-sm text-gray-500 mt-1">
                    {{ $order->jasa->technician->nama_asli ?? 'Teknisi' }}
                </p>
                <div class="flex items-center gap-2 mt-3">
                    <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-md">
                        {{ date('H:i', strtotime($order->order_time)) }}
                    </span>
                    <span class="text-gray-300">|</span>
                    <span class="text-xs text-gray-500">
                        {{ $order->order_date->translatedFormat('d F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Pesanan --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Informasi Pesanan</h3>
        </div>
        
        <div class="p-4 space-y-5">
            {{-- Tanggal & Waktu --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2">Tanggal Service</p>
                    <div class="flex items-center gap-2 text-sm text-gray-900">
                        <i class="bi bi-calendar3 text-gray-400"></i>
                        <span class="font-medium">{{ $order->order_date->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2">Waktu Service</p>
                    <div class="flex items-center gap-2 text-sm text-gray-900">
                        <i class="bi bi-clock text-gray-400"></i>
                        <span class="font-medium">{{ date('H:i', strtotime($order->order_time)) }}</span>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200">

            {{-- Keluhan --}}
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-3">Keluhan yang Dipilih</p>
                <div class="flex flex-wrap gap-2">
                    @forelse($order->keluhan ?? [] as $keluhan)
                        <span class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg">
                            {{ $keluhan }}
                        </span>
                    @empty
                        <p class="text-sm text-gray-400 italic">Tidak ada keluhan yang dipilih</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Layanan Tambahan - BISA EDIT --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden" x-data="{ openModal: null }">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Layanan Tambahan</h3>
            <span class="text-xs text-gray-400 font-normal normal-case">(Opsional)</span>
        </div>

        <div class="p-4 space-y-3">
            @forelse($order->jasa->layanan_tambahan as $indexGrup => $grup)
                @php
                    $selectedInThisGroup = $layanan_tambahan[$indexGrup] ?? [];
                    $selectedCount = count($selectedInThisGroup);
                @endphp

                {{-- Button Trigger --}}
                <button 
                    type="button"
                    @click="openModal = {{ $indexGrup }}"
                    class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl border transition-all duration-200 {{ $selectedCount > 0 ? 'border-indigo-400 bg-indigo-50/50' : 'border-gray-200 bg-white hover:border-indigo-300 hover:bg-gray-50' }}"
                >
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 {{ $selectedCount > 0 ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-500' }} rounded-lg flex items-center justify-center shrink-0 transition-colors">
                            <i class="bi {{ $selectedCount > 0 ? 'bi-check-circle-fill' : 'bi-plus-circle' }}"></i>
                        </div>
                        <div class="min-w-0 text-left">
                            <span class="font-semibold text-gray-800 text-sm block truncate">{{ $grup['judul'] }}</span>
                            @if($selectedCount > 0)
                                <span class="text-xs text-indigo-600 font-medium">
                                    {{ $selectedCount }} item dipilih
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Klik untuk pilih layanan</span>
                            @endif
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-gray-400"></i>
                </button>

                {{-- Bottom Sheet Modal - Z-INDEX PALING TINGGI --}}
                <div 
                    x-show="openModal === {{ $indexGrup }}" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-[2147483647] flex items-end justify-center bg-black/60 backdrop-blur-sm"
                    style="display: none;"
                    x-cloak
                >
                    {{-- Backdrop Click to Close --}}
                    <div class="absolute inset-0" @click="openModal = null"></div>

                    {{-- Modal Content --}}
                    <div 
                        x-show="openModal === {{ $indexGrup }}"
                        x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="translate-y-full"
                        x-transition:enter-end="translate-y-0"
                        x-transition:leave="transition ease-in duration-200 transform"
                        x-transition:leave-start="translate-y-0"
                        x-transition:leave-end="translate-y-full"
                        @click.away="openModal = null"
                        class="relative w-full max-w-lg bg-white rounded-t-3xl shadow-2xl max-h-[85vh] flex flex-col"
                    >
                        {{-- Drag Handle --}}
                        <div class="pt-4 pb-2 flex justify-center">
                            <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
                        </div>

                        {{-- Modal Header --}}
                        <div class="px-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                            <h4 class="text-lg font-bold text-gray-900">{{ $grup['judul'] }}</h4>
                            <button 
                                @click="openModal = null" 
                                class="w-9 h-9 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="flex-1 overflow-y-auto p-6 space-y-3">
                            @foreach($grup['items'] as $indexItem => $item)
                                @php 
                                    $uniqueId = 'layanan-' . $indexGrup . '-' . $indexItem;
                                    $cleanHarga = (int) str_replace(['.', ','], '', $item['harga']); 
                                @endphp

                                <label 
                                    for="{{ $uniqueId }}" 
                                    class="group flex items-center justify-between p-4 bg-gray-50 border-2 border-gray-100 rounded-xl cursor-pointer transition-all duration-200 hover:border-indigo-300 hover:bg-indigo-50/30 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50"
                                >
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <input 
                                                type="checkbox" 
                                                id="{{ $uniqueId }}" 
                                                name="layanan_tambahan[{{ $indexGrup }}]" 
                                                value="{{ json_encode(['nama' => $item['nama'], 'harga' => $cleanHarga]) }}"
                                                wire:model.live="layanan_tambahan.{{ $indexGrup }}"
                                                class="peer w-5 h-5 text-indigo-600 border-2 border-gray-300 rounded-lg focus:ring-indigo-500 focus:ring-2 checked:border-indigo-600"
                                            >
                                            <i class="bi bi-check-lg absolute inset-0 m-auto text-white text-sm opacity-0 peer-checked:opacity-100 pointer-events-none flex items-center justify-center"></i>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 group-has-[:checked]:text-indigo-900">{{ $item['nama'] }}</span>
                                    </div>
                                    <span class="text-sm font-bold text-indigo-600">Rp {{ number_format($cleanHarga, 0, ',', '.') }}</span>
                                </label>
                            @endforeach
                        </div>

                        {{-- Modal Footer --}}
                        <div class="p-6 border-t border-gray-100 bg-gray-50">
                            <button 
                                @click="openModal = null"
                                class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-indigo-200"
                            >
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <i class="bi bi-tools text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Jasa ini tidak memiliki layanan tambahan</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Ringkasan Harga --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Ringkasan Harga</h3>
        </div>
        
        <div class="p-5 space-y-4">
            {{-- Harga Jasa Dasar --}}
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Harga Jasa Dasar</span>
                <span class="font-semibold text-gray-900">
                    Rp {{ number_format($order->jasa->harga_jasa ?? 0, 0, ',', '.') }}
                </span>
            </div>
            
            {{-- Detail Layanan Tambahan per Grup --}}
            @php 
                $layananTambahan = $layanan_tambahan ?? [];
                $totalTambahan = 0;
            @endphp

            @if(!empty($layananTambahan))
                <div class="space-y-3" wire:key="summary-tambahan-detail">
                    @foreach($layananTambahan as $indexGrup => $items)
                        @php
                            $grupInfo = $order->jasa->layanan_tambahan[$indexGrup] ?? null;
                            $judulGrup = $grupInfo['judul'] ?? 'Layanan Tambahan';
                            $totalGrup = 0;
                        @endphp
                        
                        @if(!empty($items))
                            <div class="bg-indigo-50 rounded-lg border border-indigo-100 overflow-hidden">
                                {{-- Header Grup --}}
                                <div class="px-3 py-2 bg-indigo-100/50 border-b border-indigo-100">
                                    <span class="text-xs font-bold text-indigo-800 uppercase tracking-wide">{{ $judulGrup }}</span>
                                </div>
                                
                                {{-- List Items --}}
                                <div class="p-3 space-y-2">
                                    @foreach($items as $item)
                                        @php
                                            $itemData = is_string($item) ? json_decode($item, true) : $item;
                                            $namaItem = $itemData['nama'] ?? '-';
                                            $hargaItem = (int) str_replace(['.', ','], '', $itemData['harga'] ?? 0);
                                            $totalGrup += $hargaItem;
                                            $totalTambahan += $hargaItem;
                                        @endphp
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center">
                                                <span class="text-gray-700">{{ $namaItem }}</span>
                                            </div>
                                            <span class="font-medium text-gray-900">Rp {{ number_format($hargaItem, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                
                                {{-- Subtotal Grup --}}
                                <div class="px-3 py-2 bg-indigo-100/30 border-t border-indigo-100 flex items-center justify-between">
                                    <span class="text-xs font-semibold text-indigo-700">Subtotal {{ $judulGrup }}</span>
                                    <span class="text-sm font-bold text-indigo-700">Rp {{ number_format($totalGrup, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                {{-- Total Layanan Tambahan --}}
                <div class="flex items-center justify-between text-sm p-3 bg-gray-100 rounded-lg border border-gray-200">
                    <span class="font-semibold text-gray-700">Total Layanan Tambahan</span>
                    <span class="font-bold text-gray-900">Rp {{ number_format($totalTambahan, 0, ',', '.') }}</span>
                </div>
            @endif
            
            <hr class="border-gray-200">
            
            {{-- Total Harga Keseluruhan --}}
            <div class="flex items-center justify-between pt-2">
                <span class="text-base font-bold text-gray-900">Total Harga</span>
                <span class="text-base font-bold text-blue-600">
                    Rp {{ number_format(($order->jasa->harga_jasa ?? 0) + $totalTambahan, 0, ',', '.') }}
                </span>
            </div>

            <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                <div class="flex gap-2">
                    <i class="bi bi-info-circle-fill text-blue-500"></i>
                    <p class="text-xs text-blue-800 leading-relaxed">
                        <span class="font-bold">Informasi Biaya:</span> 
                        Nilai transaksi yang tercantum adalah harga prakiraan. Biaya final akan dikonfirmasi kembali oleh teknisi setelah proses diagnosa kerusakan selesai dilakukan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    @if($order->lacak_pesanan->first()->status_order === 'keranjang')
        <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-200 z-40">
            <div class="max-w-7xl mx-auto flex gap-3">

                @if($isExpired)
                    <a 
                        href="{{ route('detail-product', $order->id_jasa) }}" 
                        wire:navigate
                        class="flex-1 py-3.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2 shadow-lg shadow-red-200"
                    >
                        <i class="bi bi-pencil-square"></i>
                        <span>Pesan Ulang</span>
                    </a>
                @else
                    <button
                        type="button"
                        @click="showCheckoutModal = true"
                        class="flex-1 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                        <i class="bi bi-credit-card"></i>
                        <span>Pesan Sekarang</span>
                    </button>
                @endif
                {{-- @if($order->status === 'keranjang')
                    <button 
                        wire:click="checkout"
                        class="flex-1 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2 shadow-lg shadow-blue-200"
                    >
                        <i class="bi bi-credit-card"></i>
                        <span>Checkout Sekarang</span>
                    </button>
                @elseif($order->status === 'menunggu_pembayaran')
                    <button 
                        wire:click="bayar"
                        class="flex-1 py-3.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2 shadow-lg shadow-green-200"
                    >
                        <i class="bi bi-wallet2"></i>
                        <span>Bayar Sekarang</span>
                    </button>
                @elseif($order->status === 'diproses' || $order->status === 'selesai')
                    <a 
                        href="{{ route('chat', $order->chat_room?->id) }}"
                        class="flex-1 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2 shadow-lg shadow-indigo-200"
                    >
                        <i class="bi bi-chat-dots"></i>
                        <span>Chat Teknisi</span>
                    </a>
                @endif --}}
                
                {{-- <button 
                    wire:click="batalkanPesanan"
                    wire:confirm="Apakah Anda yakin ingin membatalkan pesanan ini?"
                    class="px-5 py-3.5 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors"
                >
                    <i class="bi bi-x-lg"></i>
                </button> --}}
            </div>
        </div>
    @endif

    {{-- ── Modal Info Tipe Layanan (auto-open) ── --}}
<div x-show="showServiceModal"
     x-transition.opacity
     @keydown.escape.window="showServiceModal = false"
     style="display:none"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     role="dialog"
     aria-modal="true">

    <div class="absolute inset-0" @click="showServiceModal = false" aria-hidden="true"></div>

    <div class="relative w-full max-w-sm bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">

        {{-- Panggilan --}}
        <template x-if="tipe === 'panggilan'">
            <div>
                <div class="bg-blue-50 dark:bg-blue-900/20 px-5 pt-6 pb-4 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center mb-3">
                        <i class="bi bi-house-door-fill text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Layanan Home Service</h3>
                    <span class="mt-1.5 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        <i class="bi bi-geo-alt-fill text-xs"></i>
                        Teknisi datang ke lokasi Anda
                    </span>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed text-center">
                        Layanan yang dipilih <span class="font-semibold text-gray-800 dark:text-white">hanya tersedia untuk kunjungan ke rumah</span> dan tidak dapat dilakukan di bengkel teknisi.
                    </p>
                    <div class="space-y-2 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-start gap-2.5">
                            <i class="bi bi-check-circle-fill text-blue-500 mt-0.5 flex-shrink-0"></i>
                            <span>Teknisi akan datang sesuai jadwal yang Anda pilih</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="bi bi-check-circle-fill text-blue-500 mt-0.5 flex-shrink-0"></i>
                            <span>Pastikan alamat yang dimasukkan sudah benar</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="bi bi-check-circle-fill text-blue-500 mt-0.5 flex-shrink-0"></i>
                            <span>Harap berada di lokasi saat teknisi tiba</span>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200 text-center">
                        Apakah Anda bersedia melanjutkan layanan ini?
                    </p>
                </div>
                <div class="flex gap-2 px-5 pb-5">
                    <a href="{{ url()->previous() }}"
                       class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300 text-center hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Kembali
                    </a>
                    <button type="button"
                            @click="showServiceModal = false"
                            class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-sm font-semibold text-white transition-all">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </template>

        {{-- Bengkel --}}
        <template x-if="tipe === 'bengkel'">
            <div>
                <div class="bg-purple-50 dark:bg-purple-900/20 px-5 pt-6 pb-4 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center mb-3">
                        <i class="bi bi-tools text-2xl text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Layanan Bengkel</h3>
                    <span class="mt-1.5 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">
                        <i class="bi bi-shop text-xs"></i>
                        Kunjungi lokasi bengkel teknisi
                    </span>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed text-center">
                        Layanan yang dipilih <span class="font-semibold text-gray-800 dark:text-white">hanya tersedia di bengkel teknisi</span> dan tidak dapat dilakukan dengan kunjungan ke rumah.
                    </p>
                    <div class="space-y-2 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-start gap-2.5">
                            <i class="bi bi-check-circle-fill text-purple-500 mt-0.5 flex-shrink-0"></i>
                            <span>Bawa perangkat Anda ke lokasi bengkel teknisi</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="bi bi-check-circle-fill text-purple-500 mt-0.5 flex-shrink-0"></i>
                            <span>Datang sesuai jadwal yang sudah dipilih</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="bi bi-check-circle-fill text-purple-500 mt-0.5 flex-shrink-0"></i>
                            <span>Alamat bengkel akan ditampilkan setelah pesanan dikonfirmasi</span>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200 text-center">
                        Apakah Anda bersedia melanjutkan layanan ini?
                    </p>
                </div>
                <div class="flex gap-2 px-5 pb-5">
                    <a href="{{ url()->previous() }}"
                       class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300 text-center hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Kembali
                    </a>
                    <button type="button"
                            @click="showServiceModal = false"
                            class="flex-1 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 active:scale-[0.98] text-sm font-semibold text-white transition-all">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

{{-- ── Modal Konfirmasi Checkout ── --}}
<div x-show="showCheckoutModal"
     x-transition.opacity
     @keydown.escape.window="showCheckoutModal = false"
     style="display:none"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     role="dialog"
     aria-modal="true">

    <div class="absolute inset-0" @click="showCheckoutModal = false" aria-hidden="true"></div>

    <div class="relative w-full max-w-sm bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">

        {{-- Konfirmasi panggilan --}}
        <template x-if="tipe === 'panggilan'">
            <div>
                <div class="bg-blue-50 dark:bg-blue-900/20 px-5 pt-6 pb-4 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center mb-3">
                        <i class="bi bi-house-door-fill text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Konfirmasi Pesanan</h3>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed text-center">
                        Dengan memesan, Anda menyetujui bahwa layanan ini adalah <span class="font-semibold text-gray-800 dark:text-white">home service</span> — teknisi akan datang ke lokasi Anda.
                    </p>
                    <div class="flex items-start gap-2.5 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800">
                        <i class="bi bi-info-circle-fill text-blue-500 flex-shrink-0 mt-0.5"></i>
                        <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                            Pastikan lokasi Anda benar dan anda berada di rumah pada <span class="font-semibold">{{ $order->order_date->translatedFormat('d F Y') }}</span> pukul <span class="font-semibold">{{ date('H:i', strtotime($order->order_time)) }}</span>.
                        </p>
                    </div>
                </div>
                <div class="flex gap-2 px-5 pb-5">
                    <button type="button"
                            @click="showCheckoutModal = false"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Batal
                    </button>
                    <button type="button"
                            @click="showCheckoutModal = false"
                            wire:click="checkout"
                            class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-sm font-semibold text-white transition-all flex items-center justify-center gap-1.5">
                        <i class="bi bi-credit-card text-sm"></i>
                        Pesan Sekarang
                    </button>
                </div>
            </div>
        </template>

        {{-- Konfirmasi bengkel --}}
        <template x-if="tipe === 'bengkel'">
            <div>
                <div class="bg-purple-50 dark:bg-purple-900/20 px-5 pt-6 pb-4 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center mb-3">
                        <i class="bi bi-tools text-2xl text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Konfirmasi Pesanan</h3>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed text-center">
                        Dengan memesan, Anda menyetujui bahwa layanan ini dilakukan di <span class="font-semibold text-gray-800 dark:text-white">bengkel teknisi</span> — Anda perlu membawa perangkat ke lokasi.
                    </p>
                    <div class="flex items-start gap-2.5 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-100 dark:border-purple-800">
                        <i class="bi bi-info-circle-fill text-purple-500 flex-shrink-0 mt-0.5"></i>
                        <p class="text-xs text-purple-700 dark:text-purple-300 leading-relaxed">
                            Harap tiba di bengkel pada <span class="font-semibold">{{ $order->order_date->translatedFormat('d F Y') }}</span> pukul <span class="font-semibold">{{ date('H:i', strtotime($order->order_time)) }}</span>.
                        </p>
                    </div>
                </div>
                <div class="flex gap-2 px-5 pb-5">
                    <button type="button"
                            @click="showCheckoutModal = false"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Batal
                    </button>
                    <button type="button"
                            @click="showCheckoutModal = false"
                            wire:click="checkout"
                            class="flex-1 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 active:scale-[0.98] text-sm font-semibold text-white transition-all flex items-center justify-center gap-1.5">
                        <i class="bi bi-credit-card text-sm"></i>
                        Pesan Sekarang
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

</div>