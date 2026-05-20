<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $avatar;
    public $current_avatar;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->current_avatar = Auth::user()->avatar;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();
        $this->email = Auth::user()->email;
        $this->current_avatar = $user->avatar;

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'max:1024'],
        ]);

        if ($this->avatar) {
            // 1. Hapus foto lama jika ada dan bukan URL (Google)
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }

            // 2. Simpan foto baru ke folder 'avatars'
            $path = $this->avatar->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update tampilan foto saat ini
        $this->current_avatar = $user->avatar;
        $this->avatar = null; // Reset input file

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-5">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Profile information</h2>
        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
            {{ __("Perbarui nama, alamat email, dan foto profil Anda.") }}
        </p>
    </div>

    <form wire:submit="updateProfileInformation" class="space-y-5">

        {{-- Avatar --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 pb-5 border-b border-gray-100 dark:border-gray-800">
            @if ($avatar)
                {{-- Jika ada file baru yang sedang di-upload (Temporary Preview) --}}
                <img class="h-16 w-16 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700 flex-shrink-0"
                    src="{{ $avatar->temporaryUrl() }}"
                    alt="Avatar Preview">
            @elseif ($current_avatar)
                {{-- Jika tidak ada upload baru, tapi ada avatar lama di database --}}
                <img class="h-16 w-16 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700 flex-shrink-0"
                    src="{{ asset('storage/' . $current_avatar) }}"
                    alt="Avatar Current">
            @else
                {{-- KUNCI FIX: Jika semuanya kosong, tampilkan Icon Person Bootstrap --}}
                <div class="h-16 w-16 rounded-full ring-2 ring-gray-200 dark:ring-gray-700 bg-gray-50 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-person text-3xl text-gray-400 dark:text-gray-500"></i>
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">JPG, PNG, or GIF — max 1 MB</p>
                <label for="avatar"
                       class="inline-flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                    </svg>
                    Upload photo
                </label>
                <input type="file" wire:model="avatar" id="avatar" class="sr-only" accept="image/*">
                <div wire:loading wire:target="avatar" class="mt-1.5 text-xs text-blue-500 flex items-center gap-1">
                    <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    Uploading...
                </div>
                <x-input-error class="mt-1.5" :messages="$errors->get('avatar')" />
            </div>
        </div>

        {{-- Name & Email --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="name" value="{{ __('Nama Lengkap') }}" class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5" />
                <x-text-input wire:model="name" id="name" type="text" class="block w-full" required autocomplete="name" />
                <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
            </div>
            <div>
                <x-input-label for="email" value="{{ __('Alamat Email') }}" class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5" />
                <x-text-input wire:model="email" id="email" type="email" class="block w-full" required autocomplete="email" />
                <x-input-error class="mt-1.5" :messages="$errors->get('email')" />
            </div>
        </div>

        {{-- Email verification --}}
        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg">
                <p class="text-sm text-amber-700 dark:text-amber-300 flex-1">
                    {{ __('Your email is unverified.') }}
                    <button wire:click.prevent="sendVerification" class="underline font-medium hover:no-underline ml-1">
                        {{ __('Resend verification') }}
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="text-xs text-amber-600 dark:text-amber-400">{{ __('Verification link sent!') }}</p>
                @endif
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-3 pt-1">
            <x-primary-button class="w-full sm:w-auto justify-center">{{ __('Simpan Perubahan') }}</x-primary-button>
            <x-action-message on="profile-updated" class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                </svg>
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>