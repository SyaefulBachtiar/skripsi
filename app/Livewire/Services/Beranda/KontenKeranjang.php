<?php

namespace App\Livewire\Services\Beranda;

use App\Models\Order;
use App\Models\Role_users\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class KontenKeranjang extends Component
{
    use WithPagination;

    public function goToDetail($id)
    {
        return $this->redirect(route('rincian.pesanan', $id), navigate: true);
    }

    public function hapusPesanan($orderId)
    {
        try {
            $customerId = Customer::where('user_id', Auth::id())->value('id');

            // Pastikan pesanan milik customer yang sedang login dan statusnya keranjang
            $order = Order::whereHas('lacak_pesanan', function ($query) {
                    $query->where('status_order', 'keranjang');
                })
                ->where('id', $orderId)
                ->where('id_customer', $customerId)
                ->latest()
                ->first();

            if (!$order) {
                session()->flash('Pesanan tidak ditemukan atau tidak dapat dihapus.');
                return $this->redirect(request()->header('Referer'), navigate: true);
            }

            $order->delete();

            session()->flash('success', 'Pesanan berhasil dihapus dari keranjang.');
            
            return $this->redirect(request()->header('Referer'), navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus pesanan: ', $e->getMessage());

            return $this->redirect(request()->header('Referer'), navigate: true);
        }
    }

    public function render()
    {
        $customerId = Customer::where('user_id', Auth::id())->value('id');

        $data =  Order::whereHas('lacak_pesanan', function ($query) {
                $query->where('status_order', 'keranjang');
            })
            ->where('id_customer', $customerId)
            ->with(['jasa:id,nama_jasa,thumbnails'])
            ->paginate(10);

        // dd($data);

        return view('livewire.services.beranda.konten-keranjang', [
            'data' => $data
        ]);
    }
}
