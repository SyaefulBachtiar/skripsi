<div class="space-y-3">

    {{-- ── Page Header ── --}}
    <div class="flex items-center gap-2 mb-5">
        <i class="bi bi-clock-history text-blue-600 text-lg"></i>
        <h2 class="text-base font-bold text-gray-800 dark:text-white">Riwayat Pesanan</h2>
    </div>

    @forelse($riwayat as $item)
        @php
            $review = is_array($item['review'])
                ? (!empty($item['review']) ? (object) $item['review'][0] : null)
                : $item->review?->first();

            $hasLayanan = !empty($item['layanan_tambahan'])
                && isset($item['layanan_tambahan'][0]['nama']);
        @endphp

        <div x-data="{ open: false }"
             class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

            {{-- ── Card Body ── --}}
            <div class="p-4">

                {{-- Row 1: Tanggal + Badge --}}
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-gray-400 font-medium">
                        {{ \Carbon\Carbon::parse($item['order_date'])->translatedFormat('d M Y') }}
                    </span>
                    <span class="px-2.5 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 text-[10px] font-bold uppercase rounded-full">
                        Selesai
                    </span>
                </div>

                {{-- Row 2: Thumbnail + Info Jasa --}}
                <div class="flex gap-3 items-start">
                    <img src="{{ asset('storage/' . ($item['jasa']['thumbnails'][0] ?? 'default.png')) }}"
                         alt="{{ $item['jasa']['nama_jasa'] }}"
                         class="w-14 h-14 rounded-xl object-cover flex-shrink-0 border border-gray-100 dark:border-gray-700">

                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white leading-tight line-clamp-1">
                            {{ $item['jasa']['nama_jasa'] }}
                        </h3>

                        {{-- Teknisi --}}
                        <div class="flex items-center gap-1.5 mt-1">
                            <img src="{{ asset('storage/' . ($item['jasa']['technician']['foto_wajah'] ?? 'default.png')) }}"
                                 alt="{{ $item['jasa']['technician']['nama_asli'] }}"
                                 class="w-4 h-4 rounded-full object-cover flex-shrink-0">
                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $item['jasa']['technician']['nama_asli'] }}
                            </span>
                        </div>

                        {{-- Harga --}}
                        <p class="text-xs font-bold text-blue-600 dark:text-blue-400 mt-1">
                            Rp {{ number_format($item['total_harga'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Toggle Detail --}}
                <button type="button"
                        @click="open = !open"
                        class="w-full mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-center gap-1.5 text-[10px] font-bold text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 uppercase tracking-widest transition-colors">
                    <span x-text="open ? 'Sembunyikan Detail' : 'Lihat Detail'"></span>
                    <i class="bi text-xs" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </button>
            </div>

            {{-- ── Expandable Detail ── --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 style="display: none;"
                 class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">

                <div class="p-4 space-y-4">

                    {{-- Keluhan --}}
                    @if(!empty($item['keluhan']))
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">
                                Keluhan / Masalah
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($item['keluhan'] as $keluhan)
                                    <span class="px-2.5 py-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-xs text-gray-600 dark:text-gray-300">
                                        {{ $keluhan }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Layanan Tambahan --}}
                    @if($hasLayanan)
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">
                                Layanan Tambahan
                            </p>
                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700 overflow-hidden">
                                @foreach($item['layanan_tambahan'] as $layanan)
                                    <div class="flex justify-between items-center px-3 py-2">
                                        <span class="text-xs text-gray-600 dark:text-gray-300">{{ $layanan['nama'] }}</span>
                                        <span class="text-xs font-bold text-gray-800 dark:text-white">
                                            Rp {{ number_format($layanan['harga'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Ulasan --}}
                    @if($review)
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">
                                Ulasan Anda
                            </p>
                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-blue-100 dark:border-blue-900/30 p-3">
                                {{-- Bintang --}}
                                <div class="flex items-center gap-0.5 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= ($review->rating ?? 0) ? '-fill' : '' }} text-amber-400 text-xs"></i>
                                    @endfor
                                    <span class="ml-1.5 text-[10px] font-semibold text-gray-500 dark:text-gray-400">
                                        {{ $review->rating ?? 0 }}/5
                                    </span>
                                </div>

                                {{-- Komentar --}}
                                <p class="text-xs text-gray-600 dark:text-gray-300 italic leading-relaxed">
                                    "{{ $review->text_comment ?? 'Tidak ada komentar' }}"
                                </p>

                                {{-- Foto Review --}}
                                @if(!empty($review->foto_review))
                                    <div class="flex gap-2 mt-3">
                                        @foreach($review->foto_review as $foto)
                                            <div x-data="{ modal: false }">
                                                <img src="{{ asset('storage/' . $foto) }}"
                                                     @click="modal = true"
                                                     class="w-12 h-12 rounded-lg object-cover border border-gray-100 dark:border-gray-700 cursor-zoom-in hover:opacity-80 transition">
                                                <div x-show="modal"
                                                     x-transition.opacity
                                                     @click.self="modal = false"
                                                     @keydown.escape.window="modal = false"
                                                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80"
                                                     style="display:none;">
                                                    <div class="relative w-full max-w-lg">
                                                        <button @click="modal = false"
                                                                class="absolute -top-8 right-0 text-white text-2xl leading-none">&times;</button>
                                                        <img src="{{ asset('storage/' . $foto) }}" class="w-full rounded-xl shadow-2xl">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- CTA --}}
                    <a href="#"
                       class="block w-full py-2.5 text-center text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-xl transition-colors shadow-sm">
                        <i class="bi bi-arrow-repeat mr-1"></i> Pesan Kembali
                    </a>

                </div>
            </div>

        </div>
    @empty
        <div class="flex flex-col items-center justify-center gap-3 py-16 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 text-center">
            <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <i class="bi bi-card-checklist text-2xl text-gray-400"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">Belum ada riwayat pesanan</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Pesanan yang selesai akan muncul di sini</p>
            </div>
        </div>
    @endforelse

</div>