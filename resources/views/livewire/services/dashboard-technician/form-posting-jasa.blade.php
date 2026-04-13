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

    public function updatedFormNewImages()
    {
        $this->form->addImage();
    }

    public function removeNewImage($index)
    {
        $this->form->removeNewImage($index);
    }

    public function removeOldImage($path)
    {
        if ($this->jasa) {
            // 1. Update array di Form Object
            $this->form->old_image_paths = array_values(array_diff($this->form->old_image_paths, [$path]));

            // 2. Update database langsung (Opsional, agar database bersih seketika)
            $this->jasa->update([
                'thumbnails' => $this->form->old_image_paths
            ]);

            // 3. Hapus file fisik
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }

            // ⭐ PENTING: Kirim event ke Alpine.js agar variabel 'oldPaths' di JS ter-update
            $this->dispatch('old-images-updated', paths: $this->form->old_image_paths);
            
            // session()->flash('success', 'Gambar berhasil dihapus!');
        }
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
                        // Karena event Livewire 3 mengirim data dalam object/array
                        this.oldPaths = event.paths; 
                        console.log('Alpine oldPaths updated:', this.oldPaths);
                    });

                    $wire.on('new-images-updated', (event) => {
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

                {{-- Bagian Upload & Preview --}}
                <div x-data="{ 
                    showModal: false, 
                    modalImage: '',
                    openModal(url) {
                        this.modalImage = url;
                        this.showModal = true;
                    }
                }" class="space-y-4">
                    <x-input-label :value="__('Foto Thumbnail Jasa (Maksimal 5)')" />

                    {{-- Area Drop/Click Upload --}}
                    <div 
                        class="relative border-2 border-dashed border-gray-300 rounded-2xl p-6 bg-gray-50 flex flex-col items-center justify-center cursor-pointer"
                        :class="(oldPaths.length + $wire.form.images.length) >= 5 ? 'opacity-50 cursor-not-allowed' : ''"
                        @click="if((oldPaths.length + $wire.form.images.length) < 5) $refs.fileInput.click()"
                    >

                        <div 
                            wire:loading.flex 
                            wire:target="form.new_images"
                            class="absolute inset-0 bg-white/70 backdrop-blur-sm items-center justify-center rounded-2xl z-10"
                        >
                            <div class="flex flex-col items-center gap-2">
                                <i class="bi bi-arrow-repeat animate-spin text-2xl text-indigo-600"></i>
                                <span class="text-xs text-gray-600 font-medium">Uploading...</span>
                            </div>
                        </div>
                        
                        <i class="bi bi-cloud-arrow-up text-4xl text-gray-400"></i>
                        <p class="text-xs text-gray-600 mt-2 font-medium">Klik untuk upload foto jasa</p>
                        
                        <input 
                            type="file" 
                            wire:model="form.new_images"
                            x-ref="fileInput" 
                            class="hidden" 
                            accept=".jpg,.jpeg,.png" 
                            multiple 
                        >
                    </div>

                    {{-- Grid Preview --}}
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                        
                        {{-- Preview Gambar Lama (Database) --}}
                        @foreach($form->old_image_paths as $index => $path)
                            <div class="relative aspect-square" wire:key="old-img-{{ $index }}">
                                <div @click="openModal('{{ Storage::url($path) }}')" class="w-full h-full rounded-xl overflow-hidden border border-gray-200 cursor-pointer shadow-sm">
                                    <img src="{{ Storage::url($path) }}" class="w-full h-full object-cover">
                                    <div class="absolute bottom-0 w-full bg-black/50 text-white text-[8px] text-center py-1">Lama</div>
                                </div>
                                <button type="button" wire:click="removeOldImage('{{ $path }}')" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        @endforeach

                        {{-- Preview Gambar Baru (Temporary) --}}
                        @foreach($form->images as $index => $file)
                            <div class="relative aspect-square" wire:key="new-img-{{ $index }}">
                                @if ($file && method_exists($file, 'temporaryUrl'))
                                    <div @click="openModal('{{ $file->temporaryUrl() }}')" class="w-full h-full rounded-xl overflow-hidden border border-indigo-200 cursor-pointer shadow-sm">
                                        <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover">
                                        <div class="absolute bottom-0 w-full bg-indigo-600/50 text-white text-[8px] text-center py-1">Baru</div>
                                    </div>
                                @else
                                    <div class="w-full h-full bg-gray-200 animate-pulse rounded-xl flex items-center justify-center">
                                        <i class="bi bi-hourglass-split animate-spin text-gray-400"></i>
                                    </div>
                                @endif
                                <button type="button" wire:click="removeNewImage({{ $index }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    {{-- Lightbox Modal --}}
                    <template x-teleport="body">
                        <div x-show="showModal" class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/90 p-4" style="display: none;" @keydown.escape.window="showModal = false">
                            <button @click="showModal = false" class="absolute top-5 right-5 text-white/70 p-2"><i class="bi bi-x-lg text-3xl"></i></button>
                            <div class="max-w-5xl w-full h-full flex items-center justify-center" @click.away="showModal = false">
                                <img :src="modalImage" class="max-w-full max-h-full rounded-lg object-contain border border-white/10">
                            </div>
                        </div>
                    </template>
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