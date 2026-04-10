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
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 overflow-hidden relative">
            <livewire:layout.navigation />

            <header class="flex sm:hidden">
                {{ $header ?? '' }}
            </header>

            <!-- Page Content -->
            <main class="mt-10">
                {{ $slot }}
            </main>

            {{-- Alert Container --}}
            <div class="fixed right-5 bottom-20 flex flex-col gap-3 z-50">
                
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
        </div>
    </body>
</html>
