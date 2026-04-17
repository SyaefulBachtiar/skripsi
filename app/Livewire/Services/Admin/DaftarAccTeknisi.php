<?php

namespace App\Livewire\Services\Admin;

use App\Models\Role_users\Technician;
use Exception;
use Livewire\Component;
use Livewire\WithPagination;

class DaftarAccTeknisi extends Component
{

    use WithPagination;

    public $search = '';

    public function approveTeknisi($id)
    {
        try {
            $technician = Technician::findOrFail($id);
                
            $technician->update([
                    'verifikasi' => 'diverifikasi',
                    'alasan_ditolak' => null
                ]);

            session()->flash('success', 'Teknisi berhasil disetujui');

            return $this->redirect(request()->header('Referer'), navigate: true);
        } catch (Exception $e) {
            session()->flash('error', 'Data gagal diperbarui '. $e);
            $this->redirect(request()->header('Referer'), navigate: true);
        }
    }

    public function rejectTeknisi($id, $reason)
    {
        try {
            Technician::where('id', $id)
                ->update([
                    'verifikasi' => 'ditolak',
                    'alasan_ditolak' => $reason
                ]);

            session()->flash('success', 'Teknisi berhasil di Tolak');

            return $this->redirect(request()->header('Referer'), navigate: true);
        } catch (Exception $e) {
            session()->flash('error', 'Data gagal diperbarui ' . $e);
            
            $this->redirect(request()->header('Referer'), navigate: true);
        }
    }

    public function render()
    {
        $data = Technician::where('verifikasi', 'diproses')
        ->where(function($query) {
            $query->where('nama_asli', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($q) {
                      $q->where('email', 'like', '%' . $this->search . '%');
                  });
        })
        ->with(['user'])
        ->orderBy('updated_at', 'asc')
        ->paginate(10);

        return view('livewire.services.admin.daftar-acc-teknisi', [
            'data' => $data
        ]);
    }
}
