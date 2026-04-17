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
            $order = Order::where('id', $orderId)
                ->where('id_customer', $customerId)
                ->where('status', 'keranjang')
                ->latest()
                ->first();

            if (!$order) {
                session()->flash('Pesanan tidak ditemukan atau tidak dapat dihapus.');
                return $this->redirect(request()->header('Referer'), navigate: true);
            }

            $order->delete();

            session()->flash('error', 'Pesanan berhasil dihapus dari keranjang.');
            
            return $this->redirect(request()->header('Referer'), navigate: true);

        } catch (\Exception $e) {
            session()->flash('Gagal menghapus pesanan. Silakan coba lagi.');

            return $this->redirect(request()->header('Referer'), navigate: true);
        }
    }

    public function render()
    {
        $customerId = Customer::where('user_id', Auth::id())->value('id');

        $data =  Order::where('id_customer', $customerId)
            ->with(['jasa:id,nama_jasa,thumbnails'])
            ->where('status', 'keranjang')
            ->paginate(10);

        // dd($data);

        return view('livewire.services.beranda.konten-keranjang', [
            'data' => $data
        ]);
    }
}
