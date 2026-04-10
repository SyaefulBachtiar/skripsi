<?php

namespace App\Livewire\Services\DashboardTechnician;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DaftarProdukJasa extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch () {
        $this->resetPage();
    }

    public function render()
    {
        $jasa = Auth::user()->technician?->jasa()
            ->select('id', 'nama_jasa', 'harga_jasa', 'thumbnails', 'ketersediaan_tanggal')
            ->where('nama_jasa', 'like', '%' . $this->search . '%' )
            ->latest()
            ->paginate(10);
        

        return view('livewire.services.dashboard-technician.daftar-produk-jasa', [
            'jasa' => $jasa ?? collect()->paginate()
        ]);
    }
}
