<?php

namespace App\Livewire\Forms;

use App\Models\Jasa;
use App\Models\Role_users\Technician;
use Illuminate\Support\Facades\Auth;
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

    public $old_image_paths = [];

    public function store($id = null)
    {
        $this->validate();

        $technician = Technician::where('user_id', Auth::id())->first();

        $imagePaths = $this->old_image_paths ?? [];

        if (!empty($this->new_images)) {
            foreach ($this->new_images as $image) {
                // Pastikan ini adalah TemporaryUploadedFile
                if ($image && method_exists($image, 'store')) {
                    $path = $image->store('services/thumbnails', 'public');
                    $imagePaths[] = $path;
                }
            }
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
            'thumbnails'            => $imagePaths
        ];

        // Jika upload gambar baru, tambahkan ke data yang diupdate
        if(!empty($imagePaths)) {
            $data['thumbnails'] = $imagePaths;
        }

        // Menggunakan updateOrCreate
        Jasa::updateOrCreate(['id' => $id], $data);

        return true;
    }
}