<?php

namespace App\Livewire\Services\DashboardTechnician;

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

        LacakPesanan::create([
            'id_order'     => $orderId,
            'status_order' => $this->status_update,
            'note'         => $this->catatan_progres,
            'foto_bukti'   => $pathFoto,
        ]);

        $this->reset(['status_update', 'bukti_pengerjaan', 'catatan_progres']);
        
        session()->flash('success', 'Progres berhasil di perbarui!');
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
                ->where('status', '!=', 'selesai');
        })
        ->with(['latestStatus'])
        ->paginate(10);

        // dd($data->toArray());

        return view('livewire.services.dashboard-technician.data-pesanan', [
            'data' => $data
        ]);
    }
}
