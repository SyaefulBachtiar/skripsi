<?php

namespace App\Livewire\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RiwayatPesanan extends Component
{

    public $riwayat = [];

    public function mount()
    {
        $this->riwayat = Order::whereHas('customer', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereHas('review') // Hanya yang sudah di-review
            ->with([
                'jasa:id,id_technician,nama_jasa,thumbnails,harga_jasa', 
                'jasa.technician:id,nama_asli,foto_wajah',
                'review:id,id_order,rating,text_comment,foto_review,created_at',
                'customer:id,user_id',
                'customer.user:id,avatar',
                'detail_order',  
                'lacak_pesanan'  
            ])
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.services.riwayat-pesanan');
    }
}
