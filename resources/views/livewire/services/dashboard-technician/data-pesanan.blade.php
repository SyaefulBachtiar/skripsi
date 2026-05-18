<div class="p-3 sm:p-6 bg-white rounded-2xl shadow-sm border border-slate-200/80">

    {{-- ═══════════════════════════════════════
         HEADER SECTION
    ════════════════════════════════════════ --}}
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

    {{-- ═══════════════════════════════════════
         LIST PESANAN
    ════════════════════════════════════════ --}}
    <div class="space-y-3 sm:space-y-4">
        @forelse($data as $order)
            @php
                $isSelesai = ($order->latestStatus->status_order ?? '') === 'selesai';
            @endphp

            {{-- ─── CARD PESANAN ─────────────────────── --}}
            <div x-data="{ openUpdate: false }"
                 class="border border-slate-200 rounded-xl sm:rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow duration-200">

                {{-- ── Card Header ──────────────────── --}}
                {{--
                    Mobile: identitas + tombol satu baris,
                            badge status di baris kedua (jika ada)
                    sm+:    layout lama flex-row
                --}}
                <div class="px-3 py-3 sm:px-5 sm:py-3.5 flex flex-col gap-2 bg-slate-50/70 border-b border-slate-200">

                    {{-- Baris 1: Identitas + Tombol --}}
                    <div class="flex items-center justify-between gap-2">

                        <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0">
                            <div class="w-9 h-9 sm:w-11 sm:h-11 bg-white border border-slate-200 rounded-lg sm:rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                                <i class="bi bi-box-seam text-lg sm:text-xl text-slate-400"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 mb-0.5 flex-wrap">
                                    <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest whitespace-nowrap">
                                        Order #{{ $order['id'] }}
                                    </span>
                                    <span class="w-1 h-1 bg-slate-300 rounded-full shrink-0"></span>
                                    <span class="text-[10px] sm:text-[11px] text-slate-500 font-medium whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($order['order_date'])->format('d M Y') }}
                                    </span>
                                </div>
                                <h2 class="text-xs sm:text-sm font-bold text-slate-800 leading-tight truncate">
                                    Servis &mdash; <span class="text-indigo-700">Jasa #{{ $order['id_jasa'] }}</span>
                                </h2>
                            </div>
                        </div>

                        {{-- Tombol selalu di kanan atas --}}
                        <button @click="openUpdate = !openUpdate"
                                class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 sm:px-3.5 py-1.5 sm:py-2
                                       bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800
                                       text-white text-[10px] sm:text-xs font-bold rounded-lg transition-colors
                                       shadow-sm shadow-indigo-200 shrink-0 whitespace-nowrap">
                            <i class="bi bi-pencil-square text-[10px] sm:text-[11px]"></i>
                            <span>Update / Detail</span>
                        </button>
                    </div>

                    {{-- Baris 2: Badge (hanya jika selesai) --}}
                    @if($isSelesai)
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-600 text-[10px] font-bold uppercase tracking-wider rounded-lg">
                                <i class="bi bi-hourglass-split text-[10px]"></i>
                                Menunggu Pembayaran
                            </span>
                        </div>
                    @endif
                </div>

                {{-- ── Info Singkat (Selalu Muncul) ──── --}}
                <div class="px-3 py-3 sm:px-5 sm:py-4">
                    <div class="flex flex-col gap-3 sm:grid sm:grid-cols-2 sm:gap-4">

                        {{-- Keluhan --}}
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

                        {{-- Total Harga: inline di mobile (label kiri, angka kanan), block di sm+ --}}
                        <div class="flex flex-row sm:flex-col sm:items-end items-center justify-between pt-2.5 sm:pt-0 border-t border-slate-100 sm:border-0 sm:justify-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider sm:mb-1">Total Biaya Saat Ini</p>
                            <p class="text-lg sm:text-xl font-black text-slate-800 tabular-nums">
                                Rp {{ number_format($order['total_harga'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ── Panel Collapsible ─────────────── --}}
                <div x-show="openUpdate" x-collapse class="border-t border-slate-200">

                    <div x-data="{ activeTab: '{{ $isSelesai ? 'rincian' : 'update' }}' }">

                        {{-- Tab Navigation --}}
                        {{--
                            flex-1 + min-w agar semua tab muat rata,
                            overflow-x-auto sebagai fallback jika layar sangat sempit
                        --}}
                        <div class="flex border-b border-slate-200 bg-slate-50/60 overflow-x-auto">

                            @if(!$isSelesai)
                                <button @click="activeTab = 'update'"
                                        :class="activeTab === 'update' ? 'border-indigo-600 text-indigo-700 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 hover:bg-white/60'"
                                        class="flex-1 min-w-[72px] inline-flex items-center justify-center gap-1 py-2.5 sm:py-3 px-1.5 sm:px-4
                                               text-[10px] sm:text-[11px] uppercase tracking-wide border-b-2 transition-all whitespace-nowrap">
                                    <i class="bi bi-arrow-repeat text-[11px]"></i>
                                    <span class="hidden xs:inline">Update</span> Progres
                                </button>

                                <button @click="activeTab = 'riwayat'"
                                        :class="activeTab === 'riwayat' ? 'border-indigo-600 text-indigo-700 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 hover:bg-white/60'"
                                        class="flex-1 min-w-[72px] inline-flex items-center justify-center gap-1 py-2.5 sm:py-3 px-1.5 sm:px-4
                                               text-[10px] sm:text-[11px] uppercase tracking-wide border-b-2 transition-all whitespace-nowrap">
                                    <i class="bi bi-clock-history text-[11px]"></i> Riwayat
                                </button>

                                <button @click="activeTab = 'layanan'"
                                        :class="activeTab === 'layanan' ? 'border-indigo-600 text-indigo-700 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 hover:bg-white/60'"
                                        class="flex-1 min-w-[72px] inline-flex items-center justify-center gap-1 py-2.5 sm:py-3 px-1.5 sm:px-4
                                               text-[10px] sm:text-[11px] uppercase tracking-wide border-b-2 transition-all whitespace-nowrap">
                                    <i class="bi bi-plus-circle text-[11px]"></i>
                                    <span class="hidden xs:inline">Tambah</span> Item
                                </button>

                            @else
                                @foreach(['Progres', 'Riwayat', 'Item'] as $lockedTab)
                                    <button disabled
                                            class="flex-1 min-w-[72px] inline-flex items-center justify-center gap-1 py-2.5 sm:py-3 px-1.5 sm:px-4
                                                   text-[10px] sm:text-[11px] uppercase tracking-wide border-b-2 border-transparent
                                                   text-slate-300 cursor-not-allowed whitespace-nowrap">
                                        <i class="bi bi-lock-fill text-[10px]"></i> {{ $lockedTab }}
                                    </button>
                                @endforeach
                            @endif

                            <button @click="activeTab = 'rincian'"
                                    :class="activeTab === 'rincian' ? 'border-indigo-600 text-indigo-700 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 hover:bg-white/60'"
                                    class="flex-1 min-w-[72px] inline-flex items-center justify-center gap-1 py-2.5 sm:py-3 px-1.5 sm:px-4
                                           text-[10px] sm:text-[11px] uppercase tracking-wide border-b-2 transition-all whitespace-nowrap">
                                <i class="bi bi-receipt text-[11px]"></i> Rincian
                            </button>
                        </div>

                        {{-- ════════════════════════════════
                             TAB CONTENTS
                        ═════════════════════════════════ --}}
                        <div class="p-3 sm:p-5 bg-white">

                            @if(!$isSelesai)

                                {{-- ──────────────────────────────────
                                     TAB 1: UPDATE PROGRES
                                ─────────────────────────────────── --}}
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

                                {{-- ──────────────────────────────────
                                     TAB 2: RIWAYAT (TIMELINE)
                                ─────────────────────────────────── --}}
                                <div x-show="activeTab === 'riwayat'" style="display: none;">
                                    <div class="relative border-l-2 border-indigo-100 ml-2 sm:ml-3 space-y-4 sm:space-y-5 pb-2">
                                        @forelse($order->lacak_pesanan as $riwayat)
                                            <div class="relative pl-5 sm:pl-6">
                                                <div class="absolute -left-[9px] top-1 w-4 h-4 bg-indigo-500 rounded-full border-4 border-white shadow-sm ring-1 ring-indigo-200"></div>

                                                <div class="bg-slate-50 border border-slate-100 p-3 sm:p-3.5 rounded-xl shadow-sm">
                                                    {{-- Status atas, tanggal bawah di mobile; berdampingan di sm+ --}}
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

                                {{-- ──────────────────────────────────
                                     TAB 3: TAMBAH ITEM / SPAREPART
                                ─────────────────────────────────── --}}
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

                                        {{-- Upload foto: full-width di mobile, flex-row di sm+ --}}
                                        <div class="flex flex-col gap-2.5 sm:flex-row sm:items-end sm:gap-3 pt-1">
                                            <div class="flex-1">
                                                <label class="block text-[10px] font-bold text-indigo-600 uppercase tracking-wider mb-1.5">
                                                    Upload Foto <span class="normal-case font-normal text-indigo-400">(Opsional)</span>
                                                </label>
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

                                    {{-- Daftar Item Tambahan --}}
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

                            {{-- ──────────────────────────────────
                                 TAB 4: RINCIAN PESANAN (Selalu Ada)
                            ─────────────────────────────────── --}}
                            <div x-show="activeTab === 'rincian'" style="display: none;">

                                @if($isSelesai)
                                    <div class="mb-3.5 flex gap-2 sm:gap-2.5 bg-amber-50 border border-amber-200 p-3 sm:p-3.5 rounded-xl">
                                        <i class="bi bi-info-circle-fill text-amber-500 text-sm sm:text-base shrink-0 mt-0.5"></i>
                                        <p class="text-[11px] sm:text-xs text-amber-800 leading-relaxed">
                                            Pekerjaan ini sudah <strong>selesai</strong>. Semua form update dikunci hingga proses pembayaran dari pelanggan selesai.
                                        </p>
                                    </div>
                                @endif

                                <div class="space-y-3.5 sm:space-y-4">

                                    {{-- Info Waktu & Tipe --}}
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

                                    {{-- Breakdown Tagihan --}}
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

                                            {{-- Total Akhir --}}
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
</div>