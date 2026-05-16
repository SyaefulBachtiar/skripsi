<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-5">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Password &amp; security</h2>
        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
            {{ __('Gunakan kata sandi yang panjang dan acak untuk menjaga keamanan.') }}
        </p>
    </div>

    <form wire:submit="updatePassword" class="space-y-4">
        <div>
            <x-input-label for="update_password_current_password"
                value="{{ __('Kata sandi saat ini') }}"
                class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5" />
            <x-text-input wire:model="current_password"
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="block w-full"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-1.5" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="update_password_password"
                    value="{{ __('Password Baru') }}"
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5" />
                <x-text-input wire:model="password"
                    id="update_password_password"
                    name="password"
                    type="password"
                    class="block w-full"
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="update_password_password_confirmation"
                    value="{{ __('Konfirmasi Password') }}"
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5" />
                <x-text-input wire:model="password_confirmation"
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="block w-full"
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-1">
            <x-primary-button class="w-full sm:w-auto justify-center">{{ __('Update password') }}</x-primary-button>
            <x-action-message class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1" on="password-updated">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                </svg>
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>