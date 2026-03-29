<?php

use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');


// Route Authentication
Route::middleware(['auth'])->group(function () {

    // Route Technician
    Route::middleware(['role:technician'])
        ->prefix('teknisi')
        ->group(function () {

            // Routing To Page Dashboard
            Route::view('dashboard', 'pages.technician.dashboard')
                ->name('dashboard_technician');

            // Routing To Page Pesan
            Route::view('pesan', 'pages.technician.pesan')
                ->name('pesan_technician');

            // Route To Page Posting
            Route::view('posting', 'pages/technician.posting')
                ->name('posting');

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
            Route::view('notifikasi', 'pages.customer.notifikasi')
                ->name('notifikasi');

            // Route To Page Pesan 
            Route::view('pesan', 'pages.customer.pesan')
                ->name('pesan');
            
        });

    // Route Admin
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->group(function () {
            Route::view('dashboard-admin', 'pages.admin.dashboard')
                ->name('dashboard_admin');
        });
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');


require __DIR__.'/auth.php';
