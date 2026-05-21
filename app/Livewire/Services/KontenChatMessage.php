<?php

namespace App\Livewire\Services;

use App\Events\OrderMasuk;
use App\Events\PesananMasuk;
use App\Models\ChatMessages;
use App\Models\ChatRooms;
use App\Models\DetailOrder;
use App\Models\LacakPesanan;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class KontenChatMessage extends Component
{

    use WithFileUploads;

    public $roomChatId;
    public $message = '';
    public $data_pesanan;

    public $photo;
    public $photoPreview;

    protected function getListeners()
    {
        return [
            "echo-private:servisio-chat.{$this->roomChatId},.PesananMasuk" => '$refresh',
            "echo-private:App.Models.User." . Auth::id() . ",.OrderMasuk" => '$refresh',
            'refreshMessages' => '$refresh'
        ];
    }

    public function mount () {

        ChatMessages::where('chat_room_id', $this->roomChatId)
        ->where('sender_id', '!=', Auth::id())
        ->where('is_read', false)
        ->update(['is_read' => true]);

        $this->data_pesanan = ChatRooms::where('id', $this->roomChatId)
            ->select('id', 'order_id')
            ->with([
                    'order:id,id_jasa,order_date,order_time,total_harga',
                    'order.jasa:id,nama_jasa,thumbnails',
                    'order.lacak_pesanan' => function ($q) {
                        $q->select('id','id_order', 'status_order')
                            ->latest();
                    }
                ])
            ->first();

        if (!$this->data_pesanan) {
            abort(404, 'Percakapan tidak ditemukan');
        }

            // dd($this->data_pesanan->toArray());
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:5120',
        ]);

        // Buat preview
        $this->photoPreview = $this->photo->temporaryUrl();
    }

    public function removePhoto()
    {
        $this->reset(['photo', 'photoPreview']);
    }

    public function getMessagesProperty()
    {
        // dd($this->data_pesanan->order_id);
        // 1. Ambil data pesan teks/gambar
        $chats = ChatMessages::where('chat_room_id', $this->roomChatId)->get();
        
        // 2. Ambil data riwayat pelacakan pesanan
        $trackings = LacakPesanan::where('id_order', $this->data_pesanan->order_id)->get();

        // 3. Buat penampung baru untuk menggabungkan keduanya
        $detailOrders = DetailOrder::where('id_order', $this->data_pesanan->order_id)->get();
        
        $merged = collect();

        // Memasukkan chat biasa
        foreach ($chats as $chat) {
            $merged->push((object)[
                'is_system'  => $chat->type === 'system',
                'id'         => 'chat_'.$chat->id,
                'sender_id'  => $chat->sender_id,
                'message'    => $chat->message,
                'type'       => $chat->type,
                'is_read'    => $chat->is_read,
                'created_at' => Carbon::parse($chat->created_at),
            ]);
        }

        // Memasukkan riwayat pelacakan pesanan sebagai "Pesan Sistem"
        foreach ($trackings as $track) {
            $merged->push((object)[
                'is_system'  => true,
                'id'         => 'track_'.$track->id,
                'message'    => ucwords(str_replace('_', ' ', $track->status_order)),
                'note'       => $track->note ?? null,
                'type'       => 'status',
                'created_at' => Carbon::parse($track->created_at),
            ]);
        }

        // Memasukkan permintaan layanan tambahan
        foreach ($detailOrders as $detail) {
            $merged->push((object)[
                'is_system'      => true,
                'id'             => 'detail_'.$detail->id,
                'type'           => 'layanan_tambahan',
                'detail_layanan' => $detail, // Simpan objek utuh untuk dibaca di frontend
                'created_at'     => Carbon::parse($detail->created_at),
            ]);
        }

        // 4. Urutkan semuanya berdasarkan waktu kejadian secara Ascending (dari lama ke terbaru)
        return $merged->sortBy('created_at')->values();
    }

    public function respondLayanan($detailId, $isApproved)
    {
        $detail = DetailOrder::findOrFail($detailId);

        // Jika sudah pernah direspon, batalkan
        if ($detail->acc_customer !== null) {
            return;
        }

        // Update status persetujuan
        $detail->update(['acc_customer' => $isApproved]);

        // Jika ditolak, kembalikan (kurangi) harga pesanan karena sebelumnya sudah ditambah oleh teknisi
        if (!$isApproved) {
            $order = Order::find($detail->id_order);
            if ($order) {
                $order->decrement('total_harga', $detail->harga_layanan_tambahan);
            }
        }

        // Kirim pesan notifikasi ke chat
        $statusTeks = $isApproved ? 'menyetujui' : 'menolak';
        $msgSystem = ChatMessages::create([
            'chat_room_id' => $this->roomChatId,
            'sender_id'    => Auth::id(), // ID Pelanggan
            'message'      => "Pelanggan telah {$statusTeks} penambahan item: {$detail->nama_layanan_tambahan}",
            'type'         => 'system',
            'is_read'      => false
        ]);

        broadcast(new PesananMasuk($msgSystem->chat_room_id))->toOthers();

        broadcast(new OrderMasuk($order->jasa->technician->user_id))->toOthers();

        // Refresh data agar total harga terupdate di header chat
        $this->data_pesanan->refresh();
        $this->dispatch('scroll-to-bottom');
    }

    public function bayar()
    {
        try {
            // 1. Ambil data order
            $order = \App\Models\Order::findOrFail($this->data_pesanan->order_id);

            // 2. Validasi Keamanan: Pastikan hanya customer pemilik order yang bisa melakukan pembayaran
            if ($order->customer->user_id !== Auth::id()) {
                session()->flash('error', 'Akses ditolak. Anda tidak berhak memproses pembayaran ini.');
                return;
            }

            // 3. Pastikan statusnya memang sudah selesai/menunggu pembayaran
            $statusTerakhir = $order->lacak_pesanan()->latest()->first();
            if (!$statusTerakhir || $statusTerakhir->status_order !== 'selesai') {
                session()->flash('error', 'Pesanan belum selesai dikerjakan oleh teknisi.');
                return;
            }

            // 4. SIMULASI PEMBAYARAN: Langsung catat ke Lacak Pesanan
            LacakPesanan::create([
                'id_order'     => $order->id,
                'status_order' => 'sudah_dibayar',
                'note'         => 'Pembayaran telah berhasil dilakukan oleh pelanggan via sistem (Simulasi).',
                'foto_bukti'   => null,
            ]);

            // 5. Kirim Notifikasi ke Chat Room (Pesan Sistem)
            $msgSystem = ChatMessages::create([
                'chat_room_id' => $this->roomChatId,
                'sender_id'    => Auth::id(), // ID Pelanggan
                'message'      => 'Pelanggan telah menyelesaikan pembayaran sebesar Rp ' . number_format($order->total_harga, 0, ',', '.'),
                'type'         => 'system',
                'is_read'      => false
            ]);
            // dd($order->jasa->technician->user_id);
            broadcast(new PesananMasuk($msgSystem->chat_room_id))->toOthers();

            broadcast(new OrderMasuk($order->jasa->technician->user_id))->toOthers();

            $this->data_pesanan->refresh();
            
            session()->flash('success', 'Pembayaran berhasil diselesaikan!');
            
            // Redirect kembali ke halaman ini sendiri agar tampilan refresh
            return $this->redirect(request()->header('Referer'), navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function sendPhotoMessage()
    {
        $this->validate([
            'photo' => 'required|image|max:5120',
        ]);

        try {
            $room = ChatRooms::where('id', $this->roomChatId)
                ->where(function($query) {
                    $query->whereHas('customer', function($q) {
                        $q->where('user_id', Auth::id());
                    })
                    ->orWhereHas('technician', function($q) {
                        $q->where('user_id', Auth::id());
                    });
                })
                ->firstOrFail();

            // Simpan foto ke storage
            $photoPath = $this->photo->store('chat-photos', 'public');

            ChatMessages::create([
                'chat_room_id' => $room->id,
                'sender_id'    => Auth::id(),
                'message'      => $photoPath,
                'type'         => 'image',
                'is_read'      => false
            ]);

            $room->touch();
            $this->reset(['photo', 'photoPreview', 'message']);
            $this->dispatch('scroll-to-bottom');

            broadcast(new PesananMasuk($this->roomChatId))->toOthers();

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengirim foto: ' . $e->getMessage());
        }
    }

    public function sendMessage()
    {
        if (!$this->photo && empty(trim($this->message))) {
            return;
        }

        $validatedData = $this->validate([
            'message' => 'required|string|max:1000',
        ]);


        try {
            $room = ChatRooms::where('id', $this->roomChatId)
                ->where(function($query) {
                    $query->whereHas('customer', function($q) {
                        $q->where('user_id', Auth::id());
                    })
                    ->orWhereHas('technician', function($q) {
                        $q->where('user_id', Auth::id());
                    });
                })
                ->firstOrFail();

            if ($this->photo) {
                $this->validate(['photo' => 'image|max:5120']);
                
                $photoPath = $this->photo->store('chat-photos', 'public');

                ChatMessages::create([
                    'chat_room_id' => $room->id,
                    'sender_id'    => Auth::id(),
                    'message'      => $photoPath,
                    'type'         => 'image',
                    'is_read'      => false
                ]);
            }

            if (!empty(trim($this->message))) {
                $this->validate(['message' => 'required|string|max:1000']);

                ChatMessages::create([
                    'chat_room_id' => $room->id,
                    'sender_id'    => Auth::id(),
                    'message'      => strip_tags($this->message),
                    'type'         => 'text', // Pastikan type default adalah text
                    'is_read'      => false
                ]);
            }

            $room->touch();
            $this->reset(['photo', 'photoPreview', 'message']);
            $this->dispatch('scroll-to-bottom');

            broadcast(new PesananMasuk($this->roomChatId))->toOthers();

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Jika user mencoba kirim pesan ke room yang bukan miliknya
            session()->flash('error', 'Akses ditolak atau percakapan tidak ditemukan.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengirim pesan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.services.konten-chat-message', [
            'messages' => $this->messages
        ]);
    }
}
