<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Servisio') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        {{-- internal style --}}
        <style>
            @keyframes shrink {
                from { width: 100%; }
                to { width: 0%; }
            }
            .animate-shrink-width {
                animation: shrink 3s linear backwards;
            }
        </style>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 overflow-hidden">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            {{-- @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif --}}

            <!-- Page Content -->
            <main class="mt-10">
                {{ $slot }}
            </main>

            {{-- Alert Container --}}
            <div class="fixed right-5 bottom-20 flex flex-col gap-3">
                
                {{-- Alert Success --}}
                @if (session()->has('success'))
                <div 
                    x-data="{ show: true }" 
                    x-init="setTimeout(() => show = false, 3000)" {{-- Hilang otomatis setelah 3 detik --}}
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="translate-x-full opacity-0" {{-- Dari kanan luar layar --}}
                    x-transition:enter-end="translate-x-0 opacity-100"    {{-- Ke posisi normal --}}
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="translate-x-0 opacity-100"   {{-- Dari posisi normal --}}
                    x-transition:leave-end="translate-x-full opacity-0"  {{-- Menutup ke kiri --}}
                    class="flex items-center gap-3 bg-slate-900 dark:bg-blue-600 rounded-xl py-3 px-5 shadow-2xl border-b-4 border-green-500 overflow-hidden"
                >
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-green-500">
                        <i class="bi bi-check-lg text-white"></i>
                    </div>
                    
                    <div class="flex flex-col">
                        <span class="text-white text-sm font-semibold">{{ session('success') }}</span>
                    </div>

                    {{-- Bar Loading Dekoratif (opsional: sebagai indikator waktu) --}}
                    <div class="absolute bottom-0 left-0 h-1 bg-green-400 animate-shrink-width"></div>
                </div>
                @endif

                {{-- Alert Error (Gagal) --}}
                @if (session()->has('error'))
                <div 
                    x-data="{ show: true }" 
                    x-init="setTimeout(() => show = false, 3000)"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="translate-x-full opacity-0"
                    x-transition:enter-end="translate-x-0 opacity-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="translate-x-0 opacity-100"
                    x-transition:leave-end="translate-x-full opacity-0"
                    class="flex items-center gap-3 bg-red-600 rounded-xl py-3 px-5 shadow-2xl border-b-4 border-red-800"
                >
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-white">
                        <i class="bi bi-exclamation-triangle-fill text-red-600"></i>
                    </div>
                    <span class="text-white text-sm font-semibold">{{ session('error') }}</span>
                </div>
                @endif
            </div>

             {{-- Bottom bar --}}
            <nav class="flex sm:hidden fixed bottom-0 w-full bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg border-t border-slate-200 dark:border-slate-800 z-50 pb-safe">
                <div class="flex justify-around items-center w-full h-16 px-2">

                    @php
                        // Helper untuk styling link aktif
                        $activeClass = 'text-blue-600 dark:text-blue-400 relative';
                        $inactiveClass = 'text-slate-400 dark:text-slate-500 hover:text-slate-600';
                    @endphp

                    {{-- Logika untuk Technician --}}
                    @if(auth()->user()->role === 'technician')
                        
                        {{-- Dashboard --}}
                        <a href="{{ route('dashboard_technician') }}" class="flex-1 flex flex-col items-center justify-center transition-all duration-300 {{ request()->routeIs('dashboard_technician') ? $activeClass : $inactiveClass }}">
                            <i class="bi {{ request()->routeIs('dashboard_technician') ? 'bi-columns-gap' : 'bi-columns-gap' }} text-xl"></i>
                            <span class="text-[10px] font-medium mt-1">Dashboard</span>
                            @if(request()->routeIs('dashboard_technician'))
                                <div class="absolute -top-[17px] w-8 h-1 bg-blue-600 rounded-b-full"></div>
                            @endif
                        </a>

                        {{-- Pesan --}}
                        <a href="{{ route('pesan_technician') }}" class="flex-1 flex flex-col items-center justify-center transition-all duration-300 {{ request()->routeIs('pesan_technician') ? $activeClass : $inactiveClass }}">
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
                        <a href="{{ route('pesan') }}" class="flex-1 flex flex-col items-center justify-center transition-all duration-300 {{ request()->routeIs('pesan') ? $activeClass : $inactiveClass }}">
                            <i class="bi {{ request()->routeIs('pesan') ? 'bi-chat-dots-fill' : 'bi-chat-dots' }} text-xl"></i>
                            <span class="text-[10px] font-medium mt-1">Pesan</span>
                            @if(request()->routeIs('pesan'))
                                <div class="absolute -top-[17px] w-8 h-1 bg-blue-600 rounded-b-full"></div>
                            @endif
                        </a>
                    @endif
                </div>
            </nav>
        </div>
    </body>
</html>
