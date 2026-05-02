<div class="p-4 sm:p-6 bg-white rounded-xl shadow-md border border-slate-200">
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-indigo-100 rounded-lg">
                <i class="bi bi-tools text-indigo-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-800">Daftar Pekerjaan Aktif</h1>
                <p class="text-xs text-slate-500">Update progres pengerjaan untuk pelanggan Anda.</p>
            </div>
        </div>
        {{-- <div class="text-right">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold uppercase tracking-wider">
                {{ $data['total'] }} Total Pesanan
            </span>
        </div> --}}
    </div>

    {{-- List Pesanan --}}
    <div class="space-y-4">
        @forelse($data as $order)
            <div x-data="{ openUpdate: false }" class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/30 hover:shadow-sm transition-shadow">
                
                {{-- Header Card --}}
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border-b border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                            <i class="bi bi-box-seam text-2xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest">ORDER #{{ $order['id'] }}</span>
                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                <span class="text-xs font-semibold text-slate-500">{{ \Carbon\Carbon::parse($order['order_date'])->format('d M Y') }}</span>
                            </div>
                            <h2 class="text-sm font-bold text-slate-800">Layanan Servis ID Jasa: {{ $order['id_jasa'] }}</h2>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-end sm:self-center">
                        <button @click="openUpdate = !openUpdate" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition shadow-md shadow-indigo-100">
                            <i class="bi bi-pencil-square"></i>
                            <span>Update Progres</span>
                        </button>
                    </div>
                </div>

                {{-- Detail singkat (Selalu Muncul) --}}
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Keluhan --}}
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Daftar Keluhan:</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($order['keluhan'] as $keluhan)
                                <span class="px-2 py-1 bg-white border border-slate-200 rounded-md text-[10px] text-slate-600">
                                    {{ $keluhan }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    {{-- Harga --}}
                    <div class="flex flex-col md:items-end justify-center">
                        <p class="text-[10px] text-slate-400 uppercase">Estimasi Biaya:</p>
                        <p class="text-lg font-black text-slate-800">Rp {{ number_format($order['total_harga'], 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Panel Update (Collapsible) --}}
                <div x-show="openUpdate" x-collapse class="p-4 bg-white border-t border-slate-100 space-y-5">
                    <hr class="border-slate-100">
                    
                    <form wire:submit.prevent="updateProgres({{ $order['id'] }})" class="space-y-4">
                        {{-- Status Selector --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Status Pengerjaan:</label>
                                <select wire:model="status_update" class="w-full text-sm border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50">
                                    <option value="{{ $order->latestStatus->status_order ?? '-' }}">{{ $order->latestStatus->status_order ?? '-' }}</option>
                                    <option value="dikerjakan">Dikerjakan</option>
                                    <option value="menunggu_sparepart">Menunggu Sparepart</option>
                                    <option value="hampir_selesai">Hampir Selesai</option>
                                    <option value="selesai">Selesai (Siap Bayar)</option>
                                    <option value="dibatalkan">Batalakan</option>

                                </select>
                            </div>
                            
                            {{-- Bukti Upload --}}
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Upload Bukti (Foto):</label>
                                <div class="relative group">
                                    <input type="file" wire:model="bukti_pengerjaan" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                    <div class="flex items-center gap-3 px-4 py-2 border-2 border-dashed border-slate-200 rounded-lg group-hover:border-indigo-400 transition-colors bg-slate-50">
                                        <i class="bi bi-camera text-slate-400 group-hover:text-indigo-500"></i>
                                        <span class="text-xs text-slate-500 group-hover:text-indigo-600">Pilih atau Ambil Foto</span>
                                    </div>
                                </div>
                                <div wire:loading wire:target="bukti_pengerjaan" class="text-[10px] text-indigo-600 mt-1">
                                    <i class="bi bi-arrow-repeat animate-spin"></i> Mengunggah foto...
                                </div>
                            </div>
                        </div>

                        {{-- Catatan Teknisi --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Catatan Tambahan (Opsional):</label>
                            <textarea wire:model="catatan_progres" rows="2" class="w-full text-sm border-slate-200 rounded-lg focus:ring-indigo-500 bg-slate-50" placeholder="Contoh: Penggantian freon berhasil, sedang pengecekan pipa..."></textarea>
                        </div>

                        {{-- Button Simpan --}}
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="openUpdate = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition">
                                <i class="bi bi-check-circle mr-1"></i> Simpan Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-16 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                <i class="bi bi-clipboard-x text-5xl text-slate-300"></i>
                <p class="text-sm text-slate-400 mt-3 font-medium">Belum ada pesanan aktif yang perlu diupdate.</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        <div class="mt-6">
            {{-- Sesuaikan dengan cara Anda merender link pagination (misal: $data_pesan->links()) --}}
        </div>
    </div>
</div>