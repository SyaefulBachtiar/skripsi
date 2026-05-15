<div class="px-4 py-2 pt-3 bg-white rounded-xl shadow-sm border border-gray-100"
    x-data="{ 
        showImageModal: false, 
        imageModalUrl: '',
        showDetailModal: false,
        showRejectModal: false,
        selectedItem: null,
        rejectReason: '',
        map: null,
        openImageModal(url) {
            this.imageModalUrl = url;
            this.showImageModal = true;
        },
        initMap() {
            if (this.map) {
                this.map.remove();
                this.map = null;
            }
            const lat = parseFloat(this.selectedItem?.latitude);
            const lng = parseFloat(this.selectedItem?.longitude);
            if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                setTimeout(() => {
                    this.map = L.map('map-detail').setView([lat, lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(this.map);
                    L.marker([lat, lng]).addTo(this.map);

                    const customIcon = L.divIcon({
                html: `<div style='
                    width: 36px; 
                    height: 36px; 
                    background: #4f46e5; 
                    border: 3px solid white; 
                    border-radius: 50% 50% 50% 0; 
                    transform: rotate(-45deg);
                    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                    display: flex; 
                    align-items: center; 
                    justify-content: center;
                '>
                    <i class='bi bi-person-fill' style='
                        transform: rotate(45deg); 
                        color: white; 
                        font-size: 16px;
                        display: block;
                        line-height: 30px;
                        text-align: center;
                    ''></i>
                </div>`,
                className: '',
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -36]
            });

                    this.map.invalidateSize();
                }, 300);
            }
        },
        openDetailModal(item) {
            this.selectedItem = item;
            this.showDetailModal = true;
            this.$nextTick(() => {
                this.initMap();
            });
        },
        openRejectModal(item) {
            this.selectedItem = item;
            this.rejectReason = '';
            this.showRejectModal = true;
        },
        submitReject() {
            this.$wire.rejectTeknisi(this.selectedItem.id, this.rejectReason);
            this.showRejectModal = false;
        }
    }">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-4 border-b border-gray-200">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Verifikasi Teknisi</h2>
            <p class="text-sm text-gray-500 mt-0.5">Daftar teknisi yang menunggu persetujuan verifikasi</p>
        </div>

        {{-- Input Search --}}
        <div class="relative w-full md:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="bi bi-search text-gray-400"></i>
            </span>
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                placeholder="Cari nama teknisi..."
            >
        </div>
    </div>

    {{-- Desktop: Table View --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Teknisi</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Foto Wajah</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tgl Daftar</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($data as $item)
                <tr class="bg-white hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{$item->user->profile_photo_url}}" 
                                 class="w-10 h-10 rounded-full object-cover border border-gray-200">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $item->nama_asli }}</div>
                                <div class="text-xs text-gray-500">{{ $item->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <img src="{{ asset('storage/'.$item->foto_wajah) }}" 
                             class="w-12 h-12 rounded-lg object-cover border border-gray-200 cursor-zoom-in hover:opacity-80 transition-opacity"
                             @click="openImageModal('{{ asset('storage/'.$item->foto_wajah) }}')">
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                            {{ $item->verifikasi }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-gray-600 text-xs">
                        {{ $item->updated_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-4 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button @click="openDetailModal({{ json_encode($item) }})" 
                                    class="px-3 py-1.5 bg-indigo-600 text-white rounded-md text-xs font-medium hover:bg-indigo-700 transition-colors">
                                Detail
                            </button>
                            <button @click="openRejectModal({{ json_encode($item) }})" 
                                    class="px-3 py-1.5 bg-red-600 text-white rounded-md text-xs font-medium hover:bg-red-700 transition-colors">
                                Tolak
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                        <i class="bi bi-inbox text-3xl block mb-2"></i>
                        <p class="text-sm">Tidak ada teknisi yang perlu diverifikasi</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile: Card List View --}}
    <div class="md:hidden space-y-3">
        @forelse($data as $item)
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center gap-3">
                    <img src="{{$item->user->profile_photo_url}}" class="w-11 h-11 rounded-full object-cover border border-gray-200">
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">{{ $item->nama_asli }}</div>
                        <div class="text-xs text-gray-500">{{ $item->user->email }}</div>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-800 border border-amber-200">
                    {{ $item->verifikasi }}
                </span>
            </div>

            <div class="flex items-center gap-4 mb-3 pb-3 border-b border-gray-100">
                <div>
                    <span class="text-[10px] text-gray-400 uppercase block mb-1">Foto Wajah</span>
                    <img src="{{ asset('storage/'.$item->foto_wajah) }}" 
                         class="w-16 h-16 rounded-lg object-cover border border-gray-200 cursor-pointer"
                         @click="openImageModal('{{ asset('storage/'.$item->foto_wajah) }}')">
                </div>
                <div class="flex-1">
                    <span class="text-[10px] text-gray-400 uppercase block mb-1">Tgl Daftar</span>
                    <p class="text-xs text-gray-600">{{ $item->updated_at->format('d M Y') }}</p>
                </div>
            </div>

            <div class="flex gap-2">
                <button @click="openDetailModal({{ json_encode($item) }})" 
                        class="flex-1 px-3 py-2 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700 transition-colors">
                    Detail
                </button>
                <button @click="openRejectModal({{ json_encode($item) }})" 
                        class="flex-1 px-3 py-2 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700 transition-colors">
                    Tolak
                </button>
            </div>
        </div>
        @empty
        <div class="text-center py-10 text-gray-400 border border-gray-200 rounded-xl border-dashed">
            <i class="bi bi-inbox text-3xl block mb-2"></i>
            <p class="text-sm">Tidak ada teknisi yang perlu diverifikasi</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-2 pt-4 border-t border-gray-200">
        {{ $data->links() }}
    </div>

    {{-- ==================== MODAL IMAGE PREVIEW ==================== --}}
    <template x-teleport="body">
        <div x-show="showImageModal" 
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/95 p-4" 
             style="display: none;" 
             @keydown.escape.window="showImageModal = false">
            
            <button @click="showImageModal = false" 
                    class="absolute top-4 right-4 text-white/70 hover:text-white p-2 transition-colors">
                <i class="bi bi-x-lg text-2xl"></i>
            </button>

            <div class="max-w-5xl w-full h-full flex items-center justify-center" @click.away="showImageModal = false">
                <img :src="imageModalUrl" 
                     class="max-w-full max-h-[85vh] rounded-lg object-contain shadow-2xl"
                     x-show="showImageModal"
                     x-transition>
            </div>
        </div>
    </template>

    {{-- ==================== MODAL DETAIL TEKNISI ==================== --}}
    <template x-teleport="body">
        <div x-show="showDetailModal" 
             class="fixed inset-0 z-[1000] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4"
             style="display: none;"
             @keydown.escape.window="showDetailModal = false"
             @click.self="showDetailModal = false"
        >
            
            <div 
                class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col"
            >
                
                {{-- Header Modal --}}
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Detail Teknisi</h3>
                        <p class="text-sm text-gray-500" x-text="selectedItem?.nama_asli"></p>
                    </div>
                    <button @click="showDetailModal = false" 
                            class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-full transition-colors">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                {{-- Content Modal --}}
                <div class="overflow-y-auto p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        {{-- Kolom Kiri: Foto & Info Dasar --}}
                        <div class="lg:col-span-1 space-y-5">
                            {{-- Foto Wajah --}}
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 block">Foto Wajah</label>
                                <img :src="selectedItem ? '/storage/' + selectedItem.foto_wajah : ''" 
                                     class="w-full h-48 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-90 transition-opacity"
                                     @click="selectedItem ? openImageModal('/storage/' + selectedItem.foto_wajah) : null">
                            </div>

                            {{-- Info Dasar --}}
                            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                                <div>
                                    <span class="text-xs text-gray-500 block">User ID</span>
                                    <p class="text-sm font-medium text-gray-900" x-text="selectedItem?.user_id"></p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 block">Nama Lengkap</span>
                                    <p class="text-sm font-medium text-gray-900" x-text="selectedItem?.nama_asli"></p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 block">Status</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 mt-1"
                                          x-text="selectedItem?.verifikasi">
                                    </span>
                                </div>
                            </div>

                            {{-- Koordinat --}}
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 block">Koordinat Lokasi</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <span class="text-xs text-gray-500 block">Latitude</span>
                                        <p class="text-sm font-mono text-gray-900 bg-white px-2 py-1 rounded border border-gray-200 mt-1" 
                                           x-text="selectedItem?.latitude || '-'"></p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 block">Longitude</span>
                                        <p class="text-sm font-mono text-gray-900 bg-white px-2 py-1 rounded border border-gray-200 mt-1" 
                                           x-text="selectedItem?.longitude || '-'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Detail Informasi --}}
                        <div class="lg:col-span-2 space-y-5">
                            
                            {{-- Spesialisasi --}}
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Spesialisasi</label>
                                <p class="text-sm text-gray-900" x-text="selectedItem?.spesialisasi || 'Tidak ada spesialisasi'"></p>
                            </div>

                            {{-- Pengalaman --}}
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 block">Pengalaman Kerja</label>
                                <template x-if="selectedItem?.pengalaman && selectedItem.pengalaman.length > 0">
                                    <ul class="space-y-2">
                                        <template x-for="(exp, index) in selectedItem.pengalaman" :key="index">
                                            <li class="flex gap-3 p-3 bg-white rounded-lg border border-gray-200">
                                                <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-bold" 
                                                      x-text="index + 1"></span>
                                                <p class="text-sm text-gray-700" x-text="exp"></p>
                                            </li>
                                        </template>
                                    </ul>
                                </template>
                                <template x-if="!selectedItem?.pengalaman || selectedItem.pengalaman.length === 0">
                                    <p class="text-sm text-gray-500 italic">Belum ada pengalaman kerja</p>
                                </template>
                            </div>

                            {{-- Sertifikat --}}
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 block">Sertifikat</label>
                                <div class="grid grid-cols-4 gap-2">
                                    <template x-if="selectedItem?.sertifikat && selectedItem.sertifikat.length > 0">
                                        <template x-for="(path, index) in selectedItem.sertifikat" :key="index">
                                            <div class="aspect-square rounded-lg overflow-hidden border border-gray-200 cursor-pointer hover:opacity-80 transition-opacity"
                                                 @click="openImageModal('/storage/' + path)">
                                                <img :src="'/storage/' + path" class="w-full h-full object-cover">
                                            </div>
                                        </template>
                                    </template>
                                </div>
                                <template x-if="!selectedItem?.sertifikat || selectedItem.sertifikat.length === 0">
                                    <p class="text-sm text-gray-500 italic">Tidak ada sertifikat</p>
                                </template>
                            </div>

                            {{-- Foto Kegiatan --}}
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 block">Foto Kegiatan</label>
                                <div class="grid grid-cols-4 gap-2">
                                    <template x-if="selectedItem?.foto_kegiatan && selectedItem.foto_kegiatan.length > 0">
                                        <template x-for="(foto, index) in selectedItem.foto_kegiatan" :key="index">
                                            <div class="aspect-square rounded-lg overflow-hidden border border-gray-200 cursor-pointer hover:opacity-80 transition-opacity"
                                                 @click="openImageModal('/storage/' + foto)">
                                                <img :src="'/storage/' + foto" class="w-full h-full object-cover">
                                            </div>
                                        </template>
                                    </template>
                                </div>
                                <template x-if="!selectedItem?.foto_kegiatan || selectedItem.foto_kegiatan.length === 0">
                                    <p class="text-sm text-gray-500 italic">Tidak ada foto kegiatan</p>
                                </template>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Deskripsi</label>
                                <p class="text-sm text-gray-900 whitespace-pre-wrap" x-text="selectedItem?.deskripsi || 'Tidak ada deskripsi'"></p>
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Alamat Lengkap</label>
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-900 font-medium" x-text="selectedItem?.detail_alamat || 'Alamat tidak tersedia'"></p>
                                    <p class="text-xs text-gray-500">
                                        <span x-text="selectedItem?.kelurahan"></span><span x-show="selectedItem?.kelurahan">, </span>
                                        <span x-text="selectedItem?.kecamatan"></span><span x-show="selectedItem?.kecamatan">, </span>
                                        <span x-text="selectedItem?.kabupaten"></span><span x-show="selectedItem?.kabupaten">, </span>
                                        <span x-text="selectedItem?.provinsi"></span>
                                    </p>
                                </div>
                            </div>

                            {{-- Map --}}
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 block">Lokasi di Map</label>
                                <div id="map-detail" class="w-full h-64 rounded-lg border border-gray-200 bg-gray-100"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button @click="showDetailModal = false" 
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                    <button @click="selectedItem ? openRejectModal(selectedItem) : null; showDetailModal = false" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                        Tolak
                    </button>
                    <button @click="$wire.approveTeknisi(selectedItem?.id)" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                        Setujui
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ==================== MODAL TOLAK / REJECT ==================== --}}
    <template x-teleport="body">
        <div x-show="showRejectModal" 
             class="fixed inset-0 z-[1000] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4"
             style="display: none;"
             @keydown.escape.window="showRejectModal = false"
             @click.self="showRejectModal = false"
        >
            
            <div 
                class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
            >
                
                {{-- Header --}}
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200 bg-red-50">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-x-circle-fill text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Tolak Verifikasi</h3>
                        <p class="text-sm text-gray-500" x-text="selectedItem?.nama_asli"></p>
                    </div>
                </div>

                {{-- Content --}}
                <div class="px-6 py-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        x-model="rejectReason"
                        rows="4"
                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all resize-none"
                        placeholder="Masukkan alasan penolakan..."></textarea>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="bi bi-info-circle mr-1"></i>
                        Alasan ini akan dikirimkan ke teknisi terkait
                    </p>
                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button @click="showRejectModal = false" 
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors">
                        Batal
                    </button>
                    <button 
                        @click="submitReject"
                        :disabled="!rejectReason.trim()"
                        :class="!rejectReason.trim() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-700'"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium transition-colors">
                        Kirim Penolakan
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>