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
                        <th class="px-4 py-3.5 sm:px-6">Teknisi / Pengguna</th>
                        <th class="px-4 py-3.5 sm:px-6">Alasan Penolakan</th>
                        <th class="px-4 py-3.5 sm:px-6">Status Keaktifan</th>
                        <th class="px-4 py-3.5 sm:px-6">Tanggal Daftar</th>
                        <th class="px-4 py-3.5 sm:px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs sm:text-sm">
                    @forelse($data_users as $user)
                        @php
                            $userAvatarUrl = $user->profile_photo_url ?? asset('assets/default_profile/default_profile_teknisi.webp');
                        @endphp
                        <tr wire:key="user-rejected-{{ $user->id }}" class="hover:bg-slate-50/40 dark:hover:bg-slate-800/20 transition-colors">
                            
                            {{-- KOLOM 1: AVATAR, NAMA, & EMAIL --}}
                            <td class="px-4 py-3 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div 
                                        @click="openLightbox('{{ $userAvatarUrl }}', '{{ $user->name }}')"
                                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl overflow-hidden bg-slate-100 border border-gray-100 dark:border-gray-800 flex-shrink-0 shadow-sm cursor-zoom-in hover:scale-105 hover:border-indigo-400 dark:hover:border-indigo-500 transition-all duration-200"
                                        title="Klik untuk memperbesar foto"
                                    >
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

                            {{-- KOLOM 2: ALASAN DITOLAK BADGE & TEXT --}}
                            <td class="px-4 py-3 sm:px-6 align-middle">
                                <div class="max-w-[220px] sm:max-w-[300px] flex flex-col gap-1">
                                    <span class="inline-flex items-center gap-1 w-max px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 border border-red-100 dark:border-transparent">
                                        <i class="bi bi-x-circle-fill text-[9px]"></i> Verifikasi Ditolak
                                    </span>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 italic truncate" title="{{ $user->technician->alasan_ditolak ?? 'Tidak ada alasan spesifik' }}">
                                        "{{ $user->technician->alasan_ditolak ?? 'Tidak ada catatan alasan.' }}"
                                    </p>
                                </div>
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

                            {{-- KOLOM 5: AKSI TOMBOL MANAGEMENT (HANYA EDIT) --}}
                            <td class="px-4 py-3 sm:px-6 align-middle text-center">
                                {{-- Tombol Detail / Edit tunggal --}}
                                <button 
                                    type="button" 
                                    wire:click="editUser('{{ $user->id }}')" 
                                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all duration-200 border border-transparent hover:border-indigo-100 dark:hover:border-transparent inline-flex items-center gap-1.5 font-semibold text-xs"
                                    title="Tinjau Ulang / Edit Data Teknisi"
                                >
                                    <i class="bi bi-pencil-square text-sm"></i>
                                    <span class="hidden sm:inline">Tinjau Data</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500 italic">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="bi bi-person-x text-3xl opacity-40"></i>
                                    <p class="text-xs sm:text-sm font-medium">Tidak ada data teknisi ditolak yang cocok dengan pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION LINK --}}
    @if($data_users->hasPages())
        <div class="pt-2">
            {{ $data_users->links() }}
        </div>
    @endif

    {{-- ── TEMPLATE LIGHTBOX FOTO PROFIL GLOBAL ── --}}
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
                <p x-text="lightboxName" class="text-white font-bold text-sm sm:text-base mb-4 tracking-wider uppercase bg-white/5 px-4 py-1.5 rounded-full border border-white/10"></p>
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
