<div 
    x-data="{
        isLoading: true,
        isEditing: false, {{-- State untuk mengontrol tampilan form --}}
        showModal: false, 
        modalImage: '',
        openModal(url) {
            this.modalImage = url;
            this.showModal = true;
        }
    }"
    x-init="setTimeout(() => isLoading = false, 800)"
    class="max-w-4xl mx-auto space-y-4 mt-4"
>
    {{-- Header & Button Edit --}}
    <div class="flex justify-between items-center px-2">
        <h2 class="text-xl font-bold text-gray-800">
            <span x-show="!isEditing">Profil Data Diri</span>
            <span x-show="isEditing">Edit Data Diri</span>
        </h2>

        <button 
            @click="isEditing = !isEditing"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm"
            :class="isEditing 
                ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' 
                : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-indigo-100'"
        >
            <template x-if="!isEditing">
                <div class="flex items-center gap-2">
                    <i class="bi bi-pencil-square"></i>
                    <span>Edit Profil</span>
                </div>
            </template>
            <template x-if="isEditing">
                <div class="flex items-center gap-2">
                    <i class="bi bi-x-lg"></i>
                    <span>Batal</span>
                </div>
            </template>
        </button>
    </div>

    {{-- Skeleton Loading --}}
    <div x-show="isLoading" class="space-y-4 animate-pulse">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 space-y-6">
            <div class="h-20 w-full bg-gray-100 rounded-2xl"></div>
            <div class="grid grid-cols-3 gap-4">
                <div class="h-24 bg-gray-100 rounded-xl"></div>
            </div>
        </div>
    </div>

    {{-- Container Content --}}
    <div x-show="!isLoading" x-cloak>
        
        {{-- Tampilan View (Hanya muncul jika isEditing = false) --}}
        <div 
            x-show="!isEditing" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden"
        >
            {{-- Header Status --}}
            <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="bi bi-person-check text-white text-xl"></i>
                    </div>
                    <span class="font-bold text-gray-700">Informasi Terverifikasi</span>
                </div>

                @if($data->verifikasi === 'diproses')
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100 uppercase">Diproses</span>
                @elseif($data->verifikasi === 'diverifikasi')
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-wider">Terverifikasi</span>
                @endif
            </div>

            <div class="p-6 space-y-2">
                {{-- Data Profil (Nama, Foto Wajah, dll sesuai kode sebelumnya) --}}
                <div class="grid sm:grid-cols-3 gap-6">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Nama Lengkap</div>
                    <div class="sm:col-span-2 font-bold text-gray-800">{{ $data->nama_asli }}</div>
                </div>

                <hr class="border-gray-50">

                {{-- Foto Wajah --}}
                <div class="grid sm:grid-cols-3 gap-4 sm:gap-6 items-start">
                    <div class="space-y-1">
                        <div class="text-sm font-semibold text-gray-400 uppercase tracking-widest">Foto Identitas</div>
                        <p class="text-xs text-gray-400 leading-relaxed italic">Foto wajah asli/KTP untuk validasi teknisi.</p>
                    </div>
                    <div class="sm:col-span-2">
                        <div class="relative w-32 h-40 group cursor-pointer" @click="openModal('{{ asset('storage/' . $data->foto_wajah) }}')">
                            <img 
                                src="{{ asset('storage/' . $data->foto_wajah) }}" 
                                class="w-full h-full object-cover rounded-2xl shadow-sm border border-gray-100 group-hover:brightness-90 transition-all"
                                alt="Foto Identitas"
                            >
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="bi bi-zoom-in text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-50">

                {{-- Foto Kegiatan --}}
                <div class="grid sm:grid-cols-3 gap-4 sm:gap-6 items-start pb-4">
                    <div class="space-y-1">
                        <div class="text-sm font-semibold text-gray-400 uppercase tracking-widest">Portofolio Kerja</div>
                        <p class="text-xs text-gray-400 leading-relaxed italic">Dokumentasi hasil service di lapangan.</p>
                    </div>
                    <div class="sm:col-span-2">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @forelse($data->foto_kegiatan ?? [] as $item)
                                <div class="relative aspect-square group cursor-pointer overflow-hidden rounded-xl border border-gray-100" @click="openModal('{{ asset('storage/' . $item) }}')">
                                    <img 
                                        src="{{ asset('storage/' . $item) }}" 
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                        alt="Foto Kegiatan"
                                    >
                                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <i class="bi bi-eye text-white"></i>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-8 text-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                                    <i class="bi bi-images text-gray-300 text-3xl block mb-2"></i>
                                    <span class="text-xs text-gray-400 font-medium uppercase tracking-tighter">Belum ada foto kegiatan</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tampilan Form Edit (Hanya muncul jika isEditing = true) --}}
        <div 
            x-show="isEditing" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
        >
            <livewire:services.dashboard-technician.form-data-diri />
        </div>

    </div>

    {{-- Lightbox Modal --}}
    <template x-teleport="body">
        <div x-show="showModal" class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/95 p-4" style="display: none;" @keydown.escape.window="showModal = false" x-transition>
            <button @click="showModal = false" class="absolute top-5 right-5 text-white/70 hover:text-white p-2">
                <i class="bi bi-x-lg text-3xl"></i>
            </button>
            <div class="max-w-5xl w-full h-full flex items-center justify-center" @click.away="showModal = false">
                <img :src="modalImage" class="max-w-full max-h-full rounded-lg object-contain shadow-2xl">
            </div>
        </div>
    </template>
</div>