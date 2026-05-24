<?php

namespace App\Livewire\Services\DashboardTechnician;

use App\Events\OrderMasuk;
use App\Events\PesananMasuk;
use App\Models\ChatMessages;
use App\Models\ChatRooms;
use App\Models\Jasa;
use App\Models\LacakPesanan;
use App\Models\Order;
use App\Services\OneSignalService;
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

        $order = Order::with('jasa', 'customer')->find($id_order);

        if (!$order) {
            session()->flash('error', 'Data pesanan tidak ditemukan.');
            return $this->redirect(request()->header('Referer'), navigate: true);
        }

        $tipe = $order->jasa->tipe_layanan;

        if ($tipe === 'panggilan') {
            $pesanOtomatis = "Terimakasih telah menunggu. Pesanan Anda telah dikonfirmasi. Teknisi akan datang ke lokasi Anda sesuai jadwal yang telah dipilih. Mohon pastikan ada orang di lokasi saat teknisi tiba.";
        } else {
            $pesanOtomatis = "Terimakasih telah menunggu. Pesanan Anda telah dikonfirmasi. Silakan bawa perangkat Anda ke lokasi bengkel kami sesuai dengan jadwal yang telah Anda pilih.";
        }

        $chatRoomId = ChatRooms::where('order_id', $order->id)->value('id');

        if ($chatRoomId) {
            ChatMessages::create([
                'chat_room_id' => $chatRoomId,
                'sender_id'    => Auth::id(),
                'message'      => $pesanOtomatis,
                'is_read'      => false
            ]);

            $room = ChatRooms::with(['technician', 'customer'])->find($chatRoomId);
            $recipientUserId = $room ? $room->customer->user_id : null;

            broadcast(new PesananMasuk($chatRoomId, $recipientUserId))->toOthers();
        }

        $customerUserId = $order->customer->user_id;
        broadcast(new OrderMasuk($customerUserId, 'Status pesanan Anda telah diperbarui.'))->toOthers();        

        app(OneSignalService::class)->sendToUser(
            recipientUserId: $customerUserId,
            title: '✅ Pesanan Dikonfirmasi — Servisio',
            body: 'Pesanan Anda telah dikonfirmasi oleh teknisi. Silakan cek detail pesanan.',
            data: ['type' => 'message', 'room_id' => $chatRoomId],
            url: $chatRoomId ? url('/chat-room/' . $chatRoomId) : url('/lacak')
        );


        session()->flash('success', 'Pesanan berhasil dikonfirmasi!');

        return $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function tolak ($id_order)
    {
        LacakPesanan::create([
            'id_order' => $id_order,
            'status_order' => 'ditolak',
        ]);

        $order = Order::with('jasa', 'customer')->find($id_order);

        if (!$order) {
            session()->flash('error', 'Data pesanan tidak ditemukan.');
            return $this->redirect(request()->header('Referer'), navigate: true);
        }

        $chatRoomId = ChatRooms::where('order_id', $order->id)->value('id');

        if ($chatRoomId) {
            ChatMessages::create([
                'chat_room_id' => $chatRoomId,
                'sender_id'    => Auth::id(),
                'message'      => 'Pesanan anda di tolak',
                'is_read'      => false
            ]);

            $room = ChatRooms::with(['technician', 'customer'])->find($chatRoomId);
            $recipientUserId = $room ? $room->customer->user_id : null;

            broadcast(new PesananMasuk($chatRoomId, $recipientUserId))->toOthers();
        }

        $customerUserId = $order->customer->user_id;
        broadcast(new OrderMasuk($customerUserId, 'Status pesanan Anda telah diperbarui.'))->toOthers();        

        app(OneSignalService::class)->sendToUser(
            recipientUserId: $customerUserId,
            title: 'Pesanan Ditolak — Servisio',
            body: 'Pesanan Anda telah ditolak oleh teknisi.',
            data: ['type' => 'message', 'room_id' => $chatRoomId],
            url: $chatRoomId ? url('/chat-room/' . $chatRoomId) : url('/lacak')
        );

        session()->flash('success', 'Pesanan berhasil ditolak.');
        return $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function render()
    {

        $data = Jasa::with(['order' => function ($query) {
            $query->select('id', 'id_jasa', 'id_customer', 'order_date', 'order_time', 'keluhan', 'layanan_tambahan', 'total_harga')
                ->whereHas('latestStatus', function ($q) {
                    $q->where('status_order', 'menunggu_konfirmasi');
                })
                ->with([
                        'customer:id,user_id,detail_alamat,kecamatan,kelurahan,kabupaten,latitude,longitude', 
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
                ->select('id', 'id_technician', 'nama_jasa', 'tipe_layanan')
                ->where('id_technician', Auth::user()->technician->id)
                ->paginate(5);

        // dd($data->toArray());

        return view('livewire.services.dashboard-technician.list-antrian', [
            'data' => $data
        ]);
    }
}
