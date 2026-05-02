<?php

namespace App\Livewire\Services\DashboardTechnician;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class QuickAccess extends Component
{
    public $count_pesan = 0;

    public function mount ()
    {
        $this->count_pesan = Order::whereHas('jasa', function ($query) {
            $query->where('id_technician', Auth::user()->technician->id);
        })
        ->whereHas('latestStatus', function ($q) {
            $q->where('status_order', 'dikonfirmasi');
        })
        ->count();
    }

    public function render()
    {
        return view('livewire.services.dashboard-technician.quick-access');
    }
}
