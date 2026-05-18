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
            $this->form->tipe_layanan = $payload['tipe_layanan'];
            $this->form->active = filter_var($payload['active'], FILTER_VALIDATE_BOOLEAN);
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
                tipe_layanan: @js($jasa?->tipe_layanan ?? 'panggilan'),
                active: Boolean(@js($jasa?->active ?? true)),
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
                        tipe_layanan: this.tipe_layanan,
                        active: this.active,
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
        class="pt-8"
        x-data="postingJasa($wire)"
        x-init="init()"
    >
        <div class="px-4 sm:px-6 lg:px-8 pb-20 sm:pb-0">

            <form @submit.prevent="submitForm" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
                @csrf

                {{-- Section 1: Informasi Dasar --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-2">Informasi Jasa</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Jasa --}}
                        <div>
                            <x-input-label for="nama_jasa" :value="__('Nama Jasa')" />
                            <x-text-input 
                                id="nama_jasa" 
                                x-model="nama_jasa"
                                type="text" 
                                class="mt-2 block w-full" 
                                placeholder="Contoh: Service AC Split"
                                required 
                            />
                            <x-input-error :messages="$errors->get('form.nama_jasa')" class="mt-2" />
                        </div>

                        {{-- Harga Jasa --}}
                        <div>
                            <x-input-label for="harga_jasa" :value="__('Harga Jasa (Mulai Dari)')" />
                            <div class="relative mt-2">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm font-medium">Rp</span>
                                </div>
                                <x-text-input 
                                    type="text" 
                                    x-model="hargaUtama"
                                    @input="hargaUtama = formatRupiah($event.target.value)"
                                    class="pl-10 block w-full" 
                                    placeholder="0" 
                                />
                            </div>
                            <x-input-error :messages="$errors->get('form.harga_jasa')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Tipe Layanan (Tambahkan blok ini) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                        <div class="col-span-1 md:col-span-2 mt-2">
                            <x-input-label :value="__('Tipe Layanan')" />
                            <div class="flex flex-wrap gap-4 mt-2">
                                <label 
                                    class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-all duration-200" 
                                    :class="tipe_layanan === 'panggilan' ? 'border-indigo-500 bg-indigo-50 shadow-sm' : 'border-gray-200 hover:bg-gray-50'"
                                >
                                    <input type="radio" x-model="tipe_layanan" value="panggilan" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">Panggilan ke Rumah</span>
                                        <span class="text-xs text-gray-500">Teknisi datang ke lokasi pelanggan</span>
                                    </div>
                                </label>

                                <label 
                                    class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-all duration-200" 
                                    :class="tipe_layanan === 'bengkel' ? 'border-indigo-500 bg-indigo-50 shadow-sm' : 'border-gray-200 hover:bg-gray-50'"
                                >
                                    <input type="radio" x-model="tipe_layanan" value="bengkel" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">Bawa ke Bengkel</span>
                                        <span class="text-xs text-gray-500">Pelanggan datang ke tempat teknisi</span>
                                    </div>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('form.tipe_layanan')" class="mt-2" />
                        </div>

                        {{-- Status Jasa --}}
                        <div>
                            <x-input-label :value="__('Status Jasa')" />
                            <div class="flex flex-col sm:flex-row gap-4 mt-2">
                                <label 
                                    class="flex-1 flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-all duration-200" 
                                    :class="active === true ? 'border-green-500 bg-green-50 shadow-sm' : 'border-gray-200 hover:bg-gray-50'"
                                >
                                    <input type="radio" x-model="active" :value="true" class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">Aktif</span>
                                    </div>
                                </label>

                                <label 
                                    class="flex-1 flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-all duration-200" 
                                    :class="active === false ? 'border-red-500 bg-red-50 shadow-sm' : 'border-gray-200 hover:bg-gray-50'"
                                >
                                    <input type="radio" x-model="active" :value="false" class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">Tidak Aktif</span>
                                    </div>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('form.active')" class="mt-2" />
                        </div>

                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <x-input-label for="deskripsi_jasa" :value="__('Deskripsi Jasa')" />
                        <textarea 
                            id="deskripsi_jasa"
                            rows="4" 
                            x-model="deskripsi_jasa"
                            class="mt-2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" 
                            placeholder="Jelaskan detail layanan Anda..."
                        ></textarea>
                        <x-input-error :messages="$errors->get('form.deskripsi_jasa')" class="mt-2" />
                    </div>
                </div>

                {{-- Section 2: Foto Thumbnail --}}
                <div class="space-y-2 pt-4" x-data="{ 
                    showModal: false, 
                    modalImage: '',
                    openModal(url) {
                        this.modalImage = url;
                        this.showModal = true;
                    }
                }">
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-2">Foto Thumbnail</h3>
                    <p class="text-sm text-gray-500">Unggah foto untuk menampilkan jasa Anda (Maksimal 5 foto)</p>

                    {{-- Upload Area --}}
                    <div 
                        class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 flex flex-col items-center justify-center cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition-all"
                        :class="(oldPaths.length + $wire.form.images.length) >= maxImages ? 'opacity-50 cursor-not-allowed' : ''"
                        @click="if((oldPaths.length + $wire.form.images.length) < maxImages) $refs.fileInput.click()"
                    >
                        {{-- Loading Indicator --}}
                        <div 
                            wire:loading.flex 
                            wire:target="form.new_images"
                            class="absolute inset-0 bg-white/90 backdrop-blur-sm z-10 items-center justify-center rounded-xl"
                        >
                            <div class="flex flex-col items-center">
                                <i class="bi bi-arrow-repeat animate-spin text-3xl text-indigo-600"></i>
                                <span class="text-sm text-indigo-600 mt-2 font-medium">Mengunggah...</span>
                            </div>
                        </div>
                        
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-3">
                            <i class="bi bi-cloud-arrow-up text-2xl text-indigo-600"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-700">Klik untuk upload foto jasa</p>
                        <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG (Max 2MB)</p>
                        
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
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        
                        {{-- Old Images --}}
                        @foreach($form->old_image_paths as $index => $path)
                            <div class="relative aspect-square group" wire:key="old-img-{{ $index }}">
                                <div 
                                    @click="openModal('{{ Storage::url($path) }}')"
                                    class="w-full h-full rounded-xl overflow-hidden border border-gray-200 cursor-pointer hover:shadow-md transition-all"
                                >
                                    <img src="{{ Storage::url($path) }}" class="w-full h-full object-cover" alt="Thumbnail {{ $index + 1 }}">
                                    <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs text-center py-1.5 font-medium">Lama</div>
                                </div>
                                <button 
                                    type="button" 
                                    wire:click="removeOldImage({{ $index }})"
                                    wire:loading.attr="disabled"
                                    class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-lg transition-colors"
                                >
                                    <i class="bi bi-x text-sm"></i>
                                </button>
                            </div>
                        @endforeach

                        {{-- New Images --}}
                        @foreach($form->images as $index => $file)
                            <div class="relative aspect-square group" wire:key="new-img-{{ $index }}">
                                @if ($file && method_exists($file, 'temporaryUrl'))
                                    <div 
                                        @click="openModal('{{ $file->temporaryUrl() }}')"
                                        class="w-full h-full rounded-xl overflow-hidden border-2 border-indigo-200 cursor-pointer hover:shadow-md transition-all"
                                    >
                                        <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover" alt="New {{ $index + 1 }}">
                                        <div class="absolute bottom-0 left-0 right-0 bg-indigo-600 text-white text-xs text-center py-1.5 font-medium">Baru</div>
                                    </div>
                                @else
                                    <div class="w-full h-full bg-gray-100 rounded-xl flex items-center justify-center border border-gray-200">
                                        <i class="bi bi-hourglass-split text-gray-400 animate-pulse text-xl"></i>
                                    </div>
                                @endif
                                <button 
                                    type="button" 
                                    wire:click="removeNewImage({{ $index }})"
                                    wire:loading.attr="disabled"
                                    class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-lg transition-colors"
                                >
                                    <i class="bi bi-x text-sm"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    {{-- Counter --}}
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">
                            Total foto: <span x-text="oldPaths.length + $wire.form.images.length" class="text-indigo-600 font-bold"></span> / 5
                        </p>
                        <div x-show="oldPaths.length + $wire.form.images.length >= maxImages" x-transition class="text-sm text-amber-600 font-medium flex items-center gap-1">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>Batas maksimal tercapai</span>
                        </div>
                    </div>

                    {{-- Lightbox Modal --}}
                    <template x-teleport="body">
                        <div 
                            x-show="showModal" 
                            class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/95 p-4"
                            style="display: none;"
                            @keydown.escape.window="showModal = false"
                            x-transition.opacity.duration.300ms
                        >
                            <button 
                                @click="showModal = false" 
                                class="absolute top-5 right-5 text-white/70 hover:text-white p-2 transition-colors"
                            >
                                <i class="bi bi-x-lg text-2xl"></i>
                            </button>
                            <div 
                                class="max-w-5xl w-full h-full flex items-center justify-center" 
                                @click.away="showModal = false"
                            >
                                <img 
                                    :src="modalImage" 
                                    class="max-w-full max-h-[85vh] rounded-xl object-contain shadow-2xl"
                                    x-show="showModal"
                                    x-transition.scale.duration.300ms
                                    alt="Preview"
                                >
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Section 3: Pilihan Keluhan --}}
                <div class="space-y-2 pt-4">
                    <div class="border-b border-gray-100 pb-2">
                        <h3 class="text-lg font-semibold text-gray-900">Pilihan Keluhan</h3>
                        <p class="text-sm text-gray-500 mt-1">Tambahkan keluhan umum yang sering dialami pelanggan</p>
                    </div>
                    
                    <div class="space-y-3">
                        <template x-for="(keluhan, index) in pilihanKeluhan" :key="index">
                            <div class="flex gap-3 items-center">
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="bi bi-patch-question"></i>
                                    </span>
                                    <x-text-input 
                                        type="text" 
                                        x-model="pilihanKeluhan[index]" 
                                        class="block w-full pl-10" 
                                        placeholder="Contoh: Mati Total / Tidak Dingin"
                                        required
                                    />
                                </div>
                                <button type="button" @click="removeKeluhan(index)" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-colors">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addKeluhan()" class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 px-4 py-2 rounded-lg transition-colors">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Tambah Pilihan Keluhan</span>
                    </button>
                    
                    <x-input-error :messages="$errors->get('form.keluhan')" class="mt-2" />
                </div>

                {{-- Section 4: Ketersediaan --}}
                <div class="space-y-2 pt-4">
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-2">Ketersediaan</h3>
                    
                    {{-- Tanggal --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <x-input-label :value="__('Tanggal Ketersediaan')" />
                            
                            <label class="inline-flex items-center gap-2 cursor-pointer bg-indigo-50 px-3 py-2 rounded-lg">
                                <input type="checkbox" x-model="isEveryday" @change="if(isEveryday) tanggalKetersediaan = []" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4">
                                <span class="text-sm font-medium text-indigo-700">{{ __('Tersedia Setiap Hari') }}</span>
                            </label>
                        </div>

                        <div x-show="!isEveryday" x-transition class="space-y-3">
                            <template x-for="(tgl, index) in tanggalKetersediaan" :key="index">
                                <div class="flex gap-3 items-center">
                                    <x-text-input 
                                        type="date" 
                                        x-model="tanggalKetersediaan[index]" 
                                        class="block w-full" 
                                        x-bind:required="!isEveryday"
                                    />
                                    <button type="button" @click="removeTanggal(index)" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-colors">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </template>

                            <button type="button" @click="addTanggal()" class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 px-4 py-2 rounded-lg transition-colors">
                                <i class="bi bi-plus-lg"></i>
                                <span>Tambah Tanggal</span>
                            </button>
                            
                            <p x-show="tanggalKetersediaan.length === 0" class="text-sm text-amber-600 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i>
                                <span>Pilih minimal satu tanggal atau centang 'Setiap Hari'</span>
                            </p>
                        </div>

                        <div x-show="isEveryday" class="p-4 bg-indigo-50 border border-indigo-100 rounded-xl text-indigo-700 flex items-center gap-2">
                            <i class="bi bi-info-circle-fill text-lg"></i>
                            <span class="text-sm font-medium">Jasa Anda akan tampil tersedia setiap hari</span>
                        </div>
                    </div>

                    {{-- Jam --}}
                    <div class="space-y-3 pt-4 border-t border-gray-100">
                        <x-input-label :value="__('Jam Ketersediaan')" />
                        <div class="space-y-3">
                            <template x-for="(jam, index) in jamKetersediaan" :key="index">
                                <div class="flex gap-3 items-center">
                                    <x-text-input type="time" x-model="jamKetersediaan[index]" class="block w-full" />
                                    <button type="button" @click="removeJam(index)" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-colors">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addJam()" class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 px-4 py-2 rounded-lg transition-colors">
                            <i class="bi bi-plus-lg"></i>
                            <span>Tambah Jam</span>
                        </button>
                    </div>
                </div>

                {{-- Section 5: Layanan Tambahan --}}
                <div class="space-y-2 pt-4">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-2 gap-2">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Layanan Tambahan</h3>
                            <p class="text-sm text-gray-500 mt-1">Tambahkan layanan atau sparepart tambahan</p>
                        </div>
                        <button type="button" @click="addLayanan()" class="bg-indigo-600 text-white px-2 py-1.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors flex items-center gap-2">
                            <i class="bi bi-plus-lg"></i>
                            <span>Tambah</span>
                        </button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(grup, gIndex) in layanan" :key="gIndex">
                            <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1 mr-4">
                                        <x-input-label :value="__('Kategori / Judul Layanan')" class="text-xs text-gray-500 mb-1" />
                                        <x-text-input x-model="grup.judul" type="text" class="w-full font-semibold" placeholder="Contoh: Cuci AC" />
                                    </div>
                                    <button type="button" @click="removeLayanan(gIndex)" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>

                                <div class="bg-white rounded-lg border border-gray-200 p-2 space-y-3">
                                    <template x-for="(item, iIndex) in grup.items" :key="iIndex">
                                        <div class="grid grid-cols-12 gap-2 items-center">
                                            <div class="col-span-5">
                                                <x-text-input x-model="item.nama" placeholder="Nama Sparepart/Jasa" class="w-full text-sm" />
                                            </div>
                                            <div class="col-span-5 relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-xs font-medium">Rp</span>
                                                <x-text-input 
                                                    type="text" 
                                                    x-model="item.harga"
                                                    @input="item.harga = formatRupiah($event.target.value)"
                                                    class="w-full text-sm pl-8" 
                                                    placeholder="Harga" 
                                                />
                                            </div>
                                            <div class="col-span-2 flex justify-center items-center">
                                                <button type="button" @click="removeItemLayanan(gIndex, iIndex)" class="text-red-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                    <button type="button" @click="addItemLayanan(gIndex)" class="text-sm text-indigo-600 font-medium hover:text-indigo-800 flex items-center gap-1 mt-2">
                                        <i class="bi bi-plus-lg"></i>
                                        <span>Tambah Item</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-6 border-t border-gray-100">
                    <x-primary-button 
                        class="w-full justify-center py-3.5 text-base font-semibold flex items-center gap-2" 
                        wire:loading.attr="disabled"
                        wire:target="save"
                    >
                        <span wire:loading.remove wire:target="save">
                            <i class="bi bi-check-lg"></i>
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