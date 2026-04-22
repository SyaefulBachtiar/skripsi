<div class="space-y-6">
    {{-- Search Input --}}
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="bi bi-search text-gray-400"></i>
        </div>
        <input 
            type="text" 
            wire:model.live="search"
            placeholder="Cari nama teknisi..."
            class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm shadow-sm"
        >
    </div>

    {{-- Data Pesan --}}
    <div class="space-y-3">
        @forelse($data_pesan as $chat)
            <div
                wire:click="navigateChatMsg('{{ $chat->id }}')"
                class="flex items-center gap-4 p-4 border-b border-gray-200 group w-full"
            >
                {{-- Avatar --}}
                <div class="relative flex-shrink-0">
                    <img 
                        src="{{ asset('storage/' . ($chat->technician->foto_wajah ?? 'default-avatar.jpg')) }}" 
                        alt="{{ $chat->technician->nama_asli }}"
                        class="w-14 h-14 rounded-full object-cover border-2 border-gray-100"
                    >
                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></span>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-start flex-col">
                            <h4 class="text-sm font-bold text-gray-900 truncate">
                                {{ $chat->technician->nama_asli }}
                            </h4>
                            <h4 class="text-sm font-semibold text-gray-600">
                                {{ $chat->order->jasa->nama_jasa }}
                            </h4>
                        </div>
                        
                        {{-- Waktu --}}
                        @if($chat->last_message)
                            @php
                                $lastChatDate = $chat->last_message->created_at;
                            @endphp
                            <span class="text-[11px] font-medium {{ $chat->unread_messages_count > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                                @if($lastChatDate->isToday())
                                    {{ $lastChatDate->format('H:i') }}
                                @elseif($lastChatDate->isYesterday())
                                    Kemarin
                                @elseif($lastChatDate->isCurrentWeek())
                                    {{ $lastChatDate->translatedFormat('l') }}
                                @else
                                    {{ $lastChatDate->format('d/m/y') }}
                                @endif
                            </span>
                        @else
                            <span class="text-[11px] text-gray-400">-</span>
                        @endif
                    </div>
                    
                    {{-- Pesan Terakhir --}}
                    <div class="flex items-center gap-2">
                        <p class="text-sm text-gray-500 truncate flex-1 {{ $chat->unread_messages_count > 0 ? 'font-medium text-gray-700' : '' }}">
                            @if($chat->last_message)
                                @if($chat->last_message->sender_id === auth()->id())
                                    <span class="text-gray-400">Anda:</span>
                                @endif
                                {{ $chat->last_message->message }}
                            @else
                                <span class="italic text-gray-400">Belum ada percakapan</span>
                            @endif
                        </p>
                        
                        {{-- Badge Unread --}}
                        @if($chat->unread_messages_count > 0)
                            <span class="flex-shrink-0 w-5 h-5 bg-blue-600 text-white text-[10px] flex items-center justify-center rounded-full font-bold">
                                {{ $chat->unread_messages_count }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-16 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm">
                    <i class="bi bi-chat-dots text-2xl text-gray-300"></i>
                </div>
                <p class="text-sm font-semibold text-gray-800">Tidak ada pesan ditemukan</p>
                <p class="text-xs text-gray-500 mt-1">Coba cari dengan nama teknisi lain</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($data_pesan->hasPages())
            <div class="pt-4 border-t border-gray-100">
                {{ $data_pesan->links() }}
            </div>
        @endif
    </div>
</div>