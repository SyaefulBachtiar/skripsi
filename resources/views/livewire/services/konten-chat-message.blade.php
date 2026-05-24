@php
    $realtimeOrder = $data_pesanan->order->fresh();
    $statusPesanan = $realtimeOrder->lacak_pesanan->last()->status_order ?? '';
    $isCustomer = auth()->user()->customer()->exists();
@endphp
<div class="flex flex-col h-full {{ $statusPesanan === 'selesai' ? 'pb-20' : 'pb-0' }}">
    <div
        id="chat-container"
        class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-3"
        style="padding-bottom: 5rem;"
        x-data
        x-init="$el.scrollTop = $el.scrollHeight"
        @scroll-to-bottom.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight })"
    >

        {{-- ── Header Info Jasa ──────────────────────── --}}
        <div class="bg-white rounded-2xl p-3 sm:p-4 mb-4 sm:mb-6 shadow-sm border border-gray-100">

            {{-- Baris Atas: Thumbnail + Info + Tombol Detail --}}
            <div class="flex items-center gap-2.5 sm:gap-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 shrink-0 rounded-xl overflow-hidden bg-gray-100 border border-gray-100">
                    <img
                        src="{{ asset('storage/' . ($data_pesanan->order->jasa->first_thumbnail ?? 'default.jpg')) }}"
                        alt="Thumbnail Jasa"
                        class="w-full h-full object-cover"
                    >
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Layanan yang dipesan</p>
                    <h1 class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $data_pesanan->order->jasa->nama_jasa }}</h1>
                </div>

                @if ($isCustomer)
                    <a
                        href="{{ route('rincian.pesanan', $data_pesanan->order_id) }}"
                        wire:navigate
                        class="shrink-0 flex flex-col items-center justify-center gap-0.5 p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors"
                    >
                        <i class="bi bi-file-earmark-text text-base sm:text-lg leading-none"></i>
                        <span class="text-[9px] sm:text-[10px] font-semibold leading-none">Detail</span>
                    </a>
                @endif
            </div>

            {{-- Baris Bawah: Jadwal, Status, Total --}}
            <div class="mt-3 pt-3 border-t border-gray-50 grid grid-cols-2 gap-y-3 gap-x-3">

                {{-- Jadwal --}}
                <div class="min-w-0">
                    <p class="text-[9px] text-gray-400 uppercase font-semibold mb-0.5">Jadwal Servis</p>
                    <p class="text-[10px] sm:text-[11px] font-medium text-gray-700 leading-snug">
                        <i class="bi bi-calendar3 text-blue-500 mr-0.5"></i>
                        {{ \Carbon\Carbon::parse($data_pesanan->order->order_date)->translatedFormat('d M Y') }},
                        {{ \Carbon\Carbon::parse($data_pesanan->order->order_time)->format('H:i') }}
                    </p>
                </div>

                {{-- Status --}}
                <div class="text-right min-w-0">
                    <p class="text-[9px] text-gray-400 uppercase font-semibold mb-0.5">Status</p>
                    @php $statusNow = $data_pesanan->order->lacak_pesanan->first()->status_order ?? ''; @endphp
                    <span class="inline-block px-1.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold uppercase tracking-tight
                        {{ $statusNow === 'menunggu_konfirmasi' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                        {{ str_replace('_', ' ', $statusNow ?: 'Tidak Tersedia') }}
                    </span>
                </div>

                {{-- Total Harga (full width) --}}
                <div class="col-span-2 bg-gray-50 px-3 py-2 rounded-xl flex justify-between items-center">
                    <p class="text-[10px] text-gray-500 font-medium">Total Biaya (Estimasi)</p>
                    <p class="text-sm font-black text-blue-600 tabular-nums">
                        Rp {{ number_format($data_pesanan->order->total_harga, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Daftar Pesan ─────────────────────────── --}}
        @php $lastDate = null; @endphp

        @forelse($messages as $msg)
            @php
                $msgDate  = $msg->created_at->translatedFormat('Y-m-d');
                $today    = now()->translatedFormat('Y-m-d');
                $yesterday = now()->subDay()->translatedFormat('Y-m-d');
            @endphp

            {{-- Pemisah Tanggal --}}
            @if($lastDate !== $msgDate)
                <div class="flex justify-center my-3">
                    <span class="text-[10px] bg-gray-200/50 backdrop-blur-sm px-3 py-1 rounded-full text-gray-500 font-semibold shadow-sm uppercase tracking-tight">
                        @if($msgDate == $today) Hari Ini
                        @elseif($msgDate == $yesterday) Kemarin
                        @elseif($msg->created_at->isCurrentWeek()) {{ $msg->created_at->translatedFormat('l') }}
                        @else {{ $msg->created_at->translatedFormat('d F Y') }}
                        @endif
                    </span>
                </div>
                @php $lastDate = $msgDate; @endphp
            @endif

            {{-- ─── Pesan Sistem ─────────────────────── --}}
            @if($msg->is_system)

                @if($msg->type === 'status')
                    {{-- Bubble: Update Status --}}
                    <div class="flex justify-center my-5 w-full px-4">
                        <div class="w-full max-w-sm sm:max-w-md flex flex-col items-center">
                            
                            {{-- Penanda Garis & Badge Status Otomatis --}}
                            <div class="w-full flex items-center justify-center gap-3 mb-2">
                                <div class="h-[1px] bg-slate-200 dark:bg-slate-700/50 flex-1"></div>
                                <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 rounded-full border border-slate-200 dark:border-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                    <span class="text-[9px] font-bold text-slate-500 dark:text-slate-400 tracking-wider uppercase">Info Pesanan</span>
                                </div>
                                <div class="h-[1px] bg-slate-200 dark:bg-slate-700/50 flex-1"></div>
                            </div>

                            {{-- Isi Konten Utama --}}
                            <div class="text-center px-2 w-full">
                                {{-- Status Teks Utama --}}
                                <p class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 leading-relaxed">
                                    {{ $msg->message }}
                                </p>

                                {{-- Catatan / Note (Tampil tanpa background kotak kaku) --}}
                                @if($msg->note)
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto text-center font-normal leading-snug">
                                        <span class="text-slate-400 dark:text-slate-600 font-serif text-xs">“</span>{{ $msg->note }}<span class="text-slate-400 dark:text-slate-600 font-serif text-xs">”</span>
                                    </p>
                                @endif

                                {{-- Foto Bukti (Rapi, Sejajar, & Aspek Rasio Dikunci) --}}
                                @if (isset($msg->foto_bukti) && $msg->foto_bukti)
                                    <div class="mt-3 max-w-[240px] sm:max-w-[280px] mx-auto overflow-hidden rounded-xl border border-slate-200/70 dark:border-slate-700/60 shadow-sm bg-slate-50">
                                        <img 
                                            src="{{ asset('storage/' . $msg->foto_bukti) }}" 
                                            alt="{{ $msg->message }}"
                                            class="w-full h-40 object-cover hover:scale-102 transition-transform duration-300"
                                            loading="lazy"
                                        >
                                    </div>
                                @endif

                                {{-- Jam / Waktu Tipis di Bagian Bawah --}}
                                <span class="text-[9px] font-medium text-slate-400 dark:text-slate-500 mt-2 block tracking-wide">
                                    {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}
                                </span>
                            </div>

                        </div>
                    </div>

                @elseif($msg->type === 'layanan_tambahan')
                    {{-- Bubble: Permintaan Persetujuan Item --}}
                    <div class="flex justify-center my-3 w-full px-2">
                        <div class="bg-blue-50 border border-blue-100 p-3 sm:p-4 rounded-xl text-center shadow-sm w-full max-w-xs sm:max-w-sm">
                            <div class="flex items-center justify-center gap-1.5 mb-2">
                                <i class="bi bi-tools text-blue-500 text-[11px]"></i>
                                <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider">Penambahan Layanan / Sparepart</span>
                            </div>

                            <div class="bg-white p-2.5 sm:p-3 rounded-lg border border-blue-50 mb-3 text-left shadow-sm">
                                <div class="flex justify-between items-start gap-2">
                                    <p class="text-xs sm:text-sm font-bold text-gray-800 min-w-0 break-words">{{ $msg->detail_layanan->nama_layanan_tambahan }}</p>
                                    <p class="text-xs sm:text-sm font-black text-blue-600 shrink-0 tabular-nums ml-2">Rp {{ number_format($msg->detail_layanan->harga_layanan_tambahan, 0, ',', '.') }}</p>
                                </div>

                                @if($msg->detail_layanan->deskripsi)
                                    <p class="text-[11px] text-gray-500 mt-1.5 italic leading-relaxed">"{{ $msg->detail_layanan->deskripsi }}"</p>
                                @endif

                                @if($msg->detail_layanan->foto)
                                    <img
                                        src="{{ asset('storage/' . $msg->detail_layanan->foto) }}"
                                        class="mt-2 w-full h-28 sm:h-32 object-cover rounded-md border border-gray-200 cursor-pointer hover:opacity-90 transition"
                                        @click="window.open('{{ asset('storage/' . $msg->detail_layanan->foto) }}')"
                                        alt="Foto layanan"
                                    >
                                @endif
                            </div>

                            {{-- Tombol Aksi / Status Keputusan --}}
                            @if($msg->detail_layanan->acc_customer === null)
                                @if(auth()->user()->role === 'customer')
                                    <div class="flex gap-2">
                                        <button wire:click="respondLayanan({{ $msg->detail_layanan->id }}, false)"
                                                class="flex-1 py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold rounded-lg transition shadow-sm">
                                            Tolak
                                        </button>
                                        <button wire:click="respondLayanan({{ $msg->detail_layanan->id }}, true)"
                                                class="flex-1 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition shadow-sm">
                                            Setuju
                                        </button>
                                    </div>
                                @else
                                    <div class="py-1.5 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-bold border border-amber-100 flex items-center justify-center gap-1.5">
                                        <i class="bi bi-hourglass-split"></i> Menunggu Respon Pelanggan
                                    </div>
                                @endif
                            @else
                                @if($msg->detail_layanan->acc_customer)
                                    <div class="py-1.5 bg-green-100 text-green-700 rounded-lg text-[11px] font-bold flex items-center justify-center gap-1 border border-green-200">
                                        <i class="bi bi-check-circle-fill"></i> Pelanggan Setuju
                                    </div>
                                @else
                                    <div class="py-1.5 bg-red-100 text-red-700 rounded-lg text-[11px] font-bold flex items-center justify-center gap-1 border border-red-200">
                                        <i class="bi bi-x-circle-fill"></i> Pelanggan Menolak
                                    </div>
                                @endif
                            @endif

                            <span class="text-[9px] text-gray-400 mt-2 block">{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                    </div>

                @elseif($msg->type === 'system')
                    {{-- Pesan Sistem Teks --}}
                    <div class="flex justify-center my-2 w-full px-4">
                        <span class="text-[10px] bg-slate-100/80 border border-slate-200 text-slate-500 px-3 sm:px-4 py-1.5 rounded-full text-center max-w-[90%] sm:max-w-[80%] leading-relaxed shadow-sm font-medium">
                            {{ $msg->message }}
                        </span>
                    </div>
                @endif

            {{-- ─── Bubble Chat Biasa ─────────────────── --}}
            @else
                @php $isMine = $msg->sender_id === Auth::id(); @endphp

                <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} items-end gap-2 px-1">
                    {{-- max-w dibatasi 78% di mobile, 70% di sm+ agar tidak terlalu lebar --}}
                    <div class="max-w-[78%] sm:max-w-[70%] flex flex-col {{ $isMine ? 'items-end' : 'items-start' }}">

                        @if($msg->type === 'image')
                            {{-- Bubble Foto --}}
                            <div class="rounded-2xl overflow-hidden shadow-sm {{ $isMine ? 'rounded-br-none' : 'rounded-bl-none' }}">
                                <img
                                    src="{{ asset('storage/' . $msg->foto) }}"
                                    alt="Foto"
                                    class="max-w-full max-h-56 sm:max-h-64 object-cover cursor-pointer hover:opacity-90 transition block"
                                    @click="window.open('{{ asset('storage/' . $msg->foto) }}')"
                                >
                                <p>{{ $msg->message }}</p>
                            </div>
                        @else
                            {{-- Bubble Teks --}}
                            <div class="px-3 sm:px-4 py-2 sm:py-2.5 rounded-2xl text-xs sm:text-sm shadow-sm
                                {{ $isMine
                                    ? 'bg-blue-600 text-white rounded-br-none'
                                    : 'bg-white text-gray-800 border border-gray-200 rounded-bl-none'
                                }}">
                                <p class="leading-relaxed whitespace-pre-wrap break-words">{{ $msg->message }}</p>
                            </div>
                        @endif

                        {{-- Meta: Jam + Centang --}}
                        <div class="flex items-center gap-1 mt-0.5 px-1">
                            <span class="text-[9px] sm:text-[10px] text-gray-400">{{ $msg->created_at->format('H:i') }}</span>
                            @if($isMine)
                                <i class="bi {{ $msg->is_read ? 'bi-check2-all text-blue-500' : 'bi-check2 text-gray-300' }} text-[11px]"></i>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        @empty
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center h-48 sm:h-64 opacity-25 select-none">
                <i class="bi bi-chat-dots text-4xl sm:text-5xl mb-2 sm:mb-3"></i>
                <p class="text-[11px] sm:text-xs font-bold uppercase tracking-widest">Mulai Percakapan</p>
            </div>
        @endforelse
    </div>

    <div class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-lg border-t border-gray-200 z-50 shadow-[0_-4px_20px_-8px_rgba(0,0,0,0.08)]">
        <div class="w-full max-w-2xl mx-auto px-3 py-2.5 sm:px-4 sm:py-3">

            @if($statusPesanan === 'selesai_total' || $statusPesanan === 'ditolak')
                @if($isCustomer)
                    @if(!$hasReviewed && $statusPesanan !== 'ditolak')
                        <div 
                            class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm"
                            x-data="{ currentRating: @entangle('rating') }"
                        >
                            <div class="text-center mb-3">
                                <span class="inline-block px-2.5 py-0.5 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase tracking-wider mb-1">
                                    Penilaian Layanan
                                </span>
                                <h3 class="text-xs sm:text-sm font-bold text-gray-800">Bagaimana hasil kerja teknisi kami?</h3>
                            </div>

                            <form wire:submit.prevent="simpanReview" class="space-y-3">
                                {{-- Interaksi Klik Bintang Berubah Warna --}}
                                <div class="flex justify-center items-center gap-2 py-1">
                                    <template x-for="i in 5">
                                        <button 
                                            type="button" 
                                            @click="currentRating = i"
                                            class="transition-transform active:scale-90"
                                        >
                                            <i 
                                                class="bi text-2xl" 
                                                :class="i <= currentRating ? 'bi-star-fill text-amber-400' : 'bi-star text-gray-200'"
                                            ></i>
                                        </button>
                                    </template>
                                </div>

                                {{-- Input Kolom Komentar Teks --}}
                                <div>
                                    <textarea 
                                        wire:model.defer="text_comment" 
                                        placeholder="Tulis testimoni atau masukan untuk teknisi..."
                                        rows="2"
                                        class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 rounded-xl p-2.5 resize-none outline-none transition"
                                    ></textarea>
                                    @error('text_comment') <span class="text-[10px] text-red-500 font-semibold block mt-0.5">{{ $message }}</span> @enderror
                                </div>

                                {{-- Input Unggah Foto Review Opsional ── --}}
                                <div class="flex items-center justify-between gap-4 bg-slate-50 border border-slate-100 rounded-xl p-2">
                                    <div class="flex items-center gap-2">
                                        <input 
                                            type="file" 
                                            id="review-photo" 
                                            wire:model="foto_review" 
                                            accept="image/*" 
                                            class="hidden"
                                        >
                                        <label 
                                            for="review-photo" 
                                            class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg text-[11px] font-bold cursor-pointer transition flex items-center gap-1.5 shadow-sm"
                                        >
                                            <i class="bi bi-camera-fill"></i>
                                            <span>{{ $foto_review ? 'Ganti Foto' : 'Tambah Foto' }}</span>
                                        </label>
                                        <span class="text-[10px] text-gray-400 font-medium">Opsional (Bisa kosong)</span>
                                    </div>

                                    {{-- Preview mini jika foto review dipilih --}}
                                    @if($foto_review)
                                        <div class="w-10 h-10 rounded-lg overflow-hidden border border-gray-200 bg-white shadow-sm">
                                            <img src="{{ $foto_review->temporaryUrl() }}" class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                </div>
                                @error('foto_review') <span class="text-[10px] text-red-500 font-semibold block">{{ $message }}</span> @enderror

                                {{-- Tombol Kirim Form --}}
                                <button 
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-md shadow-indigo-100 flex items-center justify-center gap-2"
                                >
                                    <span wire:loading.remove wire:target="simpanReview">Kirim Ulasan</span>
                                    <span wire:loading wire:target="simpanReview" class="flex items-center gap-1">
                                        <i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...
                                    </span>
                                </button>
                            </form>
                        </div>
                    @else
                        @if ($statusPesanan === 'ditolak')
                            <div class="flex items-center gap-3 bg-rose-50 px-3 py-3 rounded-xl border border-rose-200 shadow-sm">
                                <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center shrink-0 shadow-sm">
                                    <i class="bi bi-x-circle-fill text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-[10px] font-bold text-rose-600 uppercase tracking-wider mb-0.5">Pesanan Ditolak</p>
                                        
                                        {{-- Link Kembali ke Beranda untuk Cari Jasa Lain (Hanya untuk Customer) --}}
                                        @if($isCustomer)
                                            <a href="{{ route('beranda') }}" wire:navigate class="text-[10px] font-bold text-blue-600 hover:text-blue-700 hover:underline inline-flex items-center gap-0.5 transition-colors">
                                                <span>Cari Jasa Lain</span>
                                                <i class="bi bi-arrow-right-short text-base"></i>
                                            </a>
                                        @endif
                                    </div>
                                    <p class="text-xs font-semibold text-rose-800 leading-snug">
                                        Maaf, pesanan ini telah ditolak atau dibatalkan oleh teknisi.
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 bg-green-50 px-3 py-3 rounded-xl border border-green-200">
                                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center shrink-0 shadow-sm">
                                    <i class="bi bi-check-circle-fill text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-[10px] font-bold text-green-600 uppercase tracking-wider mb-0.5">Pesanan Selesai</p>
                                        
                                        {{-- Link Menuju Halaman Riwayat (Hanya untuk Customer) --}}
                                        @if($isCustomer)
                                            <a href="{{ route('riwayat') }}" wire:navigate class="text-[10px] font-bold text-blue-600 hover:text-blue-700 hover:underline inline-flex items-center transition-colors">
                                                <span>Lihat Riwayat</span>
                                                <i class="bi bi-arrow-right-short text-sm text-base"></i>
                                            </a>
                                        @endif
                                    </div>
                                    <p class="text-sm font-bold text-green-800 truncate">
                                        Rp {{ number_format($realtimeOrder->total_harga, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endif
                @else
                    @if ($statusPesanan === 'ditolak')
                        {{-- ── TAMPILAN PESANAN DITOLAK UNTUK TEKNISI (ARCHIVED BADGE STYLE) ── --}}
                        <div class="flex items-center gap-3 bg-slate-50 px-3 py-3 rounded-xl border border-slate-200 shadow-sm">
                            <div class="w-10 h-10 bg-slate-200 text-slate-600 rounded-full flex items-center justify-center shrink-0 shadow-sm">
                                <i class="bi bi-archive-fill text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Pesanan Dibatalkan</p>
                                    
                                    {{-- Link Kembali ke Dashboard Utama Teknisi --}}
                                    <a href="{{ route('dashboard_technician') }}" wire:navigate class="text-[10px] font-bold text-blue-600 hover:text-blue-700 hover:underline inline-flex items-center gap-0.5 transition-colors">
                                        <span>Ke Dashboard</span>
                                        <i class="bi bi-arrow-right-short text-base"></i>
                                    </a>
                                </div>
                                <p class="text-xs font-semibold text-slate-700 leading-snug">
                                    Pesanan ini telah ditolak.
                                </p>
                            </div>
                        </div>
                    @else
                        {{-- ── PANEL SLOTS UNTUK TEKNISI (HANYA RANGKUMAN TAMPILAN SUKSES) ── --}}
                        <div class="flex items-center gap-3 bg-green-50 px-3 py-3 rounded-xl border border-green-200 shadow-sm">
                            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center shrink-0 shadow-sm">
                                <i class="bi bi-check-circle-fill text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-bold text-green-600 uppercase tracking-wider mb-0.5">Pesanan Selesai & Lunas</p>
                                <p class="text-sm font-bold text-green-800 truncate">
                                    Rp {{ number_format($realtimeOrder->total_harga, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endif
                @endif

            @elseif(($statusPesanan === 'selesai' || $statusPesanan === 'pembayaran_ditolak') && $isCustomer)
                {{-- ── Panel Pembayaran (Hanya Customer jika status selesai) ── --}}
                <div class="flex items-center gap-3 mb-3 bg-blue-50 px-3 py-2.5 rounded-xl border border-blue-100">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                        <i class="bi bi-receipt text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-0.5">Total Tagihan Servis</p>
                        <p class="text-lg sm:text-xl font-black text-blue-700 tabular-nums">
                            Rp {{ number_format($realtimeOrder->total_harga, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="bayar"
                    wire:loading.attr="disabled"
                    class="w-full py-3 sm:py-3.5 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-wait active:scale-[0.98] text-white text-sm font-bold rounded-xl shadow-md shadow-green-200 transition-all flex items-center justify-center gap-2"
                >
                    <span wire:loading.remove wire:target="bayar" class="flex items-center gap-2">
                        <i class="bi bi-wallet2 text-base sm:text-lg"></i>
                        <span>Bayar Sekarang</span>
                    </span>
                    <span wire:loading wire:target="bayar" class="flex items-center gap-2">
                        <i class="bi bi-arrow-repeat animate-spin text-base sm:text-lg"></i>
                        <span>Memproses...</span>
                    </span>
                </button>

            @else
                {{-- ── Form Input Chat (Default) ── --}}
                
                {{-- Preview Foto (jika ada) --}}
                @if($photoPreview)
                    <div class="bg-white border border-gray-200 p-2.5 sm:p-3 rounded-xl mb-2 shadow-sm">
                        <div class="flex items-center gap-2.5 sm:gap-3">
                            <div class="relative shrink-0">
                                <img
                                    src="{{ $photoPreview }}"
                                    alt="Preview"
                                    class="h-14 w-14 sm:h-16 sm:w-16 object-cover rounded-lg border border-gray-200"
                                >
                                <button
                                    wire:click="removePhoto"
                                    class="absolute -top-1.5 -right-1.5 w-5 h-5 sm:w-6 sm:h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-sm hover:bg-red-600 transition"
                                >
                                    <i class="bi bi-x-lg text-[9px] sm:text-xs"></i>
                                </button>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-gray-700 truncate">{{ $photo->getClientOriginalName() }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ round($photo->getSize() / 1024) }} KB</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Input Bar --}}
                <form
                    wire:submit.prevent="sendMessage"
                    class="flex items-end gap-1.5 sm:gap-2 bg-gray-100/80 rounded-2xl p-1.5 focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-100 transition-all border border-transparent focus-within:border-blue-200"
                >
                    {{-- Tombol Foto --}}
                    <div class="shrink-0">
                        <input
                            type="file"
                            id="photo-input"
                            wire:model="photo"
                            accept="image/*"
                            class="hidden"
                        >
                        <label
                            for="photo-input"
                            class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl cursor-pointer transition-all {{ $photo ? 'text-blue-600 bg-blue-50' : '' }}"
                        >
                            <i class="bi bi-image text-lg sm:text-xl"></i>
                        </label>
                    </div>

                    {{-- Textarea --}}
                    <textarea
                        wire:model.live="message"
                        placeholder="{{ $photo ? 'Tambahkan keterangan (opsional)...' : 'Ketik pesan...' }}"
                        rows="1"
                        class="flex-1 bg-transparent border-none focus:ring-0 text-xs sm:text-sm py-2 sm:py-2.5 px-1 max-h-28 resize-none overflow-y-auto"
                        x-data="{
                            resize() {
                                $el.style.height = 'auto';
                                $el.style.height = Math.min($el.scrollHeight, 112) + 'px';
                            }
                        }"
                        x-init="resize()"
                        @input="resize()"
                        @keydown.enter.prevent="if(!$event.shiftKey) { $wire.sendMessage(); $el.style.height = '36px' }"
                    ></textarea>

                    {{-- Tombol Kirim --}}
                    <button
                        type="submit"
                        @disabled(empty(trim($message)) && !$photo)
                        wire:loading.attr="disabled"
                        wire:target="sendMessage,photo"
                        class="shrink-0 w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center bg-blue-600 text-white rounded-xl shadow-md shadow-blue-200 hover:bg-blue-700 active:scale-95 disabled:opacity-40 disabled:shadow-none transition-all"
                    >
                        <span wire:loading.remove wire:target="sendMessage,photo">
                            <i class="bi bi-send-fill text-xs sm:text-sm"></i>
                        </span>
                        <span wire:loading wire:target="sendMessage,photo">
                            <i class="bi bi-arrow-repeat animate-spin text-xs sm:text-sm"></i>
                        </span>
                    </button>
                </form>

                {{-- Safe area untuk iPhone home bar --}}
                <div class="h-safe-bottom" style="height: env(safe-area-inset-bottom, 0px);"></div>

            @endif

        </div>
    </div>

</div>