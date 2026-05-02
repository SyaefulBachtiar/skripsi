<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role_users\Customer;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Socialite;

class GoogleController extends Controller
{
    public function redirect(Request $request)
    {
        // Ambil role dari URL, contoh: /auth/google/redirect?role=technician
        // Default-kan ke 'customer' jika tidak ada parameter
        $role = $request->query('role', 'customer');

        // Simpan ke session agar bisa diambil saat callback
        session(['register_as_role' => $role]);

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {

            $googleUser = Socialite::driver('google')->user();

            $role = session()->pull('register_as_role', 'customer');

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            } else {
                $user = User::updateOrCreate(
                    ['email' => $googleUser->getEmail()],
                    [
                        'name'              => $googleUser->getName(),
                        'google_id'         => $googleUser->getId(),
                        'avatar'            => $googleUser->getAvatar(),
                        'email_verified_at' => now(),
                        'password'          => bcrypt(str()->random(24)),
                        'role'              => $role,
                    ]);
                
                Customer::create([
                    'user_id' => $user->id,
                ]);
            }

            Auth::login($user, remember: true);

            // dd($role);

            // Redirect dinamis berdasarkan role
            if ($user->role === 'technician') {
                // dd($user->role);
                return redirect()->intended(route('dashboard_technician'));
            }

            return redirect()->intended(route('beranda'));

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }
    }
}
