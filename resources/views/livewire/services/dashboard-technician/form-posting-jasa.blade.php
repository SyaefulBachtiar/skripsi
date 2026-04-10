<?php 

use App\Livewire\Forms\FormPostingJasa;
use Livewire\Volt\Component;
use App\Models\Jasa;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

new class extends Component
{
    use WithFileUploads;

    public FormPostingJasa $form;
    // public $temp_images = []; 
    public $new_images = [];
    public ?Jasa $jasa = null;

    public function mount($id_jasa = null)
    {
        if ($id_jasa) {
            // Query data Jasa berdasarkan ID
            $this->jasa = Jasa::find($id_jasa);

            if ($this->jasa) {
                $this->form->nama_jasa = $this->jasa->nama_jasa;
                $this->form->harga_jasa = $this->jasa->harga_jasa;
                $this->form->deskripsi_jasa = $this->jasa->deskripsi;
                $this->form->is_setiap_hari = $this->jasa->is_setiap_hari;
                $this->form->ketersediaan_tanggal = $this->jasa->ketersediaan_tanggal ?? [];
                $this->form->ketersediaan_jam = $this->jasa->ketersediaan_jam ?? [''];
                $this->form->keluhan = $this->jasa->keluhan ?? [];
                $this->form->layanan_tambahan = $this->jasa->layanan_tambahan ?? [];
                $this->form->old_image_paths = $this->jasa->thumbnails ?? [];
            }
        }
    }

    public function removeOldImage($path)
    {
        if ($this->jasa) {
        // 1. Ambil data thumbnails terbaru dari database
        $currentThumbnails = $this->jasa->thumbnails ?? [];

        // 2. Buat array baru tanpa path yang ingin dihapus
        $updatedThumbnails = array_values(array_diff($currentThumbnails, [$path]));

        // 3. Update database secara langsung
        $this->jasa->update([
            'thumbnails' => $updatedThumbnails
        ]);

        // 4. Sinkronkan property Form agar tampilan UI tetap update
        $this->form->old_image_paths = $updatedThumbnails;

        // 5. (Opsional) Hapus file fisik dari storage agar tidak memenuhi server
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }

        // 6. Beritahu Alpine.js bahwa data sudah berubah
        // $this->dispatch('old-images-updated', paths: $updatedThumbnails);
        
        session()->flash('success', 'Image berhasil di hapus!');
        return redirect()->to(request()->header('Referer'));


        }
    }

    public function removeNewImage($index)
    {
        unset($this->new_images[$index]);
        $this->new_images = array_values($this->new_images);
        
        // ⭐ DISPATCH EVENT: Beritahu Alpine.js
        $this->dispatch('new-images-updated', count: count($this->new_images));
    }

    public function save($payload)
    {
        $this->form->nama_jasa = $payload['nama_jasa'];
        $this->form->harga_jasa = $payload['harga_jasa'];
        $this->form->deskripsi_jasa = $payload['deskripsi_jasa'];
        $this->form->is_setiap_hari = $payload['isEveryday'];
        $this->form->ketersediaan_tanggal = $payload['tanggalKetersediaan'];
        $this->form->ketersediaan_jam = $payload['jamKetersediaan'];
        $this->form->keluhan = $payload['pilihanKeluhan'];
        $this->form->layanan_tambahan = $payload['layanan'];
        $this->form->old_image_paths = $payload['oldImagePaths'] ?? [];
        $this->form->new_images      = $this->new_images ?? [];

        // $this->form->store($this->jasa?->id);

        // session()->flash('success', $this->jasa ? 'Jasa berhasil diperbarui!' : 'Jasa berhasil diposting!');

        // return $this->redirect(request()->header('Referer'), navigate: true);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () {
                $this->form->store($this->jasa?->id);
            });

            $this->new_images = [];
            
            session()->flash('success', 'Data tersimpan!');
            return redirect()->to(request()->header('Referer'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ini akan melempar error kembali ke blade $errors
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
            // logger($e->getMessage());
            return $this->redirect(request()->header('Referer'), navigate: true);
        }
        
    }
    
}

