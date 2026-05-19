<?php

namespace App\Livewire\Services;

use Livewire\Component;

class Map extends Component
{

    public $lat;
    public $lng;
    public $customerName;

    public function mount($lat, $lng, $customerName = 'Lokasi Pelanggan')
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->customerName = $customerName;
    }

    public function render()
    {
        return view('livewire.services.map');
    }
}
