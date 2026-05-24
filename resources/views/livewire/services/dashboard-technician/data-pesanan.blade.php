<div 
    class="p-3 sm:p-6 bg-white rounded-2xl shadow-sm border border-slate-200/80"
    x-data="{
        showAvatarModal: false,
        avatarModalImage: '',
        avatarModalName: '',
        openAvatarLightbox(url, name) {
            this.avatarModalImage = url;
            this.avatarModalName = name;
            this.showAvatarModal = true;
        }
    }"
>

    {{-- ══════════════════════════════════════
         HEADER
    ══════════════════════════════════════ --}}
    <div class="mb-5 flex items-center justify-between pb-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 sm:w-10 sm:h-10 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                <i class="bi bi-tools text-indigo-600 text-base sm:text-lg"></i>
            </div>
            <div>
                <h1 class="text-sm sm:text-base font-bold text-slate-800 leading-tight">Daftar Pekerjaan Aktif</h1>
                <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Update progres pengerjaan untuk pelanggan Anda.</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         LIST PESANAN
    ══════════════════════════════════════ --}}
    <div class="space-y-3 sm:space-y-4">
        @forelse($data as $order)
            @php
                $statusOrder    = $order->latestStatus->status_order ?? '';
                $isSelesai      = $statusOrder === 'selesai' || $statusOrder === 'sudah_dibayar';
                $isSudahDibayar = $statusOrder === 'sudah_dibayar';
                $isDitolak      = $statusOrder === 'pembayaran_ditolak';
            @endphp

            {{-- ── CARD ──────────────────────────────── --}}
            {{--
                x-data mencakup state modal tolak & konfirmasi sekaligus.
                orderId dipakai untuk wire:click agar tidak hardcode di dalam modal.
            --}}
            <div x-data="{
                     openUpdate:    false,
                     showTolak:     false,
                     showKonfirm:   false,
                     orderId:       {{ $order['id'] }}
                 }"
                 class="border border-slate-200 rounded-xl sm:rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow duration-200">

                {{-- ─ Card Header ───────────────────── --}}
                <div class="px-3 py-3 sm:px-5 sm:py-3.5 flex flex-col gap-2.5 bg-slate-50/70 border-b border-slate-200">

                    {{-- Baris 1: Identitas + Tombol --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0">
                            {{-- Avatar Customer --}}
                            @php
                                $customerUser = $order->customer->user ?? null;
                                $customerAvatar = $customerUser?->avatar;
                                $customerName = $customerUser?->name ?? 'Customer';

                                $avatarUrl = ($customerAvatar && !str_starts_with($customerAvatar, 'default'))
                                    ? (Str::startsWith($customerAvatar, ['http://', 'https://']) ? $customerAvatar : asset('storage/' . $customerAvatar))
                                    : null;
                            @endphp

                            <div 
                                @if($avatarUrl) @click="openAvatarLightbox('{{ $avatarUrl }}', '{{ $customerName }}')" @endif
                                class="w-9 h-9 sm:w-11 sm:h-11 shrink-0 rounded-lg sm:rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-slate-100 flex items-center justify-center transition-transform active:scale-95 {{ $avatarUrl ? 'cursor-zoom-in hover:border-indigo-400' : '' }}"
                            >
                                @if($avatarUrl)
                                    <img src="{{ $avatarUrl }}"
                                        alt="{{ $customerName }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <span class="text-sm font-bold text-slate-500 uppercase">
                                        {{ mb_substr($customerName, 0, 1) }}
                                    </span>
                                @endif
                            </div>

                            <div class="min-w-0">
                                {{-- Nama Customer --}}
                                <div class="flex items-center gap-1.5 mb-0.5 flex-wrap">
                                    <span class="text-xs font-bold text-slate-800 truncate">{{ $customerName }}</span>
                                    <span class="w-1 h-1 bg-slate-300 rounded-full shrink-0"></span>
                                    <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest whitespace-nowrap">
                                        #{{ $order['id'] }}
                                    </span>
                                </div>

                                {{-- Badge Status + Tanggal --}}
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @php
                                        $badgeConfig = match($statusOrder) {
                                            'dikonfirmasi'        => ['bg-blue-50',    'border-blue-200',   'text-blue-700',   'bi-check-circle',         'Dikonfirmasi'],
                                            'dikerjakan'          => ['bg-indigo-50',  'border-indigo-200', 'text-indigo-700', 'bi-tools',                'Dikerjakan'],
                                            'menunggu_sparepart'  => ['bg-amber-50',   'border-amber-200',  'text-amber-700',  'bi-hourglass-split',      'Menunggu Sparepart'],
                                            'hampir_selesai'      => ['bg-teal-50',    'border-teal-200',   'text-teal-700',   'bi-stars',                'Hampir Selesai'],
                                            'selesai'             => ['bg-orange-50',  'border-orange-200', 'text-orange-700', 'bi-clock',                'Menunggu Pembayaran'],
                                            'sudah_dibayar'       => ['bg-emerald-50', 'border-emerald-200','text-emerald-700','bi-cash-coin',            'Sudah Dibayar'],
                                            'pembayaran_ditolak'  => ['bg-rose-50',    'border-rose-200',   'text-rose-700',   'bi-exclamation-octagon',  'Pembayaran Ditolak'],
                                            default               => ['bg-slate-50',   'border-slate-200',  'text-slate-600',  'bi-circle',               ucwords(str_replace('_', ' ', $statusOrder))],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md border text-[10px] font-bold {{ $badgeConfig[0] }} {{ $badgeConfig[1] }} {{ $badgeConfig[2] }}">
                                        <i class="bi {{ $badgeConfig[3] }} text-[9px]"></i>
                                        {{ $badgeConfig[4] }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($order['order_date'])->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button @click="openUpdate = !openUpdate"
                                class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 sm:px-3.5 py-1.5 sm:py-2
                                       bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white
                                       text-[10px] sm:text-xs font-bold rounded-lg transition-colors
                                       shadow-sm shadow-indigo-200 shrink-0 whitespace-nowrap">
                            <i class="bi bi-pencil-square text-[10px] sm:text-[11px]"></i>
                            <span>Update / Detail</span>
                        </button>
                    </div>

                    {{-- Baris 2: Badge Status + Tombol Aksi (hanya jika selesai) --}}
                    @if($isSelesai || $statusOrder === 'pembayaran_ditolak')
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-100">
                            <div>
                                @if($isSudahDibayar)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-600 text-[10px] font-bold uppercase tracking-wider rounded-lg">
                                        <i class="bi bi-cash-coin text-[11px]"></i>
                                        Pelanggan Sudah Bayar
                                    </span>
                                @elseif($isDitolak)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 border border-rose-200 text-rose-600 text-[10px] font-bold uppercase tracking-wider rounded-lg shadow-sm animate-pulse">
                                        <i class="bi bi-exclamation-octagon-fill text-[11px]"></i>
                                        Konfirmasi Pembayaran Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-600 text-[10px] font-bold uppercase tracking-wider rounded-lg">
                                        <i class="bi bi-hourglass-split text-[10px]"></i>
                                        Menunggu Pembayaran
                                    </span>
                                @endif
                            </div>

                            @if($isSudahDibayar || $statusOrder === 'pembayaran_ditolak')
                                <div class="flex items-center gap-2 self-end sm:self-auto">
                                    @if ($isSudahDibayar)
                                        {{-- Tombol Tolak → buka modal tolak --}}
                                        <button type="button"
                                                @click="showTolak = true"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5
                                                    bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600
                                                    text-[10px] sm:text-xs font-bold uppercase tracking-wider rounded-lg transition-colors">
                                            <i class="bi bi-x-circle text-xs"></i>
                                            <span>Tolak</span>
                                        </button>
                                    @endif
                                    {{-- Tombol Konfirmasi → buka modal konfirmasi --}}
                                    <button type="button"
                                            @click="showKonfirm = true"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white
                                                text-[10px] sm:text-xs font-black uppercase tracking-wider rounded-lg transition-colors
                                                shadow-sm shadow-emerald-100">
                                        <i class="bi bi-check2-all text-xs"></i>
                                        <span>{{ $statusOrder === 'pembayaran_ditolak' ? 'sudah dibayar' : 'konfirmasi' }}</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- ─ Info Singkat ───────────────────── --}}
                <div class="px-3 py-3 sm:px-5 sm:py-4">
                    <div class="flex flex-col gap-3 sm:grid sm:grid-cols-2 sm:gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Daftar Keluhan</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($order['keluhan'] as $keluhan)
                                    <span class="px-2 py-0.5 sm:py-1 bg-slate-50 border border-slate-200 rounded-md text-[10px] sm:text-[11px] text-slate-600 font-medium">
                                        {{ $keluhan }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex flex-row sm:flex-col sm:items-end items-center justify-between pt-2.5 sm:pt-0 border-t border-slate-100 sm:border-0 sm:justify-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider sm:mb-1">Total Biaya Saat Ini</p>
                            <p class="text-lg sm:text-xl font-black text-slate-800 tabular-nums">
                                Rp {{ number_format($order['total_harga'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════
                     MODAL: TOLAK PEMBAYARAN
                ══════════════════════════════════════ --}}
                <div x-show="showTolak"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                     style="display: none;">

                    <div x-show="showTolak"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                         @click.outside="showTolak = false"
                         class="w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden">

                        {{-- Modal Header --}}
                        <div class="px-5 pt-5 pb-4 border-b border-slate-100 flex items-start gap-3">
                            <div class="w-10 h-10 shrink-0 bg-rose-100 rounded-xl flex items-center justify-center">
                                <i class="bi bi-x-circle-fill text-rose-500 text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-800 leading-tight">Tolak Pembayaran</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Order #{{ $order['id'] }}</p>
                            </div>
                            <button @click="showTolak = false"
                                    class="ml-auto shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                                <i class="bi bi-x-lg text-xs"></i>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="px-5 py-4 space-y-3">
                            <div class="bg-rose-50 border border-rose-100 rounded-xl p-3 flex gap-2.5">
                                <i class="bi bi-exclamation-triangle-fill text-rose-400 text-sm shrink-0 mt-0.5"></i>
                                <p class="text-[11px] text-rose-700 leading-relaxed">
                                    Tindakan ini akan <strong>menolak konfirmasi pembayaran</strong> dan mengembalikan status pesanan. Lakukan hanya jika dana belum masuk ke rekening Anda.
                                </p>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center">
                                <span class="text-[11px] text-slate-500 font-medium">Total Tagihan</span>
                                <span class="text-sm font-black text-slate-800 tabular-nums">
                                    Rp {{ number_format($order['total_harga'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-5 pb-5 flex gap-2.5">
                            <button type="button"
                                    @click="showTolak = false"
                                    class="flex-1 py-2.5 text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                                Batal
                            </button>
                            <button type="button"
                                    wire:click="tolakPembayaran({{ $order['id'] }})"
                                    wire:loading.attr="disabled"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5
                                           bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white
                                           text-xs font-bold rounded-xl transition-colors shadow-sm shadow-rose-100">
                                <i class="bi bi-x-circle"></i>
                                Ya, Tolak Pembayaran
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════
                     MODAL: KONFIRMASI PEMBAYARAN
                ══════════════════════════════════════ --}}
                <div x-show="showKonfirm"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                     style="display: none;">

                    <div x-show="showKonfirm"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                         @click.outside="showKonfirm = false"
                         class="w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden">

                        {{-- Modal Header --}}
                        <div class="px-5 pt-5 pb-4 border-b border-slate-100 flex items-start gap-3">
                            <div class="w-10 h-10 shrink-0 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <i class="bi bi-check-circle-fill text-emerald-500 text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-800 leading-tight">Konfirmasi Pembayaran</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Order #{{ $order['id'] }}</p>
                            </div>
                            <button @click="showKonfirm = false"
                                    class="ml-auto shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                                <i class="bi bi-x-lg text-xs"></i>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="px-5 py-4 space-y-3">
                            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 flex gap-2.5">
                                <i class="bi bi-info-circle-fill text-emerald-400 text-sm shrink-0 mt-0.5"></i>
                                <p class="text-[11px] text-emerald-700 leading-relaxed">
                                    Pastikan dana telah <strong>masuk ke rekening Anda</strong> sebelum mengkonfirmasi. Tindakan ini akan menyelesaikan pesanan secara permanen.
                                </p>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center">
                                <span class="text-[11px] text-slate-500 font-medium">Total Tagihan</span>
                                <span class="text-sm font-black text-emerald-600 tabular-nums">
                                    Rp {{ number_format($order['total_harga'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-5 pb-5 flex gap-2.5">
                            <button type="button"
                                    @click="showKonfirm = false"
                                    class="flex-1 py-2.5 text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                                Batal
                            </button>
                            <button type="button"
                                    wire:click="konfirmasiPembayaran({{ $order['id'] }})"
                                    wire:loading.attr="disabled"
                                    @click="showKonfirm = false"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5
                                           bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white
                                           text-xs font-bold rounded-xl transition-colors shadow-sm shadow-emerald-100">
                                <i class="bi bi-check2-all"></i>
                                Ya, Konfirmasi
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ─ Panel Collapsible ─────────────── --}}
                <div x-show="openUpdate" x-collapse class="border-t border-slate-200">
                    <div x-data="{ activeTab: '{{ $isSelesai ? 'rincian' : 'update' }}' }">

                        {{-- Tab Navigation --}}
                        <div class="flex border-b border-slate-200 bg-slate-50/60 overflow-x-auto">
                            @if(!$isSelesai)
                                <button @click="activeTab = 'update'"
                                        :class="activeTab === 'update' ? 'border-indigo-600 text-indigo-700 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 hover:bg-white/60'"
                                        class="flex-1 min-w-[72px] inline-flex items-center justify-center gap-1 py-2.5 sm:py-3 px-1.5 sm:px-4 text-[10px] sm:text-[11px] uppercase tracking-wide border-b-2 transition-all whitespace-nowrap">
                                    <i class="bi bi-arrow-repeat text-[11px]"></i>
                                    <span class="hidden xs:inline">Update</span> Progres
                                </button>
                                <button @click="activeTab = 'riwayat'"
                                        :class="activeTab === 'riwayat' ? 'border-indigo-600 text-indigo-700 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 hover:bg-white/60'"
                                        class="flex-1 min-w-[72px] inline-flex items-center justify-center gap-1 py-2.5 sm:py-3 px-1.5 sm:px-4 text-[10px] sm:text-[11px] uppercase tracking-wide border-b-2 transition-all whitespace-nowrap">
                                    <i class="bi bi-clock-history text-[11px]"></i> Progres
                                </button>
                                <button @click="activeTab = 'layanan'"
                                        :class="activeTab === 'layanan' ? 'border-indigo-600 text-indigo-700 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 hover:bg-white/60'"
                                        class="flex-1 min-w-[72px] inline-flex items-center justify-center gap-1 py-2.5 sm:py-3 px-1.5 sm:px-4 text-[10px] sm:text-[11px] uppercase tracking-wide border-b-2 transition-all whitespace-nowrap">
                                    <i class="bi bi-plus-circle text-[11px]"></i>
                                    <span class="hidden xs:inline">Tambah</span> Item
                                </button>
                            @else
                                @foreach(['Progres', 'Riwayat', 'Item'] as $lockedTab)
                                    <button disabled
                                            class="flex-1 min-w-[72px] inline-flex items-center justify-center gap-1 py-2.5 sm:py-3 px-1.5 sm:px-4 text-[10px] sm:text-[11px] uppercase tracking-wide border-b-2 border-transparent text-slate-300 cursor-not-allowed whitespace-nowrap">
                                        <i class="bi bi-lock-fill text-[10px]"></i> {{ $lockedTab }}
                                    </button>
                                @endforeach
                            @endif
                            <button @click="activeTab = 'rincian'"
                                    :class="activeTab === 'rincian' ? 'border-indigo-600 text-indigo-700 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 hover:bg-white/60'"
                                    class="flex-1 min-w-[72px] inline-flex items-center justify-center gap-1 py-2.5 sm:py-3 px-1.5 sm:px-4 text-[10px] sm:text-[11px] uppercase tracking-wide border-b-2 transition-all whitespace-nowrap">
                                <i class="bi bi-receipt text-[11px]"></i> Rincian
                            </button>
                        </div>

                        {{-- ══════════════════════════════
                             TAB CONTENTS
                        ══════════════════════════════ --}}
                        <div class="p-3 sm:p-5 bg-white">
                            @if(!$isSelesai)

                                {{-- ── TAB 1: UPDATE PROGRES ──────── --}}
                                <div x-show="activeTab === 'update'">
                                    <form wire:submit.prevent="updateProgres({{ $order['id'] }})" class="space-y-3.5 sm:space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                                    Status Pengerjaan
                                                </label>
                                                @php
                                                    $currentStatus = $order->latestStatus->status_order ?? '-';
                                                    $displayStatus = $currentStatus === 'selesai'
                                                        ? 'Menunggu Pembayaran'
                                                        : ucwords(str_replace('_', ' ', $currentStatus));
                                                @endphp
                                                <select wire:model="status_update"
                                                        class="w-full text-xs sm:text-sm border-slate-200 rounded-lg bg-slate-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                                    <option value="{{ $currentStatus }}">Saat ini: {{ $displayStatus }}</option>
                                                    <option value="dikerjakan">Dikerjakan</option>
                                                    <option value="menunggu_sparepart">Menunggu Sparepart</option>
                                                    <option value="hampir_selesai">Hampir Selesai</option>
                                                    <option value="selesai">Selesai (Siap Bayar)</option>
                                                    <option value="dibatalkan">Batalkan</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                                    Upload Bukti (Foto)
                                                </label>
                                                @if($bukti_pengerjaan)
                                                    <div class="mb-2.5 relative w-20 h-20 rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-slate-50">
                                                        <img src="{{ $bukti_pengerjaan->temporaryUrl() }}" class="w-full h-full object-cover">
                                                        <button type="button" wire:click="$set('bukti_pengerjaan', null)"
                                                                class="absolute top-1 right-1 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow transition-transform active:scale-90">
                                                            <i class="bi bi-x text-xs leading-none"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                                <div class="relative group">
                                                    <input type="file" wire:model="bukti_pengerjaan"
                                                           class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full h-full">
                                                    <div class="flex items-center gap-2.5 px-3 sm:px-4 py-2.5 border-2 border-dashed border-slate-200 rounded-lg group-hover:border-indigo-400 group-hover:bg-indigo-50/30 transition-colors bg-slate-50">
                                                        <i class="bi bi-camera text-slate-400 group-hover:text-indigo-500 text-base transition-colors shrink-0"></i>
                                                        <span class="text-[11px] sm:text-xs text-slate-500 group-hover:text-indigo-600 transition-colors">Pilih atau Ambil Foto</span>
                                                    </div>
                                                </div>
                                                <div wire:loading wire:target="bukti_pengerjaan"
                                                     class="flex items-center gap-1 text-[11px] text-indigo-600 mt-1.5">
                                                    <i class="bi bi-arrow-repeat animate-spin"></i> Mengunggah foto...
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                                Catatan Tambahan <span class="normal-case font-normal text-slate-400">(Opsional)</span>
                                            </label>
                                            <textarea wire:model="catatan_progres" rows="2"
                                                      class="w-full text-xs sm:text-sm border-slate-200 rounded-lg bg-slate-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                                      placeholder="Contoh: Penggantian freon berhasil..."></textarea>
                                        </div>
                                        <div class="flex items-center justify-end gap-2 pt-2.5 border-t border-slate-100">
                                            <button type="button" @click="openUpdate = false"
                                                    class="px-3 sm:px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition">
                                                Tutup
                                            </button>
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-4 sm:px-5 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white text-xs font-bold rounded-lg transition shadow-sm shadow-green-100">
                                                <i class="bi bi-check-circle"></i> Simpan Update
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {{-- ── TAB 2: RIWAYAT (TIMELINE) ──── --}}
                                <div x-show="activeTab === 'riwayat'" style="display: none;">
                                    <div class="relative border-l-2 border-indigo-100 ml-2 sm:ml-3 space-y-4 sm:space-y-5 pb-2">
                                        @forelse($order->lacak_pesanan as $riwayat)
                                            <div class="relative pl-5 sm:pl-6">
                                                <div class="absolute -left-[9px] top-1 w-4 h-4 bg-indigo-500 rounded-full border-4 border-white shadow-sm ring-1 ring-indigo-200"></div>
                                                <div class="bg-slate-50 border border-slate-100 p-3 sm:p-3.5 rounded-xl shadow-sm">
                                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 sm:gap-2 mb-1">
                                                        <h4 class="text-[11px] font-bold uppercase tracking-wide
                                                            {{ $riwayat->status_order === 'selesai' ? 'text-amber-600' : 'text-indigo-700' }}">
                                                            {{ $riwayat->status_order === 'selesai'
                                                                ? 'Menunggu Pembayaran'
                                                                : ucwords(str_replace('_', ' ', $riwayat->status_order)) }}
                                                        </h4>
                                                        <span class="text-[10px] text-slate-500 font-medium bg-white px-2 py-0.5 rounded-md border border-slate-200 self-start sm:shrink-0">
                                                            {{ $riwayat->created_at->format('d M Y, H:i') }}
                                                        </span>
                                                    </div>
                                                    @if($riwayat->note)
                                                        <p class="text-xs text-slate-600 mt-1.5 leading-relaxed whitespace-pre-wrap border-l-2 border-slate-200 pl-2 italic">
                                                            {{ $riwayat->note }}
                                                        </p>
                                                    @endif
                                                    @if($riwayat->foto_bukti)
                                                        <a href="{{ asset('storage/'.$riwayat->foto_bukti) }}" target="_blank"
                                                           class="mt-2.5 inline-block w-20 h-20 sm:w-24 sm:h-24 rounded-lg overflow-hidden border border-slate-200 shadow-sm hover:opacity-80 hover:shadow-md transition">
                                                            <img src="{{ asset('storage/'.$riwayat->foto_bukti) }}" class="w-full h-full object-cover">
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="pl-5 py-4">
                                                <p class="text-xs text-slate-400 italic">Belum ada riwayat terekam.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- ── TAB 3: TAMBAH ITEM ─────────── --}}
                                <div x-show="activeTab === 'layanan'" style="display: none;"
                                     x-data="{
                                         isiOtomatis(e) {
                                             if (e.target.value) {
                                                 let data = JSON.parse(e.target.value);
                                                 $wire.set('nama_layanan_baru', data.nama);
                                                 $wire.set('harga_layanan_baru', data.harga.replace(/[^0-9]/g, ''));
                                             }
                                         }
                                     }">
                                    <form wire:submit.prevent="tambahLayanan({{ $order['id'] }})"
                                          class="bg-indigo-50/40 border border-indigo-100 p-3 sm:p-4 rounded-xl mb-3 sm:mb-4 space-y-3">
                                        <h4 class="text-[11px] sm:text-xs font-bold text-indigo-800 uppercase tracking-wide flex items-center gap-1.5">
                                            <i class="bi bi-plus-square text-indigo-500"></i> Tambah Item / Sparepart
                                        </h4>
                                        @if(!empty($order['jasa']['layanan_tambahan']))
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                                    Pilih dari Template <span class="normal-case font-normal text-slate-400">(Opsional)</span>
                                                </label>
                                                <select @change="isiOtomatis"
                                                        class="w-full text-xs rounded-lg border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 bg-white text-slate-600">
                                                    <option value="">-- Pilih item dari daftar jasa --</option>
                                                    @foreach($order['jasa']['layanan_tambahan'] as $grup)
                                                        <optgroup label="{{ $grup['judul'] }}">
                                                            @foreach($grup['items'] as $item)
                                                                <option value="{{ json_encode($item) }}">
                                                                    {{ $item['nama'] }} — Rp {{ $item['harga'] }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                            <div class="sm:col-span-8">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Item</label>
                                                <input type="text" wire:model="nama_layanan_baru"
                                                       class="w-full text-xs rounded-lg border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                                                       placeholder="Nama Sparepart / Layanan" required>
                                                @error('nama_layanan_baru')
                                                    <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="sm:col-span-4">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Harga</label>
                                                <div class="relative">
                                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-medium pointer-events-none">Rp</span>
                                                    <input type="number" wire:model="harga_layanan_baru"
                                                           class="w-full text-xs rounded-lg border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 pl-8"
                                                           placeholder="0" required min="0">
                                                </div>
                                                @error('harga_layanan_baru')
                                                    <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                                Deskripsi <span class="normal-case font-normal text-slate-400">(Opsional)</span>
                                            </label>
                                            <textarea wire:model="deskripsi_layanan_baru" rows="2"
                                                      class="w-full text-xs rounded-lg border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 placeholder-slate-400"
                                                      placeholder="Alasan penggantian atau keterangan tambahan..."></textarea>
                                            @error('deskripsi_layanan_baru')
                                                <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="flex flex-col gap-2.5 sm:flex-row sm:items-end sm:gap-3 pt-1">
                                            <div class="flex-1">
                                                <label class="block text-[10px] font-bold text-indigo-600 uppercase tracking-wider mb-1.5">
                                                    Upload Foto <span class="normal-case font-normal text-indigo-400">(Opsional)</span>
                                                </label>
                                                @if($foto_layanan_baru)
                                                    <div class="mb-2.5 relative w-20 h-20 rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-slate-50">
                                                        <img src="{{ $foto_layanan_baru->temporaryUrl() }}" class="w-full h-full object-cover">
                                                        <button type="button" wire:click="$set('foto_layanan_baru', null)"
                                                                class="absolute top-1 right-1 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow transition-transform active:scale-90">
                                                            <i class="bi bi-x text-xs leading-none"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                                <input type="file" wire:model="foto_layanan_baru" accept="image/*"
                                                       class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0
                                                              file:text-xs file:font-semibold file:bg-indigo-100 file:text-indigo-700
                                                              hover:file:bg-indigo-200 bg-white border border-indigo-200 rounded-lg text-slate-500">
                                                <div wire:loading wire:target="foto_layanan_baru"
                                                     class="flex items-center gap-1 text-[11px] text-indigo-600 mt-1.5">
                                                    <i class="bi bi-arrow-repeat animate-spin"></i> Mengunggah...
                                                </div>
                                                @error('foto_layanan_baru')
                                                    <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="sm:w-40 sm:shrink-0">
                                                <button type="submit"
                                                        wire:loading.attr="disabled" wire:target="tambahLayanan"
                                                        class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5
                                                               bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800
                                                               text-white text-xs font-bold rounded-lg transition shadow-sm">
                                                    <i class="bi bi-check-circle"></i> Tambah Item
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                                        <div class="px-3 sm:px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                                            <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Daftar Item Tambahan</h4>
                                        </div>
                                        <div class="divide-y divide-slate-100">
                                            @forelse($order['detail_order'] as $detail)
                                                <div class="px-3 sm:px-4 py-3 flex gap-2.5 sm:gap-3 hover:bg-slate-50/70 transition-colors">
                                                    @if(!empty($detail['foto']))
                                                        <a href="{{ asset('storage/'.$detail['foto']) }}" target="_blank"
                                                           class="w-10 h-10 sm:w-12 sm:h-12 shrink-0 rounded-lg overflow-hidden border border-slate-200 shadow-sm hover:opacity-80 transition">
                                                            <img src="{{ asset('storage/'.$detail['foto']) }}" class="w-full h-full object-cover">
                                                        </a>
                                                    @else
                                                        <div class="w-10 h-10 sm:w-12 sm:h-12 shrink-0 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                                                            <i class="bi bi-box-seam text-base sm:text-lg"></i>
                                                        </div>
                                                    @endif
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex justify-between items-start gap-1.5">
                                                            <span class="text-xs font-bold text-slate-800 leading-snug min-w-0 break-words">{{ $detail['nama_layanan_tambahan'] }}</span>
                                                            <span class="text-[11px] sm:text-xs font-black text-indigo-600 shrink-0 whitespace-nowrap ml-1">
                                                                Rp {{ number_format($detail['harga_layanan_tambahan'], 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                        @if(!empty($detail['deskripsi']))
                                                            <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 leading-relaxed line-clamp-2"
                                                               title="{{ $detail['deskripsi'] }}">
                                                                {{ $detail['deskripsi'] }}
                                                            </p>
                                                        @endif
                                                        <div class="mt-1.5">
                                                            @if($detail['acc_customer'] === 1)
                                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-700 bg-green-50 px-2 py-0.5 rounded-md border border-green-200">
                                                                    <i class="bi bi-check-circle-fill text-[9px]"></i> Disetujui
                                                                </span>
                                                            @elseif($detail['acc_customer'] === 0)
                                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-md border border-red-200">
                                                                    <i class="bi bi-x-circle-fill text-[9px]"></i> Ditolak
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                                                    <i class="bi bi-hourglass-split text-[9px]"></i> Menunggu Respon
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="px-4 py-8 text-center">
                                                    <i class="bi bi-receipt text-3xl text-slate-300 mb-2 block"></i>
                                                    <p class="text-xs text-slate-400 italic">Belum ada layanan/sparepart tambahan direkam.</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                            @endif

                            {{-- ── TAB 4: RINCIAN (Selalu Ada) ───── --}}
                            <div x-show="activeTab === 'rincian'" style="display: none;">
                                @if($isSelesai)
                                    <div class="mb-4 flex gap-2 sm:gap-2.5 bg-amber-50 border border-amber-200 p-3 sm:p-3.5 rounded-xl">
                                        <i class="bi bi-info-circle-fill text-amber-500 text-sm sm:text-base shrink-0 mt-0.5"></i>
                                        <p class="text-[11px] sm:text-xs text-amber-800 leading-relaxed">
                                            @if($isSudahDibayar)
                                                Pelanggan telah melakukan konfirmasi pembayaran. Silakan periksa saldo atau bukti transfer Anda sebelum menekan tombol <strong>Konfirmasi Pembayaran</strong>.
                                            @else
                                                Pekerjaan ini sudah <strong>selesai</strong>. Semua form update dikunci hingga proses pembayaran dari pelanggan selesai.
                                            @endif
                                        </p>
                                    </div>
                                @endif
                                <div class="space-y-3.5 sm:space-y-4">
                                    @if(($order->jasa->tipe_layanan ?? 'panggilan') === 'panggilan')
                                        <div class="space-y-3">
                                            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lokasi Pengerjaan</h3>
                                            <div class="p-3 bg-indigo-50/50 border border-indigo-100 rounded-xl flex items-start gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-indigo-600 shrink-0 shadow-sm">
                                                    <i class="bi bi-geo-alt-fill"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-slate-800">{{ $order->customer->user->name }}</p>
                                                    <p class="text-[11px] text-slate-600 leading-relaxed mt-0.5">
                                                        {{ $order->customer->detail_alamat }}, {{ $order->customer->kelurahan }},
                                                        {{ $order->customer->kecamatan }}, {{ $order->customer->kabupaten }}
                                                    </p>
                                                </div>
                                            </div>
                                            @if($order->customer->latitude && $order->customer->longitude)
                                                <div class="rounded-xl overflow-hidden border border-slate-200 h-[200px] relative shadow-inner">
                                                    @livewire('services.map', [
                                                        'lat'          => $order->customer->latitude,
                                                        'lng'          => $order->customer->longitude,
                                                        'customerName' => $order->customer->user->name
                                                    ], key('map-order-'.$order->id))
                                                </div>
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ $order->customer->latitude }},{{ $order->customer->longitude }}"
                                                   target="_blank"
                                                   class="inline-flex items-center gap-1.5 text-[10px] font-bold text-indigo-600 hover:text-indigo-700 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm transition-all">
                                                    <i class="bi bi-cursor-fill"></i>
                                                    Buka di Google Maps
                                                </a>
                                            @endif
                                        </div>
                                        <hr class="border-slate-100">
                                    @endif
                                    <div class="bg-slate-50 border border-slate-100 p-3 sm:p-4 rounded-xl grid grid-cols-2 gap-3 sm:gap-4">
                                        <div>
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jadwal Servis</span>
                                            <span class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug">
                                                {{ \Carbon\Carbon::parse($order['order_date'])->translatedFormat('l, d F Y') }}<br>
                                                <span class="text-slate-500 font-normal">Pukul</span> {{ \Carbon\Carbon::parse($order['order_time'])->format('H:i') }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tipe Layanan</span>
                                            <span class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug">
                                                @if(($order['jasa']['tipe_layanan'] ?? 'panggilan') === 'panggilan')
                                                    Panggilan<br>
                                                    <span class="text-[10px] sm:text-xs text-slate-400 font-normal">Teknisi ke lokasi</span>
                                                @else
                                                    Di Bengkel<br>
                                                    <span class="text-[10px] sm:text-xs text-slate-400 font-normal">Pelanggan antar barang</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                                        <div class="px-3 sm:px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                                            <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Breakdown Tagihan</h4>
                                        </div>
                                        <div class="p-3 sm:p-4 space-y-3">
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-xs sm:text-sm text-slate-600 font-medium min-w-0 break-words">
                                                    Jasa Dasar ({{ $order['jasa']['nama_jasa'] ?? 'Servis' }})
                                                </span>
                                                <span class="text-xs sm:text-sm font-bold text-slate-800 tabular-nums shrink-0 ml-2">
                                                    Rp {{ number_format($order['jasa']['harga_jasa'] ?? 0, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            @if(count($order['detail_order'] ?? []) > 0)
                                                <div class="border-t border-slate-100 pt-3 space-y-2">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
                                                        Sparepart &amp; Tindakan Tambahan
                                                    </span>
                                                    @foreach($order['detail_order'] as $detail)
                                                        <div class="flex justify-between items-start pl-2.5 sm:pl-3 border-l-2 border-indigo-100 py-1 gap-2">
                                                            <div class="flex flex-col min-w-0">
                                                                <span class="text-xs sm:text-sm text-slate-600 font-medium leading-snug break-words">
                                                                    {{ $detail['nama_layanan_tambahan'] }}
                                                                </span>
                                                                @if($detail['acc_customer'] === 0)
                                                                    <span class="text-[10px] text-red-500 font-medium mt-0.5">
                                                                        <i class="bi bi-x-circle"></i> Ditolak — tidak ditagihkan
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <span class="text-xs sm:text-sm tabular-nums shrink-0 font-semibold ml-2
                                                                {{ $detail['acc_customer'] === 0 ? 'text-slate-300 line-through' : 'text-slate-700' }}">
                                                                Rp {{ number_format($detail['harga_layanan_tambahan'], 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="border-t border-slate-200 pt-3 flex justify-between items-end gap-2">
                                                <div class="min-w-0">
                                                    <span class="block text-[11px] sm:text-xs font-bold text-slate-800 uppercase tracking-wide">Total Tagihan</span>
                                                    <span class="text-[10px] text-slate-400 mt-0.5 block">Termasuk item disetujui</span>
                                                </div>
                                                <span class="text-lg sm:text-xl font-black text-indigo-600 tabular-nums shrink-0">
                                                    Rp {{ number_format($order['total_harga'], 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>{{-- end tab contents --}}
                    </div>{{-- end x-data activeTab --}}
                </div>{{-- end collapsible panel --}}

            </div>{{-- end card --}}
        @empty
            <div class="py-12 sm:py-16 text-center bg-slate-50/70 rounded-2xl border-2 border-dashed border-slate-200">
                <i class="bi bi-clipboard-x text-4xl sm:text-5xl text-slate-300 mb-3 block"></i>
                <p class="text-sm font-semibold text-slate-400">Belum ada pesanan aktif.</p>
                <p class="text-xs text-slate-300 mt-1">Semua pekerjaan sudah selesai atau belum ada yang masuk.</p>
            </div>
        @endforelse

        <div class="mt-4 pt-4 border-t border-slate-100">
            {{ $data->links() }}
        </div>
    </div>

    <template x-teleport="body">
        <div 
            x-show="showAvatarModal" 
            class="fixed inset-0 z-[99999] flex flex-col items-center justify-center bg-slate-950/90 p-4" 
            style="display: none;" 
            @keydown.escape.window="showAvatarModal = false"
        >
            {{-- Tombol Close --}}
            <button @click="showAvatarModal = false" class="absolute top-5 right-5 text-white/70 hover:text-white p-2 transition-transform active:scale-90">
                <i class="bi bi-x-lg text-2xl sm:text-3xl"></i>
            </button>
            
            {{-- Kontainer Gambar + Nama Keterangan Atas --}}
            <div class="max-w-4xl w-full h-full flex flex-col items-center justify-center" @click.away="showAvatarModal = false">
                <p x-text="avatarModalName" class="text-white/80 font-bold text-sm sm:text-base mb-3 tracking-wide uppercase"></p>
                <img :src="avatarModalImage" class="max-w-full max-h-[75vh] sm:max-h-[80vh] rounded-2xl object-contain border border-white/10 shadow-2xl transition-all duration-300">
            </div>
        </div>
    </template>
</div>