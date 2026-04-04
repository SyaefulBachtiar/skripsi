<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {

    // Customer Register
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    // Customer Login
    Volt::route('login', 'pages.auth.login')
        ->name('login');

    // Forgot Password
    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    // Reset Password
    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');
    
    // Technician
    Route::prefix('teknisi')
        ->group(function () {
            
            // Teknisi Register
            Volt::route('register', 'pages.auth.register-technician')
                ->name('register.technician');
            
            // Teknisi Login
            Volt::route('login', 'pages.auth.login-technician')
                ->name('login.technician');
        });
    
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});
