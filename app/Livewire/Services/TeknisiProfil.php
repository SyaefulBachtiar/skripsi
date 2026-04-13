<?php

namespace App\Livewire\Services;

use App\Models\Jasa;
use App\Models\Role_users\Technician;
use Livewire\Component;
use Livewire\WithPagination;

class TeknisiProfil extends Component
{

    use WithPagination;

    public $id_technician;

    public $kategori = '', $searchKategori = '', $search = '', $searchJasa = '';

    public function updatingSearchJasa() { $this->resetPage(); }
    public function updatingKategori() { $this->resetPage(); }

    public function selectOption($target, $name)
    {
        if ($target === 'search') {
            $this->search = $name;       // Filter query Jasa
            $this->searchJasa = $name;   // Set teks input agar sesuai pilihan
        } elseif ($target === 'kategori') {
            $this->kategori = $name;     // Filter query Kategori
            $this->searchKategori = $name; // Set teks input kategori
        }
        
        $this->resetPage(); // Selalu reset halaman ke 1 setelah filter berubah
    }

    public function resetFilter()
    {
        $this->reset(['search', 'searchJasa', 'kategori', 'searchKategori']);
        $this->resetPage();
    }
    
    public function render()
    {
        // 1. Ambil Data Teknisi
        $technician = Technician::with(['user' => function ($query) {
            $query->select('id', 'name', 'avatar');
        }])
        ->select('id', 'user_id', 'spesialisasi', 'pengalaman', 'sertifikat', 'deskripsi')
        ->findOrFail($this->id_technician);

        // 2. Olah Sugesti Dropdown Jasa (Berdasarkan apa yang diketik)
        $options_nama_jasa = [];
        if (strlen($this->searchJasa) >= 1) {
            $options_nama_jasa = Jasa::where('id_technician', $this->id_technician)
                ->where('nama_jasa', 'like', '%' . $this->searchJasa . '%')
                ->select('nama_jasa as name')
                ->distinct()
                ->limit(5)
                ->get();
        }

        // 3. Olah Dropdown Kategori (Dari array spesialisasi Teknisi)
        // Kita ubah array ["AC", "Kulkas"] menjadi format yang dikenali select-search
        $spesialisasi_raw = $technician->spesialisasi ?? [];
        $options_kategori = collect($spesialisasi_raw)
            ->filter(function ($item) {
                // Filter berdasarkan teks yang diketik di kolom kategori
                return empty($this->searchKategori) || str_contains(strtolower($item), strtolower($this->searchKategori));
            })
            ->map(function ($item) {
                return (object) ['name' => $item];
            });

        // 4. Query Data Jasa (Filter Utama)
        $query = Jasa::where('id_technician', $this->id_technician);

        // Filter Nama Jasa (Search)
        if ($this->search !== '') {
            $query->where('nama_jasa', 'like', '%' . $this->search . '%');
        }

        // Filter Kategori (Mencari di nama jasa atau deskripsi karena kategori adalah spesialisasi teknisi)
        if ($this->kategori !== '') {
            $query->where(function($q) {
                $q->where('nama_jasa', 'like', '%' . $this->kategori . '%')
                  ->orWhere('deskripsi', 'like', '%' . $this->kategori . '%');
            });
        }

        return view('livewire.services.teknisi-profil', [
            'data_technician' => $technician,
            'data_jasa' => $query->orderBy('created_at', 'desc')->paginate(10),
            'options_nama_jasa' => $options_nama_jasa,
            'options_kategori' => $options_kategori
        ]);
    }
}
