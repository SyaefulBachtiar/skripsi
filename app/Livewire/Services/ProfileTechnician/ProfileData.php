<?php

namespace App\Livewire\Services\ProfileTechnician;

use App\Models\Role_users\Technician;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileData extends Component
{

    public function render()
    {
        $data = Technician::where('user_id', Auth::id())->first();

        // dd($this->data);
        return view('livewire.services.profile-technician.profile-data', [
            'data' => $data
        ]);
    }
}
