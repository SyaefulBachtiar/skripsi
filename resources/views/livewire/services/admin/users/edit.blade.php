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
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        {{-- KOLOM KIRI: FORM EDIT DATA UTAMA --}}
        <div class="{{ $hasMap ? 'lg:col-span-7' : 'lg:col-span-12' }} space-y-5">
            
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-4 pb-4 mb-5 border-b border-gray-100 dark:border-gray-800">
                    <div 
                        @click="openLightbox('{{ $user->profile_photo_url ?? asset('assets/default_profile/default_profile_teknisi.webp') }}', 'Foto Profil')"
                        class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-50 border border-gray-100 dark:border-gray-800 cursor-zoom-in shrink-0 shadow-sm"
                    >
                        <img src="{{ $user->profile_photo_url ?? asset('assets/default_profile/default_profile_teknisi.webp') }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900 dark:text-white uppercase tracking-wide">Edit Akun Pengguna</h3>
                        <p class="text-xs text-gray-400 font-medium">Ubah kredensial hak akses dan verifikasi dasar sistem Servisio</p>
                    </div>
                </div>

                {{-- FORM DATA INPUT UTAMA --}}
                <form wire:submit.prevent="update" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                            <input type="text" wire:model="name" class="w-full text-xs sm:text-sm border-gray-200 dark:border-gray-800 bg-slate-50 dark:bg-gray-900 rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            @error('name') <span class="text-[10px] text-red-500 font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Alamat Email</label>
                            <input type="email" wire:model="email" class="w-full text-xs sm:text-sm border-gray-200 dark:border-gray-800 bg-slate-50 dark:bg-gray-900 rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            @error('email') <span class="text-[10px] text-red-500 font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Hak Akses Sistem (Role)</label>
                            <select wire:model.live="role" class="w-full text-xs sm:text-sm border-gray-200 dark:border-gray-800 bg-slate-50 dark:bg-gray-900 rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-700 dark:text-gray-300">
                                <option value="customer">Pelanggan (Customer)</option>
                                <option value="technician">Teknisi (Technician)</option>
                                <option value="admin">Administrator (Admin)</option>
                            </select>
                        </div>
                        
                        @if($role !== 'admin')
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Saldo Dompet Elektronik (Rp)</label>
                                <input type="number" wire:model="saldo" class="w-full text-xs sm:text-sm border-gray-200 dark:border-gray-800 bg-slate-50 dark:bg-gray-900 rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                @error('saldo') <span class="text-[10px] text-red-500 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>

                    @if($role !== 'admin')
                        <div class="pt-2 border-t border-dashed border-gray-100 dark:border-gray-800 space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Status Verifikasi Berkas Akun</label>
                                <select wire:model.live="verifikasi" class="w-full text-xs sm:text-sm border-gray-200 dark:border-gray-800 bg-slate-50 dark:bg-gray-900 rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-700 dark:text-gray-300">
                                    <option value="diproses">Sedang Diproses</option>
                                    <option value="diverifikasi">Diverifikasi & Aktif</option>
                                    <option value="ditolak">Ditolak Systems</option>
                                </select>
                            </div>

                            @if($verifikasi === 'ditolak')
                                <div x-transition.duration.300ms>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Keterangan Alasan Berkas Ditolak</label>
                                    <textarea wire:model="alasan_ditolak" rows="3" placeholder="Tulis alasan penolakan secara jelas..." class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-800 bg-slate-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-indigo-500 p-3 resize-none"></textarea>
                                    @error('alasan_ditolak') <span class="text-[10px] text-red-500 font-medium mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="flex justify-end gap-2.5 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-md shadow-indigo-100 dark:shadow-none flex items-center gap-1.5">
                            <i class="bi bi-check-circle-fill"></i>
                            <span wire:loading.remove>Simpan Perubahan</span>
                            <span wire:loading>Menyimpan Data...</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── SEKSI TAMBAHAN: DETAIL BERKAS TEKNISI (HANYA RENDERING JIKA ROLE = TECHNICIAN) ── --}}
            @if($role === 'technician' && $user->technician)
                <div x-transition class="space-y-5">
                    {{-- Blok Deskripsi & Alamat Fisik --}}
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-4">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-50 dark:border-gray-800 pb-2">
                            <i class="bi bi-person-lines-fill text-indigo-500"></i> Detail Portofolio & Keahlian Teknisi
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Deskripsi Bio Teknisi</span>
                                <p class="text-gray-700 dark:text-gray-300 font-medium mt-1 leading-relaxed">{{ $user->technician->deskripsi ?? 'Belum mengisi deskripsi.' }}</p>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Alamat Korespondensi</span>
                                <p class="text-gray-700 dark:text-gray-300 font-medium mt-1 leading-relaxed">
                                    {{ $user->technician->detail_alamat ?? '-' }}, {{ $user->technician->kelurahan }}, {{ $user->technician->kecamatan }}, {{ $user->technician->kabupaten }}, {{ $user->technician->provinsi }}
                                </p>
                            </div>
                        </div>

                        {{-- Loop badges Spesialisasi & Pengalaman Kerja --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1.5">Spesialisasi Alat</span>
                                <div class="flex flex-wrap gap-1">
                                    @if(is_array($user->technician->spesialisasi))
                                        @foreach($user->technician->spesialisasi as $spesialis)
                                            <span class="px-2 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 font-semibold text-[11px] rounded-md dark:bg-indigo-950/20 dark:text-indigo-400 dark:border-transparent">
                                                {{ $spesialis }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum ditentukan</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1.5">Riwayat Pengalaman Kerja</span>
                                <div class="flex flex-wrap gap-1">
                                    @if(is_array($user->technician->pengalaman))
                                        @foreach($user->technician->pengalaman as $kerja)
                                            <span class="px-2 py-1 bg-slate-100 text-slate-700 font-semibold text-[11px] rounded-md dark:bg-gray-800 dark:text-gray-300">
                                                {{ $kerja }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum mengisi riwayat</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Blok Dokumen Sertifikasi Keahlian --}}
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-3">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-50 dark:border-gray-800 pb-2">
                            <i class="bi bi-patch-check-fill text-indigo-500"></i> Lampiran Berkas Sertifikat
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @if(is_array($user->technician->sertifikat))
                                @foreach($user->technician->sertifikat as $index => $cert)
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
                                <p class="text-xs text-gray-400 italic col-span-3 py-2">Tidak melampirkan file sertifikat.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Blok Dokumentasi Foto Kegiatan Lapangan --}}
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-3">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-50 dark:border-gray-800 pb-2">
                            <i class="bi bi-images text-indigo-500"></i> Dokumentasi Foto Kegiatan Perbaikan Lapangan
                        </h4>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                            @if(is_array($user->technician->foto_kegiatan))
                                @foreach($user->technician->foto_kegiatan as $idx => $kegiatan)
                                    <div 
                                        @click="openLightbox('{{ asset('storage/' . $kegiatan) }}', 'Foto Kegiatan Lapangan #' + ({{ $idx }} + 1))"
                                        class="aspect-square rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm cursor-zoom-in group bg-slate-50"
                                    >
                                        <img src="{{ asset('storage/' . $kegiatan) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                    </div>
                                @endforeach
                            @else
                                <p class="text-xs text-gray-400 italic col-span-4 py-2">Tidak melampirkan berkas foto kegiatan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- SEKSI KANAN: MAP COMPONENT --}}
        @if($hasMap)
            <div class="lg:col-span-5 space-y-5">
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-4 shadow-sm space-y-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block">
                        <i class="bi bi-geo-alt-fill"></i> Peta Posisi Kedudukan Rumah / Workshop
                    </span>
                    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 h-[280px] relative shadow-inner z-0">
                        @livewire('services.map', [
                            'lat'          => $lat,
                            'lng'          => $lng,
                            'customerName' => $name
                        ], key('map-user-edit-' . $id))
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                        <p class="text-[11px] font-semibold text-gray-500">
                            Koordinat Terdaftar: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $lat }}, {{ $lng }}</span>
                        </p>
                    </div>
                </div>
            </div>
        @endif
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