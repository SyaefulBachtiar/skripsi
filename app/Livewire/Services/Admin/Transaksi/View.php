<?php

namespace App\Livewire\Services\Admin\Transaksi;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class View extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data_users_transaksi = Order::select('id', 'id_customer', 'id_jasa', 'order_date', 'total_harga', 'created_at')
            ->when(trim($this->search), function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereHas('customer.user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })->orWhereHas('jasa', function ($q) {
                        $q->where('nama_jasa', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->with([
                'jasa:id,nama_jasa,thumbnails',
                'customer:id,user_id',
                'customer.user:id,name,avatar',
                'latestStatus'
            ])
            ->latest() 
            ->paginate(10);


        return view('livewire.services.admin.transaksi.view', [
            'data_transaksi' => $data_users_transaksi,
        ]);
    }
}
