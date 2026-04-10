<?php

namespace App\Livewire\Forms;

use App\Models\Role_users\Technician;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FormTechnician extends Form {
    
    #[Validate([
        'spesialisasi' => 'required|array|min:1'
    ], message: [
        'spesialisasi.required' => 'Spesialisasi wajib diisi'
    ])]
    public $spesialisasi = [];

    #[Validate('nullable|array|min:1')]
    public $experience_list = [''];

    #[Validate([
        'bio' => 'required|string|min:5'
    ], message: [
        'bio.required' => 'Deskripsi wajib diisi!',
        'bio.min' => 'Minimal 5 karakter!'
    ])]
    public $bio = '';

    #[Validate([
        'certificates' => 'nullable|array|max:5',
        'certificates.*' => 'image|max:2048'
    ], message: [
        'certificates.max' => 'Maksimal hanya boleh mengunggah 5 gambar!',
    ])]
    public $certificates = [];

    public $existing_certificates = [];

    public function setValues($technician)
    {
        if ($technician) {
            $this->spesialisasi = $technician->spesialisasi ?? [];
            $this->experience_list = $technician->pengalaman ?? [''];
            $this->bio = $technician->deskripsi ?? '';
            $this->existing_certificates = $technician->sertifikat ?? [];
        }
    }

    public function store()
    {
        $this->validate();

        // Simpan file sertifikat dan ambil path-nya
        $certificatePaths = $this->existing_certificates; // Mulai dari yang sudah ada
        if (!empty($this->certificates)) {
            foreach ($this->certificates as $file) {
                $certificatePaths[] = $file->store('technician-certificates', 'public');
            }
        }

        // Simpan ke database
        Technician::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'spesialisasi' => $this->spesialisasi,
                'pengalaman' => array_values($this->experience_list),
                'sertifikat' => array_values($certificatePaths),
                'deskripsi' => $this->bio,
            ]
        );

        return true;
    }

}
?>