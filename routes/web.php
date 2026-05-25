<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Models\ChatRooms;
use App\Models\Role_users\Technician;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::post('/ping-online', function () {
    if (Auth::check()) {
        Auth::user()->update(['last_seen_at' => now()]);
    }
    return response()->json(['ok' => true]);
})->middleware('auth');

// Route To Page Beranda
Route::view('beranda', 'pages.customer.beranda')
    ->name('beranda');

 Route::prefix('teknisi')
        ->group(function () {
            
            // Teknisi Register
            Volt::route('register', 'pages.auth.register-technician')
                ->name('register.technician');
            
            // Teknisi Login
            Volt::route('login', 'pages.auth.login-technician')
                ->name('login.technician');
        });


// Route Authentication
Route::middleware(['auth'])->group(function () {

    // Route Technician
    Route::middleware(['role:technician,admin'])
        ->prefix('teknisi')
        ->group(function () {

            // Routing To Page Dashboard
            Route::get('dashboard', function() {
                
                $data = Technician::where('user_id', Auth::id())->first();

                return view('pages.technician.dashboard', ['data' => $data]);
            })->name('dashboard_technician');

            // Routing To Page Pesan
            Route::view('pesan', 'pages.technician.pesan')
                ->name('pesan_technician');

            // Route To Page Profile
            Route::view('profile-teknisi', 'pages.technician.profile')
                ->name('profile.technician');

            
            Route::prefix('dashboard')
                ->group(function () {
                    
                    // Route To Page Posting Jasa
                    Route::view('posting-jasa', 'pages.technician.posting')
                        ->name('posting.jasa');

                    // Route To Page Riwayat
                    Route::view('riwayat', 'pages.technician.riwayat')
                        ->name('riwayat.technician');

                    // Route To Page Jasa Saya
                    Route::view('jasa', 'pages.technician.jasa-saya')
                        ->name('jasa.technician');

                    // Route To Page Detail Jasa
                    Route::get('detail-jasa/{id_jasa}', function ($id_jasa) {
                            return view('pages.detail-jasa', [
                                'id_jasa' => $id_jasa
                            ]);
                    })->name('detail_jasa.technician');

                    // Route To Page Laporan
                    Route::view('pesanan', 'pages.technician.pesanan')
                        ->name('pesanan.technician');

                    // Route To Page Edit Jasa
                    Route::get('edit-jasa/{id_jasa}', function ($id_jasa) {
                        return view('pages.technician.edit-jasa', [
                            'id_jasa' => $id_jasa
                        ]);
                    })->name('edit.jasa');
                    
                });

        });


    // Route Customer
    Route::middleware(['role:customer,admin,technician'])
        ->group(function () {

            // Route To Page Lacak
            Route::view('lacak', 'pages.customer.lacak')
                ->name('lacak');

            // Route To Page Notifikasi
            Route::view('riwayat', 'pages.customer.riwayat')
                ->name('riwayat');

            // Route To Page Pesan 
            Route::view('pesan', 'pages.customer.pesan')
                ->name('pesan');

            // Route To Page Detail Product
            Route::get('detail/{id}', function ($id) {
                return view('pages.detail-jasa', [
                    'id_jasa' => $id
                ]);
            })->name('detail-product');

            // Route To Profile Teknisi
            Route::get('teknisi/{id}', function ($id) {
                return view('pages.profile-technician', [
                    'id_technician' => $id
                ]);
            })->name('technician.profile');

            // Route To Rincian Pesanan
            Route::get('rincian-pesanan/{id}', function ($id) {
                return view('pages.customer.rincian-pesanan', [
                    'id_order' => $id
                ]);
            })->name('rincian.pesanan');

            // Route TO Keranjang Pesanan
            Route::view('keranjang-pesanan', 'pages.customer.keranjang')
                ->name('keranjang.pesanan');
            
        });

    

    // Route Admin
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->group(function () {

            Route::view('dashboard-admin', 'pages.admin.dashboard')
                ->name('dashboard_admin');

            Route::view('users', 'pages.admin.users.view')->name('users.view');

            Route::view('ditolak', 'pages.admin.ditolak.view')->name('ditolak.view');

            Route::view('transaksi', 'pages.admin.transaksi.view')->name('transaksi.view');
    });

    // Route To Room Chat
    // Route::get('chat-room/{id}', function ($id) {
    //     $order = ChatRooms::select('id', 'technician_id')
    //         ->with(['technician:id,nama_asli,foto_wajah'])
    //         ->findOrFail($id);
    //         // dd($order->technician);
    //     return view('pages.chat-room', [
    //         'data' => $order
    //     ]);
    // })
    // ->name('chat.room');


});

// Route To Page Set Address
Route::view('atur-alamat', 'pages.atur-alamat')
    ->name('atur_alamat');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');


require __DIR__.'/auth.php';
