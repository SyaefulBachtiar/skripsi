<?php

namespace App\Livewire\Services;

use App\Models\Role_users\Customer;
use App\Models\Role_users\Technician;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AddressCard extends Component
{
    public $alamat;

    public function mount () {
        $user = Auth::user();
        if($user->role === 'customer'){
            $this->alamat = Customer::where('user_id', Auth::id())->value('detail_alamat');
        } else {
            $this->alamat = Technician::where('user_id', Auth::id())->value('detail_alamat');
        }
    }

    public function render()
    {
        return view('livewire.services.address-card');
    }
}
