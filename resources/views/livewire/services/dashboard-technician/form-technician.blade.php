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
            // Memanggil method store() di dalam Form Object
            $status = $this->form->store();

            if ($status) {
                session()->flash('success', 'Berhasil!');

                // Refresh halaman saat ini
                return $this->redirect(request()->header('Referer'), navigate: true);
            }
        } catch (Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return $this->redirect(request()->header('Referer'), navigate: true);
        }
    }

    public function deleteOldCertificate($index)
    {
        // 1. Ambil array sertifikat lama dari form object
        $certs = $this->form->existing_certificates;

        if (isset($certs[$index])) {
            $pathFile = $certs[$index];

            // 2. OPSIONAL: Hapus file fisik dari storage agar tidak jadi sampah (sangat disarankan)
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($pathFile)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pathFile);
            }

            // 3. Hapus dari array lokal
            unset($certs[$index]);
            $newCerts = array_values($certs); // Reset index array

            // 4. UPDATE LANGSUNG KE DATABASE
            \App\Models\Role_users\Technician::where('user_id', auth()->id())
                ->update([
                    'sertifikat' => $newCerts // Laravel otomatis melakukan json_encode karena ada $casts di Model
                ]);

            // 5. Update state di Form Object agar tampilan sinkron
            $this->form->existing_certificates = $newCerts;

            session()->flash('success', 'Foto sertifikat berhasil dihapus permanen.');
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

            removeImage(index) {
                this.images.splice(index, 1);
                // Catatan: Jika ingin menghapus file di backend Livewire juga, 
                // Anda butuh logika tambahan menggunakan wire.upload
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
                    <div class="mt-4 flex text-sm text-slate-600 dark:text-slate-400">
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
                    <div class="relative group aspect-square bg-slate-100 rounded-lg overflow-hidden border border-slate-200">
                        <img src="{{ asset('storage/' . $path) }}" class="h-full w-full object-cover">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button" wire:click="deleteOldCertificate({{ $index }})" class="p-1.5 bg-red-500 rounded-full text-white">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach

                {{-- Tampilkan Sertifikat BARU (Temporary Upload) --}}
                <template x-for="(img, index) in images" :key="index">
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
                <button @click="showModal = false" class="absolute top-5 right-5 text-white text-3xl hover:text-slate-300">
                    <i class="bi bi-x-lg"></i>
                </button>

                <img :src="modalImage" class="max-w-full max-h-full rounded-lg shadow-2xl shadow-white/10">
            </div>
        </div>

        {{-- Deskripsi --}}
        <div>
            <x-input-label for="deskripsi" :value="__('Deskripsi Singkat')" class="mb-2" />
            <textarea 
                wire:model="form.bio"
                rows="4"
                class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                placeholder="Ceritakan singkat tentang keahlian Anda..."
            ></textarea>
            <x-input-error :messages="$errors->get('form.bio')" class="mt-2" />
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