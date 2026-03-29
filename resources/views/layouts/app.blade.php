<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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

             {{-- Bottom bar --}}
            <nav class="flex sm:hidden fixed bottom-0 w-full bg-white/80 backdrop-blur-md">
                <div class="flex justify-between items-center w-full h-16">

                    {{-- Bottom bar technician --}}
                    @if(auth()->user()->role === 'technician')

                        {{-- Dashboard --}}
                        <a href="{{ route('dashboard_technician') }}" class="h-full flex items-center justify-center px-2 {{ request()->routeIs('dashboard_technician') ? 'border-t border-black' : '' }}">
                            <div class="flex flex-col items-center">
                                <i class="bi bi-columns-gap text-2xl"></i>
                                <span class="text-xs">Dashboard</span>
                            </div>
                        </a>
                        
                        {{-- Pesan --}}
                        <a href="{{ route('pesan_technician') }}" class="h-full flex items-center justify-center px-2 {{ request()->routeIs('pesan_technician') ? 'border-t border-black' : '' }}">
                            <div class="flex flex-col items-center">
                                <i class="bi bi-chat-dots text-2xl"></i>
                                <span class="text-xs">Pesan</span>
                            </div>
                        </a>

                        {{-- Posting --}}
                        <a href="{{ route('posting') }}" class="h-full flex items-center justify-center px-2 {{ request()->routeIs('posting') ? 'border-t border-black' : '' }}">
                            <div class="flex flex-col items-center">
                                <i class="bi bi-capslock text-2xl"></i>
                                <span class="text-xs">Posting</span>
                            </div>
                        </a>
                    
                    {{-- Bottom Bar customer --}}
                    @elseif(auth()->user()->role === 'customer')
                        
                        {{-- Beranda --}}
                        <a href="{{ route('beranda') }}" class="h-full flex items-center justify-center px-2 {{ request()->routeIs('beranda') ? 'border-t border-black' : '' }}">
                            <div class="flex flex-col items-center">
                                <i class="bi bi-house text-2xl"></i>
                                <span class="text-xs">Beranda</span>
                            </div>
                        </a>

                        {{-- Lacak --}}
                        <a href="{{ route('lacak') }}" class="h-full flex items-center justify-center px-2 {{ request()->routeIs('lacak') ? 'border-t border-black' : '' }}">
                            <div class="flex flex-col items-center">
                                <i class="bi bi-list-check text-2xl"></i>
                                <span class="text-xs">Lacak</span>
                            </div>
                        </a>

                        {{-- Notifikasi --}}
                        <a href="{{ route('notifikasi') }}" class="h-full flex items-center justify-center px-2 {{ request()->routeIs('notifikasi') ? 'border-t border-black' : '' }}">
                            <div class="flex flex-col items-center">
                                <i class="bi bi-bell text-2xl"></i>
                                <span class="text-xs">Notifikasi</span>
                            </div>
                        </a>

                        {{-- Pesan --}}
                        <a href="{{ route('pesan') }}" class="h-full flex items-center justify-center px-2 {{ request()->routeIs('pesan') ? 'border-t border-black' : '' }}">
                            <div class="flex flex-col items-center">
                                <i class="bi bi-chat-dots text-2xl"></i>
                                <span class="text-xs">Pesan</span>
                            </div>
                        </a>
                    @endif
                </div>
            </nav>
        </div>
    </body>
</html>
