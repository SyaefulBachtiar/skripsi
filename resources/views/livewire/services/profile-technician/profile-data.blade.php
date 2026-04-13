<div 
    x-data="{ 
        isEditing: false,
        isLoading: true
    }" 
    x-init="setTimeout(() => isLoading = false, 800)"
>
    {{-- SKELETON LOADING --}}
    <div x-show="isLoading" class="space-y-4 animate-pulse">
        <div class="flex justify-end">
            <div class="h-10 w-32 bg-gray-200 rounded-lg"></div>
        </div>
        <div class="bg-white rounded-2xl shadow-md p-6 space-y-6">
            <div class="space-y-2">
                <div class="h-5 w-24 bg-gray-200 rounded"></div>
                <div class="h-20 w-full bg-gray-100 rounded-xl"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <div class="h-5 w-24 bg-gray-200 rounded"></div>
                    <div class="flex gap-2">
                        <div class="h-8 w-16 bg-gray-100 rounded-lg"></div>
                        <div class="h-8 w-16 bg-gray-100 rounded-lg"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="h-5 w-24 bg-gray-200 rounded"></div>
                    <div class="h-8 w-32 bg-gray-100 rounded-lg"></div>
                </div>
            </div>
        </div>
    </div>

    <div 
        x-show="!isLoading"
        x-cloak x-transition.opacity.duration.500ms
        class="space-y-4"
    >
        @if(auth()->user()->id === $data->user_id)
            {{-- Tombol Edit Utama --}}
            <div class="flex justify-end">
                <button 
                    @click="isEditing = !isEditing" 
                    class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition-all shadow-sm text-sm sm:text-base"
                    :class="isEditing ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-indigo-600 text-white hover:bg-indigo-700'"
                >
                    <i class="bi" :class="isEditing ? 'bi-x-lg' : 'bi-pencil-square'"></i>
                    <span x-text="isEditing ? 'Batal Edit' : 'Edit Profil Teknisi'"></span>
                </button>
            </div>
        @endif

        {{-- Form Edit (Muncul jika isEditing = true) --}}
        <div 
            x-show="isEditing" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
        >
            <livewire:services.dashboard-technician.form-technician />
        </div>

        {{-- Konten Tampilan (Muncul jika isEditing = false) --}}
        <div x-show="!isEditing" 
            x-transition:enter="transition ease-out duration-300"
            class="bg-white rounded-2xl shadow-md overflow-hidden">
            
            <div class="p-6 space-y-6">
                {{-- Deskripsi Section --}}
                <section>
                    <h1 class="text-md sm:text-base font-bold text-gray-800 flex items-center gap-2 mb-2">
                        <i class="bi bi-card-text text-indigo-500 leading-none"></i> Deskripsi
                    </h1>
                    <p class="text-sm sm:text-base text-gray-600 leading-relaxed bg-gray-50 p-3 rounded-xl border border-gray-100">
                        {{ $data->deskripsi ?? 'Belum ada deskripsi.' }}
                    </p>
                </section>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Spesialis Section --}}
                    <section>
                        <h1 class="text-md sm:text-base font-bold text-gray-800 flex items-center gap-2 mb-3">
                            <i class="bi bi-tools text-indigo-500 leading-none"></i> Spesialis
                        </h1>
                        <div class="flex flex-wrap gap-2">
                            @forelse($data->spesialisasi ?? [] as $item)
                                <span class="bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg text-sm font-medium border border-indigo-100 shadow-sm">
                                    {{ $item }}
                                </span>
                            @empty
                                <span class="text-gray-400 italic text-sm">Belum ada spesialisasi</span>
                            @endforelse
                        </div>
                    </section>

                    {{-- Pengalaman Section --}}
                    <section>
                        <h1 class="text-md sm:text-base font-bold text-gray-800 flex items-center gap-2 mb-3">
                            <i class="bi bi-briefcase text-indigo-500"></i> Pengalaman
                        </h1>
                        <div class="flex flex-wrap gap-2">
                            @forelse($data->pengalaman ?? [] as $item)
                                <span class="bg-green-50 text-green-700 px-3 py-1.5 rounded-lg text-sm font-medium border border-green-100 shadow-sm">
                                    {{ $item }}
                                </span>
                            @empty
                                <span class="text-gray-400 italic text-sm">Belum ada data pengalaman</span>
                            @endforelse
                        </div>
                    </section>
                </div>

                {{-- Sertifikat Section --}}
                <section x-data="{ 
                    showModal: false, 
                    modalImage: '',
                    openModal(url) {
                        this.modalImage = url;
                        this.showModal = true;
                    }
                }">
                    <h1 class="text-md sm:text-base font-bold text-gray-800 flex items-center gap-2 mb-4">
                        <i class="bi bi-patch-check text-indigo-500"></i> Sertifikat Keahlian
                    </h1>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @forelse($data->sertifikat ?? [] as $item)
                            <div 
                                @click="openModal('{{ asset('storage/' . $item) }}')"
                                class="relative aspect-square overflow-hidden rounded-xl border-2 border-gray-100 shadow-sm cursor-pointer"
                            >
                                <img 
                                    src="{{ asset('storage/' . $item) }}" 
                                    alt="Sertifikat" 
                                    class="w-full h-full object-cover"
                                >
                                
                                {{-- Indikator klik (statis) --}}
                                <div class="absolute bottom-2 right-2 bg-black/40 p-1.5 rounded-lg">
                                    <i class="bi bi-fullscreen text-white text-xs"></i>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-8 text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                <i class="bi bi-image text-4xl text-gray-300"></i>
                                <p class="text-gray-400 text-sm mt-2">Tidak ada file sertifikat.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Lightbox Modal --}}
                    <template x-teleport="body">
                        <div 
                            x-show="showModal" 
                            class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/95 p-4"
                            style="display: none;"
                            @keydown.escape.window="showModal = false"
                        >
                            {{-- Tombol Close --}}
                            <button 
                                @click="showModal = false" 
                                class="absolute top-5 right-5 text-white/80 p-2 hover:text-white"
                            >
                                <i class="bi bi-x-lg text-3xl"></i>
                            </button>

                            {{-- Image Wrapper --}}
                            <div class="max-w-5xl w-full h-full flex items-center justify-center" @click.away="showModal = false">
                                <img 
                                    :src="modalImage" 
                                    class="max-w-full max-h-full rounded-lg object-contain border border-white/10 shadow-2xl"
                                >
                            </div>
                        </div>
                    </template>
                </section>
            </div>
        </div>
        {{-- SKELETON LOADING --}}
        <div x-show="isLoading" class="space-y-4 animate-pulse">
            <div class="flex justify-end">
                <div class="h-10 w-32 bg-gray-200 rounded-lg"></div>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-6 space-y-6">
                <div class="space-y-2">
                    <div class="h-5 w-24 bg-gray-200 rounded"></div>
                    <div class="h-20 w-full bg-gray-100 rounded-xl"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <div class="h-5 w-24 bg-gray-200 rounded"></div>
                        <div class="flex gap-2">
                            <div class="h-8 w-16 bg-gray-100 rounded-lg"></div>
                            <div class="h-8 w-16 bg-gray-100 rounded-lg"></div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="h-5 w-24 bg-gray-200 rounded"></div>
                        <div class="h-8 w-32 bg-gray-100 rounded-lg"></div>
                    </div>
                </div>
            </div>
    </div>
</div>