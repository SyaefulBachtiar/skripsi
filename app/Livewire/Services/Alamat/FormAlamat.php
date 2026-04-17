<?php

namespace App\Livewire\Services\Alamat;

use App\Models\Role_users\Customer;
use App\Models\Role_users\Technician;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;
use Livewire\Component;

class FormAlamat extends Component
{

    public $latitude, $longitude, $detail_alamat;
    
    // Properti untuk Wilayah
    public $provinsi = '', $kabupaten = '', $kecamatan = '', $kelurahan = '';
    public $provinces = [], $cities = [], $districts = [], $villages = [];
    public $searchProvinsi = '', $searchKabupaten = '', $searchKecamatan = '', $searchKelurahan = '';

    public function mount () {
       $user = Auth::user();
        $profile = ($user->role === 'customer') 
            ? Customer::where('user_id', $user->id)->first() 
            : Technician::where('user_id', $user->id)->first();

        $this->provinces = Province::orderBy('name')->get();

        if ($profile) {
            $this->latitude = $profile->latitude;
            $this->longitude = $profile->longitude;
            $this->detail_alamat = $profile->detail_alamat;
            $this->provinsi = $profile->provinsi;
            $this->kabupaten = $profile->kabupaten;
            $this->kecamatan = $profile->kecamatan;
            $this->kelurahan = $profile->kelurahan;

            $this->searchProvinsi  = $profile->provinsi  ?? '';
            $this->searchKabupaten = $profile->kabupaten ?? '';
            $this->searchKecamatan = $profile->kecamatan ?? '';
            $this->searchKelurahan = $profile->kelurahan ?? '';

            // Load data awal untuk dropdown jika sudah ada data
            if($this->provinsi) $this->cities = City::whereHas('province', fn($q) => $q->where('name', $this->provinsi))->get();
            if($this->kabupaten) $this->districts = District::whereHas('city', fn($q) => $q->where('name', $this->kabupaten))->get();
            if($this->kecamatan) $this->villages = Village::whereHas('district', fn($q) => $q->where('name', $this->kecamatan))->get();
        } else {
            $this->latitude = -6.3227;
            $this->longitude = 107.3376;
        }
    }

    public function selectOption($target, $name)
    {
        $this->$target = $name;
        
        $searchVar = 'search' . ucfirst($target);
        $this->$searchVar = $name;

        if ($target === 'provinsi') {
            // Reset level bawah
            $this->kabupaten = $this->kecamatan = $this->kelurahan = '';
            $this->searchKabupaten = $this->searchKecamatan = $this->searchKelurahan = '';
            $this->districts = $this->villages = [];
            $this->updatedProvinsi($name);
        }
        if ($target === 'kabupaten') {
            $this->kecamatan = $this->kelurahan = '';
            $this->searchKecamatan = $this->searchKelurahan = '';
            $this->villages = [];
            $this->updatedKabupaten($name);
        }
        if ($target === 'kecamatan') {
            $this->kelurahan = '';
            $this->searchKelurahan = '';
            $this->updatedKecamatan($name);
        }
        if ($target === 'kelurahan') {
            // tidak ada level bawah
        }
    }
    // Hook: Saat Provinsi berubah
    public function updatedProvinsi($name)
    {
        $this->cities = City::whereHas('province', fn($q) => $q->where('name', $name))->orderBy('name')->get();
        $this->kabupaten = $this->kecamatan = $this->kelurahan = null;
        $this->districts = $this->villages = [];
    }

    // Hook: Saat Kabupaten berubah
    public function updatedKabupaten($name)
    {
        $this->districts = District::whereHas('city', fn($q) => $q->where('name', $name))->orderBy('name')->get();
        $this->kecamatan = $this->kelurahan = null;
        $this->villages = [];
    }

    // Hook: Saat Kecamatan berubah
    public function updatedKecamatan($name)
    {
        $this->villages = Village::whereHas('district', fn($q) => $q->where('name', $name))->orderBy('name')->get();
        $this->kelurahan = null;
    }

    public function save()
    {
        $this->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'detail_alamat' => 'required|string|max:500',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'kelurahan' => 'required',
        ]);

        $customer_verif = Technician::where('user_id', Auth::id())->value('verifikasi');

        if($customer_verif === 'ditolak') {
            Technician::where('user_id', Auth::id())
            ->update([
                'verifikasi' => 'diproses'
            ]);
        }

        try {
            $data = [
                'detail_alamat' => $this->detail_alamat,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'provinsi' => $this->provinsi,
                'kabupaten' => $this->kabupaten,
                'kecamatan' => $this->kecamatan,
                'kelurahan' => $this->kelurahan,
            ];

            $model = Auth::user()->role === 'customer' ? Customer::class : Technician::class;
            $model::updateOrCreate(['user_id' => Auth::id()], $data);

            session()->flash('success', 'Alamat berhasil diperbarui!');
            return $this->redirect(request()->header('Referer'), navigate: true);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {

        // Filter Provinsi berdasarkan pencarian
        $this->provinces = Province::where('name', 'like', '%' . $this->searchProvinsi . '%')
            ->orderBy('name')->limit(15)->get();

        // Hanya query ulang jika sedang dalam mode pencarian (bukan setelah pilih)
        if ($this->provinsi && $this->searchKabupaten !== $this->kabupaten) {
            $this->cities = City::whereHas('province', fn($q) => $q->where('name', $this->provinsi))
                ->where('name', 'like', '%' . $this->searchKabupaten . '%')
                ->orderBy('name')->limit(15)->get();
        }

        if ($this->kabupaten && $this->searchKecamatan !== $this->kecamatan) {
            $this->districts = District::whereHas('city', fn($q) => $q->where('name', $this->kabupaten))
                ->where('name', 'like', '%' . $this->searchKecamatan . '%')
                ->orderBy('name')->limit(15)->get();
        }

        if ($this->kecamatan && $this->searchKelurahan !== $this->kecamatan) {
            $this->villages = Village::whereHas('district', fn($q) => $q->where('name', $this->kecamatan))
                ->where('name', 'like', '%' . $this->searchKelurahan . '%')
                ->orderBy('name')->limit(15)->get();
        }

        return view('livewire.services.alamat.form-alamat');
    }
}
