<?php

namespace App\Livewire\Services\DashboardTechnician;

use App\Events\OrderMasuk;
use App\Models\DetailOrder;
use App\Models\LacakPesanan;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DataPesanan extends Component
{

    use WithPagination, WithFileUploads;

    public $status_update;
    public $bukti_pengerjaan;
    public $catatan_progres;

    public $nama_layanan_baru;
    public $harga_layanan_baru;
    public $deskripsi_layanan_baru;
    public $foto_layanan_baru;

    public function updateProgres($orderId)
    {
        $this->validate([
            'status_update' => 'required',
            'bukti_pengerjaan' => 'nullable|image|max:2048',
            'catatan_progres' => 'nullable|string|max:255',
        ], [
            'status_update.required' => 'Status pengerjaan harus dipilih.',
            'bukti_pengerjaan.image' => 'File harus berupa gambar.',
            'bukti_pengerjaan.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $pathFoto = null;
        if ($this->bukti_pengerjaan) {
            $pathFoto = $this->bukti_pengerjaan->store('bukti-progres', 'public');
        }

        $order = Order::find($orderId);
        if ($order) {
            $order->update([
                'status' => $this->status_update,
            ]);
        }

        LacakPesanan::create([
            'id_order'     => $orderId,
            'status_order' => $this->status_update,
            'note'         => $this->catatan_progres,
            'foto_bukti'   => $pathFoto,
        ]);

        $this->reset(['status_update', 'bukti_pengerjaan', 'catatan_progres']);
        
        $order = Order::find($orderId);

        if ($order) {
            $customerUserId = $order->customer->user_id;
            broadcast(new OrderMasuk($customerUserId, 'Status pesanan Anda telah diperbarui.'))->toOthers();
        }

        session()->flash('success', 'Progres berhasil di perbarui!');
        $this->redirect(request()->header('Referer') ?? route('dashboard'));
    }

    public function tambahLayanan($orderId)
    {
        $this->validate([
            'nama_layanan_baru' => 'required|string|max:255',
            'harga_layanan_baru' => 'required|numeric|min:0',
            'deskripsi_layanan_baru' => 'nullable|string|max:1000',
            'foto_layanan_baru' => 'nullable|image|max:2048',
        ]);

        $order = Order::findOrFail($orderId);
        $hargaBaru = (int) $this->harga_layanan_baru;

        $pathFoto = null;
        if ($this->foto_layanan_baru) {
            $pathFoto = $this->foto_layanan_baru->store('layanan-tambahan', 'public');
        }

        DetailOrder::create([
            'id_order' => $order->id,
            'nama_layanan_tambahan' => $this->nama_layanan_baru,
            'harga_layanan_tambahan' => $hargaBaru,
            'deskripsi' => $this->deskripsi_layanan_baru,
            'foto' => $pathFoto,
        ]);

        // 2. Update total_harga pesanan di tabel order
        $order->increment('total_harga', $hargaBaru);

        $this->reset(['nama_layanan_baru', 'harga_layanan_baru', 'deskripsi_layanan_baru', 'foto_layanan_baru']);

        if ($order) {
            $customerUserId = $order->customer->user_id;
            broadcast(new OrderMasuk($customerUserId, 'Status pesanan Anda telah diperbarui.'))->toOthers();
        }
        
        session()->flash('success', 'Layanan/Sparepart tambahan berhasil ditambahkan!');
        $this->redirect(request()->header('Referer') ?? route('dashboard'));
    }

    public function render()
    {
        $data = Order::whereHas('jasa', function ($query) {
            $query->where('id_technician', Auth::user()->technician->id);
        })
        ->wherehas('latestStatus', function ($query) {
            $query->where('status_order', '!=', 'keranjang')
                ->where('status_order', '!=', 'menunggu_konfirmasi')
                ->where('status_order', '!=', 'dibatalkan')
                ->where('status_order', '!=', 'sudah_dibayar');
        })
        ->with([
            'latestStatus',
            'jasa', 
            'detail_order',
            'lacak_pesanan' => function ($q) {
                $q->latest();
            }
        ])
        ->paginate(10);

        // dd($data->toArray());

        return view('livewire.services.dashboard-technician.data-pesanan', [
            'data' => $data
        ]);
    }
}
