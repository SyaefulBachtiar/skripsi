<?php

namespace App\Livewire\Forms;

use App\Models\Jasa;
use App\Models\Role_users\Technician;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FormPostingJasa extends Form
{
    #[Validate([
        'nama_jasa' => 'required|string|min:5|max:255'
    ], message: [
        'nama_jasa.required' => 'Nama jasa wajib diisi.',
        'nama_jasa.min' => 'Nama jasa minimal 5 karakter.',
        'nama_jasa.max' => 'Nama jasa maksimal 255 karakter.'
    ])]
    public string $nama_jasa = '';

    #[Validate([
        'harga_jasa' => 'required|numeric|min:0'
    ], message: [
        'harga_jasa.required' => 'Harga jasa wajib diisi.',
        'harga_jasa.numeric' => 'Harga harus berupa angka.',
        'harga_jasa.min' => 'Harga tidak boleh negatif.'
    ])]
    public string $harga_jasa = '';

    #[Validate([
        'deskripsi_jasa' => 'required|string|min:5|max:2000'
    ], message: [
        'deskripsi_jasa.required' => 'Deskripsi jasa wajib diisi.',
        'deskripsi_jasa.min' => 'Deskripsi minimal 5 karakter.',
        'deskripsi_jasa.max' => 'Deskripsi maksimal 2000 karakter.'
    ])]
    public string $deskripsi_jasa = '';

    #[Validate([
        'tipe_layanan' => 'required|in:panggilan,bengkel'
    ], message: [
        'tipe_layanan.required' => 'Tipe layanan wajib dipilih.',
        'tipe_layanan.in' => 'Pilihan tipe layanan tidak valid.'
    ])]
    public string $tipe_layanan = 'panggilan';

    public bool $active = true;

    public bool $is_setiap_hari = false;
    public array $ketersediaan_tanggal = [];
    public array $ketersediaan_jam = [];
    public array $keluhan = [];
    public array $layanan_tambahan = [];

    #[Validate([
        'new_images' => 'nullable|array|max:5',
        'new_images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
    ], onUpdate: false, message: [
        'new_images.max' => 'Maksimal hanya boleh mengunggah 5 gambar.',
        'new_images.*.image' => 'File harus berupa gambar.',
        'new_images.*.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',
        'new_images.*.max' => 'Ukuran gambar maksimal 2MB.'
    ])]
    public array $new_images = [];

    public array $images = [];
    public array $old_image_paths = [];

    /**
     * Set nilai form dari model Jasa yang ada
     */
    public function setValues(Jasa $jasa): void
    {
        $this->nama_jasa = $jasa->nama_jasa;
        $this->harga_jasa = $jasa->harga_jasa;
        $this->deskripsi_jasa = $jasa->deskripsi;
        $this->tipe_layanan = $jasa->tipe_layanan ?? 'panggilan';
        $this->active = $jasa->active;
        $this->is_setiap_hari = $jasa->is_setiap_hari;
        $this->ketersediaan_tanggal = $jasa->ketersediaan_tanggal ?? [];
        $this->ketersediaan_jam = $jasa->ketersediaan_jam ?? [];
        $this->keluhan = $jasa->keluhan ?? [];
        $this->layanan_tambahan = $jasa->layanan_tambahan ?? [];
        $this->old_image_paths = $jasa->thumbnails ?? [];
    }

    /**
     * Tambahkan gambar baru ke koleksi
     */
    public function addImage(): void
    {
        $this->validateOnly('new_images');

        if (empty($this->new_images)) {
            return;
        }

        $totalImages = count($this->images) + count($this->old_image_paths);
        
        if ($totalImages >= 5) {
            $this->addError('new_images', 'Total gambar tidak boleh lebih dari 5.');
            $this->reset('new_images');
            return;
        }

        foreach ($this->new_images as $file) {
            if (count($this->images) + count($this->old_image_paths) < 5) {
                $this->images[] = $file;
            }
        }

        $this->reset('new_images');
    }

    /**
     * Hapus gambar baru dari koleksi temporary
     */
    public function removeNewImage(int $index): void
    {
        if (isset($this->images[$index])) {
            array_splice($this->images, $index, 1);
        }
    }

    /**
     * Hapus gambar lama dari storage dan array
     */
    public function removeOldImage(int $index): void
    {
        if (isset($this->old_image_paths[$index])) {
            $path = $this->old_image_paths[$index];
            
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            
            array_splice($this->old_image_paths, $index, 1);
        }
    }

    /**
     * Simpan data jasa ke database
     * 
     * @throws ValidationException
     */
    public function store(?int $id = null): void
    {
        $this->validate();

        $technician = Technician::where('user_id', Auth::id())->firstOrFail();

        $finalPaths = $this->old_image_paths;

        foreach ($this->images as $file) {
            if ($file && method_exists($file, 'store')) {
                $finalPaths[] = $file->store('services/thumbnails', 'public');
            }
        }

        $data = [
            'id_technician' => $technician->id,
            'nama_jasa' => $this->nama_jasa,
            'harga_jasa' => $this->harga_jasa,
            'deskripsi' => $this->deskripsi_jasa,
            'tipe_layanan' => $this->tipe_layanan,
            'active' => $this->active,
            'is_setiap_hari' => $this->is_setiap_hari,
            'ketersediaan_tanggal' => $this->is_setiap_hari ? [] : array_values(array_filter($this->ketersediaan_tanggal)),
            'ketersediaan_jam' => array_values(array_filter($this->ketersediaan_jam)),
            'layanan_tambahan' => $this->layanan_tambahan,
            'keluhan' => array_values(array_filter($this->keluhan)),
            'thumbnails' => array_values($finalPaths)
        ];

        Jasa::updateOrCreate(['id' => $id], $data);

        $this->reset();
    }
}