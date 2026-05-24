<?php

namespace App\Livewire\Services\Beranda;

use App\Events\OrderMasuk;
use App\Models\ChatMessages;
use App\Models\ChatRooms;
use App\Models\LacakPesanan;
use App\Models\Order;
use App\Services\OneSignalService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KontenRincianPesanan extends Component
{
    public $id_order;
    public $order;
    public $layanan_tambahan = [];

    public function mount () 
    {
        $this->order = Order::with(['jasa.technician.user', 'customer.user', 'lacak_pesanan' => function ($query) {
                $query->latest();
            }])
            ->whereHas('customer', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('id', $this->id_order)
            ->first();

        // dd($this->order->lacak_pesanan);

        if (!$this->order) {
            abort(404, 'Pesanan tidak ditemukan');
        }

        $this->layanan_tambahan = $this->order->layanan_tambahan ?? [];
    }

    public function checkout()
    {
        try {
            $this->order->update([
                    'layanan_tambahan' => $this->layanan_tambahan,
                    'status' => 'menunggu_konfirmasi'
                ]);

            $chatRoom = ChatRooms::updateOrCreate(
                [
                    'order_id' => $this->order->id,
                ],
                [
                    'customer_id'   => $this->order->id_customer,
                    'technician_id' => $this->order->jasa->technician->id,
                ]
            );

            if ($chatRoom->wasRecentlyCreated) {
                ChatMessages::create([
                    'chat_room_id'  => $chatRoom->id,
                    'sender_id'     => $this->order->jasa->technician->user_id,
                    'message'       => 'Terimakasih telah memesan jasa kami. Saat ini Anda sedang menunggu konfirmasi dari teknisi untuk melanjutkan layanan sesuai jadwal yang telah dipilih. Mohon menunggu agar proses penjadwalan dapat segera diproses.',
                    'is_read'       => false
                ]);
            }

            LacakPesanan::updateOrCreate(
                [
                    'id_order' => $this->order->id
                ],
                [
                    'status_order' => 'menunggu_konfirmasi'
                ]
            );
            
            // Ambil ID User Teknisi dari Jasa
            $technicianUserId = $this->order->jasa->technician->user_id;
            broadcast(new OrderMasuk($technicianUserId, 'Ada pesanan baru masuk!'))->toOthers();

            $namaJasa = $this->order->jasa->nama_jasa ?? 'Layanan Servis';
            $namaCustomer = $this->order->customer->user->name ?? 'Customer';

            app(OneSignalService::class)->sendToUser(
                recipientUserId: $technicianUserId,
                title: '🔔 Pesanan Baru Masuk!',
                body: "{$namaCustomer} telah memesan jasa [{$namaJasa}]. Segera cek aplikasi untuk melakukan konfirmasi.",
                data: [
                    'type' => 'order',
                    'order_id' => $this->order->id,
                    'room_chat_id' => $chatRoom->id
                ],
                url: url('/teknisi/dashboard')
            );

            return $this->redirect(route('chat.room', ['id' => $chatRoom->id]), navigate: true);

        } catch (Exception $e) {
            session()->flash('error', 'Checkout gagal ' . $e);
            return $this->redirect(request()->header('Referer'), navigate: true);
        }
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
