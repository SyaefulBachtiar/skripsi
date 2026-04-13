<?php

namespace App\Livewire\Services\Beranda;

use App\Models\Jasa;
use App\Models\Role_users\Technician;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Product extends Component
{
    use WithPagination;

    public $search = '', $searchJasa = '';
    public $wilayah = '';
    public $searchWilayah = '';

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
            $this->search = $name;
            $this->searchJasa = $name;
        } elseif ($target === 'wilayah') {
            $this->wilayah = $name;
            $this->searchWilayah = $name;
        }
        $this->resetPage();
    }

    public function render()
    {
        // 1. Sugesti Pencarian Jasa (Dropdown)
        $options_jasa = [];
        if (strlen($this->searchJasa) >= 1) {
            // Kita cari dari nama_jasa yang unik atau kategori
            $options_jasa = Jasa::query()
                ->where(function($q) {
                    $keywords = explode(' ', $this->searchJasa);
                    foreach ($keywords as $word) {
                        $q->where('nama_jasa', 'like', '%' . $word . '%');
                    }
                })
                ->select('nama_jasa as name')
                ->distinct()
                ->limit(8)
                ->get();
        }

        // 2. Daftar Wilayah (Sugesti)
        $list_wilayah = Technician::query()
            ->whereNotNull('kabupaten')
            ->where('kabupaten', 'like', '%' . $this->searchWilayah . '%')
            ->select('kabupaten as name')
            ->distinct()
            ->limit(5)
            ->get();

        // 3. Query Utama Produk Jasa
        $query = Jasa::with('technician')
            ->select('id', 'id_technician', 'nama_jasa', 'harga_jasa', 'thumbnails', 'deskripsi');

        // Logic Pencarian Pintar (Extended Keywords)
        if ($this->search !== '') {
            $query->where(function ($q) {
                $words = explode(' ', $this->search);
                foreach ($words as $word) {
                    $word = trim($word);
                    if (empty($word)) continue;

                    $q->where(function ($sub) use ($word) {
                        $sub->where('nama_jasa', 'like', "%{$word}%")
                            ->orWhere('deskripsi', 'like', "%{$word}%")
                            // Mencari hingga ke data spesialisasi teknisinya
                            ->orWhereHas('technician', function ($t) use ($word) {
                                $t->where('spesialisasi', 'like', "%{$word}%")
                                  ->orWhere('deskripsi', 'like', "%{$word}%");
                            });
                    });
                }
            });
        }

        // Filter Wilayah
        if ($this->wilayah !== '') {
            $query->whereHas('technician', function ($q) {
                // Gunakan like untuk wilayah agar lebih fleksibel
                $q->where('kabupaten', 'like', '%' . $this->wilayah . '%');
            });
        }

        return view('livewire.services.beranda.product', [
            'produk' => $query->orderBy('created_at', 'desc')->paginate(12),
            'nama_jasa' => $options_jasa,
            'list_wilayah' => $list_wilayah
        ]);
    }
}