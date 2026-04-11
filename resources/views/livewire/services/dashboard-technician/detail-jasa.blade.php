<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

    <div class="space-y-4">
        <div x-data="{ 
            activeSlide: 0, 
            slides: {{ count($jasa->thumbnails ?? []) }},
            startX: 0,
            isDragging: false,
            lightbox: false,
            next() { this.activeSlide = (this.activeSlide + 1) % this.slides },
            prev() { this.activeSlide = (this.activeSlide - 1 + this.slides) % this.slides },
            onDragStart(e) { 
                this.startX = e.touches ? e.touches[0].clientX : e.clientX; 
                this.isDragging = true; 
            },
            onDragEnd(e) { 
                if (!this.isDragging) return;
                const endX = e.changedTouches ? e.changedTouches[0].clientX : e.clientX;
                const diff = this.startX - endX;
                if (Math.abs(diff) > 50) diff > 0 ? this.next() : this.prev();
                this.isDragging = false;
            },
            openLightbox() {
                this.lightbox = true;
            }
        }" 
        class="relative w-full overflow-hidden group rounded-3xl bg-gray-50 border border-gray-100 shadow-sm"
        @touchstart="onDragStart($event)"
        @touchend="onDragEnd($event)"
        @mousedown="onDragStart($event)"
        @mouseup="onDragEnd($event)"
        @mouseleave="isDragging = false">

            {{-- Wrapper Images --}}
            <div class="relative h-72 md:h-[460px] select-none">
                @forelse($jasa->thumbnails as $index => $img)
                    <div 
                        x-show="activeSlide === {{ $index }}" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute inset-0"
                        @click="openLightbox()"
                    >
                        <img src="{{ asset('storage/' . $img) }}" 
                            alt="Thumbnail {{ $index + 1 }}" 
                            class="w-full h-full object-contain pointer-events-none">
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full bg-gray-50">
                        <img src="{{ asset('assets/icons/empty_image.webp') }}" 
                            alt="empty" 
                            class="w-24 h-24 object-contain opacity-20 grayscale">
                        <p class="text-gray-400 text-xs mt-3 font-medium">Belum ada foto</p>
                    </div>
                @endforelse
            </div>

            {{-- Dots --}}
            @if(count($jasa->thumbnails ?? []) > 1)
                <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                    @foreach($jasa->thumbnails as $index => $img)
                        <button 
                            @click="activeSlide = {{ $index }}" 
                            class="h-1.5 transition-all duration-300 rounded-full"
                            :class="activeSlide === {{ $index }} ? 'w-6 bg-indigo-600' : 'w-1.5 bg-white/70 hover:bg-white'"
                        ></button>
                    @endforeach
                </div>

                <div class="hidden md:block">
                    <button @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white p-2 rounded-full shadow-md text-gray-700 transition opacity-0 group-hover:opacity-100">
                        <i class="bi bi-chevron-left text-base"></i>
                    </button>
                    <button @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white p-2 rounded-full shadow-md text-gray-700 transition opacity-0 group-hover:opacity-100">
                        <i class="bi bi-chevron-right text-base"></i>
                    </button>
                </div>
            @endif

            {{-- Lightbox --}}
            @if(count($jasa->thumbnails ?? []) > 0)
                <div 
                    x-show="lightbox"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    @click.self="lightbox = false"
                    @keydown.escape.window="lightbox = false"
                    class="fixed inset-0 z-[99] bg-black/95 flex items-center justify-center p-4"
                    style="display: none;"
                >
                    <button @click="lightbox = false" class="absolute top-6 right-6 text-white/70 hover:text-white transition">
                        <i class="bi bi-x-lg text-3xl"></i>
                    </button>

                    @foreach($jasa->thumbnails as $index => $img)
                        <img 
                            x-show="activeSlide === {{ $index }}"
                            src="{{ asset('storage/' . $img) }}" 
                            class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl"
                        >
                    @endforeach

                    @if(count($jasa->thumbnails ?? []) > 1)
                        <div class="absolute bottom-10 text-white/50 text-sm font-medium">
                            <span x-text="activeSlide + 1"></span> / {{ count($jasa->thumbnails) }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-5">

        {{-- ── Header: Nama & Rating ── --}}
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-2 flex-1">
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight tracking-tight">
                    {{ $jasa->nama_jasa }}
                </h1>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1 px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-600 rounded-lg text-xs font-bold">
                        <i class="bi bi-star-fill text-[10px]"></i>
                        <span>4.8</span>
                    </div>
                    <span class="text-gray-300 text-xs">•</span>
                    <span class="text-gray-500 text-xs font-medium">12 Pelanggan</span>
                </div>
            </div>

            @if(auth()->user()->id !== $jasa->technician->user_id)
                <button class="p-2.5 rounded-2xl bg-gray-50 text-gray-400 hover:text-rose-500 hover:bg-rose-50 transition border border-gray-100 shrink-0">
                    <i class="bi bi-bookmark-fill text-lg"></i>
                </button>
            @else
                <a href="{{ route('edit.jasa', ['id_jasa' => $jasa->id]) }}" class="flex items-center gap-1 p-1 border border-green-300 bg-green-400/40 rounded">
                    <i class="bi bi-pencil-square text-green-500"></i>
                    <span class="text-md text-green-500">Edit</span>
                </a>
            @endif
        </div>

        {{-- ── Harga ── --}}
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-black text-indigo-600 tracking-tight">
                Rp {{ number_format($jasa->harga_jasa, 0, ',', '.') }}
            </span>
            <span class="text-xs text-gray-400 font-medium">/ kunjungan</span>
        </div>

        <hr class="border-gray-100">

        {{-- ── Card Teknisi ── --}}
        @if(auth()->user()->id !== $jasa->technician->user_id)
            @if($jasa->technician?->user)
                <div class="flex items-center justify-between gap-4 p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                    <div class="flex items-center gap-3">
                        <div class="relative shrink-0">
                            <img 
                                src="{{ $jasa->technician->user->profile_photo_url }}" 
                                alt="{{ $jasa->technician->user->name }}"
                                class="w-11 h-11 rounded-full object-cover border-2 border-white shadow-sm"
                            >
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ $jasa->technician->user->name }}</p>
                            <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-widest">Teknisi Elektronik</p>
                        </div>
                    </div>
                    <a href="#" class="flex items-center gap-1.5 px-3 py-2 bg-white text-indigo-600 rounded-xl text-xs font-bold border border-indigo-100 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                        <i class="bi bi-shop text-sm"></i>
                        <span>Profil</span>
                    </a>
                </div>
            @endif
        @endif

        {{-- ── Deskripsi ── --}}
        <div x-data="{ open: true }" class="rounded-2xl border border-gray-100 overflow-hidden bg-white">
            <button @click="open = !open" class="w-full flex justify-between items-center px-4 py-3.5 hover:bg-gray-50 transition">
                <span class="font-bold text-gray-700 text-xs uppercase tracking-widest">Deskripsi Layanan</span>
                <i class="bi bi-chevron-up text-gray-400 text-sm transition-transform duration-300" :class="open ? '' : 'rotate-180'"></i>
            </button>
            <div x-show="open" x-collapse>
                <div class="px-4 pb-4 text-gray-500 leading-relaxed text-sm whitespace-pre-line border-t border-gray-50">
                    {{ $jasa->deskripsi }}
                </div>
            </div>
        </div>

        {{-- ── Ulasan ── --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-xs uppercase tracking-widest">Ulasan Terakhir</h3>
                <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-bold">2 ulasan</span>
            </div>
            <div class="p-3.5 bg-amber-50 border border-amber-100 rounded-xl text-xs text-gray-600 italic leading-relaxed">
                "Sangat puas dengan pelayanannya, teknisi ramah dan tepat waktu."
            </div>
        </div>

        <hr class="border-gray-100">

        <div class="space-y-4">

            {{-- ── Pilih Tanggal ── --}}
            <div x-data="{ selectedDate: '' }">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-widest">Tanggal Tersedia</h3>
                    {{-- @if($jasa->is_setiap_hari)
                        <span class="text-[10px] bg-green-50 text-green-600 border border-green-200 px-2 py-0.5 rounded-full font-bold">Setiap Hari</span>
                    @endif --}}
                </div>

                <div class="flex flex-wrap gap-2">
                    @if($jasa->is_setiap_hari)
                        @foreach(range(0, 6) as $day)
                            @php $date = now()->addDays($day); @endphp
                            <label class="cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="order_date" 
                                    value="{{ $date->format('Y-m-d') }}" 
                                    class="peer sr-only" 
                                    x-model="selectedDate"
                                    wire:model.live="order_date"
                                >
                                <div class="flex flex-col items-center justify-center w-14 h-[72px] bg-white border border-gray-200 rounded-2xl transition-all
                                            peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-md
                                            hover:border-indigo-300 hover:shadow-sm">
                                    <span class="text-[9px] uppercase font-bold opacity-50 peer-checked:opacity-80 leading-none">{{ $date->translatedFormat('D') }}</span>
                                    <span class="text-xl font-black leading-tight">{{ $date->format('d') }}</span>
                                    <span class="text-[9px] font-medium opacity-60">{{ $date->translatedFormat('M') }}</span>
                                </div>
                            </label>
                        @endforeach
                    @else
                        @forelse($jasa->ketersediaan_tanggal as $index => $tgl)
                            @if($jasa->ketersediaan_status === 'Ketersediaan perlu diperbarui')
                                <div class="w-full p-4 bg-red-50 rounded-2xl border border-red-100 text-center">
                                    <p class="text-red-500 text-xs font-bold">Jadwal teknisi tidak tersedia</p>
                                </div>
                            @else
                                @php $carbonTgl = \Carbon\Carbon::parse($tgl); @endphp
                                <label class="cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="order_date" 
                                        value="{{ $tgl }}" 
                                        class="peer sr-only" 
                                        wire:model.live="order_time"
                                    >
                                    <div class="flex flex-col items-center justify-center w-14 h-[72px] bg-white border border-gray-200 rounded-2xl transition-all
                                                peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-md
                                                {{ $carbonTgl->isPast() && !$carbonTgl->isToday() ? 'opacity-35 pointer-events-none' : 'hover:border-indigo-300 hover:shadow-sm' }}">
                                        <span class="text-[9px] uppercase font-bold opacity-50 leading-none">{{ $carbonTgl->translatedFormat('D') }}</span>
                                        <span class="text-xl font-black leading-tight">{{ $carbonTgl->format('d') }}</span>
                                        <span class="text-[9px] font-medium opacity-60">{{ $carbonTgl->translatedFormat('M') }}</span>
                                    </div>
                                </label>
                            @endif
                        @empty
                            <div class="w-full p-4 bg-red-50 rounded-2xl border border-red-100 text-center">
                                <p class="text-red-500 text-xs font-bold">Jadwal belum diatur teknisi</p>
                            </div>
                        @endforelse
                    @endif
                </div>
            </div>

            {{-- ── Pilih Jam ── --}}
            <div x-data="{ selectedTime: '' }">
                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-widest mb-3">Jam Tersedia</h3>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                    @forelse($jasa->ketersediaan_jam as $jam)
                        <label class="cursor-pointer">
                            <input 
                                type="radio" 
                                name="order_time" 
                                value="{{ $jam }}" 
                                class="peer sr-only" 
                                wire:model.live="order_time"
                            >
                            <div class="py-2.5 text-center bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 transition-all
                                        peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-md
                                        hover:border-indigo-300">
                                {{ $jam }}
                            </div>
                        </label>
                    @empty
                        <div class="col-span-full p-3 bg-gray-50 rounded-xl border border-gray-100 text-center">
                            <p class="text-gray-400 text-xs italic">Jam tidak tersedia</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ── Jenis Kerusakan / Keluhan ── --}}
            <div class="pt-5 border-t border-gray-100">
                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-widest mb-3">Jenis Keluhan</h3>

                <div x-data="{ showLainnya: false, keluhanLainnya: '' }" class="space-y-3">
                    <div class="flex flex-wrap gap-2">
                        @forelse($jasa->keluhan as $index => $item)
                            <label class="relative">
                                <input 
                                    type="checkbox" 
                                    name="keluhan[]" 
                                    value="{{ $item }}" 
                                    class="peer sr-only"
                                    wire:model.live="keluhan"
                                >
                                <div class="px-3.5 py-2 bg-white border border-gray-200 rounded-xl text-xs font-semibold text-gray-600 cursor-pointer transition-all
                                            peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-sm
                                            hover:border-indigo-300 hover:bg-gray-50">
                                    {{ $item }}
                                </div>
                            </label>
                        @empty
                            <p class="text-gray-400 italic text-xs">Data keluhan tidak tersedia</p>
                        @endforelse

                        <button type="button" @click="showLainnya = !showLainnya"
                            class="px-3.5 py-2 border border-dashed border-gray-300 rounded-xl text-xs font-semibold text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition-all flex items-center gap-1.5"
                            :class="showLainnya ? 'bg-indigo-50 border-indigo-400 text-indigo-600' : 'bg-white'">
                            <i class="bi text-[10px]" :class="showLainnya ? 'bi-dash-lg' : 'bi-plus-lg'"></i>
                            Lainnya
                        </button>
                    </div>

                    <div x-show="showLainnya" x-collapse>
                        <textarea 
                            name="keluhan_manual" 
                            placeholder="Sebutkan keluhan lainnya..."
                            class="w-full mt-1 rounded-2xl border border-gray-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm p-3.5 bg-gray-50 placeholder-gray-300 resize-none outline-none transition"
                            rows="3"
                            wire:model.defer="keluhan_manual"
                        ></textarea>
                    </div>
                </div>
            </div>

            {{-- ── Layanan Tambahan ── --}}
            <div class="pt-5 border-t border-gray-100" x-data="{ openModal: null }">
                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-widest mb-3">Layanan Tambahan <span class="normal-case font-normal text-gray-400">(Opsional)</span></h3>

                <div class="space-y-2">
                    @forelse($jasa->layanan_tambahan as $indexGrup => $grup)

                        {{-- Trigger Button --}}
                        <div class="flex items-center justify-between px-4 py-3.5 bg-white border border-gray-200 rounded-2xl hover:border-indigo-300 hover:bg-indigo-50/30 transition cursor-pointer shadow-sm"
                            @click="openModal = {{ $indexGrup }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-center text-indigo-500 shrink-0">
                                    <i class="bi bi-tools text-sm"></i>
                                </div>
                                <span class="font-bold text-gray-800 text-sm">{{ $grup['judul'] }}</span>
                            </div>
                            <div class="w-7 h-7 flex items-center justify-center bg-indigo-600 text-white rounded-full shadow-sm hover:scale-110 transition shrink-0">
                                <i class="bi bi-plus-lg text-xs"></i>
                            </div>
                        </div>

                        {{-- Bottom Sheet Modal --}}
                        <div x-show="openModal === {{ $indexGrup }}" 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            class="fixed inset-0 z-[100] flex items-end justify-center bg-black/50 backdrop-blur-sm"
                            style="display: none;">
                            
                            <div class="absolute inset-0" @click="openModal = null"></div>

                            <div x-show="openModal === {{ $indexGrup }}"
                                x-transition:enter="transition ease-out duration-300 transform"
                                x-transition:enter-start="translate-y-full"
                                x-transition:enter-end="translate-y-0"
                                x-transition:leave="transition ease-in duration-200 transform"
                                x-transition:leave-start="translate-y-0"
                                x-transition:leave-end="translate-y-full"
                                class="relative w-full max-w-lg bg-white rounded-t-[28px] p-6 shadow-2xl">
                                
                                <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-6"></div>

                                <div class="flex justify-between items-center mb-5">
                                    <h4 class="text-lg font-black text-gray-900">{{ $grup['judul'] }}</h4>
                                    <button @click="openModal = null" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                                        <i class="bi bi-x-lg text-sm"></i>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-2.5 max-h-[60vh] overflow-y-auto pb-2">
                                    @foreach($grup['items'] as $indexItem => $item)
                                        @php $uniqueId = 'layanan-' . $indexGrup . '-' . $indexItem; @endphp
                                        <label for="{{ $uniqueId }}" 
                                            class="flex items-center justify-between p-4 bg-gray-50 border border-gray-100 rounded-2xl cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/40 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50"
                                            >
                                            <div class="flex items-center gap-3">
                                                <input 
                                                    type="checkbox" 
                                                    id="{{ $uniqueId }}" 
                                                    name="layanan_tambahan[{{ $indexGrup }}]" 
                                                    value="{{ json_encode(['nama' => $item['nama'], 'harga' => $item['harga']]) }}"
                                                    wire:model.live="layanan_tambahan.{{ $indexGrup }}"
                                                    class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                <span class="text-sm font-semibold text-gray-700">{{ $item['nama'] }}</span>
                                            </div>
                                            <span class="text-sm font-black text-indigo-600 shrink-0 ml-3">Rp {{ $item['harga'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    @empty
                        <p class="text-gray-400 italic text-xs text-center py-4">Tidak ada layanan tambahan</p>
                    @endforelse
                </div>
            </div>

            @if(auth()->user()->id !== $jasa->technician->user_id)
                <div class="border-t border-gray-100 sticky bottom-0">
                    <button type="button" wire:click="submitOrder" wire:loading.attr="disabled" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="submitOrder">Pesan Jasa Sekarang</span>
                        <span wire:loading wire:target="submitOrder">Memproses...</span>
                    </button>
                </div>
            @endif

        </div>
    </div>

</div>

        </div>
    </div>

</div>