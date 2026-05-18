<div class="space-y-2.5 pb-20">

    {{-- Page Header --}}
    <div class="flex items-center gap-2 mb-4">
        <i class="bi bi-clock-history text-blue-600 text-lg" aria-hidden="true"></i>
        <h2 class="text-base font-semibold text-gray-800 dark:text-white">Riwayat pesanan</h2>
    </div>

    @forelse($riwayat as $item)
        @php
            $review = is_array($item['review'])
                ? (!empty($item['review']) ? (object) $item['review'][0] : null)
                : $item->review?->first();

            $fotoReview = $review?->foto_review ?? [];
            if (is_string($fotoReview)) {
                $fotoReview = json_decode($fotoReview, true) ?? [];
            }
        @endphp

        <div x-data="{ open: false }"
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
                    <img src="{{ asset('storage/' . ($item['jasa']['thumbnails'][0] ?? 'default.png')) }}"
                         alt="{{ $item['jasa']['nama_jasa'] }}"
                         class="w-13 h-13 rounded-xl object-cover flex-shrink-0 border border-gray-100 dark:border-gray-800"
                         style="width:52px;height:52px"
                         onerror="this.style.display='none'">

                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate leading-snug mb-1">
                            {{ $item['jasa']['nama_jasa'] }}
                        </h3>

                        {{-- Teknisi --}}
                        <div class="flex items-center gap-1.5 mb-1.5">
                            <img src="{{ asset('storage/' . ($item['jasa']['technician']['foto_wajah'] ?? 'default.png')) }}"
                                 alt="{{ $item['jasa']['technician']['nama_asli'] }}"
                                 class="w-4 h-4 rounded-full object-cover flex-shrink-0 border border-gray-200 dark:border-gray-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $item['jasa']['technician']['nama_asli'] }}
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
                    <span x-text="open ? 'Sembunyikan detail' : 'Lihat detail'"></span>
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
                    <div class="px-4 py-4">
                        <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2.5">
                            Keluhan / masalah
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($item['keluhan'] as $keluhan)
                                <span class="px-2.5 py-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-xs text-gray-600 dark:text-gray-300">
                                    {{ $keluhan }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Rincian Tagihan --}}
                <div class="px-4 py-4">
                    <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">
                        Rincian tagihan
                    </p>
                    <div class="border border-gray-100 dark:border-gray-800 rounded-xl overflow-hidden bg-white dark:bg-gray-900">

                        {{-- Jasa dasar --}}
                        <div class="flex items-center justify-between gap-3 px-4 py-3">
                            <span class="text-sm text-gray-600 dark:text-gray-300 truncate">
                                {{ $item['jasa']['nama_jasa'] ?? 'Jasa dasar' }}
                            </span>
                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex-shrink-0">
                                Rp {{ number_format($item['jasa']['harga_jasa'] ?? 0, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Detail tambahan --}}
                        @if(!empty($item['detail_order']) && count($item['detail_order']) > 0)
                            <div class="border-t border-gray-100 dark:border-gray-800 px-4 py-3 space-y-3">
                                <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase">
                                    Sparepart & tindakan tambahan
                                </p>
                                @foreach($item['detail_order'] as $detail)
                                    @php $ditolak = ($detail['acc_customer'] ?? 1) === 0; @endphp
                                    <div class="flex items-start justify-between gap-3
                                                pl-3 border-l-2 {{ $ditolak ? 'border-red-200 dark:border-red-900' : 'border-indigo-200 dark:border-indigo-700' }}
                                                {{ $ditolak ? 'opacity-60' : '' }}">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 truncate">
                                                {{ $detail['nama_layanan_tambahan'] }}
                                            </p>
                                            @if($ditolak)
                                                <p class="text-[11px] text-red-400 italic mt-0.5 flex items-center gap-1">
                                                    <i class="bi bi-x-circle text-xs" aria-hidden="true"></i>
                                                    Ditolak — tidak ditagihkan
                                                </p>
                                            @endif
                                        </div>
                                        <span class="text-sm flex-shrink-0 {{ $ditolak ? 'line-through text-gray-300 dark:text-gray-600' : 'font-medium text-gray-800 dark:text-gray-200' }}">
                                            Rp {{ number_format($detail['harga_layanan_tambahan'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Total --}}
                        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60">
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Total tagihan</p>
                                <p class="flex items-center gap-1 text-[11px] text-green-600 dark:text-green-400 font-medium mt-0.5">
                                    <i class="bi bi-check-circle-fill text-xs" aria-hidden="true"></i> Lunas
                                </p>
                            </div>
                            <span class="text-xl font-bold text-blue-700 dark:text-blue-400">
                                Rp {{ number_format($item['total_harga'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Ulasan --}}
                @if($review)
                    <div class="px-4 py-4">
                        <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">
                            Ulasan Anda
                        </p>
                        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl p-3">
                            {{-- Bintang --}}
                            <div class="flex items-center gap-1 mb-2" aria-label="{{ $review->rating ?? 0 }} dari 5 bintang">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= ($review->rating ?? 0) ? '-fill' : '' }} text-sm
                                              {{ $i <= ($review->rating ?? 0) ? 'text-amber-400' : 'text-gray-200 dark:text-gray-700' }}"
                                       aria-hidden="true"></i>
                                @endfor
                                <span class="ml-1.5 text-[11px] text-gray-400 dark:text-gray-500">
                                    {{ $review->rating ?? 0 }}/5
                                </span>
                            </div>

                            {{-- Komentar --}}
                            <p class="text-xs text-gray-600 dark:text-gray-400 italic leading-relaxed">
                                "{{ $review->text_comment ?? 'Tidak ada komentar' }}"
                            </p>

                            {{-- Foto review --}}
                            @if(!empty($fotoReview))
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @foreach($fotoReview as $foto)
                                        <div x-data="{ modal: false }">
                                            <img src="{{ asset('storage/' . $foto) }}"
                                                 @click="modal = true"
                                                 alt="Foto review"
                                                 class="w-12 h-12 rounded-lg object-cover border border-gray-100 dark:border-gray-800 cursor-zoom-in hover:opacity-80 transition-opacity">
                                            <div x-show="modal"
                                                 x-transition.opacity
                                                 @click.self="modal = false"
                                                 @keydown.escape.window="modal = false"
                                                 style="display:none;min-height:300px;background:rgba(0,0,0,0.85)"
                                                 class="fixed inset-0 z-50 flex items-center justify-center p-6">
                                                <div class="relative w-full max-w-sm">
                                                    <button @click="modal = false"
                                                            class="absolute -top-9 right-0 text-white text-3xl leading-none opacity-80 hover:opacity-100"
                                                            aria-label="Tutup">×</button>
                                                    <img src="{{ asset('storage/' . $foto) }}"
                                                         alt="Foto review besar"
                                                         class="w-full rounded-xl">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- CTA Pesan Kembali --}}
                <div class="px-4 py-4">
                    <a href="#"
                       class="flex items-center justify-center gap-2 w-full py-2.5
                              bg-blue-600 hover:bg-blue-700 active:scale-[0.98]
                              text-white text-sm font-medium rounded-xl transition-all">
                        <i class="bi bi-arrow-repeat text-sm" aria-hidden="true"></i>
                        Pesan kembali
                    </a>
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
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Belum ada riwayat pesanan</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Pesanan yang selesai akan muncul di sini</p>
            </div>
        </div>
    @endforelse

</div>