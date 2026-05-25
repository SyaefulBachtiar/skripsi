<div 
    class="space-y-5"
    x-data="{
        showLightbox: false,
        lightboxImage: '',
        lightboxName: '',
        openLightbox: function(url, name) {
            this.lightboxImage = url;
            this.lightboxName = name;
            this.showLightbox = true;
        }
    }"
>
    {{-- ALERT BANNER: STATUS PENOLAKAN LAMA --}}
    <div class="bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/50 p-4 rounded-2xl flex items-start gap-3 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
            <i class="bi bi-exclamation-octagon-fill text-xl"></i>
        </div>
        <div class="min-w-0">
            <h2 class="text-sm font-bold text-red-800 dark:text-red-400">Pendaftaran Akun Ditolak Sebelumnya</h2>
            <p class="text-xs text-red-600 dark:text-red-500 mt-0.5 leading-relaxed font-medium">
                Alasan Penolakan Sistem: <span class="font-bold">"{{ $tech->alasan_ditolak ?? 'Tidak ada catatan.' }}"</span>
            </p>
        </div>
    </div>

    {{-- GRID UTAMA LAYOUT COMPONENT --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        {{-- SEKSI KIRI: PROFIL DATA & DOKUMEN MEDIA (COL-SPAN 7) --}}
        <div class="lg:col-span-7 space-y-5">
            
            {{-- DATA UTAMA PROFILE CARD --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row items-center gap-4 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div 
                        @click="openLightbox('{{ $tech->foto_wajah ? asset('storage/' . $tech->foto_wajah) : asset('assets/default_profile/default_profile_teknisi.webp') }}', 'Foto Wajah Asli')"
                        class="w-20 h-20 rounded-2xl overflow-hidden bg-slate-50 border border-gray-100 dark:border-gray-800 cursor-zoom-in shrink-0 shadow-md hover:scale-105 transition-transform"
                    >
                        <img src="{{ $tech->foto_wajah ? asset('storage/' . $tech->foto_wajah) : asset('assets/default_profile/default_profile_teknisi.webp') }}" class="w-full h-full object-cover">
                    </div>
                    <div class="text-center sm:text-left min-w-0 flex-1">
                        <h3 class="text-base font-black text-gray-900 dark:text-white uppercase tracking-wide truncate">{{ $tech->nama_asli ?? 'Nama Tidak Terisi' }}</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium truncate mt-0.5">{{ $tech->user->email ?? '-' }}</p>
                        <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-[10px] font-bold uppercase rounded-md tracking-wider">
                            #{{ $tech->user_id }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Deskripsi Keahlian</span>
                        <p class="text-gray-700 dark:text-gray-300 font-medium mt-1 leading-relaxed">{{ $tech->deskripsi ?? 'Belum mengisi deskripsi profil.' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Alamat Korespondensi</span>
                        <p class="text-gray-700 dark:text-gray-300 font-medium mt-1 leading-relaxed">
                            {{ $tech->detail_alamat ?? '-' }}, {{ $tech->kelurahan }}, {{ $tech->kecamatan }}, {{ $tech->kabupaten }}, {{ $tech->provinsi }}
                        </p>
                    </div>
                </div>

                {{-- TAGS SPESIALISASI & PENGALAMAN --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1.5">Spesialisasi Jasa</span>
                        <div class="flex flex-wrap gap-1">
                            @if(is_array($tech->spesialisasi))
                                @foreach($tech->spesialisasi as $spesialis)
                                    <span class="px-2 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 font-semibold text-[11px] rounded-md dark:bg-indigo-950/20 dark:text-indigo-400 dark:border-transparent">
                                        {{ $spesialis }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-xs text-gray-400 italic">Kosong</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1.5">Pengalaman Kerja</span>
                        <div class="flex flex-wrap gap-1">
                            @if(is_array($tech->pengalaman))
                                @foreach($tech->pengalaman as $kerja)
                                    <span class="px-2 py-1 bg-slate-100 text-slate-700 font-semibold text-[11px] rounded-md dark:bg-gray-800 dark:text-gray-300">
                                        {{ $kerja }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-xs text-gray-400 italic">Kosong</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- DOKUMEN SERTIFIKAT AKADEMIK --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-3">
                <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-50 dark:border-gray-800 pb-2">
                    <i class="bi bi-patch-check-fill text-indigo-500"></i> Berkas Sertifikasi Keahlian
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @if(is_array($tech->sertifikat))
                        @foreach($tech->sertifikat as $index => $cert)
                            <div 
                                @click="openLightbox('{{ asset('storage/' . $cert) }}', 'Sertifikat Keahlian #' + ({{ $index }} + 1))"
                                class="aspect-[4/3] rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm cursor-zoom-in group relative bg-slate-50"
                            >
                                <img src="{{ asset('storage/' . $cert) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-2">
                                    <span class="text-[10px] text-white font-medium">Sertifikat #{{ $index+1 }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-xs text-gray-400 italic col-span-3 py-2">Tidak melampirkan file berkas sertifikat.</p>
                    @endif
                </div>
            </div>

            {{-- FOTO GALERI KEGIATAN --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-3">
                <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-50 dark:border-gray-800 pb-2">
                    <i class="bi bi-images text-indigo-500"></i> Dokumentasi Foto Kegiatan Lapangan
                </h4>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                    @if(is_array($tech->foto_kegiatan))
                        @foreach($tech->foto_kegiatan as $idx => $kegiatan)
                            <div 
                                @click="openLightbox('{{ asset('storage/' . $kegiatan) }}', 'Foto Kegiatan Lapangan #' + ({{ $idx }} + 1))"
                                class="aspect-square rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm cursor-zoom-in group bg-slate-50"
                            >
                                <img src="{{ asset('storage/' . $kegiatan) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                            </div>
                        @endforeach
                    @else
                        <p class="text-xs text-gray-400 italic col-span-4 py-2">Tidak melampirkan berkas foto kegiatan lapangan.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- SEKSI KANAN: LIVEWIRE MAP COMPONENT & VERIFIKASI DROPDOWN (COL-SPAN 5) --}}
        <div class="lg:col-span-5 space-y-5">
            
            {{-- REUSABLE LIVEWIRE MAP COMPONENT CARD --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-4 shadow-sm space-y-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block">
                    <i class="bi bi-geo-alt-fill"></i> Peta Lokasi Rumah / Workshop
                </span>
                
                {{-- KUNCI UTAMA: Memanggil map komponen terpusat dengan key unik id --}}
                <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 h-[260px] relative shadow-inner z-0">
                    @livewire('services.map', [
                        'lat'          => $lat,
                        'lng'          => $lng,
                        'customerName' => $customerName
                    ], key('map-rejected-tech-' . $id))
                </div>
            </div>

            {{-- ── FORM MANAGEMENT DROPDOWN VERIFIKASI UTAMA ── --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm">
                <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-50 dark:border-gray-800 pb-2.5 mb-4">
                    <i class="bi bi-shield-check text-indigo-500"></i> Keputusan Verifikasi Akun
                </h4>
                
                <form wire:submit.prevent="saveStatus" class="space-y-4">
                    {{-- Dropdown Status Terintegrasi --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Ubah Status Registrasi</label>
                        <select 
                            wire:model.live="status_verifikasi"
                            class="w-full text-xs sm:text-sm font-semibold border-gray-200 dark:border-gray-800 rounded-xl bg-slate-50 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500 text-gray-700 dark:text-gray-300 transition"
                        >
                            <option value="ditolak">Tolak Pendaftaran</option>
                            <option value="diverifikasi">Setujui & Aktifkan Teknisi</option>
                        </select>
                    </div>

                    {{-- Form Keterangan Alasan Baru --}}
                    @if($status_verifikasi === 'ditolak')
                        <div x-transition.duration.300ms>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Alasan Penolakan Berkas</label>
                            <textarea 
                                wire:model="alasan_baru" 
                                rows="3"
                                placeholder="Tulis alasan mendetail mengapa berkas ditolak... (Contoh: Berkas KTP Buram)"
                                class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-800 bg-slate-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-indigo-500 resize-none outline-none transition shadow-inner p-3"
                            ></textarea>
                            @error('alasan_baru') <span class="text-[10px] text-red-500 font-medium mt-1 block"><i class="bi bi-info-circle"></i> {{ $message }}</span> @enderror
                        </div>
                    @endif

                    {{-- Tombol Kirim Form Data --}}
                    <div class="pt-2">
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-md shadow-indigo-100 dark:shadow-none flex items-center justify-center gap-1.5 disabled:opacity-40"
                        >
                            <i class="bi bi-cloud-arrow-up-fill text-sm"></i>
                            <span wire:loading.remove>Simpan Keputusan</span>
                            <span wire:loading>Memproses Data...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- INTERAKTIF SINGLETON LIGHTBOX MODAL (TELEPORTED TO BODY LAYER) --}}
    <template x-teleport="body">
        <div 
            x-show="showLightbox" 
            class="fixed inset-0 z-[99999] flex flex-col items-center justify-center bg-slate-950/95 p-4 backdrop-blur-sm" 
            style="display: none;"
            @keydown.escape.window="showLightbox = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <button @click="showLightbox = false" class="absolute top-5 right-5 text-white/70 hover:text-white p-2.5 rounded-full bg-white/5 hover:bg-white/10 transition-all duration-200">
                <i class="bi bi-x-lg text-xl sm:text-2xl"></i>
            </button>
            <div class="max-w-3xl w-full flex flex-col items-center justify-center" @click.away="showLightbox = false">
                <p x-text="lightboxName" class="text-white font-bold text-sm sm:text-base mb-4 tracking-wider uppercase bg-white/5 px-4 py-1.5 rounded-full border border-white/10 shadow-sm"></p>
                <img 
                    :src="lightboxImage" 
                    :alt="lightboxName" 
                    class="max-w-full max-h-[70vh] sm:max-h-[75vh] rounded-2xl object-contain shadow-2xl border border-white/10"
                    x-show="showLightbox"
                    x-transition:enter="transition ease-out duration-300 transform scale-95"
                    x-transition:enter-start="scale-95"
                    x-transition:enter-end="scale-100"
                >
            </div>
        </div>
    </template>
</div>