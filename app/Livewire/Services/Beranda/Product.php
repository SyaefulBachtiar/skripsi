<?php

namespace App\Livewire\Services\Beranda;

use App\Models\Jasa;
use App\Models\Role_users\Technician;
use Livewire\Component;
use Livewire\WithPagination;

class Product extends Component
{

    use WithPagination;

    public $search = '', $searchJasa;
    public $wilayah = '';
    public $searchWilayah = '';

    // public $produk_jasa = [];

    public function updatingSearchJasa()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->reset(['search', 'searchJasa', 'wilayah', 'searchWilayah']);
        $this->resetPage();
    }

    public function selectOption($target, $name)
{
    if ($target === 'search') {
        $this->search = $name;       // Filter query Jasa
        $this->searchJasa = $name;   // Teks di input Jasa
    } elseif ($target === 'wilayah') {
        $this->wilayah = $name;      // Filter query Wilayah
        $this->searchWilayah = $name; // Teks di input Wilayah
    }
    
    $this->resetPage();
}

    public function render()
    {
        // 1. Data untuk Dropdown (Sugesti Pencarian)
        $options_jasa = [];
        if (strlen($this->searchJasa) >= 1) {
            $options_jasa = Jasa::where('nama_jasa', 'like', '%' . $this->searchJasa . '%')
                ->select('id', 'nama_jasa as name')
                ->limit(5)
                ->get();
        }

        $list_wilayah = Technician::whereNotNull('kabupaten')
            ->where('kabupaten', 'like', '%' . $this->searchWilayah . '%')
            ->select('kabupaten as name')
            ->distinct()
            ->get();

        $query = Jasa::with('technician')
            ->select('id', 'id_technician', 'nama_jasa', 'harga_jasa', 'thumbnails')
            ->orderBy('created_at', 'desc');
            
        if ($this->search !== '') {
            $query->where('nama_jasa', 'like', '%' . $this->search . '%');
        }

        if ($this->wilayah !== '') {
            $query->whereHas('technician', function ($q) {
                $q->where('kabupaten', $this->wilayah);
            });
        }

        return view('livewire.services.beranda.product', [
            'produk' => $query->paginate(10),
            'nama_jasa' => $options_jasa,
            'list_wilayah' => $list_wilayah
        ]);
    }
}
