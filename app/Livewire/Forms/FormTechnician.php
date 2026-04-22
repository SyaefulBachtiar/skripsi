<?php

namespace App\Livewire\Forms;

use App\Models\Role_users\Technician;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FormTechnician extends Form
{
    #[Validate([
        'spesialisasi' => 'required|array|min:1'
    ], message: [
        'spesialisasi.required' => 'Spesialisasi wajib diisi minimal 1 kategori.'
    ])]
    public array $spesialisasi = [];

    #[Validate([
        'experience_list' => 'nullable|array',
        'experience_list.*' => 'nullable|string|max:255'
    ])]
    public array $experience_list = [];

    #[Validate([
        'bio' => 'required|string|min:5'
    ], message: [
        'bio.required' => 'Deskripsi wajib diisi.',
        'bio.min' => 'Deskripsi minimal 5 karakter.',
    ])]
    public string $bio = '';

    // Nonaktifkan validasi otomatis saat update untuk file upload [^1^]
    #[Validate([
        'newCertificate.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ], onUpdate: false, message: [
        'newCertificate.*.image' => 'File harus berupa gambar.',
        'newCertificate.*.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',
        'newCertificate.*.max' => 'Ukuran gambar maksimal 2MB.'
    ])]
    public array $newCertificate = [];

    public array $certificates = [];
    public array $existing_certificates = [];

    public function setValues(Technician $technician): void
    {
        $this->spesialisasi = $technician->spesialisasi ?? [];
        $this->experience_list = $technician->pengalaman ?? [];
        $this->bio = $technician->deskripsi ?? '';
        $this->existing_certificates = $technician->sertifikat ?? [];
    }

    public function addCertificate(): void
    {
        // Validasi hanya field newCertificate
        $this->validateOnly('newCertificate');

        if (empty($this->newCertificate)) {
            return;
        }

        $totalCerts = count($this->certificates) + count($this->existing_certificates);
        
        if ($totalCerts >= 5) {
            $this->addError('newCertificate', 'Maksimal hanya boleh mengunggah 5 sertifikat.');
            $this->reset('newCertificate');
            return;
        }

        foreach ($this->newCertificate as $file) {
            if (count($this->certificates) + count($this->existing_certificates) < 5) {
                $this->certificates[] = $file;
            }
        }

        $this->reset('newCertificate');
    }

    public function removeCertificate(int $index): void
    {
        if (isset($this->certificates[$index])) {
            array_splice($this->certificates, $index, 1);
        }
    }

    public function removeExistingCertificate(int $index): void
    {
        if (isset($this->existing_certificates[$index])) {
            // Hapus file dari storage jika ada
            $path = $this->existing_certificates[$index];
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            array_splice($this->existing_certificates, $index, 1);
        }
    }

    /**
     * @throws ValidationException
     */
    public function store(): void
    {
        $this->validate();

        $paths = $this->existing_certificates;

        foreach ($this->certificates as $file) {
            $paths[] = $file->store('technician-certificates', 'public');
        }

        $cleanExperience = [];
        if (!empty($this->experience_list)) {
            $cleanExperience = array_values(array_filter($this->experience_list, function($value) {
                return !empty($value) && trim($value) !== '';
            }));
        }

        Technician::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'spesialisasi' => $this->spesialisasi,
                'pengalaman' => $cleanExperience,
                'sertifikat' => array_values($paths),
                'deskripsi' => $this->bio,
            ]
        );

        $this->reset();
    }
}