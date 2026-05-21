<div class="space-y-2.5 pb-20">

    {{-- Page Header --}}
    <div class="flex items-center gap-2 mb-4">
        <i class="bi bi-clock-history text-blue-600 text-lg" aria-hidden="true"></i>
        <h2 class="text-base font-semibold text-gray-800 dark:text-white">Riwayat pesanan</h2>
    </div>

    @forelse($riwayat as $item)
        @php
            // Membaca data review dari array dump
            $review = !empty($item['review']) ? (object) $item['review'][0] : null;

            $fotoReview = $review?->foto_review ?? [];
            if (is_string($fotoReview)) {
                $fotoReview = json_decode($fotoReview, true) ?? [];
            }
        @endphp

        <div x-data="{ open: false, showReplyForm: false, replyText: '' }"
             class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">

            {{-- ── Card Summary ── --}}
            <div class="p-4">

                {{-- Baris 1: Tanggal + Badge status --}}
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        {{ \Carbon\Carbon::parse($item['order_date'])->translatedFormat('d M Y') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500" aria-hidden="true"></span>
                        Selesai
                    </span>
                </div>

                {{-- Baris 2: Thumbnail + Info --}}
                <div class="flex gap-3 items-start">
                    <img src="{{ asset('storage/' . ($item['jasa']['thumbnails'][0] ?? 'default.jpg')) }}"
                         alt="{{ $item['jasa']['nama_jasa'] }}"
                         class="w-13 h-13 rounded-xl object-cover flex-shrink-0 border border-gray-100 dark:border-gray-800"
                         style="width:52px;height:52px">

                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate leading-snug mb-1">
                            {{ $item['jasa']['nama_jasa'] }}
                        </h3>

                        {{-- Pelanggan (Customer) --}}
                        <div class="flex items-center gap-1.5 mb-1.5">
                            <div class="w-4 h-4 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-700">
                                @if(!empty($item['customer']['user']['avatar']))
                                    <img src="{{ asset('storage/' . $item['customer']['user']['avatar']) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="bi bi-person-fill text-[10px] text-gray-400"></i>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                Pelanggan Order #{{ $item['id'] }}
                            </span>
                        </div>

                        {{-- Harga --}}
                        <p class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($item['total_harga'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Toggle button --}}
                <button type="button"
                        @click="open = !open"
                        class="w-full mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-center gap-1.5
                               text-[11px] font-medium text-gray-400 dark:text-gray-500
                               hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <span x-text="open ? 'Sembunyikan detail' : 'Lihat detail ulasan'"></span>
                    <i class="bi text-xs" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'" aria-hidden="true"></i>
                </button>
            </div>

            {{-- ── Expandable Detail ── --}}
            <div x-show="open"
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0 -translate-y-2"
                  x-transition:enter-end="opacity-100 translate-y-0"
                  x-transition:leave="transition ease-in duration-150"
                  x-transition:leave-start="opacity-100 translate-y-0"
                  x-transition:leave-end="opacity-0 -translate-y-2"
                  style="display:none"
                  class="border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 divide-y divide-gray-100 dark:divide-gray-800">

                {{-- Keluhan --}}
                @if(!empty($item['keluhan']))
                    <div class="px-4 py-3">
                        <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
                            Keluhan / masalah
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($item['keluhan'] as $keluhan)
                                <span class="px-2 py-0.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-md text-[11px] text-gray-600 dark:text-gray-300">
                                    {{ $keluhan }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ── SEKSI ULASAN PELANGGAN ── --}}
                @if($review)
                    <div class="px-4 py-4">
                        <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2.5">
                            Ulasan Pelanggan
                        </p>
                        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl p-3 shadow-sm">
                            
                            {{-- Bintang Rating --}}
                            <div class="flex items-center gap-1 mb-1.5" aria-label="{{ $review->rating ?? 0 }} dari 5 bintang">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= ($review->rating ?? 0) ? '-fill' : '' }} text-xs
                                              {{ $i <= ($review->rating ?? 0) ? 'text-amber-400' : 'text-gray-200 dark:text-gray-700' }}"
                                       aria-hidden="true"></i>
                                @endfor
                            </div>

                            {{-- Teks Komentar --}}
                            <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
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
                                                 style="display:none; background:rgba(0,0,0,0.85)"
                                                 class="fixed inset-0 z-[9999] flex items-center justify-center p-6">
                                                <div class="relative w-full max-w-sm">
                                                    <button @click="modal = false" class="absolute -top-9 right-0 text-white text-3xl opacity-80 hover:opacity-100">×</button>
                                                    <img src="{{ asset('storage/' . $foto) }}" class="w-full rounded-xl shadow-2xl">
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
                                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-2.5 border-l-2 border-blue-500">
                                        <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-0.5">Balasan Anda:</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 italic">"{{ $review->reply_comment }}"</p>
                                    </div>
                                @else
                                    {{-- Form Input Jika Belum Dibalas --}}
                                    <div class="space-y-2">
                                        <button type="button" 
                                                @click="showReplyForm = !showReplyForm"
                                                x-show="!showReplyForm"
                                                class="text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1 transition-colors">
                                            <i class="bi bi-reply"></i> Balas ulasan ini
                                        </button>

                                        <div x-show="showReplyForm" style="display: none;" class="space-y-2">
                                            <textarea x-model="replyText"
                                                      rows="2"
                                                      placeholder="Tulis ucapan terima kasih atau tanggapan Anda..."
                                                      class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none transition-shadow"></textarea>
                                            
                                            <div class="flex justify-end gap-2">
                                                <button type="button" 
                                                        @click="showReplyForm = false; replyText = ''"
                                                        class="px-2.5 py-1 text-[11px] font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                                    Batal
                                                </button>
                                                <button type="button"
                                                        wire:click="replyReview({{ $review->id }}, replyText)"
                                                        @click="showReplyForm = false"
                                                        :disabled="!replyText.trim()"
                                                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-[11px] font-semibold rounded-lg transition-all shadow-sm">
                                                    Kirim Balasan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                @else
                    <div class="px-4 py-3 text-center text-xs text-gray-400 italic">
                        Pelanggan belum memberikan ulasan bintang untuk orderan ini.
                    </div>
                @endif

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
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Belum ada riwayat pesanan</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Pesanan yang selesai akan muncul di sini</p>
            </div>
        </div>
    @endforelse

</div>