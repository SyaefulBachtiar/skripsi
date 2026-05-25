<?php

namespace App\Livewire\Services\Admin\Users;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class View extends Component
{
    use WithPagination;

    public $search;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete ($id)
    {
        try {

            if ($id === Auth::id()) {
                session()->flash('error', 'Tindakan ilegal! Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
                return;
            }

            $user = User::findOrFail($id);

            $avatar = $user->avatar;
            if ($avatar && !Str::startsWith($avatar, ['http://', 'https://']) && !Str::startsWith($avatar, 'default')) {
                if (Storage::disk('public')->exists($avatar)) {
                    Storage::disk('public')->delete($avatar);
                }
            }

            if ($user->role === 'technician' && $user->technician) {
                $tech = $user->technician;
                
                if ($tech->foto_wajah && Storage::disk('public')->exists($tech->foto_wajah)) {
                    Storage::disk('public')->delete($tech->foto_wajah);
                }

                if (is_array($tech->foto_kegiatan)) {
                    foreach ($tech->foto_kegiatan as $foto) {
                        if (Storage::disk('public')->exists($foto)) { Storage::disk('public')->delete($foto); }
                    }
                }
            }

            $user->delete();

            session()->flash('success', 'Data pengguna dan berkas media berhasil dihapus secara permanen.');

            return $this->redirect(request()->header('Referer'), navigate: true);
        } catch (Exception $e) {
            Log::error('Gagal hapus user ID: ' . $id . '. Error: ' . $e->getMessage());
            session()->flash('error', 'Gagal menghapus pengguna. Akun ini kemungkinan besar memiliki keterikatan data riwayat transaksi aktif.');
            return $this->redirect(request()->header('Referer'), navigate: true);
        }
    }

    public function render()
    {
        $data_users = User::select('id', 'name', 'email', 'avatar', 'last_seen_at', 'role', 'created_at')
                ->when(trim($this->search), function ($query) {
                    $query->where(function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
                })
                ->latest()
                ->paginate(10);

        return view('livewire.services.admin.users.view', [
            'data_users' => $data_users,
        ]);
    }
}
