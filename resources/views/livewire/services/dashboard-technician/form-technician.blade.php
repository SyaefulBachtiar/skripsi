<?php

use App\Livewire\Forms\FormTechnician;
use App\Models\Role_users\Technician;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    // Inisialisasi Form Object
    public FormTechnician $form;

    public function mount()
    {
        // Ambil data teknisi berdasarkan user login
        $technician = Technician::where('user_id', auth()->id())->first();
        
        // Isi form dengan data tersebut
        if ($technician) {
            $this->form->setValues($technician);
        }
    }

    public function save()
    {
        try {
            // dd($this->form->all());
            \Illuminate\Support\Facades\DB::transaction(function () {
                $this->form->store();
            });
            session()->flash('success', 'Berhasil disimpan!');
            return redirect()->to(request()->header('Referer'));
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->to(request()->header('Referer'));
        }
    }

    public function removeNewCertificate($index)
    {
        // Cek apakah index valid
        if (!isset($this->form->certificates[$index])) {
            return;
        }

        $file = $this->form->certificates[$index];

        // Hapus file temporary dari storage livewire-tmp
        try {
            if (is_object($file) && method_exists($file, 'getRealPath')) {
                $realPath = $file->getRealPath();
                if (file_exists($realPath)) {
                    unlink($realPath);
                }
            }
        } catch (\Exception $e) {
            // Log error tapi jangan stop execution
            logger('Error deleting temp file: ' . $e->getMessage());
        }

        // Hapus dari array Livewire
        unset($this->form->certificates[$index]);
        
        // Re-index array (penting untuk menjaga konsistensi index)
        $this->form->certificates = array_values($this->form->certificates);

        // Dispatch event ke Alpine bahwa sudah terhapus
        $this->dispatch('certificate-removed', remaining: count($this->form->certificates));
    }

    public function deleteOldCertificate($index)
    {
        // 1. Ambil array sertifikat lama dari form object
        $certs = $this->form->existing_certificates;

        if (isset($certs[$index])) {
            $pathFile = $certs[$index];

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($pathFile)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pathFile);
            }

            unset($certs[$index]);
            $newCerts = array_values($certs);

            \App\Models\Role_users\Technician::where('user_id', auth()->id())
                ->update([
                    'sertifikat' => $newCerts
                ]);

            $this->form->existing_certificates = $newCerts;

            session()->flash('success', 'Foto sertifikat berhasil dihapus permanen.');
            return redirect()->to(request()->header('Referer'));
        }
    }
};

?>

