<?php

namespace App\Livewire\Services;

use App\Events\OrderMasuk;
use App\Events\PesananMasuk;
use App\Models\ChatMessages;
use App\Models\ChatRooms;
use App\Models\DetailOrder;
use App\Models\LacakPesanan;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use App\Services\OneSignalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

    public $rating = 5; 
    public $text_comment = '';
    public $foto_review; 
    public $hasReviewed = false;

    protected function getListeners()
    {
        return [
            "echo-private:servisio-chat.{$this->roomChatId},.PesananMasuk" => '$refresh',
            'refreshMessages' => '$refresh'
        ];
    }

    private function getRecipientUserId(): ?string
    {
        $room = ChatRooms::with(['technician', 'customer'])
            ->find($this->roomChatId);
        
        if (!$room) return null;

        Log::info('DEBUG getRecipientUserId', [
            'auth_id' => Auth::id(),
            'technician_user_id' => $room->technician->user_id ?? 'null',
            'customer_user_id' => $room->customer->user_id ?? 'null',
        ]);

        return (Auth::id() === $room->technician->user_id)
            ? $room->customer->user_id
            : $room->technician->user_id;
    }

    private function kirimPushNotification(string $recipientUserId, string $title, string $body, string $type): void
    {
        $url = null;
        if ($type === 'message') {
            $url = url('/chat-room/' . $this->roomChatId);
        } elseif ($type === 'order') {
            $recipient = User::find($recipientUserId);
            $url = $recipient?->role === 'technician'
                ? url('/pesanan.technician')
                : url('/lacak');
        }

        app(OneSignalService::class)->sendToUser(
            recipientUserId: $recipientUserId,
            title: $title,
            body: $body,
            data: ['type' => $type, 'room_id' => $this->roomChatId],
            url: $url
        );
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

        $this->hasReviewed = Review::where('id_order', $this->data_pesanan->order_id)->exists();
    }

    public function simpanReview()
    {
        // Pastikan hanya customer yang bisa mengeksekusi ini
        if (!auth()->user()->customer()->exists()) {
            return;
        }

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'text_comment' => 'required|string|min:5|max:1000',
            'foto_review' => 'nullable|image|max:5120', // Nullable berarti Opsional (Bisa dikosongkan)
        ], [
            'rating.required' => 'Mohon pilih jumlah bintang terlebih dahulu.',
            'rating.integer'  => 'Format penilaian harus berupa angka.',
            'rating.min'      => 'Minimal penilaian adalah 1 bintang.',
            'rating.max'      => 'Maksimal penilaian adalah 5 bintang.',

            'text_comment.required' => 'Kolom komentar atau testimoni wajib diisi.',
            'text_comment.min'      => 'Komentar terlalu pendek, berikan masukan minimal 5 karakter.',
            'text_comment.max'      => 'Komentar terlalu panjang, maksimal 1000 karakter.',

            'foto_review.image' => 'File yang diunggah harus berupa gambar (jpg, jpeg, png).',
            'foto_review.max'   => 'Ukuran foto terlalu besar, maksimal adalah 5 MB.',
        ]);

        try {

            $room_chat = ChatRooms::find($this->roomChatId);
            $order = Order::find($room_chat->order_id);

            $pathFoto = null;
            if ($this->foto_review) {
                // Simpan ke storage jika customer mengunggah foto lampiran fisik
                $pathFoto = $this->foto_review->store('review-photos', 'public');
            }

            // Daftarkan ke database review
            Review::create([
                'id_technician' => $room_chat->technician_id,
                'id_order'      => $room_chat->order_id,
                'id_jasa'       => $order->jasa->id,
                'rating'        => $this->rating,
                'text_comment'  => strip_tags($this->text_comment),
                'foto_review'   => $pathFoto ? [$pathFoto] : null, // Bungkus dalam array mengikuti cast format model
            ]);

            ChatMessages::create([
                'chat_room_id' => $this->roomChatId,
                'sender_id'    => Auth::id(),
                'message'      => "Pelanggan telah memberikan ulasan bintang {$this->rating} ⭐",
                'type'         => 'system',
                'is_read'      => false
            ]);

            $this->hasReviewed = true;
            $this->reset(['text_comment', 'foto_review']);

            session()->flash('success', 'Terima kasih! Ulasan Anda berhasil disimpan.');
            
            $recipientUserId = $this->getRecipientUserId();
            broadcast(new PesananMasuk($this->roomChatId, $recipientUserId))->toOthers();

            if ($recipientUserId) {
                $user = Auth::user()->name;
                $this->kirimPushNotification(
                    $recipientUserId,
                    "Review — Servisio",
                    "{$user} memberikan review.",
                    "message",
                );
            }
            
            $this->dispatch('scroll-to-bottom');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan ulasan: ' . $e->getMessage());
            dd('gagal menyimpan ulasan: ', $e->getMessage());
        }
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
                'foto'       => $chat->attachment,
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
                'foto_bukti' => $track->foto_bukti,
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

         $order = Order::with('jasa.technician')->find($detail->id_order);

        if (!$isApproved) {
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

        $recipientUserId = $this->getRecipientUserId();
        broadcast(new PesananMasuk($msgSystem->chat_room_id, $recipientUserId))->toOthers();

        if ($order) {
            broadcast(new OrderMasuk($order->jasa->technician->user_id))->toOthers();
        }

        if ($recipientUserId) {
            $statusTeks = $isApproved ? 'menyetujui' : 'menolak';
            $this->kirimPushNotification(
                $recipientUserId,
                'Update Layanan — Servisio',
                "Pelanggan telah {$statusTeks} penambahan item: {$detail->nama_layanan_tambahan}",
                "order"
            );
        }

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
            $recipientUserId = $this->getRecipientUserId();
            broadcast(new PesananMasuk($msgSystem->chat_room_id, $recipientUserId))->toOthers();

            broadcast(new OrderMasuk($order->jasa->technician->user_id))->toOthers();

            if ($recipientUserId) {
                $total_bayar = 'Rp ' . number_format($order->total_harga, 0, ',', '.');
                $nama_jasa = $order->jasa->nama_jasa;
                $this->kirimPushNotification(
                    $recipientUserId,
                    'Pembayaran Diterima — Servisio',
                    "Pelanggan telah menyelesaikan pembayaran sebesar {$total_bayar} untuk {$nama_jasa}.",
                    'order'
                );
            }

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

            $recipientUserId = $this->getRecipientUserId();
            broadcast(new PesananMasuk($this->roomChatId, $recipientUserId))->toOthers();

            if ($recipientUserId) {
                $type = 'message';
                $this->kirimPushNotification(
                    $recipientUserId,
                    'Foto Baru — Servisio',
                    Auth::user()->name . ' mengirim sebuah foto.',
                    $type,
                );
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengirim foto: ' . $e->getMessage());
        }
    }

    public function sendMessage()
    {
        if (!$this->photo && empty(trim($this->message))) {
            return;
        }

        if ($this->photo && empty(trim($this->message))) {
            $this->validate(['photo' => 'image|max:5120']);
        } else {
            $this->validate([
                'message' => 'required|string|max:1000',
                'photo'   => 'nullable|image|max:5120',
            ]);
        }

        try {
            $room = ChatRooms::where('id', $this->roomChatId)
                ->where(function($query) {
                    $query->whereHas('customer', fn($q) => $q->where('user_id', Auth::id()))
                        ->orWhereHas('technician', fn($q) => $q->where('user_id', Auth::id()));
                })
                ->firstOrFail();

            $message = null;

            if ($this->photo) {
                $this->validate(['photo' => 'image|max:5120']);
                $photoPath = $this->photo->store('chat-photos', 'public');
                ChatMessages::create([
                    'chat_room_id' => $room->id,
                    'sender_id'    => Auth::id(),
                    'message'      => strip_tags($this->message) ?: '',
                    'attachment'   => $photoPath,
                    'type'         => 'image',
                    'is_read'      => false
                ]);
            }

            if (!empty(trim($this->message)) && empty($this->photo)) {
                $message = ChatMessages::create([
                    'chat_room_id' => $room->id,
                    'sender_id'    => Auth::id(),
                    'message'      => strip_tags($this->message),
                    'type'         => 'text',
                    'is_read'      => false
                ]);
            }

            $room->touch();
            $this->reset(['photo', 'photoPreview', 'message']);
            $this->dispatch('scroll-to-bottom');

            $recipientUserId = $this->getRecipientUserId();
            broadcast(new PesananMasuk($this->roomChatId, $recipientUserId))->toOthers();

            if ($recipientUserId) {
                $pesanTeks = $message ? $message->message : '[Foto]';
                $preview = strlen($pesanTeks) > 60
                    ? substr($pesanTeks, 0, 60) . '...'
                    : $pesanTeks;

                $this->kirimPushNotification(
                    $recipientUserId,
                    'Pesan Baru — Servisio',
                    Auth::user()->name . ': ' . $preview,
                    'message'
                );
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
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
