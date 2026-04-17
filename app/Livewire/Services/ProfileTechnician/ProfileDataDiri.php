<?php

namespace App\Livewire\Services\ProfileTechnician;

use App\Models\Role_users\Technician;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileDataDiri extends Component
{
    public $data = [];

    public function mount () {
        $this->data = Technician::where('user_id', Auth::id())
            ->select('verifikasi', 'nama_asli', 'foto_wajah', 'foto_kegiatan')
            ->first();

        // dd($this->data); 
    }

    public function render()
    {
        return view('livewire.services.profile-technician.profile-data-diri');
    }
}
