<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use App\Models\ChatMessages;
use App\Models\Order;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */

     public $unreadCount = 0;
     public $pesanan = 0;

     public function mount () 
     {
        if(Auth::user()->role === 'technician'){
            $this->unreadCount = ChatMessages::where('sender_id', '!=', Auth::id())
                ->where('is_read', false)
                ->whereHas('chat_room', function ($q) {
                    $q->where('technician_id', Auth::user()->technician->id); 
                })
                ->count();
            
            $this->pesanan = Order::whereHas('latestStatus', function ($query) {
                $query->where('status_order', 'menunggu_konfirmasi');
            })
            ->count();
            
        } elseif(Auth::user()->role === 'customer') {
            $this->unreadCount = ChatMessages::where('sender_id', '!=', Auth::id())
                ->where('is_read', false)
                ->whereHas('chat_room', function ($q) {
                    $q->where('customer_id', Auth::user()->customer->id); 
                })
                ->count();
        }

        // dd($this->unreadCount);
     }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="relative">
    <nav class="dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 fixed top-0 w-full bg-white z-50 hidden sm:block">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <div class="flex items-center gap-4">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                            <h1 class="font-semibold text-lg">Servisio</h1>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="space-x-8 sm:-my-px sm:ms-10 hidden sm:flex">
                        @auth
                            @if (auth()->user()->role === 'technician')
                                <x-nav-link :href="route('dashboard_technician')" :active="request()->routeIs('dashboard_technician')" wire:navigate>
                                    {{ __('Dashboard') }}
                                </x-nav-link>

                                <x-nav-link :href="route('dashboard_technician')" :active="request()->routeIs('dashboard_technician')" wire:navigate>
                                    {{ __('Pesanan') }}
                                </x-nav-link>

                                <x-nav-link :href="route('pesan_technician')" :active="request()->routeIs('pesan_technician')" wire:navigate>
                                    {{ __('Pesan') }}
                                </x-nav-link>

                                <x-nav-link :href="route('profile.technician')" :active="request()->routeIs('profile.technician')" wire:navigate>
                                    {{ __('Profile') }}
                                </x-nav-link>
                            @elseif(auth()->user()->role === 'customer')
                                <x-nav-link :href="route('beranda')" :active="request()->routeIs('beranda')" wire:navigate>
                                    {{ __('Beranda') }}
                                </x-nav-link>

                                <x-nav-link :href="route('lacak')" :active="request()->routeIs('lacak')" wire:navigate>
                                    {{ __('Lacak') }}
                                </x-nav-link>

                                <x-nav-link :href="route('riwayat')" :active="request()->routeIs('riwayat')" wire:navigate>
                                    {{ __('riwayat') }}
                                </x-nav-link>

                                <x-nav-link :href="route('pesan')" :active="request()->routeIs('pesan')" wire:navigate>
                                    {{ __('Pesan') }}
                                </x-nav-link>
                            @endif
                        @endauth
                        
                        @guest
                            <x-nav-link :href="route('beranda')" :active="request()->routeIs('beranda')" wire:navigate>
                                {{ __('Beranda') }}
                            </x-nav-link>

                            <x-nav-link :href="route('login')" wire:navigate>
                                {{ __('Login') }}
                            </x-nav-link>
                        @endguest
                    </div>
                </div>

                @auth
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
                @endauth
            </div>
        </div>
    </nav>

     {{-- Bottom bar --}}
    @auth
        @if(auth()->user()->role !== 'admin')
            <nav class="{{ request()->routeIs('rincian.pesanan') || request()->routeIs('chat.room') ? 'hidden' : '' }} flex sm:hidden fixed bottom-0 w-full bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg border-t border-slate-200 dark:border-slate-800 z-[9999] pb-safe">
                <div class="flex justify-around items-center w-full h-16 px-2">
                    @php
                        // Helper untuk styling link aktif
                        $activeClass = 'text-blue-600 dark:text-blue-400 relative';
                        $inactiveClass = 'text-slate-400 dark:text-slate-500 hover:text-slate-600';
                    @endphp

                    {{-- Logika untuk Technician --}}
                    @if(auth()->user()->role === 'technician')
                        
                        {{-- Dashboard --}}
                        <a href="{{ route('dashboard_technician') }}" class="relative flex-1 flex flex-col items-center justify-center transition-all duration-300 {{ request()->routeIs('dashboard_technician') ? $activeClass : $inactiveClass }}">

                            @if($pesanan > 0)
                                <span class="absolute -top-2 right-1/2 translate-x-4 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-gray-900">
                                    {{ $pesanan > 99 ? '99+' : $pesanan }}
                                </span>
                            @endif

                            <i class="bi {{ request()->routeIs('dashboard_technician') ? 'bi-columns-gap' : 'bi-columns-gap' }} text-xl"></i>
                            <span class="text-[10px] font-medium mt-1">Dashboard</span>
                            @if(request()->routeIs('dashboard_technician'))
                                <div class="absolute -top-[17px] w-8 h-1 bg-blue-600 rounded-b-full"></div>
                            @endif
                        </a>
                        
                        {{-- Pesan --}}
                        <a href="{{ route('pesan_technician') }}" class="relative flex-1 flex flex-col items-center justify-center transition-all duration-300 {{ request()->routeIs('pesan_technician') ? $activeClass : $inactiveClass }}">

                            {{-- Notifikasi Badge --}}
                            @if($unreadCount > 0)
                                <span class="absolute -top-2 right-1/2 translate-x-4 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-gray-900">
                                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                </span>
                            @endif

                            <i class="bi {{ request()->routeIs('pesan_technician') ? 'bi-chat-dots-fill' : 'bi-chat-dots' }} text-xl"></i>
                            <span class="text-[10px] font-medium mt-1">Pesan</span>
                            @if(request()->routeIs('pesan_technician'))
                                <div class="absolute -top-[17px] w-8 h-1 bg-blue-600 rounded-b-full"></div>
                            @endif
                        </a>
                        {{-- Halaman Saya(Profile) --}}
                        <a href="{{ route('profile.technician') }}" class="flex-1 flex flex-col items-center justify-center transition-all duration-300 {{ request()->routeIs('profile.technician') ? $activeClass : $inactiveClass }}">
                            <i class="bi bi-person {{ request()->routeIs('profile.technician') ? '-fill' : '' }} text-2xl"></i>
                            <span class="text-[10px] font-medium mt-1">Saya</span>
                            @if(request()->routeIs('profile.technician'))
                                <div class="absolute -top-[17px] w-8 h-1 bg-blue-600 rounded-b-full"></div>
                            @endif
                        </a>
                    {{-- Logika untuk Customer --}}
                    @elseif(auth()->user()->role === 'customer')
                        {{-- Beranda --}}
                        <a href="{{ route('beranda') }}" class="flex-1 flex flex-col items-center justify-center transition-all duration-300 {{ request()->routeIs('beranda') || request()->routeIs('detail-product') ? $activeClass : $inactiveClass }}">
                            <i class="bi {{ request()->routeIs('beranda') ? 'bi-house-fill' : 'bi-house' }} text-xl"></i>
                            <span class="text-[10px] font-medium mt-1">Beranda</span>
                            @if(request()->routeIs('beranda') || request()->routeIs('detail-product'))
                                <div class="absolute -top-[17px] w-8 h-1 bg-blue-600 rounded-b-full"></div>
                            @endif
                        </a>
                        {{-- Lacak --}}
                        <a href="{{ route('lacak') }}" class="flex-1 flex flex-col items-center justify-center transition-all duration-300 {{ request()->routeIs('lacak') ? $activeClass : $inactiveClass }}">
                            <i class="bi {{ request()->routeIs('lacak') ? 'bi-list-check' : 'bi-list-task' }} text-xl"></i>
                            <span class="text-[10px] font-medium mt-1">Lacak</span>
                            @if(request()->routeIs('lacak'))
                                <div class="absolute -top-[17px] w-8 h-1 bg-blue-600 rounded-b-full"></div>
                            @endif
                        </a>
                        {{-- Riwayat --}}
                        <a href="{{ route('riwayat') }}" class="flex-1 flex flex-col items-center justify-center transition-all duration-300 {{ request()->routeIs('riwayat') ? $activeClass : $inactiveClass }}">
                            <i class="bi {{ request()->routeIs('riwayat') ? 'bi-clock-fill' : 'bi-clock-history' }} text-xl"></i>
                            <span class="text-[10px] font-medium mt-1">Riwayat</span>
                            @if(request()->routeIs('riwayat'))
                                <div class="absolute -top-[17px] w-8 h-1 bg-blue-600 rounded-b-full"></div>
                            @endif
                        </a>
                        {{-- Pesan --}}
                        <a href="{{ route('pesan') }}" class="relative flex-1 flex flex-col items-center justify-center transition-all duration-300 {{ request()->routeIs('pesan') ? $activeClass : $inactiveClass }}">
    
                            {{-- Notifikasi Badge --}}
                            @if($unreadCount > 0)
                                <span class="absolute -top-2 right-1/2 translate-x-4 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-gray-900">
                                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                </span>
                            @endif

                            {{-- Ikon Chat --}}
                            <i class="bi {{ request()->routeIs('pesan') ? 'bi-chat-dots-fill' : 'bi-chat-dots' }} text-xl"></i>
                            
                            <span class="text-[10px] font-medium mt-1">Pesan</span>

                            @if(request()->routeIs('pesan'))
                                <div class="absolute -top-[17px] w-8 h-1 bg-blue-600 rounded-b-full"></div>
                            @endif
                        </a>
                    @endif
                </div>
            </nav>
        @endif
    @endauth
</div>