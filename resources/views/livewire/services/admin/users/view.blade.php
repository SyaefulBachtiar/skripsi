<div 
    class="space-y-4"
    x-data="{
        showLightbox: false,
        lightboxImage: '',
        lightboxName: '',
        openLightbox(url, name) {
            this.lightboxImage = url;
            this.lightboxName = name;
            this.showLightbox = true;
        },

        showDeleteModal: false,
        deleteId: null,
        deleteName: '',
        deleteEmail: '',
        openDeleteModal(id, name, email) {
            this.deleteId = id;
            this.deleteName = name;
            this.deleteEmail = email;
            this.showDeleteModal = true;
        },
        executeDelete() {
            // Memanggil fungsi backend Livewire secara langsung
            $wire.confirmDelete(this.deleteId);
            this.showDeleteModal = false;
        },
    }"
>
    <div class="mb-4 max-w-md">
        <div class="relative rounded-xl shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <i class="bi bi-search text-xs sm:text-sm"></i>
            </div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Cari berdasarkan nama atau email user..."
                class="w-full text-xs sm:text-sm pl-9 pr-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
            >
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50/70 dark:bg-slate-800/50 border-b border-gray-100 dark:border-gray-800 text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3.5 sm:px-6">User / Pengguna</th>
                        <th class="px-4 py-3.5 sm:px-6">Hak Akses (Role)</th>
                        <th class="px-4 py-3.5 sm:px-6">Status Keaktifan</th>
                        <th class="px-4 py-3.5 sm:px-6">Tanggal Daftar</th>
                        <th class="px-4 py-3.5 sm:px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs sm:text-sm">
                    @forelse($data_users as $user)
                        <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/20 transition-colors">
                            
                            {{-- KOLOM 1: AVATAR, NAMA, & EMAIL --}}
                            <td class="px-4 py-3 sm:px-6">
                                <div class="flex items-center gap-3">
                                    @php
                                        $userAvatarUrl = $user->profile_photo_url ?? asset('assets/default_profile/default_profile_teknisi.webp');
                                    @endphp
                                    <div 
                                        @click="openLightbox('{{ $userAvatarUrl }}', '{{ $user->name }}')"
                                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl overflow-hidden bg-slate-100 border border-gray-100 dark:border-gray-800 flex-shrink-0 shadow-sm cursor-zoom-in hover:scale-105 hover:border-indigo-400 dark:hover:border-indigo-500 transition-all duration-200"
                                        title="Klik untuk memperbesar foto"
                                    >
                                        {{-- Memanfaatkan accessor profile_photo_url dinamis dari model User --}}
                                        <img 
                                            src="{{ $userAvatarUrl }}" 
                                            alt="Avatar {{ $user->name }}"
                                            class="w-full h-full object-cover"
                                        >
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 truncate max-w-[150px] sm:max-w-[200px]" title="{{ $user->name }}">
                                            {{ $user->name }}
                                        </p>
                                        <p class="text-[11px] sm:text-xs text-gray-400 dark:text-gray-500 truncate max-w-[150px] sm:max-w-[200px]" title="{{ $user->email }}">
                                            {{ $user->email }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- KOLOM 2: ROLE BADGE --}}
                            <td class="px-4 py-3 sm:px-6 align-middle">
                                @php
                                    $roleBadge = match($user->role) {
                                        'admin' => ['bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400 border-purple-100 dark:border-transparent', 'Crown', 'Admin'],
                                        'technician' => ['bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400 border-indigo-100 dark:border-transparent', 'Tools', 'Teknisi'],
                                        default => ['bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 border-blue-100 dark:border-transparent', 'Person', 'Pelanggan'],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg border text-[11px] font-bold tracking-wide {{ $roleBadge[0] }}">
                                    <i class="bi bi-{{ $roleBadge[1] }} text-[10px]"></i>
                                    {{ $roleBadge[2] }}
                                </span>
                            </td>

                            {{-- KOLOM 3: LAST SEEN / STATUS ONLINE --}}
                            <td class="px-4 py-3 sm:px-6 align-middle">
                                @if($user->last_seen_at && $user->last_seen_at->isAfter(now()->subMinutes(5)))
                                    <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Online
                                    </span>
                                @else
                                    <span class="text-[11px] sm:text-xs text-gray-400 dark:text-gray-500 font-medium">
                                        {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Belum pernah aktif' }}
                                    </span>
                                @endif
                            </td>

                            {{-- KOLOM 4: TANGGAL DAFTAR --}}
                            <td class="px-4 py-3 sm:px-6 align-middle text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 font-medium">
                                {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}
                            </td>

                            {{-- KOLOM 5: AKSI TOMBOL MANAGEMENT --}}
                            <td class="px-4 py-3 sm:px-6 align-middle text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Tombol Detail / Edit --}}
                                    <a 
                                        href="{{ route('users.edit', ['id' => $user->id]) }}"
                                        wire:navigate
                                        class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors"
                                        title="Detail / Edit User"
                                    >
                                        <i class="bi bi-pencil text-sm"></i>
                                    </a>
                                    
                                    {{-- Tombol Hapus (Jangan munculkan jika akun tersebut adalah Admin yang sedang login) --}}
                                    @if($user->id !== auth()->id())
                                        <button 
                                            type="button" 
                                            @click="openDeleteModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->email }}')"
                                            class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                            title="Hapus User"
                                        >
                                            <i class="bi bi-trash3 text-sm"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- STATE JIKA DATA TIDAK DITEMUKAN / KOSONG --}}
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500 italic">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="bi bi-people text-3xl opacity-40"></i>
                                    <p class="text-xs sm:text-sm font-medium">Tidak ada data pengguna yang cocok dengan pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION LINK (Otomatis responsive bawaan Tailwind/Livewire) --}}
    @if($data_users->hasPages())
        <div class="pt-2">
            {{ $data_users->links() }}
        </div>
    @endif

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
            {{-- Tombol Tutup --}}
            <button 
                @click="showLightbox = false" 
                class="absolute top-5 right-5 text-white/70 hover:text-white p-2.5 rounded-full bg-white/5 hover:bg-white/10 transition-all duration-200"
            >
                <i class="bi bi-x-lg text-xl sm:text-2xl"></i>
            </button>
            
            {{-- Kontainer Gambar Besar --}}
            <div 
                class="max-w-3xl w-full flex flex-col items-center justify-center" 
                @click.away="showLightbox = false"
            >
                {{-- Nama User di Atas Gambar --}}
                <p 
                    x-text="lightboxName" 
                    class="text-white font-bold text-sm sm:text-base mb-4 tracking-wider uppercase bg-white/5 px-4 py-1.5 rounded-full border border-white/10"
                ></p>
                
                {{-- Elemen Foto Img Utama --}}
                <img 
                    :src="lightboxImage" 
                    :alt="lightboxName"
                    class="max-w-full max-h-[70vh] sm:max-h-[75vh] rounded-2xl object-contain shadow-2xl border border-white/10 transition-transform duration-300"
                    x-show="showLightbox"
                    x-transition:enter="transition ease-out duration-300 transform scale-95"
                    x-transition:enter-start="scale-95"
                    x-transition:enter-end="scale-100"
                >
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div 
            x-show="showDeleteModal" 
            class="fixed inset-0 z-[99998] flex items-center justify-center px-4 overflow-hidden"
            style="display: none;"
            @keydown.escape.window="showDeleteModal = false"
        >
            {{-- Backdrop Belakang --}}
            <div 
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                @click="showDeleteModal = false"
                x-show="showDeleteModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            ></div>

            {{-- Card Dialog Kotak Modal --}}
            <div 
                class="bg-white dark:bg-gray-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-800 relative z-10"
                x-show="showDeleteModal"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            >
                <div class="flex items-start gap-4">
                    {{-- Ikon Alert Warning Merah --}}
                    <div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-950/30 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0 shadow-inner">
                        <i class="bi bi-exclamation-triangle text-lg"></i>
                    </div>
                    
                    {{-- Informasi User --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white leading-snug">
                            Hapus Akun Pengguna?
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                            Tindakan ini akan menghapus data pengguna secara permanen dari sistem **Servisio**. Seluruh berkas autentikasi login yang bersangkutan akan hangus.
                        </p>

                        {{-- Review Ringkas Detail Target --}}
                        <div class="mt-3.5 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800 space-y-1">
                            <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">
                                <i class="bi bi-person mr-1 text-slate-400"></i> <span x-text="deleteName"></span>
                            </p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                <i class="bi bi-envelope mr-1 text-slate-400"></i> <span x-text="deleteEmail"></span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Baris Tombol Eksekusi Aksi --}}
                <div class="flex justify-end gap-2.5 mt-5 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button 
                        type="button" 
                        @click="showDeleteModal = false"
                        class="px-4 py-2 bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold text-xs rounded-xl transition-all shadow-sm border border-gray-200 dark:border-transparent active:scale-95"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        @click="executeDelete()"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-red-100 dark:shadow-none active:scale-95"
                    >
                        Ya, Hapus Pengguna
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
