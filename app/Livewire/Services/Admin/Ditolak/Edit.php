<?php

namespace App\Livewire\Services\Admin\Ditolak;

use App\Models\Role_users\Technician;
use Livewire\Component;

class Edit extends Component
{
    public $id;
    public $data_edit;

    public $status_verifikasi;
    public $alasan_baru = '';

    public function mount ($id)
    {
        
        if (!$id) {
            abort(404);
        }

        $this->id = $id;

        $this->data_edit = Technician::where('user_id', $id)->with('user')->first();

        if (!$this->data_edit) {
            abort(404);
        }

        $this->status_verifikasi = $this->data_edit->verifikasi;
        $this->alasan_baru = $this->data_edit->alasan_ditolak;
    }

    public function saveStatus()
    {
        $this->validate([
            'status_verifikasi' => 'required|in:diverifikasi,ditolak',
            'alasan_baru' => 'required_if:status_verifikasi,ditolak'
        ], [
            'alasan_baru.required_if' => 'Alasan wajib diisi jika status verifikasi ditolak.'
        ]);

        $technician = Technician::where('user_id', $this->id)->firstOrFail();
        
        $technician->update([
            'verifikasi' => $this->status_verifikasi,
            'alasan_ditolak' => $this->status_verifikasi === 'diverifikasi' ? null : $this->alasan_baru
        ]);

        $user = $technician->user;
        if ($user && $this->status_verifikasi === 'diverifikasi') {
            $user->update(['role' => 'technician']);
        }

        session()->flash('success', 'Status verifikasi pendaftaran teknisi berhasil diperbarui!');
        
        return $this->redirect(route('ditolak.view'), navigate: true);
    }

    public function render()
    {
        return view('livewire.services.admin.ditolak.edit', [
            'tech' => $this->data_edit,
            'lat'  => $this->data_edit->latitude ?? -6.3227,
            'lng'  => $this->data_edit->longitude ?? 107.3376,
            'customerName' => $this->data_edit->nama_asli ?? 'Teknisi'
        ]);
    }
}
