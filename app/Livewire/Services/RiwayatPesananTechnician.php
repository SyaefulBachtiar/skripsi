<?php

namespace App\Livewire\Services;

use App\Models\Order;
use App\Models\Review;
use App\Models\Role_users\Technician;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RiwayatPesananTechnician extends Component
{
    public $riwayat = [];

    public function mount()
    {
        $id_technician = Technician::where('user_id', Auth::id())->value('id');
        $this->riwayat = Order::whereHas('jasa', function ($q) use ($id_technician) {
                $q->where('id_technician', $id_technician);
            })
            ->whereHas('latestStatus', function ($q) {
                $q->whereIn('status_order', ['selesai_total', 'ditolak']);
            })
            ->with([
                'jasa:id,id_technician,nama_jasa,thumbnails,harga_jasa', 
                'review:id,id_order,rating,text_comment,foto_review,created_at,created_at,reply_comment',
                'customer:id,user_id',
                'customer.user:id,name,avatar',
                'latestStatus',
            ])
            ->latest()
            ->get();

            // dd($this->riwayat->toArray());
    }

    public function replyReview($reviewId, $text)
    {
        $review = Review::findOrFail($reviewId);
        $review->update([
            'reply_comment' => $text 
        ]);
        
        session()->flash('success', 'Balasan ulasan berhasil dikirim!');

        return $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function render()
    {
        return view('livewire.services.riwayat-pesanan-technician', [
            'riwayat' => $this->riwayat,
        ]);
    }
}
