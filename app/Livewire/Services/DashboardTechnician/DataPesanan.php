<?php

namespace App\Livewire\Services\DashboardTechnician;

use App\Events\OrderMasuk;
use App\Events\PesananMasuk;
use App\Models\ChatMessages;
use App\Models\ChatRooms;
use App\Models\DetailOrder;
use App\Models\LacakPesanan;
use App\Models\Order;
use App\Services\OneSignalService;
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

    protected function getListeners()
    {
        return [
            'refreshMessages' => '$refresh'
        ];
    }

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

        $statusMentah = $this->status_update;
        $catatanTeknisi = $this->catatan_progres;

        $this->reset(['status_update', 'bukti_pengerjaan', 'catatan_progres']);
        
        $order = Order::find($orderId);

        if ($order) {
            $customerUserId = $order->customer->user_id;
            broadcast(new OrderMasuk($customerUserId, 'Status pesanan Anda telah diperbarui.'))->toOthers();

            $statusFormat = ucwords(str_replace('_', ' ', $statusMentah)); 
        
            $bodyNotification = $catatanTeknisi 
                ? "Status: {$statusFormat}. \"{$catatanTeknisi}\""
                : "Status servis perangkat Anda saat ini: Kategori [{$statusFormat}].";

            app(OneSignalService::class)->sendToUser(
                recipientUserId: $customerUserId,
                title: '📦 Update Progres Servis',
                body: $bodyNotification,
                data: [
                    'type' => 'order',
                    'order_id' => $order->id,
                ]
            );
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

        $DetailOrder = DetailOrder::create([
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

            $namaItem = $DetailOrder->nama_layanan_tambahan;
            $biayaFormat = 'Rp ' . number_format($hargaBaru, 0, ',', '.');

            app(OneSignalService::class)->sendToUser(
                recipientUserId: $customerUserId,
                title: '🛠️ Persetujuan Biaya Tambahan - Servisio',
                body: "Teknisi mengajukan: {$namaItem} ({$biayaFormat}). Silahkan cek untuk menyetujui.",
                data: [
                    'type' => 'pesan',
                    'room_id' => $order->chat_room->id ?? null
                ]
            );
        }
        
        session()->flash('success', 'Layanan/Sparepart tambahan berhasil ditambahkan!');
        $this->redirect(request()->header('Referer') ?? route('dashboard'));
    }

    public function konfirmasiPembayaran($orderId)
    {
        try {
            $order = \App\Models\Order::findOrFail($orderId);

            // Tambah status baru di tracking timeline
            \App\Models\LacakPesanan::create([
                'id_order' => $order->id,
                'status_order' => 'selesai_total', // sesuaikan dengan enum status skripsi kamu
                'note' => 'Pembayaran telah diverifikasi oleh teknisi. Terima kasih telah menggunakan Servisio!',
            ]);

            $chatRoomId = ChatRooms::where('order_id', $order->id)->value('id');
            if ($chatRoomId) {
                ChatMessages::create([
                    'chat_room_id' => $chatRoomId,
                    'sender_id' => Auth::id(),
                    'message' => 'Pembayaran Anda telah diverifikasi oleh teknisi. Transaksi selesai!',
                    'is_read' => false
                ]);

                $room = ChatRooms::with(['technician', 'customer'])->find($chatRoomId);
                $recipientUserId = $room ? $room->customer->user_id : null;

                broadcast(new PesananMasuk($chatRoomId, $recipientUserId))->toOthers();
            }

            broadcast(new OrderMasuk($order->customer->user_id, 'Pembayaran Anda telah diverifikasi oleh teknisi!'))->toOthers();

            if ($order) {
                $customerUserId = $order->customer->user_id;
                $namaJasa = $order->jasa->nama_jasa ?? 'Layanan';
                $totalBayar = 'Rp ' . number_format($order->total_harga, 0, ',', '.');

                app(OneSignalService::class)->sendToUser(
                    recipientUserId: $customerUserId,
                    title: '✅ Pembayaran Berhasil Diverifikasi',
                    body: "Terima kasih! Pembayaran {$totalBayar} untuk [{$namaJasa}] telah sah diterima. Transaksi selesai.",
                    data: [
                        'type' => 'pesan',
                        'room_id' => $chatRoomId
                    ]
                );
            }

            session()->flash('success', 'Pembayaran berhasil dikonfirmasi!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memverifikasi pembayaran: ' . $e->getMessage());
        }
    }

    public function tolakPembayaran($orderId)
    {
        
        try {
            $order = Order::with('customer')->findOrFail($orderId);

            // Kembalikan status ke 'selesai' (Menunggu Pembayaran kembali)
            LacakPesanan::create([
                'id_order' => $order->id,
                'status_order' => 'pembayaran_ditolak', // Status dikembalikan agar tombol bayar di customer muncul lagi
                'note' => 'Pembayaran ditolak teknisi karena dana belum masuk atau bukti tidak valid.',
            ]);

            // Kirim peringatan otomatis ke Chat Room
            $chatRoomId = ChatRooms::where('order_id', $order->id)->value('id');
            if ($chatRoomId) {
                ChatMessages::create([
                    'chat_room_id' => $chatRoomId,
                    'sender_id'    => Auth::id(),
                    'message'      => '⚠️ Peringatan Sistem: Teknisi menolak konfirmasi pembayaran Anda karena dana belum masuk atau bukti tidak valid. Silakan kirimkan bukti transfer yang sah di sini atau lakukan pembayaran ulang.',
                    'is_read'      => false
                ]);
            
                $room = ChatRooms::with(['technician', 'customer'])->find($chatRoomId);
                $recipientUserId = $room ? $room->customer->user_id : null;
                broadcast(new PesananMasuk($chatRoomId, $recipientUserId))->toOthers();
            }

            // Beritahu customer secara realtime agar halaman Lacak Pesanan mereka ter-refresh
            $customerUserId = $order->customer->user_id;
            broadcast(new OrderMasuk($customerUserId, 'Pembayaran Anda ditolak oleh teknisi. Silakan cek detail pesanan.'))->toOthers();

            if ($order) {
                $namaJasa = $order->jasa->nama_jasa ?? 'Layanan';

                app(OneSignalService::class)->sendToUser(
                    recipientUserId: $customerUserId,
                    title: '⚠️ Verifikasi Pembayaran Gagal',
                    body: "Pembayaran untuk [{$namaJasa}] ditolak teknisi karena bukti tidak valid/belum masuk.",
                    data: [
                        'type' => 'pesan',
                        'room_id' => $chatRoomId
                    ]
                );
            }

            session()->flash('warning', 'Pembayaran ditolak. Status pesanan dikembalikan menjadi Menunggu Pembayaran.');

            return $this->redirect(request()->header('Referer'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
            return $this->redirect(request()->header('Referer'), navigate: true);
        }
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
                ->where('status_order', '!=', 'selesai_total');
        })
        ->with([
            'latestStatus',
            'jasa', 
            'detail_order',
            'customer.user',
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
