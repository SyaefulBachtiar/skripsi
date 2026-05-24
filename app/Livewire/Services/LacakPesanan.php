<?php

namespace App\Livewire\Services;

use App\Events\OrderMasuk;
use App\Events\PesananMasuk;
use App\Models\ChatMessages;
use App\Models\ChatRooms;
use App\Models\Order;
use App\Models\Review;
use App\Services\OneSignalService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class LacakPesanan extends Component
{

    use WithFileUploads;

    public $data = [];
    public $rating = 0;
    public $comment = '';
    public $foto_review = [];

    protected function getListeners()
    {
        return [
            'refreshMessages' => '$refresh'
        ];
    }

    public function mount ()
    {
        // $customerId = Customer::where('user_id', Auth::id())->value('id');

        // dd($customerId);

        $this->data = Order::whereHas('lacak_pesanan', function ($query) {
                $query->where('status_order', '!=', 'keranjang');
            })
            ->whereDoesntHave('review')
            ->whereHas('chat_room')
            ->with([
                    'detail_order',
                    'jasa:id,id_technician,nama_jasa,harga_jasa',
                    'jasa.technician:id,foto_wajah,nama_asli',
                    'chat_room' => function ($query) {
                        $query->select('id', 'order_id')
                            ->withCount(['chat_message as unread_count' => function ($q) {
                                    $q->where('is_read', false)
                                        ->where('sender_id', '!=', Auth::id());
                                }]);
                    },
                    'lacak_pesanan' => function ($q) {
                        $q->where('status_order', '!=', 'keranjang');
                    }
                    ])
            ->where('id_customer', Auth::user()->customer->id)
            ->get();


        // dd($this->data->toArray());
    }

    public function bayarPesanan($orderId)
    {
        try {
            $order = Order::with('chat_room')->findOrFail($orderId);

            // Validasi keamanan: Pastikan ini milik customer yang sedang login
            if ($order->id_customer !== Auth::user()->customer->id) {
                session()->flash('error', 'Akses ditolak.');
                return;
            }

            // Simulasi Pembayaran: Tambahkan Lacak Pesanan baru
            \App\Models\LacakPesanan::create([
                'id_order'     => $order->id,
                'status_order' => 'sudah_dibayar',
                'note'         => 'Pembayaran telah berhasil dilakukan oleh pelanggan.',
                'foto_bukti'   => null,
            ]);

            // Kirim notifikasi sistem ke Chat Room
            if ($order->chat_room) {
                ChatMessages::create([
                    'chat_room_id' => $order->chat_room->id,
                    'sender_id'    => Auth::id(), // ID Pelanggan
                    'message'      => 'Pelanggan telah menyelesaikan pembayaran sebesar Rp ' . number_format($order->total_harga, 0, ',', '.'),
                    'type'         => 'system',
                    'is_read'      => false
                ]);

                $recipientUserId = $order->chat_room->technician->user_id;
                if (!$recipientUserId) {
                    return;
                }

                broadcast(new PesananMasuk($order->chat_room->id, $recipientUserId))->toOthers();

                broadcast(new OrderMasuk($order->jasa->technician->user_id, 'Pembayaran!'))->toOthers();

                $namaJasa = $order->jasa->nama_jasa;
                $harga = number_format($order->total_harga, 0, ',', '.');
                $user = Auth::user()->name;

                app(OneSignalService::class)->sendToUser(
                    recipientUserId: $recipientUserId,
                    title: 'Pembayaran - Servisio',
                    body: "{$user} melakukan Pembayaran sebesar {$harga} untuk [{$namaJasa}].",
                    data: [
                        'type' => 'order',
                        'order_id' => $order->id,
                        'room_id'   => $order->chat_room->id,
                    ]
                );
            }

            session()->flash('success', 'Pembayaran berhasil diselesaikan!');
            
            // Redirect ulang agar tampilan update ke mode Review
            return $this->redirect(request()->header('Referer'), navigate: true);

        } catch (Exception $e) {
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function submitReview($orderId, $technicianId, $jasaId)
    {

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
            'foto_review.*' => 'image|max:2048',
            'foto_review' => 'nullable|array|max:2',
        ]);

        try {
            $uploadedPaths = [];
            if ($this->foto_review) {
                foreach ($this->foto_review as $photo) {
                    $uploadedPaths[] = $photo->store('reviews', 'public');
                }
            }

            Review::create([
                'id_order' => $orderId,
                'id_technician' => $technicianId,
                'id_jasa' => $jasaId,
                'rating' => $this->rating,
                'text_comment' => $this->comment,
                'foto_review' => $uploadedPaths
            ]);

            $room = ChatRooms::where('order_id', $orderId)->first();
            $order = Order::find($orderId);
            

            ChatMessages::create([
                'chat_room_id' => $room->id,
                'sender_id'    => Auth::id(),
                'message'      => "Pelanggan telah memberikan ulasan bintang {$this->rating} ⭐",
                'type'         => 'system',
                'is_read'      => false
            ]);
            
            broadcast(new PesananMasuk($room->id, $room->technician->user_id))->toOthers();

            broadcast(new OrderMasuk($order->jasa->technician->user_id, 'Pembayaran!'))->toOthers();

            $user = Auth::user()->name;

            app(OneSignalService::class)->sendToUser(
                recipientUserId: $room->technician->user_id,
                title: 'Review - Servisio',
                body: "{ $user } memberikan review.",
                data: [
                    'type' => 'message',
                    'order_id' => $order->id,
                    'room_id'   => $room->id,
                ]
            );

            session()->flash('success', 'Ulasan berhasil dikirim, silahkan lihat di Riwayat!');

            return $this->redirect(request()->header('Referer'), navigate: true);

        } catch (Exception $e) {
            session()->flash('error', 'Gagal memberikan penilaian '. $e);
            return $this->redirect(request()->header('Referer'), navigate: true);
        }


    }

    public function removePhoto($index)
    {
        array_splice($this->foto_review, $index, 1);
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

    public function render()
    {
        return view('livewire.services.lacak-pesanan');
    }
}
