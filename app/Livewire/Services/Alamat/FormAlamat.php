<?php

namespace App\Livewire\Services\Alamat;

use App\Models\Role_users\Customer;
use App\Models\Role_users\Technician;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FormAlamat extends Component
{

    public $latitude;
    public $longitude;
    public $detail_alamat;

    public function mount () {
        $user = Auth::user();
        $userRole = '';

        if($user->role === 'customer') {
            $userRole = Customer::where('user_id', Auth::id())->first();
        } else {
            $userRole = Technician::where('user_id', Auth::id())->first();
        }

        if($userRole){
            $this->latitude = $userRole->latitude;
            $this->longitude = $userRole->longitude;
            $this->detail_alamat = $userRole->detail_alamat;
        }else {
            $this->latitude = -6.3227;
            $this->longitude = 107.3376;
        }
    }

    public function save()
    {
        $this->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'detail_alamat' => 'required|string|max:500'
        ]);

        try {
            $user = Auth::user();
            $data = [
                'detail_alamat' => $this->detail_alamat,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ];

            if($user->role === 'customer'){
                Customer::updateOrCreate(['user_id' => Auth::id()], $data);
            } else {
                Technician::updateOrCreate(['user_id' => Auth::id()], $data);
            }

            session()->flash('success', 'Alamat berhasil diperbarui!');

            // Refresh halaman saat ini
            return $this->redirect(request()->header('Referer'), navigate: true);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.services.alamat.form-alamat');
    }
}
