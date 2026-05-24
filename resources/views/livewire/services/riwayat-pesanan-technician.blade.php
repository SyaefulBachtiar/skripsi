<div class="space-y-3 pb-20">

    @forelse($riwayat as $item)
        @php
            // KUNCI PERBAIKAN 1: Gunakan method Collection ->first() agar anti-error saat review kosong (Ditolak)
            $review = $item->review->first();

            // Ambil status order terakhir untuk kondisional tampilan badge warna
            $statusOrderNow = $item->latestStatus->status_order ?? '';

            $fotoReview = $review?->foto_review ?? [];
            if (is_string($fotoReview)) {
                $fotoReview = json_decode($fotoReview, true) ?? [];
            }
        @endphp

        <div x-data="{ open: false, showReplyForm: false, replyText: '' }"
             class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden shadow-sm">

            {{-- ── Card Summary ── --}}
            <div class="p-4">

                {{-- Baris 1: Tanggal + Badge status --}}
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        {{ \Carbon\Carbon::parse($item->order_date)->translatedFormat('d M Y') }}
                    </span>

                    @if($statusOrderNow === 'ditolak')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 border border-red-100 dark:border-transparent">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            Ditolak
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-green-50 text-green-700 dark:bg-green-950/30 dark:text-green-400 border border-green-100 dark:border-transparent">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Selesai Total
                        </span>
                    @endif
                </div>

                {{-- Baris 2: Thumbnail + Info --}}
                <div class="flex gap-3 items-start">
                    {{-- Ganti syntax array [] menjadi tanda panah objek -> untuk data relasi Eloquent --}}
                    <img src="{{ asset('storage/' . ($item->jasa->thumbnails[0] ?? 'default.jpg')) }}"
                         alt="{{ $item->jasa->nama_jasa }}"
                         class="w-13 h-13 rounded-xl object-cover flex-shrink-0 border border-gray-100 dark:border-gray-800"
                         style="width:52px;height:52px">

                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate leading-snug mb-0.5 uppercase">
                            {{ $item->jasa->nama_jasa }}
                        </h3>

                        {{-- Pelanggan (Customer) --}}
                        <div class="flex items-center gap-1.5 mb-1">
                            <div class="w-4 h-4 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-700 shrink-0">
                                @if(!empty($item->customer->user->avatar))
                                    <img src="{{ asset('storage/' . $item->customer->user->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="bi bi-person-fill text-[10px] text-gray-400"></i>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate font-medium">
                                {{ $item->customer->user->name ?? 'Pelanggan' }} • Order #{{ $item->id }}
                            </span>
                        </div>

                        {{-- Harga --}}
                        <p class="text-sm font-black text-indigo-600 dark:text-indigo-400">
                            Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Toggle button --}}
                <button type="button"
                        @click="open = !open"
                        class="w-full mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-center gap-1.5
                               text-[11px] font-bold text-gray-400 dark:text-gray-500
                               hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    <span x-text="open ? 'Sembunyikan Detail' : 'Lihat Detail & Ulasan'"></span>
                    <i class="bi text-xs transition-transform duration-200" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'" aria-hidden="true"></i>
                </button>
            </div>

            {{-- ── Expandable Detail ── --}}
            <div x-show="open"
                  x-collapse
                  style="display:none"
                  class="border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/30 divide-y divide-gray-100 dark:divide-gray-800">

                {{-- Keluhan --}}
                @if(!empty($item->keluhan))
                    <div class="px-4 py-3">
                        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1.5">
                            Keluhan Perangkat
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($item->keluhan as $keluhan)
                                <span class="px-2 py-0.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-md text-[11px] font-medium text-gray-600 dark:text-gray-300">
                                    {{ $keluhan }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ── SEKSI ULASAN PELANGGAN ── --}}
                <div class="px-4 py-4">
                    @if($review)
                        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
                            Ulasan Pelanggan
                        </p>
                        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl p-3 shadow-sm">
                            
                            {{-- Bintang Rating --}}
                            <div class="flex items-center gap-1 mb-1.5" aria-label="{{ $review->rating }} dari 5 bintang">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }} text-xs
                                              {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200 dark:text-gray-700' }}"
                                       aria-hidden="true"></i>
                                @endfor
                            </div>

                            {{-- Teks Komentar --}}
                            <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed font-medium italic">
                                "{{ $review->text_comment ?? 'Tidak ada komentar tertulis.' }}"
                            </p>

                            {{-- Lampiran Foto Review --}}
                            @if(!empty($fotoReview))
                                <div class="flex flex-wrap gap-2 mt-2.5">
                                    @foreach($fotoReview as $foto)
                                        <div x-data="{ modal: false }">
                                            <img src="{{ asset('storage/' . $foto) }}"
                                                 @click="modal = true"
                                                 alt="Foto ulasan customer"
                                                 class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-gray-700 cursor-zoom-in hover:opacity-80 transition-opacity">
                                            
                                            {{-- Lightbox Modal Gambar --}}
                                            <div x-show="modal"
                                                 x-transition.opacity
                                                 @click.self="modal = false"
                                                 @keydown.escape.window="modal = false"
                                                 style="display:none; background:rgba(15,23,42,0.9)"
                                                 class="fixed inset-0 z-[9999] flex items-center justify-center p-6 backdrop-blur-sm">
                                                <div class="relative w-full max-w-sm">
                                                    <button @click="modal = false" class="absolute -top-9 right-0 text-white text-3xl opacity-80 hover:opacity-100">×</button>
                                                    <img src="{{ asset('storage/' . $foto) }}" class="w-full rounded-xl shadow-2xl border border-white/10">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- ── INPUT BALASAN ULASAN (REPLY) ── --}}
                            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800/80">
                                @if(!empty($review->reply_comment))
                                    {{-- Tampilan Jika Sudah Dibalas --}}
                                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-2.5 border-l-2 border-indigo-500">
                                        <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide mb-0.5">Tanggapan Anda:</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 italic">"{{ $review->reply_comment }}"</p>
                                    </div>
                                @else
                                    {{-- Form Input Jika Belum Dibalas --}}
                                    <div class="space-y-2">
                                        <button type="button" 
                                                @click="showReplyForm = !showReplyForm"
                                                x-show="!showReplyForm"
                                                class="text-xs font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 flex items-center gap-1 transition-colors">
                                            <i class="bi bi-reply-fill text-sm"></i> Balas ulasan ini
                                        </button>

                                        <div x-show="showReplyForm" style="display: none;" class="space-y-2">
                                            <textarea x-model="replyText"
                                                      rows="2"
                                                      placeholder="Tulis ucapan terima kasih atau tanggapan Anda..."
                                                      class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-gray-200 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 resize-none outline-none transition"></textarea>
                                            
                                            <div class="flex justify-end gap-2">
                                                <button type="button" 
                                                        @click="showReplyForm = false; replyText = ''"
                                                        class="px-2.5 py-1 text-[11px] font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                                    Batal
                                                </button>
                                                <button type="button"
                                                        wire:click="replyReview({{ $review->id }}, replyText)"
                                                        @click="showReplyForm = false"
                                                        :disabled="!replyText.trim()"
                                                        class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-[11px] font-bold rounded-lg transition-all shadow-sm">
                                                    Kirim Balasan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                    @else
                        <div class="text-center py-4 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl">
                            @if($statusOrderNow === 'ditolak')
                                <p class="text-xs text-red-500 dark:text-red-400 font-medium">
                                    <i class="bi bi-info-circle-fill mr-1"></i> Pesanan ditolak. Tidak ada ulasan yang tersedia.
                                </p>
                            @else
                                <p class="text-xs text-gray-400 dark:text-gray-500 italic">
                                    <i class="bi bi-hourglass-split mr-1"></i> Pelanggan belum memberikan ulasan atau rating bintang.
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center gap-3 py-16 text-center
                    bg-white dark:bg-gray-900
                    rounded-2xl border border-dashed border-gray-200 dark:border-gray-800">
            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <i class="bi bi-card-checklist text-xl text-gray-400 dark:text-gray-600" aria-hidden="true"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-600 dark:text-gray-400">Belum ada riwayat pesanan</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Pesanan yang diselesaikan atau ditolak akan muncul di sini</p>
            </div>
        </div>
    @endforelse

</div>