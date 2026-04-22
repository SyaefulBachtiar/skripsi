<div class="space-y-3">

    @forelse($data as $item)
        @php
            $lacakCollection = is_iterable($item->lacak_pesanan)
                ? collect($item->lacak_pesanan)
                : collect([$item->lacak_pesanan]);

            $latestStatus = $lacakCollection->last()?->status_order ?? '-';
            $isSelesai    = $lacakCollection->contains('status_order', 'selesai');
            $sudahReview  = \App\Models\Review::where('id_order', $item->id)->exists();

            $statusColor = match($latestStatus) {
                'selesai'             => 'bg-green-100 text-green-700',
                'dikonfirmasi'        => 'bg-blue-100 text-blue-700',
                'dikerjakan'          => 'bg-green-100 text-green-700',
                'menunggu_konfirmasi' => 'bg-yellow-100 text-yellow-700',
                'dibatalkan'          => 'bg-red-100 text-red-700',
                default               => 'bg-gray-100 text-gray-600',
            };

            $dotColor = match($latestStatus) {
                'selesai'             => 'bg-green-500',
                'dikonfirmasi'        => 'bg-blue-500',
                'dikerjakan'          => 'bg-green-500',
                'menunggu_konfirmasi' => 'bg-yellow-400',
                'dibatalkan'          => 'bg-red-500',
                default               => 'bg-gray-400',
            };

            $tlDotFn = fn($s) => match($s) {
                'selesai'             => 'bg-green-500 ring-green-100',
                'dikonfirmasi'        => 'bg-blue-500 ring-blue-100',
                'dikerjakan'          => 'bg-green-500 ring-green-100',
                'menunggu_konfirmasi' => 'bg-yellow-400 ring-yellow-100',
                'dibatalkan'          => 'bg-red-500 ring-red-100',
                default               => 'bg-gray-400 ring-gray-100',
            };
        @endphp

        <div x-data="{ open: false }"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

            {{-- ── Card Header (klik untuk expand) ── --}}
            <div @click="open = !open"
                 class="flex items-center gap-3 px-4 py-3.5 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors select-none">

                {{-- Avatar --}}
                <img src="{{ asset('storage/' . ($item->jasa->technician->foto_wajah ?? 'default.png')) }}"
                     alt="{{ $item->jasa->technician->nama_asli }}"
                     class="w-12 h-12 rounded-full object-cover border-2 border-blue-500 flex-shrink-0">

                {{-- Info tengah --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-gray-900 dark:text-white text-sm leading-tight truncate">
                            {{ $item->jasa->technician->nama_asli }}
                        </span>
                        {{-- Status badge inline di samping nama --}}
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold flex-shrink-0 {{ $statusColor }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                            {{ str_replace('_', ' ', ucfirst($latestStatus)) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5">
                        {{ $item->jasa->nama_jasa }}
                    </p>
                    {{-- Chat button di bawah nama jasa, tidak wrap dengan nama --}}
                    <button type="button"
                            wire:click.stop="navigateChatMsg('{{ $item->chat_room->id }}')"
                            class="relative inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="bi bi-chat-dots"></i> Chat

                        @if($item->chat_room->unread_count > 0)
                                <span class="absolute -top-1 right-1 translate-x-4 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-gray-900">
                                    {{ $item->chat_room->unread_count > 99 ? '99+' : $item->chat_room->unread_count }}
                                </span>
                        @endif
                    </button>
                </div>

                {{-- Chevron --}}
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
                     :class="open ? 'rotate-180' : ''"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            {{-- ── Expandable Body ── --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 style="display: none;"
                 class="border-t border-gray-100 dark:border-gray-700">

                <div class="px-4 py-4">

                    {{-- ── Timeline ── --}}
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Riwayat Status</p>

                    <ol class="relative border-l-2 border-gray-100 dark:border-gray-700 ml-1.5 space-y-4">
                        @foreach($lacakCollection as $pesanan)
                            @if($pesanan)
                            <li class="ml-4 pb-1">
                                {{-- Timeline dot --}}
                                <span class="absolute -left-[7px] w-3.5 h-3.5 rounded-full ring-4 ring-white dark:ring-gray-800 {{ $tlDotFn($pesanan->status_order) }}"></span>

                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wide leading-tight">
                                            {{ str_replace('_', ' ', $pesanan->status_order) }}
                                        </p>
                                        @if($pesanan->note)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 italic">
                                                "{{ $pesanan->note }}"
                                            </p>
                                        @endif

                                        @if($pesanan->foto_bukti)
                                            <div class="mt-2" x-data="{ modal: false }">
                                                <p class="text-[10px] font-semibold text-gray-400 uppercase mb-1">Bukti</p>
                                                <img @click="modal = true"
                                                     src="{{ asset('storage/' . $pesanan->foto_bukti) }}"
                                                     class="w-16 h-16 object-cover rounded-lg border border-gray-200 cursor-zoom-in hover:opacity-80 transition">
                                                <div x-show="modal"
                                                     x-transition.opacity
                                                     @click.self="modal = false"
                                                     @keydown.escape.window="modal = false"
                                                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80"
                                                     style="display:none;">
                                                    <div class="relative w-full max-w-lg">
                                                        <button @click="modal = false"
                                                                class="absolute -top-8 right-0 text-white text-2xl leading-none">&times;</button>
                                                        <img src="{{ asset('storage/' . $pesanan->foto_bukti) }}"
                                                             class="w-full rounded-xl shadow-2xl">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <time class="flex-shrink-0 text-[10px] text-gray-400 whitespace-nowrap pt-0.5">
                                        {{ $pesanan->created_at->format('d M, H:i') }}
                                    </time>
                                </div>
                            </li>
                            @endif
                        @endforeach
                    </ol>

                    {{-- ── Form Review ── --}}
                    @if($isSelesai && !$sudahReview)
                        <div class="mt-5 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800/50">
                            <h4 class="flex items-center gap-2 text-sm font-bold text-amber-700 dark:text-amber-300 mb-3">
                                <i class="bi bi-star-fill text-amber-400"></i> Berikan Ulasan
                            </h4>

                            <div x-data="{ hov: 0 }">
                                {{-- Bintang --}}
                                <div class="flex items-center gap-2 mb-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                                wire:click="$set('rating', {{ $i }})"
                                                @mouseenter="hov = {{ $i }}"
                                                @mouseleave="hov = 0"
                                                class="text-2xl leading-none focus:outline-none transition-transform"
                                                :class="{{ $i }} <= (hov || {{ $rating }}) ? 'text-amber-400 scale-110' : 'text-gray-300'"
                                        >★</button>
                                    @endfor
                                    @if($rating > 0)
                                        <span class="text-xs text-amber-600 font-semibold ml-1">{{ $rating }} Bintang</span>
                                    @endif
                                </div>
                                @error('rating') <p class="text-red-500 text-[10px] mb-2">{{ $message }}</p> @enderror

                                {{-- Komentar --}}
                                <textarea wire:model.defer="comment"
                                          rows="3"
                                          placeholder="Ceritakan pengalaman Anda..."
                                          class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-amber-400 focus:border-transparent resize-none"></textarea>
                                @error('comment') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror

                                {{-- Upload Foto --}}
                                <div class="mt-3">
                                    <p class="text-[10px] font-semibold text-gray-400 uppercase mb-2">
                                        Foto <span class="normal-case font-normal">(maks. 2)</span>
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($foto_review as $index => $photo)
                                            <div class="relative w-16 h-16">
                                                <img src="{{ $photo->temporaryUrl() }}"
                                                     class="w-full h-full object-cover rounded-lg border border-gray-200">
                                                <button type="button"
                                                        wire:click="removePhoto({{ $index }})"
                                                        class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center shadow">
                                                    &times;
                                                </button>
                                            </div>
                                        @endforeach

                                        @if(count($foto_review) < 2)
                                            <label class="w-16 h-16 flex flex-col items-center justify-center gap-0.5 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-amber-400 hover:bg-amber-50/50 transition-colors">
                                                <i class="bi bi-camera text-gray-400"></i>
                                                <span class="text-[9px] text-gray-400">Tambah</span>
                                                <input type="file" wire:model="foto_review" multiple accept="image/*" class="sr-only">
                                            </label>
                                        @endif
                                    </div>
                                    @error('foto_review.*') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                </div>

                                <button type="button"
                                        wire:click="submitReview('{{ $item->id }}', '{{ $item->jasa->id_technician }}', '{{ $item->id_jasa }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="submitReview"
                                        class="mt-4 w-full py-2.5 bg-amber-500 hover:bg-amber-600 disabled:opacity-60 text-white text-sm font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                                    <span wire:loading.remove wire:target="submitReview">
                                        <i class="bi bi-send-fill"></i> Kirim Ulasan
                                    </span>
                                    <span wire:loading wire:target="submitReview">
                                        <i class="bi bi-arrow-repeat animate-spin"></i> Mengirim...
                                    </span>
                                </button>
                            </div>
                        </div>

                    @elseif($sudahReview)
                        <div class="mt-4 flex items-center gap-2 px-3 py-2.5 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800/50">
                            <i class="bi bi-check-circle-fill text-green-500 text-sm"></i>
                            <span class="text-xs font-semibold text-green-700 dark:text-green-400">Ulasan sudah diberikan</span>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    @empty
        <div class="flex flex-col items-center justify-center gap-3 py-16 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 text-center">
            <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <i class="bi bi-inbox text-2xl text-gray-400"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-500 text-sm">Belum ada pesanan</p>
                <p class="text-xs text-gray-400 mt-0.5">Pesanan aktif Anda akan muncul di sini</p>
            </div>
        </div>
    @endforelse

</div>