<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
        <div>
            <h1 class="text-sm font-bold text-gray-800 dark:text-white">Pesanan Masuk</h1>
            {{-- <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ count($data->order) }} jasa memiliki Pesanan</p> --}}
        </div>
        <div class="w-9 h-9 flex items-center justify-center rounded-xl bg-green-500 flex-shrink-0">
            <i class="bi bi-list-ol text-white text-base leading-none"></i>
        </div>
    </div>

    {{-- ── List ── --}}
    <div class="divide-y divide-gray-100 dark:divide-gray-700">

        @forelse($data as $jasa)
            @foreach($jasa->order as $order)
                @php
                    $hasLayanan = !empty($order['layanan_tambahan'][0]);
                    $avatar = $order->customer->user->avatar
                        ? asset('storage/' . $order->customer->user->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($order->customer->user->name) . '&background=0D8ABC&color=fff';
                @endphp

                <div x-data="{ open: false }">

                    {{-- ── Card Header (klik) ── --}}
                    <div @click="open = !open"
                         class="px-4 py-3.5 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors select-none">

                        {{-- Row 1: Avatar + Nama + Chevron --}}
                        <div class="flex items-center gap-3">
                            <img src="{{ $avatar }}"
                                 alt="{{ $order->customer->user->name }}"
                                 class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-600 flex-shrink-0">

                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase leading-none mb-0.5">Pelanggan</p>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold text-gray-800 dark:text-white truncate">
                                        {{ $order->customer->user->name }}
                                    </p>

                                    <button
                                        type="button"
                                        wire:click="navigateChatMsg({{ $order->chat_room->id }})"
                                        class="relative"
                                    >
                                        <i class="bi bi-chat-dots text-gray-500"></i>

                                        @if($order->chat_room->unread_count > 0)
                                            <span class="absolute -top-1 -right-4 flex-shrink-0 w-4 h-4 bg-blue-600 text-white text-[10px] flex items-center justify-center rounded-full font-bold">
                                                {{ $order->chat_room->unread_count }}
                                            </span>
                                        @endif
                                    </button>
                                </div>

                            </div>

                            <i class="bi bi-chevron-down text-gray-400 text-xs flex-shrink-0 transition-transform duration-200"
                               :class="open ? 'rotate-180' : ''"></i>
                        </div>

                        {{-- Row 2: ID + Nama Jasa + Jadwal --}}
                        <div class="flex items-end justify-between mt-2.5 pl-[52px]">
                            <div class="min-w-0">
                                <span class="text-[9px] font-bold text-blue-500 uppercase tracking-wider">#{{ $order['id'] }}</span>
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 truncate leading-tight">
                                    {{ $jasa->nama_jasa ?? 'Jasa Servis' }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0 ml-3">
                                <p class="text-[10px] font-semibold text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                                </p>
                                <p class="text-[9px] text-gray-400">{{ \Carbon\Carbon::parse($order->order_time)->format('H:i') }}</p>
                            </div>
                        </div>
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

                        <div class="px-4 py-4 space-y-4">

                            {{-- Keluhan --}}
                            @if(!empty($order->keluhan))
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">
                                        Keluhan Pelanggan
                                    </p>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($order->keluhan as $k)
                                            <span class="px-2.5 py-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-xs text-gray-600 dark:text-gray-300">
                                                {{ $k }}
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
                                        @foreach($order->layanan_tambahan[0] as $ltJson)
                                            @php $lt = json_decode($ltJson, true); @endphp
                                            <div class="flex items-center justify-between px-3 py-2">
                                                <span class="text-xs text-gray-600 dark:text-gray-300">{{ $lt->nama ?? '-' }}</span>
                                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400">
                                                    Rp {{ number_format($lt->harga ?? 0, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Footer: Total + Aksi --}}
                            <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                                <div>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wide leading-none mb-0.5">
                                        Total
                                    </p>
                                    <p class="text-base font-black text-green-600 dark:text-green-400">
                                        Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button 
                                        wire:click="tolak({{ $order->id }})"
                                        type="button"
                                        class="px-3.5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-md transition-colors"
                                    >
                                        Tolak
                                    </button>
                                    <button 
                                        wire:click="konfirmasi({{ $order->id }})"
                                        type="button"
                                        class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-md transition-colors shadow-sm"
                                    >
                                        Konfirmasi
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            @endforeach
        @empty
            <div class="flex flex-col items-center justify-center gap-3 py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="bi bi-inbox text-2xl text-gray-400"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">Tidak ada antrian</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Pesanan baru akan muncul di sini</p>
                </div>
            </div>
        @endforelse

        @if($data->hasPages())
            <div class="pt-4 border-t border-gray-100">
                {{ $data->links() }}
            </div>
        @endif

    </div>
</div>