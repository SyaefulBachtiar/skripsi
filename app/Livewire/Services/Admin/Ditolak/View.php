<?php

namespace App\Livewire\Services\Admin\Ditolak;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class View extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch ()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data_users_ditolak = User::select('id', 'name', 'email', 'avatar', 'last_seen_at', 'role', 'created_at')
                        ->whereHas('technician', function ($query) {
                            $query->where('verifikasi', 'ditolak');
                        })
                        ->when(trim($this->search), function ($query) {
                            $query->where(function ($subQuery) {
                                $subQuery->where('name', 'like', '%' . $this->search . '%')
                                        ->orWhere('email', 'like', '%' . $this->search . '%');
                            });
                        })
                        ->with([
                            'technician:id,user_id,verifikasi,alasan_ditolak'
                        ])
                        ->latest()
                        ->paginate(10);

        return view('livewire.services.admin.ditolak.view', [
            'data_users' => $data_users_ditolak,
        ]);
    }
}
