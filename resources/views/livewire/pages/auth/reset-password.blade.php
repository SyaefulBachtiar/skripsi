<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div class="w-full">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl mb-4">
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">
            Reset Password
        </h2>
        <p class="text-slate-600 dark:text-slate-400">
            Buat password baru untuk akun Anda
        </p>
    </div>

    <!-- Info Card -->
    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800 dark:text-blue-200">
                <p class="font-medium mb-1">Tips Keamanan Password:</p>
                <ul class="space-y-1 text-blue-700 dark:text-blue-300">
                    <li>• Minimal 8 karakter</li>
                    <li>• Kombinasi huruf besar, kecil, dan angka</li>
                    <li>• Hindari informasi pribadi yang mudah ditebak</li>
                </ul>
            </div>
        </div>
    </div>

    <form wire:submit="resetPassword" class="space-y-5">
        <!-- Email Address (Read-only) -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-slate-700 dark:text-slate-300 font-medium" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                </div>
                <x-text-input 
                    wire:model="email" 
                    id="email" 
                    class="block w-full pl-10 pr-3 py-3 bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 rounded-xl cursor-not-allowed" 
                    type="email" 
                    name="email" 
                    readonly
                    required 
                    autocomplete="username" 
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Email terverifikasi untuk reset password</p>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- New Password -->
        <div x-data="{ show: false, strength: 0 }" x-init="$watch('$wire.password', value => {
            strength = 0;
            if (value.length >= 8) strength++;
            if (value.match(/[A-Z]/)) strength++;
            if (value.match(/[0-9]/)) strength++;
            if (value.match(/[^A-Za-z0-9]/)) strength++;
        })">
            <x-input-label for="password" :value="__('Password Baru')" class="text-slate-700 dark:text-slate-300 font-medium" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <x-text-input 
                    wire:model="password" 
                    id="password" 
                    x-bind:type="show ? 'text' : 'password'"
                    class="block w-full pl-10 pr-10 py-3 border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white rounded-xl focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                    name="password"
                    placeholder="Masukkan password baru"
                    required 
                    autocomplete="new-password" 
                />
                <button 
                    type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                >
                    <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Password Strength Meter -->
            <div class="mt-3" x-show="$wire.password.length > 0" x-cloak x-transition>
                <div class="flex items-center gap-2 mb-2">
                    <div class="flex-1 h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-500 ease-out rounded-full"
                             :class="{
                                 'w-1/4 bg-red-500': strength === 1,
                                 'w-2/4 bg-yellow-500': strength === 2,
                                 'w-3/4 bg-blue-500': strength === 3,
                                 'w-full bg-green-500': strength === 4
                             }"
                             :style="`width: ${strength * 25}%`"></div>
                    </div>
                    <span class="text-xs font-medium w-16 text-right"
                          :class="{
                              'text-red-600': strength === 1,
                              'text-yellow-600': strength === 2,
                              'text-blue-600': strength === 3,
                              'text-green-600': strength === 4
                          }"
                          x-text="strength === 1 ? 'Lemah' : strength === 2 ? 'Cukup' : strength === 3 ? 'Kuat' : strength === 4 ? 'Sangat Kuat' : ''"></span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center gap-1" :class="$wire.password.length >= 8 ? 'text-green-600' : 'text-slate-400'">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="$wire.password.length >= 8" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            <path x-show="$wire.password.length < 8" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Minimal 8 karakter</span>
                    </div>
                    <div class="flex items-center gap-1" :class="$wire.password.match(/[A-Z]/) ? 'text-green-600' : 'text-slate-400'">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="$wire.password.match(/[A-Z]/)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            <path x-show="!$wire.password.match(/[A-Z]/)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Huruf besar (A-Z)</span>
                    </div>
                    <div class="flex items-center gap-1" :class="$wire.password.match(/[0-9]/) ? 'text-green-600' : 'text-slate-400'">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="$wire.password.match(/[0-9]/)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            <path x-show="!$wire.password.match(/[0-9]/)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Angka (0-9)</span>
                    </div>
                    <div class="flex items-center gap-1" :class="$wire.password.match(/[^A-Za-z0-9]/) ? 'text-green-600' : 'text-slate-400'">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="$wire.password.match(/[^A-Za-z0-9]/)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            <path x-show="!$wire.password.match(/[^A-Za-z0-9]/)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Karakter spesial (!@#$)</span>
                    </div>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div x-data="{ show: false }">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-slate-700 dark:text-slate-300 font-medium" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <x-text-input 
                    wire:model="password_confirmation" 
                    id="password_confirmation" 
                    x-bind:type="show ? 'text' : 'password'"
                    class="block w-full pl-10 pr-10 py-3 border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white rounded-xl focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                    name="password_confirmation" 
                    placeholder="Ulangi password baru"
                    required 
                    autocomplete="new-password" 
                />
                <button 
                    type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                >
                    <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    </svg>
                </button>
            </div>
            <!-- Password Match Indicator -->
            <div class="mt-2" x-show="$wire.password_confirmation.length > 0" x-cloak x-transition>
                <div class="flex items-center gap-2 text-sm"
                     :class="$wire.password === $wire.password_confirmation && $wire.password.length > 0 ? 'text-green-600' : 'text-red-500'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="$wire.password === $wire.password_confirmation && $wire.password.length > 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        <path x-show="$wire.password !== $wire.password_confirmation || $wire.password.length === 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span x-text="$wire.password === $wire.password_confirmation && $wire.password.length > 0 ? 'Password cocok' : 'Password tidak cocok'"></span>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
            <a href="{{ route('login') }}" wire:navigate class="text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke login
            </a>

            <button 
                type="submit" 
                wire:loading.attr="disabled"
                class="w-full sm:w-auto flex items-center justify-center px-6 py-3 border border-transparent rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-slate-900 shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                x-bind:disabled="$wire.password !== $wire.password_confirmation || $wire.password.length < 8"
            >
                <span wire:loading.remove wire:target="resetPassword">
                    {{ __('Reset Password') }}
                </span>
                <span wire:loading wire:target="resetPassword" class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </button>
        </div>
    </form>

    <!-- Security Note -->
    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <span>Link reset password ini akan expired dalam 60 menit untuk keamanan Anda.</span>
        </div>
    </div>
</div>
