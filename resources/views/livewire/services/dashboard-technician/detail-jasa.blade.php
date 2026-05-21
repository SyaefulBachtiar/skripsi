
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

    {{-- ── Modal Info Tipe Layanan ── --}}
    <div
        x-data="{
            show: true,
            showAlamatModal: {{ !$alamatLengkap && $jasa->tipe_layanan === 'panggilan' ? 'true' : 'false' }},
            tipe: '{{ $jasa->tipe_layanan }}'
        }"
        x-show="show"
        x-transition.opacity
        @keydown.escape.window="show = false"
        style="display:none"
        class="fixed inset-0 z-50 flex items-center sm:items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="tipe === 'panggilan' ? 'modal-title-panggilan' : 'modal-title-bengkel'"
    >
        {{-- Backdrop klik tutup --}}
        <div class="absolute inset-0" @click="show = false" aria-hidden="true"></div>

        {{-- Panel --}}
        <div class="relative w-full max-w-sm bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">

            {{-- ── PANGGILAN (Home Service) ── --}}
            <template x-if="tipe === 'panggilan'">
                <div>
                    {{-- Icon header --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 px-5 pt-6 pb-4 flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center mb-3">
                            <i class="bi bi-house-door-fill text-2xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h3 id="modal-title-panggilan"
                            class="text-base font-bold text-gray-900 dark:text-white">
                            Layanan Home Service
                        </h3>
                        <span class="mt-1.5 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                            <i class="bi bi-geo-alt-fill text-xs"></i>
                            Teknisi datang ke lokasi Anda
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="px-5 py-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed text-center">
                            Layanan yang dipilih <span class="font-semibold text-gray-800 dark:text-white">hanya tersedia untuk kunjungan ke rumah</span> dan tidak dapat dilakukan di bengkel teknisi.
                        </p>
                        <div class="mt-4 flex flex-col gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-blue-500 mt-0.5 flex-shrink-0"></i>
                                <span>Teknisi akan datang sesuai jadwal yang Anda pilih</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-blue-500 mt-0.5 flex-shrink-0"></i>
                                <span>Pastikan alamat yang dimasukkan sudah benar</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-blue-500 mt-0.5 flex-shrink-0"></i>
                                <span>Harap berada di lokasi saat teknisi tiba</span>
                            </div>
                        </div>
                        <p class="mt-4 text-sm font-medium text-gray-700 dark:text-gray-200 text-center">
                            Apakah Anda bersedia melanjutkan layanan ini?
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2 px-5 pb-5">
                        <a href="/beranda"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                                text-sm font-medium text-gray-600 dark:text-gray-300 text-center
                                hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            Kembali
                        </a>
                        <button type="button"
                                @click="show = false"
                                class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98]
                                    text-sm font-semibold text-white text-center transition-all">
                            Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            </template>

            {{-- ── BENGKEL ── --}}
            <template x-if="tipe === 'bengkel'">
                <div>
                    {{-- Icon header --}}
                    <div class="bg-amber-50 dark:bg-amber-900/20 px-5 pt-6 pb-4 flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center mb-3">
                            <i class="bi bi-tools text-2xl text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <h3 id="modal-title-bengkel"
                            class="text-base font-bold text-gray-900 dark:text-white">
                            Layanan Bengkel
                        </h3>
                        <span class="mt-1.5 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                            <i class="bi bi-shop text-xs"></i>
                            Kunjungi lokasi bengkel teknisi
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="px-5 py-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed text-center">
                            Layanan yang dipilih <span class="font-semibold text-gray-800 dark:text-white">hanya tersedia di bengkel teknisi</span> dan tidak dapat dilakukan dengan kunjungan ke rumah.
                        </p>
                        <div class="mt-4 flex flex-col gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-amber-500 mt-0.5 flex-shrink-0"></i>
                                <span>Bawa perangkat Anda ke lokasi bengkel teknisi</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-amber-500 mt-0.5 flex-shrink-0"></i>
                                <span>Datang sesuai jadwal yang sudah dipilih</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-amber-500 mt-0.5 flex-shrink-0"></i>
                                <span>Alamat bengkel akan ditampilkan setelah pesanan dikonfirmasi</span>
                            </div>
                        </div>
                        <p class="mt-4 text-sm font-medium text-gray-700 dark:text-gray-200 text-center">
                            Apakah Anda bersedia melanjutkan layanan ini?
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2 px-5 pb-5">
                        <a href="{{ url()->previous() }}"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                                text-sm font-medium text-gray-600 dark:text-gray-300 text-center
                                hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            Kembali
                        </a>
                        <button type="button"
                                @click="show = false"
                                class="flex-1 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-[0.98]
                                    text-sm font-semibold text-white text-center transition-all">
                            Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            </template>

        </div>
    </div>

    {{-- ── Modal Alamat Belum Lengkap ── --}}
    @if($jasa->tipe_layanan === 'panggilan' && !$alamatLengkap)
        <div x-show="showAlamatModal"
            x-transition.opacity
            style="display:none"
            class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-title-alamat">

            {{-- Panel — TIDAK bisa ditutup dengan klik backdrop atau Esc, harus isi alamat dulu --}}
            <div class="relative w-full max-w-sm bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">

                {{-- Header --}}
                <div class="bg-red-50 dark:bg-red-900/20 px-5 pt-6 pb-4 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-2xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center mb-3">
                        <i class="bi bi-geo-alt-fill text-2xl text-red-500 dark:text-red-400"></i>
                    </div>
                    <h3 id="modal-title-alamat"
                        class="text-base font-bold text-gray-900 dark:text-white">
                        Alamat Belum Lengkap
                    </h3>
                    <span class="mt-1.5 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                        <i class="bi bi-exclamation-triangle-fill text-xs"></i>
                        Diperlukan untuk home service
                    </span>
                </div>

                {{-- Body --}}
                <div class="px-5 py-4 space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed text-center">
                        Layanan ini adalah <span class="font-semibold text-gray-800 dark:text-white">home service</span> — teknisi akan datang ke lokasi Anda.
                        Untuk melanjutkan, Anda perlu mengisi alamat lengkap terlebih dahulu.
                    </p>

                    {{-- Checklist kolom yang kurang --}}
                    @php
                        $customer = \App\Models\Role_users\Customer::where('user_id', auth()->id())->first();
                        $missingFields = collect([
                            'detail_alamat' => 'Detail alamat (nama jalan, nomor rumah)',
                            'provinsi'      => 'Provinsi',
                            'kabupaten'     => 'Kabupaten / Kota',
                            'kecamatan'     => 'Kecamatan',
                            'kelurahan'     => 'Kelurahan',
                            'latitude'      => 'Titik lokasi (GPS)',
                        ])->filter(fn($label, $field) => empty($customer?->$field));
                    @endphp

                    @if($missingFields->isNotEmpty())
                        <div class="bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-800 rounded-xl p-3 space-y-1.5">
                            <p class="text-[11px] font-semibold text-red-600 dark:text-red-400 uppercase tracking-wide mb-2">
                                Data yang belum diisi:
                            </p>
                            @foreach($missingFields as $label)
                                <div class="flex items-center gap-2 text-xs text-red-600 dark:text-red-400">
                                    <i class="bi bi-x-circle-fill flex-shrink-0 text-xs"></i>
                                    <span>{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 px-5 pb-5">
                    <a href="/beranda"
                    class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                            text-sm font-medium text-gray-600 dark:text-gray-300 text-center
                            hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Kembali
                    </a>
                    <a href="{{ url('/atur-alamat') }}"
                    wire:navigate
                    class="flex-1 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 active:scale-[0.98]
                            text-sm font-semibold text-white text-center transition-all
                            flex items-center justify-center gap-1.5">
                        <i class="bi bi-pencil-square text-sm"></i>
                        Isi Alamat
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Kolom Kiri: Gallery Foto --}}
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
        class="relative w-full overflow-hidden rounded-2xl bg-gray-50 border border-gray-200 shadow-sm"
        @touchstart="onDragStart($event)"
        @touchend="onDragEnd($event)"
        @mousedown="onDragStart($event)"
        @mouseup="onDragEnd($event)"
        @mouseleave="isDragging = false">

            {{-- Wrapper Images --}}
            <div class="relative h-72 md:h-[460px] select-none cursor-pointer" @click="openLightbox()">
                @forelse($jasa->thumbnails as $index => $img)
                    <div 
                        x-show="activeSlide === {{ $index }}" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute inset-0"
                    >
                        <img src="{{ asset('storage/' . $img) }}" 
                            alt="Thumbnail {{ $index + 1 }}" 
                            class="w-full h-full object-contain pointer-events-none">
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full">
                        <img src="{{ asset('assets/icons/empty_image.webp') }}" 
                            alt="empty" 
                            class="w-24 h-24 object-contain opacity-20 grayscale">
                        <p class="text-gray-400 text-sm mt-3 font-medium">Belum ada foto</p>
                    </div>
                @endforelse
            </div>

            {{-- Dots Navigation --}}
            @if(count($jasa->thumbnails ?? []) > 1)
                <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                    @foreach($jasa->thumbnails as $index => $img)
                        <button 
                            @click="activeSlide = {{ $index }}" 
                            class="h-1.5 transition-all duration-300 rounded-full"
                            :class="activeSlide === {{ $index }} ? 'w-6 bg-indigo-600' : 'w-1.5 bg-gray-400/70 hover:bg-gray-500'"
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

            {{-- Lightbox Modal --}}
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

    {{-- Kolom Kanan: Informasi & Form --}}
    <div class="space-y-6">
        {{-- Header: Nama & Rating --}}
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-2 flex-1">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight">
                    {{ $jasa->nama_jasa }}
                </h1>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border 
                    {{ $jasa->tipe_layanan === 'panggilan' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-purple-50 text-purple-700 border-purple-200' }}">
                    <i class="bi {{ $jasa->tipe_layanan === 'panggilan' ? 'bi-house-door-fill' : 'bi-shop' }} text-sm"></i>
                    <span>{{ $jasa->tipe_layanan === 'panggilan' ? 'Panggilan ke Rumah' : 'Bawa ke Bengkel' }}</span>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1 px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-600 rounded-lg text-xs font-bold">
                        <i class="bi bi-star-fill text-[10px]"></i>
                        <span>{{ number_format($jasa->rata_rata_rating ?? 0, 1) }}</span>
                    </div>
                    <span class="text-gray-300 text-xs">•</span>
                    <span class="text-gray-500 text-xs font-medium">({{ $jasa->review_count ?? 0 }} Pelanggan)</span>
                </div>
            </div>

            {{-- @if(auth()->user()->id !== $jasa->technician->user_id)
                <button class="p-2.5 rounded-xl bg-gray-50 text-gray-400 hover:text-rose-500 hover:bg-rose-50 transition border border-gray-200 shrink-0">
                    <i class="bi bi-bookmark-fill text-lg"></i>
                </button>
            @else
                <a href="{{ route('edit.jasa', ['id_jasa' => $jasa->id]) }}" class="flex items-center gap-1.5 px-3 py-2 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition">
                    <i class="bi bi-pencil-square text-green-600"></i>
                    <span class="text-sm font-medium text-green-700">Edit</span>
                </a>
            @endif --}}
        </div>

        {{-- Harga --}}
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-indigo-600 tracking-tight">
                Rp {{ number_format($jasa->harga_jasa, 0, ',', '.') }}
            </span>
            {{-- <span class="text-sm text-gray-500 font-medium">/ kunjungan</span> --}}
        </div>

        <hr class="border-gray-200">

        {{-- Card Teknisi --}}
        @if(auth()->user()->id !== $jasa->technician->user_id)
            @if($jasa->technician?->user)
                <div class="flex items-center justify-between gap-4 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                    <div class="flex items-center gap-3">
                        <div class="relative shrink-0">
                            <img 
                                src="{{ $jasa->technician->user->profile_photo_url ?? asset('assets/default_profile/default_profile_teknisi.webp') }}" 
                                alt="{{ $jasa->technician->user->name }}"
                                class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm"
                            >
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ $jasa->technician->user->name }}</p>
                            <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wide">Teknisi Elektronik</p>
                        </div>
                    </div>
                    <a href="{{ route('technician.profile', ['id' => $jasa->id_technician]) }}" class="flex items-center gap-1.5 px-3 py-2 bg-white text-indigo-600 rounded-lg text-xs font-bold border border-indigo-200 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                        <i class="bi bi-shop text-sm"></i>
                        <span>Profile</span>
                    </a>
                </div>
            @endif
        @endif

        @if($jasa->tipe_layanan === 'bengkel')
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Lokasi Bengkel</h3>
                    <a 
                        href="https://www.google.com/maps?q={{ $jasa->technician->latitude }},{{ $jasa->technician->longitude }}" 
                        target="_blank"
                        class="text-xs font-bold text-indigo-600 hover:underline"
                    >
                        Buka di Maps <i class="bi bi-box-arrow-up-right ml-1"></i>
                    </a>
                </div>

                <div class="p-4 bg-white border border-gray-200 rounded-2xl shadow-sm space-y-4">
                    {{-- Info Alamat Teks --}}
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-700 leading-relaxed">
                                {{ $jasa->technician->detail_alamat }}
                            </p>
                            <p class="text-[11px] text-gray-500 mt-0.5">
                                {{ $jasa->technician->kecamatan }}, {{ $jasa->technician->kabupaten }}
                            </p>
                        </div>
                    </div>

                    {{-- Komponen Map --}}
                    <div class="rounded-xl overflow-hidden border border-gray-100 h-[250px]">
                        @livewire('services.map', [
                            'lat' => $jasa->technician->latitude, 
                            'lng' => $jasa->technician->longitude,
                            'customerName' => 'Bengkel ' . $jasa->technician->user->name
                        ], key('map-bengkel-'.$jasa->id))
                    </div>
                </div>
            </div>
        @endif

        {{-- Deskripsi --}}
        <div x-data="{ open: true }" class="rounded-xl border border-gray-200 overflow-hidden bg-white">
            <button @click="open = !open" class="w-full flex justify-between items-center px-4 py-3.5 hover:bg-gray-50 transition">
                <span class="font-bold text-gray-800 text-sm uppercase tracking-wide">Deskripsi Layanan</span>
                <i class="bi bi-chevron-up text-gray-400 transition-transform duration-300" :class="open ? '' : 'rotate-180'"></i>
            </button>
            <div x-show="open" x-collapse>
                <div class="px-4 pb-4 text-gray-600 leading-relaxed text-sm whitespace-pre-line border-t border-gray-100 pt-3">
                    {{ $jasa->deskripsi }}
                </div>
            </div>
        </div>

        {{-- Ulasan --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Ulasan Terakhir</h3>
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full font-semibold">{{ $jasa->review_count ?? 0 }} ulasan</span>
            </div>

            @forelse ($jasa->review as $review)
                <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-sm text-gray-700 italic leading-relaxed">
                    "{{ $review->text_comment }}"
                </div>
            @empty
                <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-sm text-gray-700 italic leading-relaxed">
                    Belum Ada Ulasan
                </div>
            @endforelse
        </div>

        <hr class="border-gray-200">

        {{-- Form Pemesanan --}}
        <div class="space-y-6">
            {{-- Pilih Tanggal --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Tanggal Tersedia <span class="text-red-500">*</span></h3>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if($jasa->is_setiap_hari)
                        @foreach(range(0, 6) as $day)
                            @php $date = now()->addDays($day); @endphp
                            <label class="cursor-pointer" wire:key="date-{{ $date->format('Y-m-d') }}">
                                <input 
                                    type="radio" 
                                    name="order_date" 
                                    value="{{ $date->format('Y-m-d') }}" 
                                    class="peer sr-only" 
                                    wire:model.live="order_date"
                                >
                                <div class="flex flex-col items-center justify-center w-16 h-20 bg-white border border-gray-200 rounded-xl transition-all
                                            peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-md
                                            hover:border-indigo-300">
                                    <span class="text-[10px] uppercase font-bold opacity-60 peer-checked:opacity-80">{{ $date->translatedFormat('D') }}</span>
                                    <span class="text-xl font-bold">{{ $date->format('d') }}</span>
                                    <span class="text-[10px] font-medium opacity-70">{{ $date->translatedFormat('M') }}</span>
                                </div>
                            </label>
                        @endforeach
                    @else
                        @forelse($jasa->ketersediaan_tanggal as $index => $tgl)
                            @if($jasa->ketersediaan_status === 'Ketersediaan perlu diperbarui')
                                <div class="w-full p-4 bg-red-50 rounded-xl border border-red-200 text-center">
                                    <p class="text-red-600 text-sm font-medium">Jadwal teknisi tidak tersedia</p>
                                </div>
                            @else
                                @php $carbonTgl = \Carbon\Carbon::parse($tgl); @endphp
                                <label class="cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="order_date" 
                                        value="{{ $carbonTgl->format('Y-m-d') }}" 
                                        class="peer sr-only" 
                                        wire:model.live="order_date"
                                    >
                                    <div class="flex flex-col items-center justify-center w-16 h-20 bg-white border border-gray-200 rounded-xl transition-all
                                                peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-md
                                                {{ $carbonTgl->isPast() && !$carbonTgl->isToday() ? 'opacity-40 pointer-events-none' : 'hover:border-indigo-300' }}">
                                        <span class="text-[10px] uppercase font-bold opacity-60">{{ $carbonTgl->translatedFormat('D') }}</span>
                                        <span class="text-xl font-bold">{{ $carbonTgl->format('d') }}</span>
                                        <span class="text-[10px] font-medium opacity-70">{{ $carbonTgl->translatedFormat('M') }}</span>
                                    </div>
                                </label>
                            @endif
                        @empty
                            <div class="w-full p-4 bg-red-50 rounded-xl border border-red-200 text-center">
                                <p class="text-red-600 text-sm font-medium">Jadwal belum diatur teknisi</p>
                            </div>
                        @endforelse
                    @endif
                </div>

                {{-- Error Tanggal --}}
                @error('order_date')
                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                        <i class="bi bi-exclamation-circle-fill text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Pilih Jam --}}
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Jam Tersedia <span class="text-red-500">*</span></h3>
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
                            <div class="py-3 text-center bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 transition-all
                                        peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-md
                                        hover:border-indigo-300">
                                {{ $jam }}
                            </div>
                        </label>
                    @empty
                        <div class="col-span-full p-4 bg-gray-50 rounded-xl border border-gray-200 text-center">
                            <p class="text-gray-500 text-sm italic">Jam tidak tersedia</p>
                        </div>
                    @endforelse
                </div>

                {{-- Error Jam --}}
                @error('order_time')
                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                        <i class="bi bi-exclamation-circle-fill text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Jenis Keluhan --}}
            <div class="pt-4 border-t border-gray-200">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Jenis Keluhan <span class="text-red-500">*</span></h3>

                <div x-data="{ showLainnya: {{ !empty($keluhan_manual) ? 'true' : 'false' }} }" class="space-y-3">
                    <div class="flex flex-wrap gap-2">
                        @forelse($jasa->keluhan as $index => $item)
                            <label class="relative" wire:key="keluhan-{{ $index }}">
                                <input 
                                    type="checkbox" 
                                    name="keluhan[]" 
                                    value="{{ $item }}" 
                                    class="peer sr-only"
                                    wire:model="keluhan"
                                    @change="$wire.set('keluhan', [...$wire.keluhan])"
                                >
                                <div class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 cursor-pointer transition-all
                                            peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-sm
                                            hover:border-indigo-300 hover:bg-gray-50">
                                    {{ $item }}
                                </div>
                            </label>
                        @empty
                            <p class="text-gray-500 italic text-sm">Data keluhan tidak tersedia</p>
                        @endforelse

                        <button type="button" @click="showLainnya = !showLainnya"
                            class="px-4 py-2.5 border border-dashed border-gray-300 rounded-xl text-sm font-medium text-gray-600 hover:border-indigo-400 hover:text-indigo-600 transition-all flex items-center gap-1.5"
                            :class="showLainnya ? 'bg-indigo-50 border-indigo-400 text-indigo-600' : 'bg-white'">
                            <i class="bi text-xs" :class="showLainnya ? 'bi-dash-lg' : 'bi-plus-lg'"></i>
                            Lainnya
                        </button>
                    </div>

                    <div x-show="showLainnya" x-collapse>
                        <textarea 
                            name="keluhan_manual" 
                            placeholder="Sebutkan keluhan lainnya..."
                            class="w-full rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm p-3 bg-gray-50 placeholder-gray-400 resize-none transition"
                            rows="3"
                            wire:model.defer="keluhan_manual"
                        ></textarea>
                    </div>
                </div>

                {{-- Error Keluhan --}}
                @error('keluhan')
                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                        <i class="bi bi-exclamation-circle-fill text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Layanan Tambahan --}}
            <div class="pt-4 border-t border-gray-200" x-data="{ openModal: null }">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Layanan Tambahan <span class="text-gray-400 font-normal normal-case">(Opsional)</span></h3>

                <div class="space-y-2">
                    @forelse($jasa->layanan_tambahan as $indexGrup => $grup)
                        @php
                            $selectedCount = count($layanan_tambahan[$indexGrup] ?? []);
                            $selectedNames = collect($layanan_tambahan[$indexGrup] ?? [])
                                ->map(fn($json) => json_decode($json, true)['nama'] ?? '')
                                ->filter()
                                ->implode(', ');
                            $selectedTotal = collect($layanan_tambahan[$indexGrup] ?? [])
                                ->sum(fn($json) => (int) str_replace(['.', ','], '', json_decode($json, true)['harga'] ?? 0));
                        @endphp

                        {{-- Trigger Button --}}
                        <div class="flex items-center justify-between px-4 py-3.5 bg-white border rounded-xl hover:border-indigo-300 hover:bg-indigo-50/30 transition cursor-pointer shadow-sm {{ $selectedCount > 0 ? 'border-indigo-400' : 'border-gray-200' }}"
                            @click="openModal = {{ $indexGrup }}">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-10 h-10 bg-indigo-50 border border-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 shrink-0">
                                    <i class="bi bi-tools"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-semibold text-gray-800 text-sm block">{{ $grup['judul'] }}</span>

                                    @if($selectedCount > 0)
                                        <span class="text-xs text-indigo-500 truncate block">
                                            {{ $selectedNames }} · Rp {{ number_format($selectedTotal, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if($selectedCount > 0)
                                    <span class="text-xs font-semibold bg-indigo-600 text-white px-2.5 py-0.5 rounded-full">
                                        {{ $selectedCount }} dipilih
                                    </span>
                                @endif

                                <div class="w-8 h-8 flex items-center justify-center bg-indigo-600 text-white rounded-full shadow-sm shrink-0">
                                    <i class="bi bi-plus-lg text-sm"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Bottom Sheet Modal --}}
                        <div x-show="openModal === {{ $indexGrup }}" 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            class="fixed inset-0 z-[99999] flex items-end justify-center bg-black/50 backdrop-blur-sm"
                            style="display: none;"
                            x-cloak
                            >
                            
                            <div class="absolute inset-0" @click="openModal = null"></div>

                            <div x-show="openModal === {{ $indexGrup }}"
                                x-transition:enter="transition ease-out duration-300 transform"
                                x-transition:enter-start="translate-y-full"
                                x-transition:enter-end="translate-y-0"
                                x-transition:leave="transition ease-in duration-200 transform"
                                x-transition:leave-start="translate-y-0"
                                x-transition:leave-end="translate-y-full"
                                class="relative w-full max-w-lg bg-white rounded-t-[28px] shadow-2xl flex flex-col max-h-[85vh]"
                            >
                                {{-- Drag Handle --}}
                                <div class="pt-4 pb-2 flex justify-center">
                                    <div class="w-12 h-1.5 bg-gray-200 rounded-full"></div>
                                </div>

                                {{-- Header --}}
                                <div class="px-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                                    <h4 class="text-lg font-bold text-gray-900">{{ $grup['judul'] }}</h4>
                                    <button @click="openModal = null" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                                        <i class="bi bi-x-lg text-sm"></i>
                                    </button>
                                </div>

                                {{-- Body --}}
                                <div class="flex-1 overflow-y-auto p-6 space-y-2">
                                    @foreach($grup['items'] as $indexItem => $item)
                                        @php 
                                            $uniqueId = 'layanan-' . $indexGrup . '-' . $indexItem;
                                            $cleanHarga = (int) str_replace(['.', ','], '', $item['harga']); 
                                        @endphp

                                        <label for="{{ $uniqueId }}" 
                                            class="flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/40 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50"
                                        >
                                            <div class="flex items-center gap-3">
                                                <input 
                                                    type="checkbox" 
                                                    id="{{ $uniqueId }}" 
                                                    name="layanan_tambahan[{{ $indexGrup }}]" 
                                                    value="{{ json_encode(['nama' => $item['nama'], 'harga' => $cleanHarga]) }}"
                                                    wire:model.live="layanan_tambahan.{{ $indexGrup }}"
                                                    class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 rounded">
                                                <span class="text-sm font-medium text-gray-700">{{ $item['nama'] }}</span>
                                            </div>
                                            <span class="text-sm font-bold text-indigo-600 shrink-0">Rp {{ number_format($cleanHarga, 0, ',', '.') }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                {{-- Footer dengan Tombol Selesai --}}
                                <div class="p-6 border-t border-gray-100 bg-gray-50">
                                    <button 
                                        @click="openModal = null"
                                        class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-indigo-200 flex items-center justify-center gap-2"
                                    >
                                        <i class="bi bi-check-lg"></i>
                                        <span>Selesai</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic text-sm text-center py-4 bg-gray-50 rounded-xl border border-dashed border-gray-200">Tidak ada layanan tambahan</p>
                    @endforelse
                </div>
            </div>

            {{-- Submit Button --}}
            @if(auth()->user()->id !== $jasa->technician->user_id)
                <div class="pt-6 border-t border-gray-200">

                    {{-- Banner peringatan alamat (panggilan & belum lengkap) --}}
                    @if($jasa->tipe_layanan === 'panggilan' && !$alamatLengkap)
                        <div class="mb-4 flex items-start gap-3 p-3.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                            <i class="bi bi-geo-alt-fill text-red-500 dark:text-red-400 flex-shrink-0 mt-0.5"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-red-700 dark:text-red-300">Alamat belum lengkap</p>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-0.5 leading-relaxed">
                                    Layanan home service memerlukan alamat lengkap Anda.
                                </p>
                                <a href="{{ url('/atur-alamat') }}"
                                wire:navigate
                                class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-red-600 dark:text-red-400 underline underline-offset-2 hover:no-underline">
                                    <i class="bi bi-arrow-right text-xs"></i>
                                    Lengkapi alamat sekarang
                                </a>
                            </div>
                        </div>
                    @endif

                    <button
                        type="button"
                        wire:click="submitOrder"
                        wire:loading.attr="disabled"
                        @if(!$alamatLengkap && $jasa->tipe_layanan === 'panggilan') disabled @endif
                        class="w-full py-4 font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2
                            {{ (!$alamatLengkap && $jasa->tipe_layanan === 'panggilan')
                                ? 'bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed'
                                : 'bg-indigo-600 hover:bg-indigo-700 text-white' }}"
                    >
                        <span wire:loading.remove wire:target="submitOrder">
                            <i class="bi bi-cart-check text-lg"></i>
                            @if($pesanan_di_keranjang)
                                Lanjutkan Pesanan
                            @else
                                Pesan Jasa Sekarang
                            @endif
                        </span>
                        <span wire:loading wire:target="submitOrder" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>