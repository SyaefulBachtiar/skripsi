<?php

namespace App\Livewire\Services\DashboardTechnician;

use App\Models\Jasa;
use App\Models\LacakPesanan;
use App\Models\Order;
use App\Models\Role_users\Customer;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DetailJasa extends Component
{

    public $jasa;
    public $order_date = '';
    public $order_time = '';
    public $keluhan = [];
    public $keluhan_manual = '';
    public $layanan_tambahan = [];
    public $pesanan_di_keranjang = [];

    public function mount ($id_jasa) {

        $this->jasa = Jasa::select('id', 'id_technician', 'nama_jasa', 'harga_jasa', 'deskripsi', 'thumbnails', 'ketersediaan_tanggal', 'ketersediaan_jam', 'tipe_layanan', 'is_setiap_hari', 'layanan_tambahan', 'keluhan')
            ->withAvg('review as rata_rata_rating', 'rating')
            ->withCount('review as review_count')
            ->with([
                'review' => function ($query) {
                    $query->select('id', 'id_jasa', 'text_comment')
                        ->latest()
                        ->limit(1);
                },
                'technician' => function ($query) {
                    $query->with(['user:id,name,avatar']);
                }
            ])
            ->where('active', true)
            ->findOrFail($id_jasa);

        // dd($this->jasa->toArray());

        foreach ($this->jasa->layanan_tambahan as $index => $grup) {
            $this->layanan_tambahan[$index] = [];
        }

        $keranjang = Order::whereHas('lacak_pesanan', function ($query) {
                $query->where('status_order', 'keranjang');
            })
            ->where('id_jasa', $this->jasa->id)
            ->where('id_customer', Customer::where('user_id', Auth::id())->value('id'))
            ->first();

        if($keranjang){
            $this->pesanan_di_keranjang = $keranjang;
            // dd($keranjang);
            $this->order_date = (string) Carbon::parse($keranjang->order_date)->format('Y-m-d');
            $this->order_time = substr($keranjang->order_time, 0, 5);
            $savedKeluhan = $keranjang->keluhan ?? [];
            $daftarKeluhanJasa = $this->jasa->keluhan ?? [];

            foreach ($savedKeluhan as $item) {
                if (in_array($item, $daftarKeluhanJasa)) {
                    // Jika ada di daftar, masukkan ke checkbox
                    $this->keluhan[] = $item;
                } else {
                    // Jika tidak ada di daftar, masukkan ke textarea manual
                    $this->keluhan_manual = $item;
                }
            }
            $this->layanan_tambahan = $keranjang->layanan_tambahan ?? [];
        } else {
            foreach ($this->jasa->layanan_tambahan as $index => $grup) {
                $this->layanan_tambahan[$index] = [];
            }
        }
    }

    public function submitOrder()
    {

        $validated = $this->validate([
            'order_date' => 'required|date',
            'order_time' => 'required|string',
            'keluhan' => 'required_without:keluhan_manual|array',
            'keluhan_manual' => 'required_without:keluhan|nullable|string|max:500',
            'layanan_tambahan' => 'nullable|array',
        ], [
            'order_date.required' => 'Tanggal harus dipilih',
            'order_time.required' => 'Jam harus dipilih',
            'keluhan.required_without' => 'Pilih setidaknya satu keluhan atau isi secara manual',
            'keluhan_manual.required_without' => 'Pilih keluhan di atas atau jelaskan di sini',
        ]);

        try {

            $totalHargaLayanan = 0;

            foreach ($this->layanan_tambahan as $grup) {
                foreach ($grup as $itemJson) {
                    if (!empty($itemJson)) {
                        $item = json_decode($itemJson, true);
                        // Bersihkan titik atau format rupiah jika harga berupa string "200.000"
                        $harga = (int) str_replace(['.', ','], '', $item['harga']);
                        $totalHargaLayanan += $harga;
                    }
                }
            }

            $totalKeseluruhan = $this->jasa->harga_jasa + $totalHargaLayanan;

            // Gabungkan keluhan dari checkbox dan manual
            $allKeluhan = $this->keluhan;
            if (!empty($this->keluhan_manual)) {
                $allKeluhan[] = $this->keluhan_manual;
            }

            $id_customer = Customer::where('user_id', Auth::id())->value('id');

            $orderId = Order::where('id_customer', $id_customer)
            ->where('id_jasa', $this->jasa->id)
            ->whereHas('latestStatus', function($q) {
                $q->where('status_order', 'keranjang');
            })
            ->value('id');
            
            $order = Order::updateOrCreate(
                [
                    'id' => $orderId,
                ],
                [
                    'id_customer' => $id_customer,
                    'id_jasa'     => $this->jasa->id,
                    'order_date' => $this->order_date,
                    'order_time' => $this->order_time,
                    'keluhan' => $allKeluhan,
                    'layanan_tambahan' => $this->layanan_tambahan,
                    'total_harga' => $totalKeseluruhan,
                    'tipe_layanan' => $this->jasa->tipe_layanan,
                ]
            );

            LacakPesanan::updateOrCreate([
                'id_order' => $order->id,
                'status_order' => 'keranjang',
            ]);

            // $orderData = [
            //     'id_customer' => $id_customer,
            //     'id_jasa' => $this->jasa->id,
            //     'order_date' => $this->order_date,
            //     'order_time' => $this->order_time,
            //     'keluhan' => $allKeluhan,
            //     'layanan_tambahan' => $this->layanan_tambahan,
            //     'total_harga' => $totalKeseluruhan,
            //     'status' => 'keranjang'
            // ];

            // dd($orderData);

            return $this->redirect(route('rincian.pesanan', ['id' => $order->id]), navigate: true);
            
        } catch (Exception $e) {
            session()->flash('error', 'Pesanan gagal ' . $e);
            return $this->redirect(request()->header('Referer'), navigate: true);
        }

        // dd($orderData);
    }

    public function render()
    {
        return view('livewire.services.dashboard-technician.detail-jasa');
    }
}
