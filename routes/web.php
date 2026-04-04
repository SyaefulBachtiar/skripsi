<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Models\Role_users\Technician;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');


// Route Authentication
Route::middleware(['auth'])->group(function () {

    // Route Technician
    Route::middleware(['role:technician'])
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

            // Route To Page Posting
            Route::view('profile-teknisi', 'pages.technician.profile')
                ->name('profile.technician');

        });


    // Route Customer
    Route::middleware(['role:customer'])
        ->group(function () {

            // Route To Page Beranda
            Route::view('beranda', 'pages.customer.beranda')
                ->name('beranda');

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
            Route::view('detail/produk', 'pages.customer.detail-product')
                ->name('detail-product');
            
        });

    // Route Admin
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->group(function () {
            Route::view('dashboard-admin', 'pages.admin.dashboard')
                ->name('dashboard_admin');
        });
});

// Route To Page Set Address
Route::view('atur-alamat', 'pages.customer.atur-alamat')
    ->name('atur_alamat');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');


require __DIR__.'/auth.php';
