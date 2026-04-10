<?php

namespace App\Livewire\Services\DashboardTechnician;

use App\Models\Jasa;
use Livewire\Component;

class DetailJasa extends Component
{

    public $jasa;
    public $order_date = '';
    public $order_time = '';
    public $keluhan = [];
    public $keluhan_manual = '';
    public $layanan_tambahan = [];

    public function mount ($id_jasa) {

        $this->jasa = Jasa::select('id', 'id_technician', 'nama_jasa', 'harga_jasa', 'deskripsi', 'thumbnails', 'ketersediaan_tanggal', 'ketersediaan_jam', 'is_setiap_hari', 'layanan_tambahan', 'keluhan')
            ->with([
                'technician' => function ($query) {
                    $query->with(['user:id,name,avatar']);
                }
            ])
            ->findOrFail($id_jasa);

        foreach ($this->jasa->layanan_tambahan as $index => $grup) {
            $this->layanan_tambahan[$index] = [];
        }

        // dd($this->jasa);
    }

    public function submitOrder()
    {
        $validated = $this->validate([
            'order_date' => 'required|date',
            'order_time' => 'required|string',
            'keluhan' => 'nullable|array',
            'keluhan_manual' => 'nullable|string|max:500',
            'layanan_tambahan' => 'nullable|array',
        ], [
            'order_date.required' => 'Tanggal harus dipilih',
            'order_time.required' => 'Jam harus dipilih',
        ]);

        // Gabungkan keluhan dari checkbox dan manual
        $allKeluhan = $this->keluhan;
        if (!empty($this->keluhan_manual)) {
            $allKeluhan[] = $this->keluhan_manual;
        }

        // Siapkan data untuk dd()
        $orderData = [
            'jasa_id' => $this->jasa->id,
            'jasa_nama' => $this->jasa->nama_jasa,
            'technician_id' => $this->jasa->id_technician,
            'order_date' => $this->order_date,
            'order_time' => $this->order_time,
            'keluhan' => $allKeluhan,
            'layanan_tambahan' => $this->layanan_tambahan,
            'harga_dasar' => $this->jasa->harga_jasa,
        ];

        dd($orderData);
    }

    public function render()
    {
        return view('livewire.services.dashboard-technician.detail-jasa');
    }
}
