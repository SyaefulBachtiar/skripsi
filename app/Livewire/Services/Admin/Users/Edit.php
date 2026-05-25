<?php

namespace App\Livewire\Services\Admin\Users;

use App\Models\Role_users\Admin;
use App\Models\Role_users\Customer;
use App\Models\Role_users\Technician;
use App\Models\User;
use Livewire\Component;

class Edit extends Component
{
    public $id;
    public $user;

    public $name;
    public $email;
    public $role;
    public $saldo;
    public $verifikasi;
    public $alasan_ditolak;

    public function mount ($id)
    {
        if (!$id) {
            abort(404);
        }

        $this->id = $id;

        $this->user = User::with(['technician', 'customer', 'admin'])->find($id);

        if (!$this->user) {
            abort(404);
        }

        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->role = $this->user->role;

        if ($this->role === 'technician' && $this->user->technician) {
            $this->saldo = $this->user->technician->saldo;
            $this->verifikasi = $this->user->technician->verifikasi;
            $this->alasan_ditolak = $this->user->technician->alasan_ditolak;
        } elseif ($this->role === 'customer' && $this->user->customer) {
            $this->saldo = $this->user->customer->saldo;
            $this->verifikasi = $this->user->customer->verifikasi;
        }
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $this->id,
            'role' => 'required|in:admin,technician,customer',
            'saldo' => 'nullable|numeric',
            'alasan_ditolak' => 'required_if:verifikasi,ditolak'
        ]);

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ]);

        if ($this->role === 'technician' && $this->user->technician) {
            $this->user->technician->update([
                'saldo' => $this->saldo ?: 0,
                'verifikasi' => $this->verifikasi,
                'alasan_ditolak' => $this->verifikasi === 'ditolak' ? $this->alasan_ditolak : null
            ]);
        } elseif ($this->role === 'customer' && $this->user->customer) {
            $this->user->customer->update([
                'saldo' => $this->saldo ?: 0,
                'verifikasi' => $this->verifikasi
            ]);
        }

        session()->flash('success', 'Data profile pengguna berhasil diperbarui!');
        
        return $this->redirect(route('users.view'), navigate: true); // Sesuaikan nama route list user kamu
    }

    public function render()
    {
        $lat = -6.3227;
        $lng = 107.3376;
        $hasMap = false;

        if ($this->role === 'technician' && $this->user->technician) {
            $lat = $this->user->technician->latitude ?? $lat;
            $lng = $this->user->technician->longitude ?? $lng;
            $hasMap = true;
        } elseif ($this->role === 'customer' && $this->user->customer) {
            $lat = $this->user->customer->latitude ?? $lat;
            $lng = $this->user->customer->longitude ?? $lng;
            $hasMap = true;
        }

        return view('livewire.services.admin.users.edit', [
            'lat' => $lat,
            'lng' => $lng,
            'hasMap' => $hasMap
        ]);
    }
}
