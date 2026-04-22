<div class="flex flex-col h-full">
    {{-- Area Bubble Chat --}}
    <div 
        id="chat-container"
        class="flex-1 overflow-y-auto pb-24 p-4 space-y-4"
        x-data
        x-init="$el.scrollTop = $el.scrollHeight"
        @scroll-to-bottom.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight })"
    >
        {{-- Header Info Jasa --}}
        <div class="bg-white rounded-2xl p-4 mb-6 shadow-sm border border-gray-100 flex flex-col gap-3">
            {{-- Baris Atas: Info Jasa & Tombol Detail --}}
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 flex-shrink-0 rounded-xl overflow-hidden bg-gray-100 border border-gray-50">
                    <img 
                        src="{{ asset('storage/' . ($data_pesanan->order->jasa->first_thumbnail ?? 'default.jpg')) }}" 
                        alt="Thumbnail Jasa"
                        class="w-full h-full object-cover"
                    >
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Layanan yang dipesan</p>
                    <h1 class="text-sm font-bold text-gray-900 truncate">{{ $data_pesanan->order->jasa->nama_jasa }}</h1>
                </div>
                <a 
                    href="{{ route('rincian.pesanan', $data_pesanan->order_id) }}" 
                    wire:navigate 
                    class="p-2 bg-blue-50 text-blue-600 rounded-lg flex items-center flex-col"
                >
                    <i class="bi bi-file-earmark-text text-lg"></i>
                    <span class="text-[10px]">Detail</span>
                </a>
            </div>

            {{-- Baris Bawah: Grid Status & Ringkasan --}}
            <div class="grid grid-cols-2 gap-y-3 gap-x-4 pt-3 border-t border-gray-50">
                {{-- Tanggal & Waktu --}}
                <div>
                    <p class="text-[9px] text-gray-400 uppercase font-semibold">Jadwal Servis</p>
                    <p class="text-[11px] font-medium text-gray-700">
                        <i class="bi bi-calendar3 text-blue-500 mr-1"></i>
                        {{ \Carbon\Carbon::parse($data_pesanan->order->order_date)->translatedFormat('d M Y') }}, 
                        {{ \Carbon\Carbon::parse($data_pesanan->order->order_time)->format('H:i') }}
                    </p>
                </div>

                {{-- Status --}}
                <div class="text-right">
                    <p class="text-[9px] text-gray-400 uppercase font-semibold">Status Pesanan</p>
                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-tighter
                        {{ $data_pesanan->order->lacak_pesanan->first()->status_order ?? '' == 'menunggu_konfirmasi' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                        {{ str_replace('_', ' ', $data_pesanan->order->lacak_pesanan->first()->status_order ?? 'Status Tidak Tersedia') }}
                    </span>
                </div>

                {{-- Total Harga --}}
                <div class="col-span-2 bg-gray-50 p-2 rounded-xl flex justify-between items-center">
                    <p class="text-[10px] text-gray-500 font-medium">Total Biaya (Estimasi)</p>
                    <p class="text-sm font-black text-blue-600">
                        Rp {{ number_format($data_pesanan->order->total_harga, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        @php $lastDate = null; @endphp

        @forelse($messages as $msg)
            @php
                $msgDate = $msg->created_at->translatedFormat('Y-m-d');
                $today = now()->translatedFormat('Y-m-d');
                $yesterday = now()->subDay()->translatedFormat('Y-m-d');
            @endphp

            @if($lastDate !== $msgDate)
                <div class="flex justify-center my-4">
                    <span class="text-[10px] bg-gray-200/50 backdrop-blur-sm px-3 py-1 rounded-full text-gray-600 font-semibold shadow-sm uppercase tracking-tighter">
                        @if($msgDate == $today)
                            Hari Ini
                        @elseif($msgDate == $yesterday)
                            Kemarin
                        @elseif($msg->created_at->isCurrentWeek())
                            {{ $msg->created_at->translatedFormat('l') }}
                        @else
                            {{ $msg->created_at->translatedFormat('d F Y') }}
                        @endif
                    </span>
                </div>
                @php $lastDate = $msgDate; @endphp
            @endif

            {{-- Bubble Chat --}}
            @php $isMine = $msg->sender_id === Auth::id(); @endphp

            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} items-end gap-2">

                <div class="max-w-[80%] flex flex-col {{ $isMine ? 'items-end' : 'items-start' }}">
                    {{-- Bubble Content --}}
                    @if($msg->type === 'image')
                        {{-- Foto Message --}}
                        <div class="rounded-2xl overflow-hidden shadow-sm {{ $isMine ? 'rounded-br-none' : 'rounded-bl-none' }}">
                            <img 
                                src="{{ asset('storage/' . $msg->message) }}" 
                                alt="Foto"
                                class="max-w-full max-h-64 object-cover cursor-pointer hover:opacity-90 transition"
                                @click="window.open('{{ asset('storage/' . $msg->message) }}')"
                            >
                        </div>
                    @else
                        {{-- Text Message --}}
                        <div class="px-4 py-2.5 rounded-2xl text-sm shadow-sm relative
                            {{ $isMine 
                                ? 'bg-blue-600 text-white rounded-br-none' 
                                : 'bg-white text-gray-800 border border-gray-200 rounded-bl-none' 
                            }}">
                            <p class="leading-relaxed whitespace-pre-wrap">{{ $msg->message }}</p>
                        </div>
                    @endif
                    
                    {{-- Meta Data --}}
                    <div class="flex items-center gap-1.5 mt-1 px-1">
                        <span class="text-[10px] text-gray-400">
                            {{ $msg->created_at->format('H:i') }}
                        </span>
                        @if($isMine)
                            <i class="bi {{ $msg->is_read ? 'bi-check2-all text-blue-500' : 'bi-check2 text-gray-300' }} text-xs"></i>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center h-64 opacity-30">
                <i class="bi bi-chat-dots text-5xl mb-3"></i>
                <p class="text-xs font-bold uppercase tracking-widest">Mulai Percakapan</p>
            </div>
        @endforelse
    </div>

    {{-- Input Pesan --}}
    <div class="fixed bottom-0 w-full max-w-md mx-auto left-0 right-0 bg-white/80 backdrop-blur-lg border-t border-gray-100 p-3 z-50">

         @if($photoPreview)
            <div class="flex-shrink-0 bg-white border-t border-gray-200 p-3">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <img 
                            src="{{ $photoPreview }}" 
                            alt="Preview"
                            class="h-20 w-20 object-cover rounded-lg border border-gray-200"
                        >
                        <button 
                            wire:click="removePhoto"
                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-sm hover:bg-red-600 transition"
                        >
                            <i class="bi bi-x-lg text-xs"></i>
                        </button>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700">{{ $photo->getClientOriginalName() }}</p>
                        <p class="text-xs text-gray-400">{{ round($photo->getSize() / 1024) }} KB</p>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="sendMessage" class="flex items-end gap-2 bg-gray-100 rounded-2xl p-1.5 focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-100 transition-all">
            <div class="flex-shrink-0">
                <input 
                    type="file" 
                    id="photo-input"
                    wire:model="photo"
                    accept="image/*"
                    class="hidden"
                >
                <label 
                    for="photo-input"
                    class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl cursor-pointer transition-all {{ $photo ? 'text-blue-600 bg-blue-50' : '' }}"
                >
                    <i class="bi bi-image text-xl"></i>
                </label>
            </div>

            <textarea 
                wire:model.live="message"
                placeholder="{{ $photo ? 'Tambahkan keterangan (opsional)...' : 'Ketik pesan...' }}"
                rows="1"
                class="flex-1 bg-transparent border-none focus:ring-0 text-sm py-2 px-1 max-h-32 resize-none overflow-y-auto scrollbar-hide"
                x-data="{ 
                    resize() { 
                        $el.style.height = 'auto'; 
                        $el.style.height = $el.scrollHeight + 'px' 
                    } 
                }"
                x-init="resize()"
                @input="resize()"
                @keydown.enter.prevent="if(!$event.shiftKey) { $wire.sendMessage(); $el.style.height = '40px' }"
            ></textarea>

            <button 
                type="submit" 
                @disabled(empty(trim($message)))
                wire:loading.attr="disabled"
                wire:target="sendMessage,photo"
                class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-blue-600 text-white rounded-xl shadow-md hover:bg-blue-700 active:scale-95 disabled:opacity-50 transition-all"
            >
                <span wire:loading.remove wire:target="sendMessage,photo">
                    <i class="bi bi-send-fill text-sm"></i>
                </span>
                <span wire:loading wire:target="sendMessage,photo">
                    <i class="bi bi-arrow-repeat animate-spin text-sm"></i>
                </span>
            </button>
        </form>
        <div class="h-2"></div>
    </div>
</div>