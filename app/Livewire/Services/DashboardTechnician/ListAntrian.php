<?php

namespace App\Livewire\Services\DashboardTechnician;

use App\Events\OrderMasuk;
use App\Models\ChatMessages;
use App\Models\Jasa;
use App\Models\LacakPesanan;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ListAntrian extends Component
{
    use WithPagination;

    protected function getListeners()
    {
        return [
            "echo-private:App.Models.User." . Auth::id() . ",.OrderMasuk" => '$refresh',
            "echo-private:App.Models.User." . Auth::id() . ",.PesananMasuk" => '$refresh',
            'refreshMessages' => '$refresh'
        ];
    }

    public function navigateChatMsg ($id) {
        try {
            ChatMessages::where('chat_room_id', $id)
                ->where('sender_id', '!=', Auth::id())
                ->where('is_read', false)
                ->update([
                    'is_read' => true
                ]);
            return $this->redirect(route('chat.room', ['id' => $id]), navigate: true);
        } catch (Exception $e) {
            session()->flash('error', 'gagal', $e);
            return $this->redirect(request()->header('Referer'), navigate: true);
        }
    }

    public function konfirmasi ($id_order) 
    {
        LacakPesanan::create([
            'id_order' => $id_order,
            'status_order' => 'dikonfirmasi',
        ]);

        $order = Order::find($id_order);

        // Ambil ID User Customer dari Order
        if ($order) {
            $customerUserId = $order->customer->user_id;
            broadcast(new OrderMasuk($customerUserId, 'Status pesanan Anda telah diperbarui.'))->toOthers();
        }

        session()->flash('success', 'Pesanan berhasil dikonfirmasi!');

        return $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function tolak ($id_order)
    {
        dd($id_order);
    }

    public function render()
    {

        $data = Jasa::with(['order' => function ($query) {
            $query->select('id', 'id_jasa', 'id_customer', 'order_date', 'order_time', 'keluhan', 'layanan_tambahan', 'total_harga')
                ->whereHas('latestStatus', function ($q) {
                    $q->where('status_order', 'menunggu_konfirmasi');
                })
                ->with([
                        'customer:id,user_id', 
                        'customer.user:id,name,avatar',
                        'chat_room' => function ($query) {
                                $query->select('id', 'order_id')
                                    ->withCount(['chat_message as unread_count' => function ($q) {
                                            $q->where('is_read', false)
                                                ->where('sender_id', '!=', Auth::id());
                                        }]);
                            }
                        ]);
                    }])
                ->select('id', 'id_technician', 'nama_jasa')
                ->where('id_technician', Auth::user()->technician->id)
                ->paginate(5);

        // dd($data->toArray());

        return view('livewire.services.dashboard-technician.list-antrian', [
            'data' => $data
        ]);
    }
}
