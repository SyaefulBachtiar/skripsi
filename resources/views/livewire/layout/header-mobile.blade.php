<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <nav class="dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 fixed top-0 w-full bg-white z-50">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex justify-between w-full">
                    <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <div class="flex items-center gap-4">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        <h1 class="font-semibold text-lg">Servisio</h1>
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <div class="flex items-center ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                
                                {{-- Lingkaran Inisial --}}
                                <div 
                                    x-data="{{ json_encode(['name' => auth()->user()->name]) }}" 
                                    x-on:profile-updated.window="name = $event.detail.name"
                                    class="flex items-center justify-center w-8 h-8 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-full uppercase"
                                    x-text="name.charAt(0)">
                                </div>

                                {{-- <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div> --}}
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                {{-- {{ __('Saya') }} --}}
                                <div class="flex items-center gap-1">
                                    <i class="bi bi-person-fill"></i>
                                    <span>Saya</span>
                                </div>
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    {{-- {{ __('Log Out') }} --}}
                                    <div class="flex items-center gap-1">
                                        <i class="bi bi-box-arrow-right"></i>
                                        <span>Log Out</span>
                                    </div>
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </nav>
</div>