<?php

namespace App\Livewire\Services\DashboardTechnician;

use App\Models\Role_users\Technician;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Config\Exception\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormDataDiri extends Component
{
    use WithFileUploads;

    public $id_technician;
    
    // Properti Form
    public $nama_asli;
    public $foto_asli; // Untuk upload baru
    public $existing_foto_asli; // Dari database
    
    public $foto_kegiatan = []; // Array utama untuk file baru
    public $temp_foto_kegiatan = []; // Penampung input file
    public $existing_foto_kegiatan = [];

    public function mount()
    {
        // Ambil data teknisi berdasarkan user yang login
        $technician = Technician::where('user_id', Auth::id())->firstOrFail();
        
        $this->id_technician = $technician->id;
        $this->nama_asli = $technician->nama_asli; // Asumsi ada kolom nama_asli
        $this->existing_foto_asli = $technician->foto_wajah; // Asumsi ada kolom foto_asli
        $this->existing_foto_kegiatan = $technician->foto_kegiatan ?? [];
    }

    public function updatedTempFotoKegiatan()
    {
        $this->validate([
            'temp_foto_kegiatan.*' => 'image|max:2048', // 2MB
        ]);

        foreach ($this->temp_foto_kegiatan as $foto) {
            // Hitung total: lama di DB + yang baru ditambah di session ini
            $totalSekarang = count($this->existing_foto_kegiatan) + count($this->foto_kegiatan);
            
            if ($totalSekarang < 5) {
                $this->foto_kegiatan[] = $foto;
            }
        }

        // Reset input agar bisa pilih file yang sama atau upload ulang
        $this->temp_foto_kegiatan = [];

        if ((count($this->existing_foto_kegiatan) + count($this->foto_kegiatan)) >= 5) {
            session()->flash('error', 'Maksimal 5 foto telah tercapai.');
        }
    }

    public function removeExistingFotoKegiatan($index)
    {
        unset($this->existing_foto_kegiatan[$index]);
        $this->existing_foto_kegiatan = array_values($this->existing_foto_kegiatan);
    }

    public function removeNewFotoKegiatan($index)
    {
        unset($this->foto_kegiatan[$index]);
        $this->foto_kegiatan = array_values($this->foto_kegiatan);
    }

    public function save()
    {
        $this->validate([
            'nama_asli' => 'required|string|max:255',
            'foto_asli' => 'nullable|image|max:10240',
        ]);

        try {
            // Gunakan Transaction agar data konsisten
            DB::transaction(function () {
                $technician = Technician::findOrFail($this->id_technician);

                // 1. Handle Foto Identitas
                $pathFotoAsli = $this->existing_foto_asli;
                if ($this->foto_asli) {
                    // Hapus yang lama jika ada
                    if ($pathFotoAsli) Storage::disk('public')->delete($pathFotoAsli);
                    $pathFotoAsli = $this->foto_asli->store('technician/identitas', 'public');
                }

                // 2. Handle Foto Kegiatan Baru
                $newPaths = [];
                foreach ($this->foto_kegiatan as $photo) {
                    $newPaths[] = $photo->store('technician/kegiatan', 'public');
                }
                
                // 3. Gabungkan & Batasi
                $finalFotoKegiatan = array_merge($this->existing_foto_kegiatan ?? [], $newPaths);
                $finalFotoKegiatan = array_slice($finalFotoKegiatan, 0, 5);

                // 4. Update Database
                if(empty($technician->verifikasi)) {
                    $technician->update([
                        'nama_asli'     => $this->nama_asli,
                        'foto_wajah'    => $pathFotoAsli,
                        'foto_kegiatan' => $finalFotoKegiatan,
                        'verifikasi'    => 'diproses'
                    ]);
                } else {
                      $technician->update([
                        'nama_asli'     => $this->nama_asli,
                        'foto_wajah'    => $pathFotoAsli,
                        'foto_kegiatan' => $finalFotoKegiatan
                    ]);
                }


                // Reset state
                $this->foto_kegiatan = [];
                $this->foto_asli = null;
                $this->existing_foto_kegiatan = $technician->foto_kegiatan;
                $this->existing_foto_asli = $technician->foto_asli;
            });

            session()->flash('success', 'Data berhasil diperbarui!');
            // Gunakan redirect route yang pasti agar tidak looping
            $this->redirect(request()->header('Referer') ?? route('dashboard'));

        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Gagal menyimpan ke database.');
            $this->dispatch('notify', type: 'error', message: 'Database error!');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.services.dashboard-technician.form-data-diri');
    }
}
