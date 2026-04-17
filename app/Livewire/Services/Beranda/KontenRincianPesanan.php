<?php

namespace App\Livewire\Services\Beranda;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KontenRincianPesanan extends Component
{
    public $id_order;
    public $order;
    public $layanan_tambahan = [];

    public function mount () 
    {
        $this->order = Order::with(['jasa.technician.user', 'customer.user'])
            ->where('id', $this->id_order)
            ->whereHas('customer', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->first();

        if (!$this->order) {
            abort(404, 'Pesanan tidak ditemukan');
        }

        $this->layanan_tambahan = $this->order->layanan_tambahan ?? [];
    }

    public function updatedLayananTambahan()
    {
        // Hitung total harga baru
        $hargaDasar = $this->order->jasa->harga_jasa ?? 0;
        $totalTambahan = 0;

        foreach ($this->layanan_tambahan as $grup) {
            if (is_array($grup)) {
                foreach ($grup as $itemJson) {
                    $item = json_decode($itemJson, true);
                    $totalTambahan += (int) ($item['harga'] ?? 0);
                }
            }
        }

        // Update database
        $this->order->update([
            'layanan_tambahan' => $this->layanan_tambahan,
            'total_harga' => $hargaDasar + $totalTambahan
        ]);

        // Refresh data order agar total di UI terupdate
        $this->order->refresh();
    }

    public function getTotalLayananTambahan()
    {
        $total = 0;
        if (is_array($this->layanan_tambahan)) {
            foreach ($this->layanan_tambahan as $grup) {
                if (is_array($grup)) {
                    foreach ($grup as $itemJson) {
                        $decoded = json_decode($itemJson, true);
                        $total += (int) ($decoded['harga'] ?? 0);
                    }
                }
            }
        }
        return $total;
    }

    // Helper untuk menghitung jumlah item yang dipilih
    public function getCountLayananTambahan()
    {
        $count = 0;
        if (is_array($this->layanan_tambahan)) {
            foreach ($this->layanan_tambahan as $grup) {
                if (is_array($grup)) {
                    $count += count($grup);
                }
            }
        }
        return $count;
    }

    public function render()
    {
        return view('livewire.services.beranda.konten-rincian-pesanan');
    }
}
