<?php

use App\Livewire\Forms\FormTechnician;
use App\Models\Role_users\Technician;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public FormTechnician $form;

    public function mount(): void
    {
        $technician = Technician::where('user_id', auth()->id())->first();
        
        if ($technician) {
            $this->form->setValues($technician);
        }
    }

    public function removeOldCert($index)
    {
        // Panggil fungsi removeExistingCertificate yang ada di FormTechnician
        $this->form->removeExistingCertificate($index);
    }

    public function removeNewCert($index)
    {
        $this->form->removeCertificate($index);
    }

    public function updatedFormNewCertificate(): void
    {
        $this->form->addCertificate();
    }

    public function save(): void
    {
        try {
            $this->form->store();
            
            session()->flash('success', 'Profil teknisi berhasil disimpan!');
            $this->redirect(request()->header('Referer') ?? route('dashboard'));
            
        } catch (ValidationException $e) {
            // Biarkan Livewire menangani error validasi otomatis
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

    <form wire:submit.prevent="save" class="space-y-5">

        {{-- Deskripsi --}}
        <div>
            <x-input-label for="bio" :value="__('Deskripsi Singkat')" class="mb-2" />
            <textarea 
                wire:model.live.debounce.300ms="form.bio"
                id="bio"
                rows="4"
                class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                placeholder="Ceritakan singkat tentang keahlian Anda..."
            ></textarea>
            <x-input-error :messages="$errors->get('form.bio')" class="mt-2" />
        </div>

        <hr class="border-slate-200 dark:border-slate-700">
        
        {{-- Spesialisasi (Multi-select) --}}
        <div x-data="{ 
            selected: @entangle('form.spesialisasi'),
            options: ['AC', 'Kulkas', 'Kelistrikan', 'Mesin Cuci', 'Water Heater', 'Pompa Air'],
            showManual: false,
            manualInput: '',
            
            add(option) {
                if (option === 'custom') {
                    this.showManual = true;
                    return;
                }
                if (option && !this.selected.includes(option) && this.selected.length < 5) {
                    this.selected.push(option);
                }
            },
            
            addManual() {
                const val = this.manualInput.trim();
                if (val && !this.selected.includes(val) && this.selected.length < 5) {
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
                <p class="text-xs font-medium" :class="selected.length >= 5 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-500 dark:text-slate-400'">
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
                    :class="selected.length >= 5 ? 'bg-slate-50 dark:bg-slate-800 cursor-not-allowed opacity-60' : ''"
                    class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm transition-all"
                >
                    <option value="" x-text="selected.length >= 5 ? 'Maksimal 5 spesialisasi' : '-- Pilih Spesialisasi --'"></option>
                    
                    <template x-for="opt in options" :key="opt">
                        <option :value="opt" x-text="opt" :disabled="selected.includes(opt)"></option>
                    </template>

                    <option value="custom" class="text-indigo-600 font-bold">+ Lainnya</option>
                </select>

                {{-- Input Manual Field --}}
                <div x-show="showManual" 
                    x-transition 
                    class="flex gap-2 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-700 w-full">
                    <x-text-input 
                        x-model="manualInput" 
                        @keydown.enter.prevent="addManual()"
                        placeholder="Ketik keahlian lainnya..." 
                        class="text-sm w-full" 
                    />
                    <button type="button" @click="addManual()" class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700 transition-colors">
                        Tambah
                    </button>
                    <button type="button" @click="showManual = false" class="px-3 py-1 text-slate-500 text-sm hover:text-slate-700 dark:hover:text-slate-300">
                        Batal
                    </button>
                </div>
            </div>

            <x-input-error :messages="$errors->get('form.spesialisasi')" class="mt-2" />
        </div>

        <hr class="border-slate-200 dark:border-slate-700">

        {{-- Pengalaman (Dynamic Input) --}}
        <div x-data="{ items: @entangle('form.experience_list') }">
            <x-input-label :value="__('Riwayat Pengalaman Kerja (Opsional)')" class="mb-2" />
            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex gap-2">
                        <x-text-input 
                            type="text" 
                            x-model="items[index]" 
                            placeholder="PT. Maju Jaya (2019-2021)" 
                            class="flex-1" 
                        />
                        <button 
                            type="button" 
                            @click="items.length > 1 ? items.splice(index, 1) : items[index] = ''" 
                            class="text-red-500 hover:text-red-700 dark:hover:text-red-400 p-2 transition-colors"
                        >
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                </template>
                <button 
                    type="button" 
                    @click="items.push('')" 
                    class="inline-flex items-center text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                    <i class="bi bi-plus-lg mr-1"></i> Tambah Pengalaman
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.experience_list')" class="mt-2" />
        </div>

        <hr class="border-slate-200 dark:border-slate-700">

        {{-- Sertifikasi (Upload & Preview) --}}
        <div x-data="{ 
            showModal: false, 
            modalImage: '',
            openModal(url) {
                this.modalImage = url;
                this.showModal = true;
            }
        }" class="space-y-4">
            
            <x-input-label :value="__('Sertifikasi Keahlian (Opsional)')" class="mb-2" />
            
            {{-- Upload Area --}}
            <div class="relative mt-2 flex justify-center rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 px-6 py-10 hover:border-indigo-400 dark:hover:border-indigo-600 transition-colors">
                <div class="text-center">
                    <div class="mx-auto h-12 w-12 text-slate-400">
                        <i class="bi bi-cloud-arrow-up text-4xl"></i>
                    </div>
                    <div class="mt-4 flex text-sm text-slate-600 dark:text-slate-400 justify-center">
                        <label class="relative cursor-pointer rounded-md font-semibold text-indigo-600 focus-within:outline-none hover:text-indigo-500">
                            <span>Upload files</span>
                            <input 
                                type="file" 
                                wire:model="form.newCertificate"
                                class="sr-only" 
                                multiple 
                                accept="image/jpeg,image/png,image/jpg"
                            >
                        </label>
                        <p class="pl-1 text-slate-500">atau drag & drop</p>
                    </div>
                    <p class="text-xs text-slate-400 mt-1 italic">
                        JPG, JPEG, PNG hingga 2MB (Maks. 5 foto)
                    </p>
                    
                    @php
                        $totalCerts = count($form->existing_certificates) + count($form->certificates);
                    @endphp
                    
                    <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $totalCerts >= 5 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' }}">
                        {{ $totalCerts }} / 5 Terupload
                    </div>
                </div>
            </div>

            <x-input-error :messages="$errors->get('form.newCertificate')" class="mt-2" />
            <x-input-error :messages="$errors->get('form.newCertificate.*')" class="mt-2" />

            {{-- Preview Grid --}}
            @if (count($form->existing_certificates) > 0 || count($form->certificates) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mt-6">
                    
                    {{-- Existing Certificates (Database) --}}
                    @foreach($form->existing_certificates as $index => $path)
                        <div class="relative aspect-square group">
                            <div 
                                @click="openModal('{{ Storage::url($path) }}')"
                                class="w-full h-full rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 cursor-pointer shadow-sm hover:shadow-md transition-shadow"
                            >
                                <img src="{{ Storage::url($path) }}" class="w-full h-full object-cover" alt="Sertifikat {{ $index + 1 }}">
                            </div>
                            <button 
                                type="button" 
                                wire:click="removeOldCert({{ $index }})" 
                                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md z-10 transition-colors"
                                wire:loading.attr="disabled"
                            >
                                <i class="bi bi-x text-lg"></i>
                            </button>
                        </div>
                    @endforeach

                    {{-- New Temporary Certificates --}}
                    @foreach($form->certificates as $index => $file)
                        <div class="relative aspect-square group">
                            @if ($file && method_exists($file, 'temporaryUrl'))
                                <div 
                                    @click="openModal('{{ $file->temporaryUrl() }}')"
                                    class="w-full h-full rounded-xl overflow-hidden border border-indigo-200 dark:border-indigo-900 cursor-pointer shadow-sm hover:shadow-md transition-shadow"
                                >
                                    <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover" alt="Preview {{ $index + 1 }}">
                                </div>
                            @else
                                <div class="w-full h-full bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center border border-slate-200 dark:border-slate-700">
                                    <i class="bi bi-hourglass-split text-slate-400 animate-pulse"></i>
                                </div>
                            @endif
                            
                            <button 
                                type="button" 
                                wire:click="removeNewCert({{ $index }})" 
                                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md z-10 transition-colors"
                                wire:loading.attr="disabled"
                            >
                                <i class="bi bi-x text-lg"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Lightbox Modal --}}
            <template x-teleport="body">
                <div 
                    x-show="showModal" 
                    class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/90 p-4 sm:p-10"
                    style="display: none;"
                    @keydown.escape.window="showModal = false"
                    x-transition.opacity
                >
                    {{-- Tombol Close --}}
                    <button 
                        @click="showModal = false" 
                        class="absolute top-5 right-5 text-white/70 hover:text-white p-2 transition-colors"
                    >
                        <i class="bi bi-x-lg text-3xl"></i>
                    </button>

                    {{-- Image Wrapper --}}
                    <div class="max-w-5xl w-full h-full flex items-center justify-center" @click.away="showModal = false">
                        <img 
                            :src="modalImage" 
                            class="max-w-full max-h-full rounded-lg object-contain border border-white/10 shadow-2xl"
                            x-show="showModal"
                            x-transition
                            alt="Preview Sertifikat"
                        >
                    </div>
                </div>
            </template>
        </div>

        {{-- Submit Button --}}
        <div class="flex items-center justify-end border-t border-slate-100 dark:border-slate-700 pt-6">
            <x-primary-button 
                wire:loading.attr="disabled" 
                wire:target="save"
                class="w-full flex justify-center items-center space-x-2 !bg-blue-700 hover:!bg-blue-800 transition-colors"
            >
                <span wire:loading.remove wire:target="save">
                    <i class="bi bi-send-check text-lg sm:text-xl"></i>
                </span>
                <span wire:loading wire:target="save" class="animate-spin">
                    <i class="bi bi-arrow-repeat text-lg sm:text-xl"></i>
                </span>
                <span class="text-base sm:text-lg">Simpan Profil</span>
            </x-primary-button>
        </div>
    </form>
</div>