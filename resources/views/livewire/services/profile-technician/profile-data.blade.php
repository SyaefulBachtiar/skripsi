<div x-data="{ isEditing: false }" class="space-y-4">

    {{-- Tombol Edit Utama --}}
    <div class="flex justify-end">
        <button 
            @click="isEditing = !isEditing" 
            class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-all shadow-sm"
            :class="isEditing ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-indigo-600 text-white hover:bg-indigo-700'"
        >
            <i class="bi" :class="isEditing ? 'bi-x-lg' : 'bi-pencil-square'"></i>
            <span x-text="isEditing ? 'Batal Edit' : 'Edit Profil Teknisi'"></span>
        </button>
    </div>

    {{-- Form Edit (Muncul jika isEditing = true) --}}
    <div x-show="isEditing" 
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
        
        <div class="p-6 space-y-8">
            {{-- Deskripsi Section --}}
            <section>
                <h1 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-2">
                    <i class="bi bi-card-text text-indigo-500"></i> Deskripsi
                </h1>
                <p class="text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100">
                    {{ $data->deskripsi ?? 'Belum ada deskripsi.' }}
                </p>
            </section>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Spesialis Section --}}
                <section>
                    <h1 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-3">
                        <i class="bi bi-tools text-indigo-500"></i> Spesialis
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
                    <h1 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-3">
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
            <section>
                <h1 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
                    <i class="bi bi-patch-check text-indigo-500"></i> Sertifikat Keahlian
                </h1>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @forelse($data->sertifikat ?? [] as $item)
                        <div class="group relative aspect-square overflow-hidden rounded-xl border-2 border-gray-100 hover:border-indigo-300 transition-all shadow-sm">
                            <img 
                                src="{{ asset('storage/' . $item) }}" 
                                alt="Sertifikat" 
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                            >
                            {{-- Overlay saat hover --}}
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all flex items-center justify-center">
                                <a href="{{ asset('storage/' . $item) }}" target="_blank" class="opacity-0 group-hover:opacity-100 bg-white p-2 rounded-full shadow-lg">
                                    <i class="bi bi-eye-fill text-indigo-600"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                            <i class="bi bi-image text-4xl text-gray-300"></i>
                            <p class="text-gray-400 text-sm mt-2">Tidak ada file sertifikat.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>