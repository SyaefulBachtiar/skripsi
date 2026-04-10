<?php

namespace App\Livewire\Services\ProfileTechnician;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileInformation extends Component
{
    
    use WithFileUploads;

    
    public $photo;

    public function updateProfile ($newName, $hasNewFile) {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $user->name = $newName;

            // Old Photo
            if($hasNewFile && $this->photo) {

                // Hapus foto lama jika ada
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Simpan foto baru
                $path = $this->photo->store('avatars', 'public');
                $user->avatar = $path;
            }

            $user->save();

            session()->flash('success', 'Profil diperbarui!');
            
            return $this->redirect(request()->header('Referer'), navigate: true);
            
        } catch (Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.services.profile-technician.profile-information');
    }
}
