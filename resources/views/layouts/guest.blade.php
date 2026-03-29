<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex">
            <!-- Left Side - Image/Illustration -->
            <div class="hidden lg:flex xl:w-full relative overflow-hidden">
                <!-- Background Gradient -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-indigo-600 to-violet-700"></div>
                
                <!-- Decorative Elements -->
                <div class="absolute top-0 left-0 w-full h-full overflow-hidden">
                    <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute top-1/2 -right-24 w-72 h-72 bg-purple-500/20 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-24 left-1/3 w-80 h-80 bg-blue-400/20 rounded-full blur-3xl"></div>
                </div>

                <!-- Pattern Overlay -->
                <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

                <!-- Content -->
                <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
                    <div class="max-w-lg text-center">
                        <!-- Logo Icon -->
                        <div class="mb-8 inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl shadow-2xl">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>

                        <h1 class="text-4xl xl:text-5xl font-bold mb-6 leading-tight">
                            Selamat Datang Kembali
                        </h1>
                        <p class="text-xl text-blue-100 leading-relaxed mb-8">
                            Platform service elektronik terpercaya dengan tracking real-time dan teknisi profesional.
                        </p>

                        <!-- Features List -->
                        <div class="space-y-4 text-left inline-block">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span class="text-blue-50">Tracking perbaikan real-time</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span class="text-blue-50">500+ teknisi terverifikasi</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span class="text-blue-50">Garansi service 30 hari</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Quote -->
                <div class="absolute bottom-8 left-0 right-0 px-12 text-center">
                    <p class="text-sm text-blue-200">
                        "Solusi terbaik untuk perbaikan elektronik Anda"
                    </p>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="w-full xl:w-full flex flex-col justify-center items-center p-6 sm:p-12 bg-white dark:bg-slate-900 relative">
                <!-- Mobile Header (visible only on mobile) -->
                <div class="lg:hidden absolute top-0 left-0 right-0 p-6 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-xl font-bold">ServiceElektronik</span>
                    </div>
                </div>

                <!-- Form Container -->
                <div class="w-full max-w-md mt-16 lg:mt-0">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <div class="absolute bottom-6 left-0 right-0 text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        &copy; {{ date('Y') }} ServiceElektronik. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>