?>
<div>

    <script>
        function postingJasa($wire) {
            return {
                nama_jasa: @js($jasa?->nama_jasa ?? ''),
                deskripsi_jasa: @js($jasa?->deskripsi ?? ''),
                hargaUtama: @js($jasa ? number_format($jasa->harga_jasa, 0, ',', '.') : ''),
                jamKetersediaan: @js($jasa?->ketersediaan_jam ?? ['']),
                pilihanKeluhan: @js($jasa?->keluhan ?? ['']),
                isEveryday: @js($jasa?->is_setiap_hari ?? false),
                tanggalKetersediaan: @js($jasa?->ketersediaan_tanggal ?? []),
                layanan: @js($jasa?->layanan_tambahan ?? []),
                images: [],
                maxImages: 5,
                oldPaths: @js($jasa?->thumbnails ?? []),
                // Fungsi Format Rupiah Realtime
                formatRupiah(angka) {
                    let number_string = angka.replace(/[^,\d]/g, '').toString(),
                        split = number_string.split(','),
                        sisa = split[0].length % 3,
                        rupiah = split[0].substr(0, sisa),
                        ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                    if (ribuan) {
                        let separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                },
                unformatRupiah(rupiah) {
                    return rupiah.replace(/[^0-9]/g, '');
                },
                addKeluhan() {
                    if(this.pilihanKeluhan.length < 10) {
                        this.pilihanKeluhan.push('');
                    } else {
                        alert('Maksimal 10 pilihan keluhan.');
                    }
                },
                removeKeluhan(index) {
                    if(this.pilihanKeluhan.length > 1) {
                        this.pilihanKeluhan.splice(index, 1);
                    } else {
                        this.pilihanKeluhan[0] = ''; 
                    }
                },
                addTanggal() {
                    this.tanggalKetersediaan.push('');
                },
                removeTanggal(index) {
                    this.tanggalKetersediaan.splice(index, 1);
                },
                // Logika Jam
                addJam() { this.jamKetersediaan.push(''); },
                removeJam(index) { this.jamKetersediaan.splice(index, 1); },
                // Logika Layanan Dinamis
                addLayanan() {
                    this.layanan.push({ judul: '', items: [{ nama: '', harga: '' }] });
                },
                removeLayanan(index) { this.layanan.splice(index, 1); },
                addItemLayanan(gIndex) {
                    this.layanan[gIndex].items.push({ nama: '', harga: '' });
                },
                removeItemLayanan(gIndex, iIndex) {
                    this.layanan[gIndex].items.splice(iIndex, 1);
                },
                init() {
                    if (this.layanan.length === 0) {
                        this.layanan = [{ judul: '', items: [{ nama: '', harga: '' }] }];
                    }
                    $wire.on('old-images-updated', (event) => {
                        this.oldPaths = event.paths;
                    });
                    $wire.on('new-images-updated', (event) => {
                        // Force update UI
                        this.$nextTick(() => {
                            console.log('New images count:', event.count);
                        });
                    });
                },
                async submitForm() {
                    let payload = {
                        nama_jasa: this.nama_jasa,
                        harga_jasa: this.unformatRupiah(this.hargaUtama),
                        deskripsi_jasa: this.deskripsi_jasa,
                        isEveryday: this.isEveryday,
                        tanggalKetersediaan: JSON.parse(JSON.stringify(this.tanggalKetersediaan)),
                        jamKetersediaan: JSON.parse(JSON.stringify(this.jamKetersediaan)),
                        pilihanKeluhan: JSON.parse(JSON.stringify(this.pilihanKeluhan)),
                        layanan: JSON.parse(JSON.stringify(this.layanan)),
                        oldImagePaths: JSON.parse(JSON.stringify(this.oldPaths))
                    };
                    console.log('Payload:', payload);
                    try {
                        await $wire.save(payload);
                    } catch(e) {
                        console.error('Wire error:', e);
                    }
                }
            }
        }
    </script>

    <div 
        class="py-10" 
        x-data="postingJasa($wire)"
        x-init="init()"
    >
        <div class="px-4 sm:px-6 lg:px-8 pb-10 sm:pb-0">
            <form @submit.prevent="submitForm" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="nama_jasa" :value="__('Nama Jasa')" />
                        <x-text-input 
                            id="nama_jasa" 
                            name="nama_jasa" 
                            x-model="nama_jasa"
                            type="text" 
                            class="mt-1 block w-full" 
                            placeholder="Contoh: Service AC Split"
                            required />
                        <x-input-error :messages="$errors->get('form.nama_jasa')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="harga_jasa" :value="__('Harga Jasa (Mulai Dari)')" />
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <x-text-input 
                                type="text" 
                                x-model="hargaUtama"
                                @input="hargaUtama = formatRupiah($event.target.value)"
                                class="pl-10 block w-full" 
                                placeholder="0" 
                            />
                            <input type="hidden" name="harga_jasa" :value="unformatRupiah(hargaUtama)">
                            <x-input-error :messages="$errors->get('form.harga_jasa')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div>
                    <x-input-label for="deskripsi_jasa" :value="__('Deskripsi Jasa')" />
                    <textarea 
                        name="deskripsi_jasa" 
                        rows="3" 
                        x-model="deskripsi_jasa"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                        placeholder="Jelaskan detail layanan Anda..."
                    ></textarea>
                    <x-input-error :messages="$errors->get('form.deskripsi_jasa')" class="mt-2" />
                </div>

                <div class="space-y-4">
                    <x-input-label :value="__('Foto Thumbnail Jasa (Maksimal 5)')" />

                    <div 
                        class="relative border-2 border-dashed border-gray-300 rounded-2xl p-6 transition-all duration-300 hover:border-indigo-400 hover:bg-indigo-50/30 bg-gray-50 flex flex-col items-center justify-center cursor-pointer group"
                        :class="(oldPaths.length + $wire.new_images.length) >= maxImages ? 'opacity-50 cursor-not-allowed grayscale' : ''"
                        @click="if((oldPaths.length + $wire.new_images.length) < maxImages) $refs.fileInput.click()"
                    >
                        {{-- Loading Indikator saat Upload --}}
                        <div 
                            wire:loading 
                            wire:target="new_images" 
                            class="absolute inset-0 bg-white/90 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl transition-opacity duration-300"
                        >
                            <div class="flex flex-col items-center animate-pulse">
                                <div class="relative">
                                    <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <div class="absolute inset-0 animate-ping h-10 w-10 rounded-full bg-indigo-400 opacity-20"></div>
                                </div>
                                <span class="text-xs text-indigo-600 mt-3 font-semibold tracking-wide">Mengunggah...</span>
                                <span class="text-[10px] text-gray-500 mt-1">Mohon tunggu sebentar</span>
                            </div>
                        </div>

                        <div class="transition-transform duration-300 group-hover:scale-110 group-hover:-translate-y-1">
                            <i class="bi bi-cloud-arrow-up text-4xl text-gray-400 group-hover:text-indigo-500 transition-colors duration-300"></i>
                        </div>
                        <p class="text-xs text-gray-600 mt-3 font-medium group-hover:text-indigo-600 transition-colors">Klik untuk upload</p>
                        <p class="text-[10px] text-gray-400 mt-1">PNG, JPG up to 2MB</p>
                        
                        <input 
                            type="file" 
                            wire:model="new_images"
                            x-ref="fileInput" 
                            class="hidden" 
                            accept="image/*" 
                            multiple 
                        >
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                        @foreach(($jasa?->thumbnails ?? []) as $index => $thumb)
                            @if(in_array($thumb, $form->old_image_paths ?? []))
                                <div class="relative aspect-square rounded-xl overflow-hidden border border-gray-200 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1" wire:key="old-{{ $index }}">
                                    {{-- Loading Overlay Hapus Gambar Lama --}}
                                    <div 
                                        wire:loading 
                                        wire:target="removeOldImage('{{ $thumb }}')" 
                                        class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex flex-col items-center justify-center transition-opacity duration-200"
                                    >
                                        <svg class="animate-spin h-6 w-6 text-red-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-[10px] text-red-500 font-medium">Menghapus...</span>
                                    </div>

                                    <img src="{{ Storage::url($thumb) }}" class="w-full h-full object-cover duration-500">
                                    
                                    <button type="button" 
                                        wire:click="removeOldImage('{{ $thumb }}')"
                                        wire:loading.attr="disabled"
                                        class="absolute top-2 right-2 bg-red-500/90 backdrop-blur-sm text-white rounded-full p-1.5 z-10 shadow-lg"
                                    >
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                    
                                    <div class="absolute bottom-0 w-full bg-gradient-to-t from-black/70 to-transparent text-white text-[10px] text-center py-1.5 font-medium backdrop-blur-sm">
                                        Lama
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @foreach($new_images as $index => $image)
                            <div class="relative aspect-square rounded-xl overflow-hidden border border-gray-200 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1" wire:key="new-{{ $index }}">
                                {{-- Loading Overlay Hapus Gambar Baru --}}
                                <div 
                                    wire:loading 
                                    wire:target="removeNewImage({{ $index }})" 
                                    class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex flex-col items-center justify-center transition-opacity duration-200"
                                >
                                    <svg class="animate-spin h-6 w-6 text-red-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-[10px] text-red-500 font-medium">Menghapus...</span>
                                </div>

                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                
                                <button type="button" 
                                    wire:click="removeNewImage({{ $index }})"
                                    wire:loading.attr="disabled"
                                    class="absolute top-2 right-2 bg-red-500/90 backdrop-blur-sm text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all duration-200 hover:bg-red-600 hover:scale-110 z-20 shadow-lg"
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                                
                                <div class="absolute bottom-0 w-full bg-gradient-to-t from-black/70 to-transparent text-white text-[10px] text-center py-1.5 font-medium backdrop-blur-sm">
                                    Baru
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-semibold text-gray-500">
                            Total: <span x-text="oldPaths.length + $wire.new_images.length" class="text-indigo-600 font-bold"></span> / 5
                        </p>
                        <div x-show="oldPaths.length + $wire.new_images.length >= maxImages" x-transition class="text-[10px] text-amber-600 font-medium">
                            <i class="bi bi-exclamation-triangle-fill mr-1"></i>Batas maksimal tercapai
                        </div>
                    </div>
                    
                    <x-input-error :messages="Arr::flatten($errors->get('form.new_images.*'))" class="mt-1" />
                </div>

                <hr class="border-gray-100">

                <div class="space-y-3">
                    <x-input-label :value="__('Pilihan Keluhan Umum')" />
                    <p class="text-[10px] text-slate-500 -mt-2">Tambahkan keluhan yang sering dialami pelanggan agar mereka mudah memilih.</p>
                    
                    <div class="space-y-2 mt-2">
                        <template x-for="(keluhan, index) in pilihanKeluhan" :key="index">
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                        <i class="bi bi-patch-question"></i>
                                    </span>
                                    <x-text-input 
                                        type="text" 
                                        x-model="pilihanKeluhan[index]" 
                                        name="pilihan_keluhan[]" 
                                        class="block w-full text-sm pl-10" 
                                        placeholder="Contoh: Mati Total / Tidak Dingin"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('form.keluhan')" class="mt-2" />
                                </div>
                                
                                {{-- Tombol Hapus --}}
                                <button type="button" @click="removeKeluhan(index)" class="text-red-500 hover:text-red-700 p-2 transition-colors">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Tombol Tambah Keluhan --}}
                    <button type="button" @click="addKeluhan()" class="mt-2 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-all">
                        <i class="bi bi-plus-circle-fill mr-2"></i> Tambah Pilihan Keluhan
                    </button>
                </div>

                <hr class="border-gray-100">

                 
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <x-input-label :value="__('Tanggal Ketersediaan')" />
                        
                        {{-- Checkbox Setiap Hari --}}
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="isEveryday" @change="if(isEveryday) tanggalKetersediaan = []" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600 font-medium">{{ __('Tersedia Setiap Hari') }}</span>
                        </label>
                    </div>

                    {{-- Bagian Input Tanggal (Sembunyikan jika 'Setiap Hari' dicentang) --}}
                    <div x-show="!isEveryday" x-transition class="space-y-2">
                        <template x-for="(tgl, index) in tanggalKetersediaan" :key="index">
                            <div class="flex gap-2">
                                <x-text-input 
                                    type="date" 
                                    x-model="tanggalKetersediaan[index]" 
                                    name="tanggal_ketersediaan[]" 
                                    class="block w-full text-sm" 
                                    x-bind:required="!isEveryday"
                                />
                                <button type="button" @click="removeTanggal(index)" class="text-red-500 hover:text-red-700 p-2">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </template>

                        {{-- Tombol Tambah Tanggal --}}
                        <button type="button" @click="addTanggal()" class="mt-2 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            <i class="bi bi-plus-lg mr-1"></i> Tambah Tanggal Spesifik
                        </button>
                        
                        <p x-show="tanggalKetersediaan.length === 0" class="text-xs text-amber-600 italic">
                            *Pilih minimal satu tanggal atau centang 'Setiap Hari'.
                        </p>
                    </div>

                    {{-- Pesan jika tersedia setiap hari --}}
                    <div x-show="isEveryday" class="p-3 bg-blue-50 border border-blue-100 rounded-xl text-blue-700 text-xs">
                        <i class="bi bi-info-circle-fill mr-1"></i> Jasa Anda akan tampil tersedia setiap hari tanpa batasan tanggal.
                    </div>
                </div>

                <hr class="border-gray-100">

                <div>
                    <x-input-label :value="__('Jam Ketersediaan')" />
                    <div class="space-y-2 mt-2">
                        <template x-for="(jam, index) in jamKetersediaan" :key="index">
                            <div class="flex gap-2">
                                <x-text-input type="time" x-model="jamKetersediaan[index]" name="jam_ketersediaan[]" class="block w-full text-sm" placeholder="Contoh: 08:00 - 17:00" />
                                <button type="button" @click="removeJam(index)" class="text-red-500 hover:text-red-700 p-2">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addJam()" class="mt-2 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        <i class="bi bi-plus-lg mr-1"></i> Tambah Jam
                    </button>
                </div>

                <hr class="border-gray-100">

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <x-input-label :value="__('Daftar Layanan / Sparepart Tambahan')" />
                        <button type="button" @click="addLayanan()" class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg text-xs font-bold hover:bg-indigo-100">
                            + Tambah Grup Layanan
                        </button>
                    </div>

                    <template x-for="(grup, gIndex) in layanan" :key="gIndex">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 relative">
                            <button type="button" @click="removeLayanan(gIndex)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                            
                            <div class="mb-4">
                                <x-input-label :value="__('Kategori / Judul Layanan')" class="text-xs text-gray-500" />
                                <x-text-input x-model="grup.judul" name="grup_layanan[]" type="text" class="mt-1 block w-full text-sm font-bold" placeholder="Contoh: Cuci AC" />
                            </div>

                            <div class="space-y-3 bg-white p-3 rounded-lg border border-gray-100">
                                <template x-for="(item, iIndex) in grup.items" :key="iIndex">
                                    <div class="grid grid-cols-12 gap-2 items-center">
                                        <div class="col-span-6">
                                            <x-text-input x-model="item.nama" placeholder="Nama Sparepart/Jasa" class="w-full text-xs" />
                                        </div>
                                        <div class="col-span-5 relative">
                                            <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-gray-400 text-[10px]">Rp</span>
                                            <x-text-input 
                                                type="text" 
                                                x-model="item.harga"
                                                @input="item.harga = formatRupiah($event.target.value)"
                                                class="w-full text-xs pl-7" 
                                                placeholder="Harga" 
                                            />
                                        </div>
                                        <div class="col-span-1 text-right">
                                            <button type="button" @click="removeItemLayanan(gIndex, iIndex)" class="text-red-400 hover:text-red-600">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <button type="button" @click="addItemLayanan(gIndex)" class="text-[10px] text-indigo-600 font-bold hover:underline">
                                    + Tambah Item
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="pt-4">
                    <x-primary-button class="w-full justify-center py-3 flex items-center gap-2" wire:loading.attr="disabled">
                        {{-- Teks normal (sembunyi saat loading) --}}
                        <span wire:loading.remove wire:target="save">
                            {{ $jasa ? __('Simpan Perubahan') : __('Posting Jasa Sekarang') }}
                        </span>

                        {{-- Teks saat loading --}}
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('Memproses...') }}
                        </span>
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>