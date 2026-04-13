<?php

use App\Livewire\Forms\FormPostingJasa;
use App\Models\Jasa;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public FormPostingJasa $form;
    public ?Jasa $jasa = null;

    public function mount(?int $id_jasa = null): void
    {
        if ($id_jasa) {
            $this->jasa = Jasa::find($id_jasa);
            
            if ($this->jasa) {
                $this->form->setValues($this->jasa);
            }
        }
    }

    public function updatedFormNewImages(): void
    {
        $this->form->addImage();
    }

    public function removeNewImage(int $index): void
    {
        $this->form->removeNewImage($index);
    }

    public function removeOldImage(int $index): void
    {
        $this->form->removeOldImage($index);
        $this->dispatch('old-images-updated', paths: $this->form->old_image_paths);
    }

    public function save(array $payload): void
    {
        try {
            $this->form->nama_jasa = $payload['nama_jasa'];
            $this->form->harga_jasa = $payload['harga_jasa'];
            $this->form->deskripsi_jasa = $payload['deskripsi_jasa'];
            $this->form->is_setiap_hari = $payload['isEveryday'];
            $this->form->ketersediaan_tanggal = $payload['tanggalKetersediaan'] ?? [];
            $this->form->ketersediaan_jam = $payload['jamKetersediaan'] ?? [];
            $this->form->keluhan = $payload['pilihanKeluhan'] ?? [];
            $this->form->layanan_tambahan = $payload['layanan'] ?? [];
            $this->form->old_image_paths = $payload['oldImagePaths'] ?? [];

            $this->form->store($this->jasa?->id);

            session()->flash('success', $this->jasa ? 'Jasa berhasil diperbarui!' : 'Jasa berhasil diposting!');
            $this->redirect(request()->header('Referer') ?? route('dashboard'));
            
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException $e) {
            session()->flash('error', 'Terjadi kesalahan database. Silakan coba lagi.');
            $this->dispatch('notify', type: 'error', message: 'Database error occurred');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }
};

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
                maxImages: 5,
                oldPaths: @js($jasa?->thumbnails ?? []),
                
                formatRupiah(angka) {
                    let number_string = angka.replace(/[^,\d]/g, '').toString();
                    let split = number_string.split(',');
                    let sisa = split[0].length % 3;
                    let rupiah = split[0].substr(0, sisa);
                    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                    
                    if (ribuan) {
                        let separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    
                    return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                },
                
                unformatRupiah(rupiah) {
                    return rupiah.replace(/[^0-9]/g, '');
                },
                
                addKeluhan() {
                    if (this.pilihanKeluhan.length < 10) {
                        this.pilihanKeluhan.push('');
                    } else {
                        alert('Maksimal 10 pilihan keluhan.');
                    }
                },
                
                removeKeluhan(index) {
                    if (this.pilihanKeluhan.length > 1) {
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
                
                addJam() {
                    this.jamKetersediaan.push('');
                },
                
                removeJam(index) {
                    this.jamKetersediaan.splice(index, 1);
                },
                
                addLayanan() {
                    this.layanan.push({ judul: '', items: [{ nama: '', harga: '' }] });
                },
                
                removeLayanan(index) {
                    this.layanan.splice(index, 1);
                },
                
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
                },
                
                async submitForm() {
                    const payload = {
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
                    
                    try {
                        await $wire.save(payload);
                    } catch (e) {
                        console.error('Error:', e);
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
            
            {{-- Flash Messages --}}
            @if (session()->has('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <form @submit.prevent="submitForm" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama Jasa --}}
                    <div>
                        <x-input-label for="nama_jasa" :value="__('Nama Jasa')" />
                        <x-text-input 
                            id="nama_jasa" 
                            x-model="nama_jasa"
                            type="text" 
                            class="mt-1 block w-full" 
                            placeholder="Contoh: Service AC Split"
                            required 
                        />
                        <x-input-error :messages="$errors->get('form.nama_jasa')" class="mt-2" />
                    </div>

                    {{-- Harga Jasa --}}
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
                            <x-input-error :messages="$errors->get('form.harga_jasa')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <x-input-label for="deskripsi_jasa" :value="__('Deskripsi Jasa')" />
                    <textarea 
                        id="deskripsi_jasa"
                        rows="3" 
                        x-model="deskripsi_jasa"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                        placeholder="Jelaskan detail layanan Anda..."
                    ></textarea>
                    <x-input-error :messages="$errors->get('form.deskripsi_jasa')" class="mt-2" />
                </div>

                <hr class="border-gray-100">

                {{-- Foto Thumbnail dengan Lightbox --}}
                <div x-data="{ 
                    showModal: false, 
                    modalImage: '',
                    openModal(url) {
                        this.modalImage = url;
                        this.showModal = true;
                    }
                }" class="space-y-4">
                    <x-input-label :value="__('Foto Thumbnail Jasa (Maksimal 5)')" />

                    {{-- Upload Area --}}
                    <div 
                        class="relative border-2 border-dashed border-gray-300 rounded-2xl p-6 bg-gray-50 flex flex-col items-center justify-center cursor-pointer hover:border-indigo-400 transition-all"
                        :class="(oldPaths.length + $wire.form.images.length) >= maxImages ? 'opacity-50 cursor-not-allowed' : ''"
                        @click="if((oldPaths.length + $wire.form.images.length) < maxImages) $refs.fileInput.click()"
                    >
                        {{-- Loading Indicator --}}
                        <div 
                            wire:loading 
                            wire:target="form.new_images"
                            class="absolute inset-0 bg-white/90 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl"
                        >
                            <div class="flex flex-col items-center animate-pulse">
                                <i class="bi bi-arrow-repeat animate-spin text-3xl text-indigo-600"></i>
                                <span class="text-xs text-indigo-600 mt-2 font-semibold">Mengunggah...</span>
                            </div>
                        </div>
                        
                        <i class="bi bi-cloud-arrow-up text-4xl text-gray-400 group-hover:text-indigo-500 transition-colors"></i>
                        <p class="text-xs text-gray-600 mt-2 font-medium">Klik untuk upload foto jasa</p>
                        <p class="text-[10px] text-gray-400 mt-1">JPG, JPEG, PNG up to 2MB</p>
                        
                        <input 
                            type="file" 
                            wire:model="form.new_images"
                            x-ref="fileInput" 
                            class="hidden" 
                            accept="image/jpeg,image/png,image/jpg" 
                            multiple 
                        >
                    </div>

                    {{-- Error Messages --}}
                    <x-input-error :messages="$errors->get('form.new_images')" class="mt-2" />
                    <x-input-error :messages="$errors->get('form.new_images.*')" class="mt-2" />

                    {{-- Preview Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                        
                        {{-- Old Images --}}
                        @foreach($form->old_image_paths as $index => $path)
                            <div class="relative aspect-square group" wire:key="old-img-{{ $index }}">
                                <div 
                                    @click="openModal('{{ Storage::url($path) }}')"
                                    class="w-full h-full rounded-xl overflow-hidden border border-gray-200 cursor-pointer shadow-sm hover:shadow-md transition-all"
                                >
                                    <img src="{{ Storage::url($path) }}" class="w-full h-full object-cover" alt="Thumbnail {{ $index + 1 }}">
                                    <div class="absolute bottom-0 w-full bg-black/50 text-white text-[10px] text-center py-1">Lama</div>
                                </div>
                                <button 
                                    type="button" 
                                    wire:click="removeOldImage({{ $index }})"
                                    wire:loading.attr="disabled"
                                    class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md z-10 transition-colors"
                                >
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        @endforeach

                        {{-- New Images --}}
                        @foreach($form->images as $index => $file)
                            <div class="relative aspect-square group" wire:key="new-img-{{ $index }}">
                                @if ($file && method_exists($file, 'temporaryUrl'))
                                    <div 
                                        @click="openModal('{{ $file->temporaryUrl() }}')"
                                        class="w-full h-full rounded-xl overflow-hidden border border-indigo-200 cursor-pointer shadow-sm hover:shadow-md transition-all"
                                    >
                                        <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover" alt="New {{ $index + 1 }}">
                                        <div class="absolute bottom-0 w-full bg-indigo-600/50 text-white text-[10px] text-center py-1">Baru</div>
                                    </div>
                                @else
                                    <div class="w-full h-full bg-gray-100 rounded-xl flex items-center justify-center border border-gray-200">
                                        <i class="bi bi-hourglass-split text-gray-400 animate-pulse"></i>
                                    </div>
                                @endif
                                <button 
                                    type="button" 
                                    wire:click="removeNewImage({{ $index }})"
                                    wire:loading.attr="disabled"
                                    class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md z-10 transition-colors"
                                >
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    {{-- Counter --}}
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-semibold text-gray-500">
                            Total: <span x-text="oldPaths.length + $wire.form.images.length" class="text-indigo-600 font-bold"></span> / 5
                        </p>
                        <div x-show="oldPaths.length + $wire.form.images.length >= maxImages" x-transition class="text-[10px] text-amber-600 font-medium">
                            <i class="bi bi-exclamation-triangle-fill mr-1"></i>Batas maksimal tercapai
                        </div>
                    </div>

                    {{-- Lightbox Modal --}}
                    <template x-teleport="body">
                        <div 
                            x-show="showModal" 
                            class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/90 p-4 sm:p-10"
                            style="display: none;"
                            @keydown.escape.window="showModal = false"
                            x-transition.opacity
                        >
                            <button 
                                @click="showModal = false" 
                                class="absolute top-5 right-5 text-white/70 hover:text-white p-2 transition-colors"
                            >
                                <i class="bi bi-x-lg text-3xl"></i>
                            </button>
                            <div 
                                class="max-w-5xl w-full h-full flex items-center justify-center" 
                                @click.away="showModal = false"
                            >
                                <img 
                                    :src="modalImage" 
                                    class="max-w-full max-h-full rounded-lg object-contain border border-white/10 shadow-2xl"
                                    x-show="showModal"
                                    x-transition
                                    alt="Preview"
                                >
                            </div>
                        </div>
                    </template>
                </div>

                <hr class="border-gray-100">

                {{-- Pilihan Keluhan --}}
                <div class="space-y-3">
                    <x-input-label :value="__('Pilihan Keluhan Umum')" />
                    <p class="text-[10px] text-slate-500">Tambahkan keluhan yang sering dialami pelanggan.</p>
                    
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
                                        class="block w-full text-sm pl-10" 
                                        placeholder="Contoh: Mati Total / Tidak Dingin"
                                        required
                                    />
                                </div>
                                <button type="button" @click="removeKeluhan(index)" class="text-red-500 hover:text-red-700 p-2">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addKeluhan()" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        <i class="bi bi-plus-circle-fill mr-2"></i> Tambah Pilihan Keluhan
                    </button>
                    
                    <x-input-error :messages="$errors->get('form.keluhan')" class="mt-2" />
                </div>

                <hr class="border-gray-100">

                {{-- Tanggal Ketersediaan --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <x-input-label :value="__('Tanggal Ketersediaan')" />
                        
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="isEveryday" @change="if(isEveryday) tanggalKetersediaan = []" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600 font-medium">{{ __('Tersedia Setiap Hari') }}</span>
                        </label>
                    </div>

                    <div x-show="!isEveryday" x-transition class="space-y-2">
                        <template x-for="(tgl, index) in tanggalKetersediaan" :key="index">
                            <div class="flex gap-2">
                                <x-text-input 
                                    type="date" 
                                    x-model="tanggalKetersediaan[index]" 
                                    class="block w-full text-sm" 
                                    x-bind:required="!isEveryday"
                                />
                                <button type="button" @click="removeTanggal(index)" class="text-red-500 hover:text-red-700 p-2">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </template>

                        <button type="button" @click="addTanggal()" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            <i class="bi bi-plus-lg mr-1"></i> Tambah Tanggal
                        </button>
                        
                        <p x-show="tanggalKetersediaan.length === 0" class="text-xs text-amber-600 italic">
                            *Pilih minimal satu tanggal atau centang 'Setiap Hari'.
                        </p>
                    </div>

                    <div x-show="isEveryday" class="p-3 bg-blue-50 border border-blue-100 rounded-xl text-blue-700 text-xs">
                        <i class="bi bi-info-circle-fill mr-1"></i> Jasa Anda akan tampil tersedia setiap hari.
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Jam Ketersediaan --}}
                <div>
                    <x-input-label :value="__('Jam Ketersediaan')" />
                    <div class="space-y-2 mt-2">
                        <template x-for="(jam, index) in jamKetersediaan" :key="index">
                            <div class="flex gap-2">
                                <x-text-input type="time" x-model="jamKetersediaan[index]" class="block w-full text-sm" />
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

                {{-- Layanan Tambahan --}}
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <x-input-label :value="__('Daftar Layanan / Sparepart Tambahan')" />
                        <button type="button" @click="addLayanan()" class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg text-xs font-bold hover:bg-indigo-100">
                            + Tambah Grup
                        </button>
                    </div>

                    <template x-for="(grup, gIndex) in layanan" :key="gIndex">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 relative">
                            <button type="button" @click="removeLayanan(gIndex)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                            
                            <div class="mb-4">
                                <x-input-label :value="__('Kategori / Judul Layanan')" class="text-xs text-gray-500" />
                                <x-text-input x-model="grup.judul" type="text" class="mt-1 block w-full text-sm font-bold" placeholder="Contoh: Cuci AC" />
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

                {{-- Submit Button --}}
                <div class="pt-4">
                    <x-primary-button 
                        class="w-full justify-center py-3 flex items-center gap-2" 
                        wire:loading.attr="disabled"
                        wire:target="save"
                    >
                        <span wire:loading.remove wire:target="save">
                            {{ $jasa ? __('Simpan Perubahan') : __('Posting Jasa Sekarang') }}
                        </span>

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