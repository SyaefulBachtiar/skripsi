<div class="space-y-2.5">
    @forelse($data as $item)
        @php
            $lacakCollection = is_iterable($item->lacak_pesanan)
                ? collect($item->lacak_pesanan)
                : collect([$item->lacak_pesanan]);
            $latestStatus   = $lacakCollection->last()?->status_order ?? '-';
            $isSelesai      = $lacakCollection->contains('status_order', 'selesai');
            $isSudahDibayar = $lacakCollection->contains('status_order', 'sudah_dibayar');
            $sudahReview    = \App\Models\Review::where('id_order', $item->id)->exists();

            $statusConfig = match($latestStatus) {
                'selesai'             => ['badge' => 'bg-green-100 text-green-800',  'dot' => 'bg-green-500',  'ring' => 'ring-green-100'],
                'sudah_dibayar'       => ['badge' => 'bg-teal-100 text-teal-800',    'dot' => 'bg-teal-500',   'ring' => 'ring-teal-100'],
                'dikonfirmasi'        => ['badge' => 'bg-blue-100 text-blue-800',    'dot' => 'bg-blue-500',   'ring' => 'ring-blue-100'],
                'dikerjakan'          => ['badge' => 'bg-indigo-100 text-indigo-800','dot' => 'bg-indigo-500', 'ring' => 'ring-indigo-100'],
                'menunggu_konfirmasi' => ['badge' => 'bg-amber-100 text-amber-800',  'dot' => 'bg-amber-400',  'ring' => 'ring-amber-100'],
                'dibatalkan'          => ['badge' => 'bg-red-100 text-red-800',      'dot' => 'bg-red-500',    'ring' => 'ring-red-100'],
                default               => ['badge' => 'bg-gray-100 text-gray-600',    'dot' => 'bg-gray-400',   'ring' => 'ring-gray-100'],
            };

            $tlDot = fn($s) => match($s) {
                'selesai','dikerjakan' => 'bg-green-500 ring-green-100',
                'sudah_dibayar'        => 'bg-teal-500 ring-teal-100',
                'dikonfirmasi'         => 'bg-blue-500 ring-blue-100',
                'menunggu_konfirmasi'  => 'bg-amber-400 ring-amber-100',
                'dibatalkan'           => 'bg-red-500 ring-red-100',
                default                => 'bg-gray-400 ring-gray-100',
            };
        @endphp

        <div x-data="{ open: false }"
             class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">

            {{-- ── Card Header ── --}}
            <div @click="open = !open"
                 class="flex items-center gap-3 px-4 py-3.5 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors select-none">

                {{-- Avatar --}}
                <div class="relative flex-shrink-0">
                    <img src="{{ asset('storage/' . ($item->jasa->technician->foto_wajah ?? 'default.png')) }}"
                         alt="{{ $item->jasa->technician->nama_asli }}"
                         class="w-11 h-11 rounded-full object-cover ring-2 ring-blue-200 dark:ring-blue-900">
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    {{-- Baris 1: Nama + status badge --}}
                    <div class="flex items-center gap-2 flex-wrap mb-0.5">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[150px]">
                            {{ $item->jasa->technician->nama_asli }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium flex-shrink-0 {{ $statusConfig['badge'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                            {{ str_replace('_', ' ', ucfirst($latestStatus)) }}
                        </span>
                    </div>
                    {{-- Baris 2: Nama jasa --}}
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mb-1.5">
                        {{ $item->jasa->nama_jasa }}
                    </p>
                    {{-- Baris 3: Chat pill --}}
                    <button type="button"
                            wire:click.stop="navigateChatMsg('{{ $item->chat_room->id }}')"
                            class="relative inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium
                                   bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400
                                   hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400
                                   transition-colors">
                        <i class="bi bi-chat-dots text-xs"></i>
                        Chat
                        @if(($item->chat_room->unread_count ?? 0) > 0)
                            <span class="absolute -top-1.5 -right-1 flex h-4 w-4 items-center justify-center rounded-full
                                         bg-red-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-gray-900">
                                {{ $item->chat_room->unread_count > 99 ? '99+' : $item->chat_room->unread_count }}
                            </span>
                        @endif
                    </button>
                </div>

                {{-- Chevron --}}
                <i class="bi bi-chevron-down text-gray-400 flex-shrink-0 transition-transform duration-200 text-sm"
                   :class="open ? 'rotate-180' : ''"></i>
            </div>

            {{-- ── Expandable Body ── --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 style="display:none"
                 class="border-t border-gray-100 dark:border-gray-800 divide-y divide-gray-100 dark:divide-gray-800">

                <div class="px-4 py-4">

                    {{-- ── Timeline ── --}}
                    <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">
                        Riwayat status
                    </p>
                    <ol class="relative border-l border-gray-200 dark:border-gray-700 ml-2 space-y-4">
                        @foreach($lacakCollection as $lacak)
                            @if($lacak)
                                <li class="ml-5">
                                    <span class="absolute -left-[5px] w-2.5 h-2.5 rounded-full ring-4 ring-white dark:ring-gray-900 {{ $tlDot($lacak->status_order) }}"></span>
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[13px] font-medium text-gray-800 dark:text-gray-200 capitalize leading-tight">
                                                {{ str_replace('_', ' ', $lacak->status_order) }}
                                            </p>
                                            @if($lacak->note)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 italic">
                                                    "{{ $lacak->note }}"
                                                </p>
                                            @endif
                                            @if($lacak->foto_bukti)
                                                <div class="mt-2" x-data="{ modal: false }">
                                                    <p class="text-[10px] font-semibold text-gray-400 uppercase mb-1">Bukti foto</p>
                                                    <img @click="modal = true"
                                                         src="{{ asset('storage/' . $lacak->foto_bukti) }}"
                                                         alt="Bukti"
                                                         class="w-14 h-14 object-cover rounded-lg border border-gray-200 dark:border-gray-700 cursor-zoom-in hover:opacity-80 transition-opacity">
                                                    {{-- Lightbox --}}
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
                                                            <img src="{{ asset('storage/' . $lacak->foto_bukti) }}"
                                                                 alt="Bukti besar"
                                                                 class="w-full rounded-xl">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <time class="flex-shrink-0 text-[11px] text-gray-400 dark:text-gray-500 whitespace-nowrap pt-0.5">
                                            {{ $lacak->created_at->format('d M, H:i') }}
                                        </time>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </div>

                {{-- ── Rincian Tagihan ── --}}
                @if($isSelesai || $isSudahDibayar)
                    <div class="px-4 py-4">
                        <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">
                            Rincian tagihan
                        </p>
                        <div class="border border-gray-100 dark:border-gray-800 rounded-xl overflow-hidden">
                            {{-- Baris jasa dasar --}}
                            <div class="flex items-center justify-between px-4 py-3 gap-3">
                                <span class="text-sm text-gray-600 dark:text-gray-300 truncate">
                                    {{ $item->jasa->nama_jasa ?? 'Jasa dasar' }}
                                </span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex-shrink-0">
                                    Rp {{ number_format($item->jasa->harga_jasa ?? 0, 0, ',', '.') }}
                                </span>
                            </div>

                            {{-- Item tambahan --}}
                            @if(count($item->detail_order ?? []) > 0)
                                <div class="border-t border-gray-100 dark:border-gray-800 px-4 py-3 space-y-2.5">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase">Tambahan</p>
                                    @foreach($item->detail_order as $detail)
                                        @php $ditolak = $detail->acc_customer === 0; @endphp
                                        <div class="flex items-start justify-between gap-3
                                                    pl-3 border-l-2 {{ $ditolak ? 'border-red-200 dark:border-red-900' : 'border-indigo-200 dark:border-indigo-700' }}">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm {{ $ditolak ? 'text-gray-400 dark:text-gray-600' : 'text-gray-700 dark:text-gray-300' }} truncate">
                                                    {{ $detail->nama_layanan_tambahan }}
                                                </p>
                                                @if($ditolak)
                                                    <p class="text-[11px] text-red-400 italic mt-0.5 flex items-center gap-1">
                                                        <i class="bi bi-x-circle text-xs"></i>
                                                        Ditolak — tidak ditagihkan
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="text-sm flex-shrink-0 {{ $ditolak ? 'line-through text-gray-300 dark:text-gray-600' : 'font-medium text-gray-800 dark:text-gray-200' }}">
                                                Rp {{ number_format($detail->harga_layanan_tambahan, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Total --}}
                            <div class="border-t border-gray-100 dark:border-gray-800 px-4 py-3 flex items-center justify-between bg-gray-50 dark:bg-gray-800/50">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">Total tagihan</p>
                                    @if($isSudahDibayar)
                                        <p class="text-[11px] text-green-600 dark:text-green-400 font-medium mt-0.5 flex items-center gap-1">
                                            <i class="bi bi-check-circle-fill text-xs"></i> Lunas
                                        </p>
                                    @else
                                        <p class="text-[11px] text-amber-500 dark:text-amber-400 mt-0.5">Belum dibayar</p>
                                    @endif
                                </div>
                                <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                    Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Tombol Bayar --}}
                        @if($latestStatus === 'selesai')
                            <button type="button"
                                    wire:click="bayarPesanan({{ $item->id }})"
                                    wire:loading.attr="disabled"
                                    class="mt-3 w-full flex items-center justify-center gap-2 py-3
                                           bg-green-600 hover:bg-green-700 active:scale-[0.98] disabled:opacity-60
                                           text-white text-sm font-semibold rounded-xl transition-all">
                                <span wire:loading.remove wire:target="bayarPesanan({{ $item->id }})">
                                    <i class="bi bi-wallet2 text-base"></i> Bayar sekarang
                                </span>
                                <span wire:loading wire:target="bayarPesanan({{ $item->id }})">
                                    <i class="bi bi-arrow-repeat animate-spin text-base"></i> Memproses...
                                </span>
                            </button>
                        @endif
                    </div>
                @endif

                {{-- ── Form Review ── --}}
                @if($isSudahDibayar && !$sudahReview)
                    <div class="px-4 py-4">
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/50 rounded-xl p-4">
                            <p class="flex items-center gap-2 text-sm font-semibold text-amber-800 dark:text-amber-300 mb-3">
                                <i class="bi bi-star-fill text-amber-400"></i>
                                Berikan ulasan
                            </p>

                            {{-- Bintang --}}
                            <div x-data="{ hov: 0 }" class="flex items-center gap-2 mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            wire:click="$set('rating', {{ $i }})"
                                            @mouseenter="hov = {{ $i }}"
                                            @mouseleave="hov = 0"
                                            class="text-[26px] leading-none transition-all focus:outline-none"
                                            :class="{{ $i }} <= (hov || {{ $rating }}) ? 'text-amber-400 scale-110' : 'text-gray-200 dark:text-gray-700'"
                                            aria-label="{{ $i }} bintang">
                                        ★
                                    </button>
                                @endfor
                                @if($rating > 0)
                                    <span class="text-xs text-amber-700 dark:text-amber-400 font-medium ml-1">
                                        {{ $rating }} bintang
                                    </span>
                                @endif
                            </div>
                            @error('rating') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror

                            {{-- Komentar --}}
                            <textarea wire:model.defer="comment"
                                      rows="3"
                                      placeholder="Ceritakan pengalaman Anda..."
                                      class="w-full px-3 py-2 text-sm rounded-lg border border-amber-200 dark:border-amber-700
                                             bg-white dark:bg-gray-900 dark:text-gray-200
                                             focus:ring-2 focus:ring-amber-400 focus:border-transparent
                                             resize-none transition-shadow"></textarea>
                            @error('comment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                            {{-- Upload Foto --}}
                            <div class="mt-3">
                                <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase mb-2">
                                    Foto <span class="normal-case font-normal">(maks. 2)</span>
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($foto_review as $idx => $fotoItem)
                                        <div class="relative w-14 h-14 flex-shrink-0">
                                            <img src="{{ $fotoItem->temporaryUrl() }}"
                                                 alt="Preview"
                                                 class="w-full h-full object-cover rounded-lg border border-amber-200 dark:border-amber-700">
                                            <button type="button"
                                                    wire:click="removePhoto({{ $idx }})"
                                                    class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full text-xs flex items-center justify-center shadow transition-colors"
                                                    aria-label="Hapus foto">
                                                ×
                                            </button>
                                        </div>
                                    @endforeach
                                    @if(count($foto_review) < 2)
                                        <label class="w-14 h-14 flex flex-col items-center justify-center gap-0.5
                                                       border-2 border-dashed border-amber-200 dark:border-amber-700 rounded-lg
                                                       cursor-pointer hover:border-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/30
                                                       transition-colors">
                                            <i class="bi bi-camera text-amber-400 text-base"></i>
                                            <span class="text-[10px] text-gray-400 dark:text-gray-500">Tambah</span>
                                            <input type="file" wire:model="foto_review" multiple accept="image/*" class="sr-only">
                                        </label>
                                    @endif
                                </div>
                                @error('foto_review.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Submit --}}
                            <button type="button"
                                    wire:click="submitReview('{{ $item->id }}', '{{ $item->jasa->id_technician }}', '{{ $item->id_jasa }}')"
                                    wire:loading.attr="disabled"
                                    wire:target="submitReview"
                                    class="mt-4 w-full flex items-center justify-center gap-2 py-2.5
                                           bg-amber-500 hover:bg-amber-600 active:scale-[0.98] disabled:opacity-60
                                           text-white text-sm font-semibold rounded-xl transition-all">
                                <span wire:loading.remove wire:target="submitReview">
                                    <i class="bi bi-send-fill text-xs"></i> Kirim ulasan
                                </span>
                                <span wire:loading wire:target="submitReview">
                                    <i class="bi bi-arrow-repeat animate-spin text-xs"></i> Mengirim...
                                </span>
                            </button>
                        </div>
                    </div>

                @elseif($sudahReview)
                    <div class="px-4 py-3">
                        <div class="flex items-center gap-2.5 px-3 py-2.5
                                    bg-green-50 dark:bg-green-900/20
                                    border border-green-100 dark:border-green-800/50
                                    rounded-xl">
                            <i class="bi bi-check-circle-fill text-green-500 dark:text-green-400 text-sm flex-shrink-0"></i>
                            <span class="text-xs font-medium text-green-700 dark:text-green-400">Ulasan sudah diberikan</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    @empty
        <div class="flex flex-col items-center justify-center gap-3 py-16 text-center
                    bg-white dark:bg-gray-900
                    rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <i class="bi bi-inbox text-xl text-gray-400 dark:text-gray-600"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Belum ada pesanan</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Pesanan aktif Anda akan muncul di sini</p>
            </div>
        </div>
    @endforelse
</div>