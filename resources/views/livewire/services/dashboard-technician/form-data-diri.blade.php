<div class="p-4 bg-white shadow-sm rounded-xl border border-gray-100 pb-20" 
     x-data="{ 
        showModal: false, 
        modalImage: '',
        openModal(url) {
            this.modalImage = url;
            this.showModal = true;
        }
     }">
    
    <form wire:submit.prevent="save" class="space-y-6">
        
        {{-- Nama Asli --}}
        <div>
            <x-input-label for="nama_asli" :value="__('Nama Lengkap (Sesuai KTP)')" class="mb-2" />
            <x-text-input 
                id="nama_asli" 
                type="text" 
                class="w-full" 
                wire:model="nama_asli" 
                placeholder="Masukkan nama lengkap..." 
            />
            <x-input-error :messages="$errors->get('nama_asli')" class="mt-2" />
        </div>

        {{-- Input Foto Identitas (Tunggal) --}}
        <div>
            <x-input-label :value="__('Foto Identitas / Foto Asli')" class="mb-2" />
            <div class="flex items-center gap-4">
                {{-- Preview Area --}}
                <div class="relative w-32 h-32 rounded-2xl border-2 border-dashed border-gray-200 overflow-hidden flex items-center justify-center bg-gray-50 group">
                    @if ($foto_asli)
                        <img src="{{ $foto_asli->temporaryUrl() }}" class="w-full h-full object-cover cursor-pointer" @click="openModal('{{ $foto_asli->temporaryUrl() }}')">
                    @elseif ($existing_foto_asli)
                        <img src="{{ asset('storage/' . $existing_foto_asli) }}" class="w-full h-full object-cover cursor-pointer" @click="openModal('{{ asset('storage/' . $existing_foto_asli) }}')">
                    @else
                        <i class="bi bi-person-bounding-box text-3xl text-gray-300"></i>
                    @endif
                    
                    {{-- Hover Overlay --}}
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity cursor-pointer" @click="$refs.fotoAsliInput.click()">
                        <i class="bi bi-camera text-white text-xl"></i>
                    </div>
                </div>

                <div class="flex-1">
                    <p class="text-xs text-gray-500 mb-2">Ambil foto wajah asli secara langsung / KTP untuk verifikasi.</p>
                    <button type="button" @click="$refs.fotoAsliInput.click()" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-wider">
                        Pilih Foto
                    </button>

                    {{-- Input File dengan atribut capture --}}
                    <input 
                        type="file" 
                        x-ref="fotoAsliInput" 
                        wire:model="foto_asli" 
                        class="hidden" 
                        accept="image/*" 
                        capture="user"
                    >
                </div>
            </div>
            <x-input-error :messages="$errors->get('foto_asli')" class="mt-2" />
        </div>

        <hr class="border-gray-100">

        {{-- Foto Kegiatan (Multiple - Maks 5) --}}
        <div class="space-y-4">
            <div class="flex justify-between items-end">
                <div>
                    <x-input-label :value="__('Foto Kegiatan (Maksimal 5)')" />
                    <p class="text-xs text-gray-500">Dokumentasi pengerjaan service sebagai portofolio.</p>
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    {{ count($existing_foto_kegiatan ?? []) + count($foto_kegiatan ?? []) }} / 5 Foto
                </span>
            </div>

            {{-- Upload Area --}}
            {{-- <div 
                class="relative border-2 border-dashed border-gray-300 rounded-2xl p-8 bg-gray-50 flex flex-col items-center justify-center transition-all"
                :class="(@js(count($existing_foto_kegiatan ?? [])) + @js(count($foto_kegiatan ?? []))) >= 5
                        ? 'opacity-50 cursor-not-allowed bg-gray-100' 
                        : 'hover:border-indigo-400 hover:bg-indigo-50/30 cursor-pointer'"
                @click="if((@js(count($existing_foto_kegiatan ?? [])) + @js(count($foto_kegiatan ?? []))) < 5) $refs.fileKegiatan.click()"
            > --}}
            <div 
                class="relative border-2 border-dashed border-gray-300 rounded-2xl p-8 bg-gray-50 flex flex-col items-center justify-center transition-all"
                :class="{{ (count($existing_foto_kegiatan ?? []) + count($foto_kegiatan ?? [])) }} >= 5
                        ? 'opacity-50 cursor-not-allowed bg-gray-100' 
                        : 'hover:border-indigo-400 hover:bg-indigo-50/30 cursor-pointer'"
                @click="if({{ (count($existing_foto_kegiatan ?? []) + count($foto_kegiatan ?? [])) }} < 5) $refs.fileKegiatan.click()"
            >
                {{-- Loading Overlay --}}
                <div 
                    wire:loading.flex
                    wire:target="temp_foto_kegiatan" 
                    class="absolute inset-0 bg-white/90 backdrop-blur-sm z-20 rounded-2xl flex-col items-center justify-center w-full h-full"
                >
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-indigo-200 border-t-indigo-600"></div>
                    <span class="mt-3 text-sm font-medium text-gray-700">Sedang mengunggah...</span>
                </div>

                <i class="bi bi-images text-4xl text-gray-300"></i>
                <p class="text-sm text-gray-600 mt-2 font-semibold">Tambah foto kegiatan</p>
                <p class="text-xs text-gray-400">Klik di sini (PNG, JPG maks 2MB)</p>
                
                {{-- <input 
                    type="file" 
                    wire:model="temp_foto_kegiatan" 
                    x-ref="fileKegiatan" 
                    class="hidden" 
                    multiple 
                    accept="image/*"
                > --}}
                <input 
                    type="file" 
                    wire:model="temp_foto_kegiatan" 
                    x-ref="fileKegiatan" 
                    class="hidden" 
                    multiple 
                    accept="image/*"
                >
            </div>

            {{-- <x-input-error :messages="$errors->get('temp_foto_kegiatan.*')" class="mt-2" /> --}}
            <x-input-error :messages="$errors->get('temp_foto_kegiatan')" class="mt-2" />

            {{-- Preview Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mt-4">
                {{-- Foto yang sudah ada di Server --}}
                @foreach($existing_foto_kegiatan ?? [] as $index => $path)
                    <div class="relative aspect-square group shadow-sm">
                        <img src="{{ asset('storage/' . $path) }}" class="w-full h-full object-cover rounded-xl border border-gray-100 cursor-pointer" @click="openModal('{{ asset('storage/' . $path) }}')">
                        <button type="button" wire:click="removeExistingFotoKegiatan({{ $index }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg hover:bg-red-700 transition-colors">
                            <i class="bi bi-x"></i>
                        </button>
                        <div class="absolute bottom-1 left-1 bg-black/50 text-[8px] text-white px-1.5 py-0.5 rounded-md">LAMA</div>
                    </div>
                @endforeach

                {{-- Foto Baru (Temporary) --}}
                @foreach($foto_kegiatan as $index => $file)
                    <div class="relative aspect-square group shadow-sm">
                        @if ($file && method_exists($file, 'temporaryUrl'))
                            <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover rounded-xl border-2 border-indigo-400 cursor-pointer shadow-indigo-100" @click="openModal('{{ $file->temporaryUrl() }}')">
                        @endif
                        <button type="button" wire:click="removeNewFotoKegiatan({{ $index }})" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg hover:bg-red-700 transition-colors border-2 border-white">
                            <i class="bi bi-x"></i>
                        </button>
                        <div class="absolute bottom-1 left-1 bg-indigo-600 text-[8px] text-white px-1.5 py-0.5 rounded-md uppercase">Baru</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="pt-4">
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center justify-center gap-2">
                <i class="bi bi-check2-circle"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- Lightbox Modal --}}
    <template x-teleport="body">
        <div x-show="showModal" class="fixed inset-0 z-[999] flex items-center justify-center bg-black/90 p-4" style="display: none;" @keydown.escape.window="showModal = false" x-transition>
            <button @click="showModal = false" class="absolute top-5 right-5 text-white/70 hover:text-white"><i class="bi bi-x-lg text-3xl"></i></button>
            <div class="max-w-5xl w-full h-full flex items-center justify-center" @click.away="showModal = false">
                <img :src="modalImage" class="max-w-full max-h-full rounded-lg object-contain shadow-2xl">
            </div>
        </div>
    </template>
</div>