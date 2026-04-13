<?php

namespace App\Livewire\Forms;

use App\Models\Jasa;
use App\Models\Role_users\Technician;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FormPostingJasa extends Form 
{
    #[Validate([
        'nama_jasa' => 'required|string|min:5'
    ], message: [
        'nama_jasa.required' => 'Nama jasa wajib diisi!',
        'nama_jasa.min' => 'Deskripsi harus lebih dari 5 karakter'
    ])]
    public $nama_jasa = '';

    #[Validate('required|numeric')]
    public $harga_jasa = '';

    #[Validate([
        'deskripsi_jasa' => 'required|string|min:5'
    ], message: [
        'deskripsi_jasa.required' => 'Deskripsi jasa wajib diisi!',
        'deskripsi_jasa.min' => 'Deskripsi harus lebih dari 5 karakter!',
    ])]
    public $deskripsi_jasa = '';

    public $is_setiap_hari = false;
    public $ketersediaan_tanggal = [];
    public $ketersediaan_jam = [];
    public $keluhan = [];
    
    // Properti untuk menangani struktur layanan tambahan
    public $layanan_tambahan = []; 

    #[Validate([
        'new_images' => 'nullable|array|max:5',
        'new_images.*' => 'image|max:2048', // Maksimal 2MB per file
    ], message: [
        'new_images.max' => 'Maksimal hanya boleh mengunggah 5 gambar!',
        'new_images.*.image' => 'File harus berupa gambar!',
        'new_images.*.max' => 'Ukuran gambar maksimal 2MB!',
    ])]
    public $new_images = [];

    public $images = [];

    public $old_image_paths = [];

    public function addImage()
    {
        // Validasi input temporary
        $this->validateOnly('new_images');

        if (empty($this->new_images)) return;

        foreach ($this->new_images as $file) {
            // Batasi maksimal 5 total gambar (lama + baru)
            if ((count($this->images) + count($this->old_image_paths)) < 5) {
                $this->images[] = $file;
            }
        }

        // Reset input agar bisa upload file yang sama jika perlu
        $this->reset('new_images');
    }

    public function removeNewImage($index)
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
    }

    public function removeExistingCertificate(int $index): void
    {
        if (isset($this->old_image_paths[$index])) {
            // Hapus file dari storage jika ada
            $path = $this->old_image_paths[$index];
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            array_splice($this->old_image_paths, $index, 1);
        }
    }

    public function store($id = null)
    {
        $this->validate();

        $technician = Technician::where('user_id', Auth::id())->first();

        // Gabungkan path lama yang tersisa dengan upload baru
        $finalPaths = $this->old_image_paths;

        foreach ($this->images as $file) {
            $finalPaths[] = $file->store('services/thumbnails', 'public');
        }

        $data = [
            'id_technician'         => $technician->id,
            'nama_jasa'             => $this->nama_jasa,
            'harga_jasa'            => $this->harga_jasa,
            'deskripsi'             => $this->deskripsi_jasa,
            'is_setiap_hari'        => $this->is_setiap_hari,
            'ketersediaan_tanggal'  => $this->is_setiap_hari ? [] : $this->ketersediaan_tanggal,
            'ketersediaan_jam'      => $this->ketersediaan_jam,
            'layanan_tambahan'      => $this->layanan_tambahan,
            'keluhan'               => $this->keluhan,
            'thumbnails'            => array_values($finalPaths)
        ];

        Jasa::updateOrCreate(['id' => $id], $data);
        return true;
    }
}