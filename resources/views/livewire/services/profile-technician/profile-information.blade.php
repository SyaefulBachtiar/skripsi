<div x-data="{
    isLoading: true,
    editingName: false,
    name: '{{ auth()->user()->name }}',
    oldName: '{{ auth()->user()->name }}',
    imageUrl: '{{ auth()->user()->profile_photo_url }}',
    
    oldImageUrl: '', {{-- Akan diisi di x-init --}}
    showStatus: false,
    hasNewFile: false,

    init() {
        this.oldImageUrl = this.imageUrl;
        setTimeout(() => this.isLoading = false, 800);
    },
    showStatus: false,
    hasNewFile: false,

    async saveData() {

        await $wire.updateProfile(this.name, this.hasNewFile);

        this.editingName = false;
        this.hasNewFile = false;
        this.oldName = this.name;
        this.oldImageUrl = this.imageUrl;
        this.showStatus = true;
        setTimeout(() => this.showStatus = false, 3000);
    },
    cancelEditName() {
        this.editingName = false;
        this.name = this.oldName;
    },
    cancelEditPhoto() {
        this.imageUrl = this.oldImageUrl;
        this.hasNewFile = false;
    },
    previewFile(event) {
        const file = event.target.files[0];
        if (file) {
            this.imageUrl = URL.createObjectURL(file);
            this.hasNewFile = true;

            @this.upload('photo', file);
        }
    }
}"
class="flex items-center justify-center">

    {{-- SKELETON WIREFRAME --}}
    <div x-show="isLoading" class="bg-white border border-gray-100 rounded-2xl p-6 w-full shadow-md animate-pulse">
        <div class="flex items-start gap-5">
            {{-- Avatar Skeleton --}}
            <div class="w-20 h-20 rounded-full bg-gray-200 shrink-0"></div>
            
            {{-- Text Skeleton --}}
            <div class="flex-1 space-y-3 pt-2">
                <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>
    </div>

    <div 
        x-show="!isLoading" 
        x-cloak x-transition.opacity.duration.500ms
        class="bg-white border border-gray-100 rounded-2xl p-6 w-full shadow-md"
    >

        <div class="flex items-start gap-5">

            {{-- Avatar Section --}}
            <div class="flex flex-col items-center gap-2 shrink-0">
                <div class="relative w-20 h-20">

                    {{-- Lingkaran avatar --}}
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 border border-gray-200 flex items-center justify-center">
                        <template x-if="imageUrl">
                            <img :src="imageUrl" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!imageUrl">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="8" r="4" fill="#CBD5E1"/>
                                <path d="M4 20c0-4 3.582-7 8-7s8 3 8 7" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </template>
                    </div>

                    {{-- Tombol kamera --}}
                    <label class="absolute bottom-0 right-0 w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center cursor-pointer border-2 border-white">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="13" r="4" stroke="white" stroke-width="2"/>
                        </svg>
                        <input wire:model="photo" type="file" class="hidden" accept="image/*" @change="previewFile">
                    </label>
                </div>

                {{-- Tombol aksi foto (hanya muncul jika ada file baru) --}}
                <div x-show="hasNewFile" x-transition class="flex gap-1.5">
                    <button @click="saveData()" class="text-xs bg-green-50 text-green-700 border border-green-200 px-2.5 py-1 rounded-lg cursor-pointer">
                        Simpan
                    </button>
                    <button @click="cancelEditPhoto()" class="text-xs bg-red-50 text-red-700 border border-red-200 px-2.5 py-1 rounded-lg cursor-pointer">
                        Batal
                    </button>
                </div>
            </div>

            {{-- Name & Email Section --}}
            <div class="flex-1 min-w-0 pt-1">

                {{-- View mode --}}
                <div x-show="!editingName" class="flex items-center gap-2 mb-1">
                    <span x-text="name" class="text-lg font-medium text-gray-800 truncate w-full"></span>
                    <button @click="editingName = true" class="text-gray-400 hover:text-indigo-500 transition shrink-0">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                </div>

                {{-- Edit mode --}}
                <div x-show="editingName" class="mb-1">
                    <input
                        type="text"
                        x-model="name"
                        @keydown.enter="saveData()"
                        @keydown.escape="cancelEditName()"
                        class="w-full text-lg font-medium text-gray-800 bg-transparent border-0 border-b-2 border-indigo-500 outline-none pb-0.5"
                    >
                    <div class="flex gap-1.5 mt-2">
                        <button @click="saveData()" class="flex items-center gap-1 text-xs bg-green-50 text-green-700 border border-green-200 px-3 py-1 rounded-full cursor-pointer">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Simpan
                        </button>
                        <button @click="cancelEditName()" class="flex items-center gap-1 text-xs bg-gray-100 text-gray-600 border border-gray-200 px-3 py-1 rounded-full cursor-pointer">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            Batal
                        </button>
                    </div>
                </div>

                <p class="text-sm text-gray-400 mb-1.5 w-full truncate">{{ auth()->user()->email }}</p>

                {{-- Status indicator --}}
                <div x-show="showStatus" x-transition class="flex items-center gap-1.5">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span class="text-xs text-green-600">Perubahan berhasil diterapkan</span>
                </div>

            </div>
        </div>
    </div>
</div>