<div class="bg-white dark:bg-slate-800 p-4 py-5 sm:p-6 rounded-xl shadow-md border border-slate-200 dark:border-slate-700">
    {{-- Header --}}
    <div class="mb-6 flex items-center space-x-3">
        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
            <i class="bi bi-person-badge-fill text-blue-600 dark:text-blue-400 text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Lengkapi Profil Teknisi</h1>
            <p class="text-sm text-slate-500">Informasi ini membantu pelanggan mengenal keahlian Anda.</p>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">

        {{-- Deskripsi --}}
        <div>
            <x-input-label for="deskripsi" :value="__('Deskripsi Singkat')" class="mb-2" />
            <textarea 
                wire:model.live="form.bio"
                rows="4"
                class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                placeholder="Ceritakan singkat tentang keahlian Anda..."
            ></textarea>
            <x-input-error :messages="$errors->get('form.bio')" class="mt-2" />
        </div>

        <hr class="border-slate-200 dark:border-slate-700">
        
        {{-- Spesialisasi (Multi-select) --}}
        <div x-data="{ 
            selected: @entangle('form.spesialisasi') || [],
            options: ['AC', 'Kulkas', 'Kelistrikan', 'Mesin Cuci', 'Water Heater', 'Pompa Air'],
            showManual: false,
            manualInput: '',
            
            add(option) {
                if(option === 'custom') {
                    this.showManual = true;
                    return;
                }
                if(option && !this.selected.includes(option) && this.selected.length < 5) {
                    this.selected.push(option);
                }
            },
            
            addManual() {
                let val = this.manualInput.trim();
                if(val && !this.selected.includes(val) && this.selected.length < 5) {
                    this.selected.push(val);
                    this.manualInput = '';
                    this.showManual = false;
                }
            },

            remove(option) {
                this.selected = this.selected.filter(i => i !== option);
            }
        }">
            <x-input-label :value="__('Kategori Spesialisasi')" class="mb-2" />
            
            {{-- Indikator Kuota --}}
            <div class="flex justify-between items-center mb-2">
                <p class="text-xs text-slate-500 font-medium" :class="selected.length >= 5 ? 'text-amber-600' : ''">
                    Terpilih <span x-text="selected.length"></span> dari 5
                </p>
            </div>

            {{-- List Badge --}}
            <div class="flex flex-wrap gap-2 mb-3">
                <template x-for="item in selected" :key="item">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        <span x-text="item"></span>
                        <button type="button" @click="remove(item)" class="ml-1.5 inline-flex text-indigo-400 hover:text-indigo-600 transition-colors">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </span>
                </template>
            </div>

            <div class="space-y-3">
                {{-- Select Dropdown --}}
                <select 
                    @change="add($event.target.value); $event.target.value = ''" 
                    :disabled="selected.length >= 5"
                    :class="selected.length >= 5 ? 'bg-slate-50 cursor-not-allowed opacity-60' : ''"
                    class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm transition-all"
                >
                    <option value="" x-text="selected.length >= 5 ? 'Maksimal 5' : '-- Pilih Spesialisasi --'"></option>
                    
                    <template x-for="opt in options" :key="opt">
                        <option :value="opt" x-text="opt" :disabled="selected.includes(opt)"></option>
                    </template>

                    <option value="custom" class="text-indigo-600 font-bold">Lainnya</option>
                </select>

                {{-- Input Manual Field (Muncul jika pilih custom) --}}
                <div x-show="showManual" 
                    x-transition 
                    class="flex gap-2 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-700 w-full">
                    <x-text-input 
                        x-model="manualInput" 
                        @keydown.enter.prevent="addManual()"
                        placeholder="Ketik keahlian lainnya..." 
                        class="text-sm w-full" 
                    />
                    <button type="button" @click="addManual()" class="px-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                        Tambah
                    </button>
                    <button type="button" @click="showManual = false" class="px-2 text-slate-500 text-sm">
                        Batal
                    </button>
                </div>
            </div>

            <x-input-error :messages="$errors->get('form.spesialisasi')" class="mt-2" />
        </div>

        <hr class="border-slate-200 dark:border-slate-700">

        {{-- Pengalaman (Dynamic Input) --}}
        <div x-data="{ items: @entangle('form.experience_list') || [''] }">
            <x-input-label :value="__('Riwayat Pengalaman Kerja (Optional)')" class="mb-2" />
            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex gap-2">
                        <x-text-input type="text" x-model="items[index]" placeholder="PT. Maju Jaya (2019-2021)" class="flex-1" />
                        <button type="button" @click="items.length > 1 ? items.splice(index, 1) : items[0] = ''" class="text-red-500 hover:text-red-700 p-2">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                </template>
                <button type="button" @click="items.push('')" class="inline-flex items-center text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    <i class="bi bi-plus-lg mr-1"></i> Tambah Pengalaman
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.experience_list')" class="mt-2" />
        </div>

        {{-- Sertifikasi (Upload & Preview) --}}
        <div x-data="{ 
            images: [], 
            showModal: false, 
            modalImage: '',
            
            handleFiles(event) {
                const newFiles = Array.from(event.target.files);
                
                // Cek jika total gambar melebihi 10
                if (this.images.length + newFiles.length > 10) {
                    alert('Maksimal 10 gambar saja yang diperbolehkan.');
                    return;
                }

                newFiles.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.images.push({
                            url: e.target.result,
                            name: file.name
                        });
                    };
                    reader.readAsDataURL(file);
                });
            },

            async removeImage(index) {
                this.images.splice(index, 1);

                await $wire.removeNewCertificate(index);
                this.images = this.images.map((img, idx) => ({...img, index: idx}));
            },

            openModal(url) {
                this.modalImage = url;
                this.showModal = true;
            }
        }" class="space-y-4">
            
            <x-input-label :value="__('Sertifikasi Keahlian (Optional)')" class="mb-2" />
            
            {{-- Upload Area --}}
            <div 
                class="relative mt-2 flex justify-center rounded-lg border border-dashed border-slate-300 dark:border-slate-700 px-6 py-10 transition-colors hover:border-indigo-400"
                :class="images.length >= 10 ? 'opacity-50 cursor-not-allowed bg-slate-100 dark:bg-slate-900' : 'cursor-pointer'"
            >
                <div class="text-center">
                    <i class="bi bi-cloud-arrow-up text-4xl text-slate-400"></i>
                    <div class="mt-4 flex text-sm text-slate-600 dark:text-slate-400 justify-center">
                        <label class="relative font-semibold text-indigo-600 focus-within:outline-none hover:text-indigo-500" :class="images.length >= 10 ? 'pointer-events-none' : 'cursor-pointer'">
                            <span>Upload files</span>
                            <input 
                                type="file" 
                                wire:model="form.certificates" 
                                class="sr-only" 
                                multiple 
                                accept="image/*"
                                :disabled="images.length >= 10"
                                @change="handleFiles($event)"
                            >
                        </label>
                        <p class="pl-1 text-slate-500">or drag and drop</p>
                    </div>
                    <p class="text-xs text-slate-500 italic">Terupload: <span x-text="images.length"></span>/10</p>
                </div>
            </div>

            {{-- Preview Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mt-4">
                
                {{-- Tampilkan Sertifikat LAMA dari Database --}}
                @foreach($form->existing_certificates as $index => $path)
                    <div class="relative aspect-square bg-slate-100 rounded-lg overflow-hidden border border-slate-200 shadow-sm">
                        <img src="{{ asset('storage/' . $path) }}" class="h-full w-full object-cover">
                        
                        {{-- Tombol Hapus SELALU TERLIHAT (tanpa hover) --}}
                        <button 
                            type="button" 
                            wire:click="deleteOldCertificate({{ $index }})"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50"
                            class="absolute top-2 right-2 p-1.5 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white rounded-full shadow-lg shadow-red-500/30 transition-all duration-200 hover:scale-110 active:scale-95 z-10"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </button>

                        {{-- Label Lama --}}
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent text-[10px] text-white px-2 py-2 font-medium backdrop-blur-[2px]">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg>
                                Lama
                            </span>
                        </div>
                    </div>
                @endforeach

                {{-- Tampilkan Sertifikat BARU (Temporary Upload) --}}
                <template x-for="(img, index) in images" :key="index">
                    <div class="relative aspect-square bg-slate-100 rounded-lg overflow-hidden border border-slate-200 shadow-sm">
                        <img :src="img.url" class="h-full w-full object-cover cursor-pointer" @click="openModal(img.url)">
                        
                        {{-- Tombol Hapus SELALU TERLIHAT (tanpa hover) --}}
                        <button 
                            type="button" 
                            @click="removeImage(index)"
                            class="absolute top-2 right-2 p-1.5 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white rounded-full shadow-lg shadow-red-500/30 transition-all duration-200 hover:scale-110 active:scale-95 z-10"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </button>

                        {{-- Label Baru dengan Nama File --}}
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent text-[10px] text-white px-2 py-2 backdrop-blur-[2px]">
                            <span class="flex items-center gap-1 truncate">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg>
                                <span x-text="img.name" class="truncate max-w-[80px]"></span>
                            </span>
                        </div>
                    </div>
                </template>
            </div>

            <x-input-error :messages="$errors->get('form.certificates')" class="mt-2" />

            {{-- Lightbox Modal --}}
            <div 
                x-show="showModal" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[99] flex items-center justify-center bg-black/90 p-4"
                @click.away="showModal = false"
                @keydown.escape.window="showModal = false"
                style="display: none;"
            >
                {{-- Close Modal --}}
                <button @click="showModal = false" type="button" class="absolute top-5 right-5 text-white text-3xl hover:text-slate-300 transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>

                <img :src="modalImage" class="max-w-full max-h-full rounded-lg shadow-2xl shadow-white/10">
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex items-center justify-end border-t border-slate-100 dark:border-slate-700">
            <x-primary-button wire:loading.attr="disabled" class="w-full flex justify-center space-x-2 !bg-blue-700">
                <i wire:loading.remove class="bi bi-send-check text-lg sm:text-2xl"></i>
                <span wire:loading class="animate-spin mr-2"><i class="bi bi-arrow-repeat text-lg sm:text-2xl"></i></span>
                <span class="text-md sm:text-lg">Simpan Profil</span>
            </x-primary-button>
        </div>
    </form>
</div>