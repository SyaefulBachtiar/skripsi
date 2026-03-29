<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Platform jasa service elektronik terpercaya dengan tracking real-time dan teknisi verified">

        <title>ServiceElektronik - Platform Jasa Service Terpercaya</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-white text-slate-900">
        
        <!-- Navigation -->
        <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            ServiceElektronik
                        </span>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center gap-8">
                        <a href="#features" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Fitur</a>
                        <a href="#how-it-works" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Cara Kerja</a>
                        <a href="#technicians" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Teknisi</a>
                        <a href="#testimonials" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Testimoni</a>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex items-center gap-4">
                        @if (Route::has('login'))
                            @auth
                                @if(auth()->user()->role === 'technician')
                                    <a href="{{ url('/teknisi/dashboard') }}" class="items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20">
                                        Dashboard
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="text-[10px] md:text-base text-slate-600 hover:text-blue-600 font-medium transition-colors">
                                    Masuk
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-[10px] md:text-base inline-flex items-center justify-center px-3 md:px-5 py-1 md:py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-sm md:rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20">
                                        Daftar Sekarang
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-[6rem] pb-20 lg:pt-24 lg:pb-32 overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-[800px] h-[800px] bg-blue-50 rounded-full blur-3xl opacity-70"></div>
                <div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-[600px] h-[600px] bg-indigo-50 rounded-full blur-3xl opacity-70"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                    <!-- Hero Content -->
                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 rounded-full text-blue-700 text-sm font-semibold mb-6">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                            </span>
                            500+ Teknisi Terverifikasi
                        </div>
                        
                        <h1 class="text-4xl lg:text-6xl font-bold tracking-tight text-slate-900 mb-6 leading-tight">
                            Service Elektronik <br>
                            <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                Cepat & Terpercaya
                            </span>
                        </h1>
                        
                        <p class="text-lg text-slate-600 mb-8 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                            Platform digital untuk layanan perbaikan elektronik dengan tracking real-time, 
                            teknisi terverifikasi, dan garansi pelayanan terbaik. Hemat waktu, transparan, dan aman.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                            <a href="#booking" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all shadow-xl shadow-blue-600/20 hover:shadow-blue-600/30 hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari Teknisi Sekarang
                            </a>
                            <a href="#how-it-works" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-slate-700 bg-white border-2 border-slate-200 rounded-xl hover:border-blue-600 hover:text-blue-600 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Lihat Cara Kerja
                            </a>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-8 pt-8 border-t border-slate-200">
                            <div>
                                <div class="text-3xl font-bold text-slate-900">10K+</div>
                                <div class="text-sm text-slate-600 mt-1">Service Selesai</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-slate-900">4.8</div>
                                <div class="text-sm text-slate-600 mt-1">Rating Rata-rata</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-slate-900">30min</div>
                                <div class="text-sm text-slate-600 mt-1">Respon Cepat</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Image/Illustration -->
                    <div class="relative">
                        <div class="relative bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-8 shadow-2xl shadow-blue-600/30">
                            <!-- Decorative circles -->
                            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                            
                            <!-- Mockup Card -->
                            <div class="relative bg-white rounded-2xl p-6 shadow-xl">
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">Status Service</div>
                                        <div class="text-sm text-green-600 font-medium">Sedang Dikerjakan</div>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full">#SRV-2024-001</span>
                                    </div>
                                </div>
                                
                                <!-- Progress Timeline -->
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-slate-900">Diterima</div>
                                            <div class="text-xs text-slate-500">10:30 WIB</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-slate-900">Diagnosa</div>
                                            <div class="text-xs text-slate-500">11:15 WIB</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 animate-pulse">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-slate-900">Perbaikan</div>
                                            <div class="text-xs text-blue-600 font-medium">Sedang berlangsung</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 opacity-50">
                                        <div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center flex-shrink-0">
                                            <div class="w-2 h-2 bg-slate-400 rounded-full"></div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-slate-700">Selesai</div>
                                            <div class="text-xs text-slate-500">Estimasi 14:00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Badge -->
                            <div class="absolute -bottom-4 -right-4 bg-white rounded-xl p-4 shadow-lg border border-slate-100">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Tech+Pro&background=0D8ABC&color=fff" alt="Technician" class="w-10 h-10 rounded-full">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Budi Santoso</div>
                                        <div class="flex items-center gap-1 text-xs text-amber-500">
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            <span class="text-slate-600">4.9 (128 reviews)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">
                        Kenapa Memilih Kami?
                    </h2>
                    <p class="text-lg text-slate-600">
                        Platform modern dengan fitur lengkap untuk pengalaman service elektronik terbaik
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Tracking Real-Time</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Pantau status perbaikan perangkat Anda secara langsung dari penerimaan hingga selesai, tanpa perlu menelepon berulang kali.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="w-14 h-14 bg-indigo-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Teknisi Terverifikasi</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Semua teknisi telah melalui proses verifikasi ketat dengan sertifikasi dan latar belakang terpercaya.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="w-14 h-14 bg-purple-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Lokasi Terdekat</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Temukan teknisi terdekat dari lokasi Anda dengan fitur pencarian berbasis radius dan maps integrasi.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="w-14 h-14 bg-green-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Estimasi Transparan</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Dapatkan estimasi biaya dan waktu perbaikan yang jelas sebelum menyetujui service, tanpa biaya tersembunyi.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="w-14 h-14 bg-amber-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Review & Rating</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Lihat ulasan dan rating dari pelanggan sebelumnya untuk memilih teknisi terbaik sesuai kebutuhan Anda.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="w-14 h-14 bg-rose-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Garansi Service</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Nikmati garansi perbaikan hingga 30 hari. Jika ada masalah sama, kami perbaiki tanpa biaya tambahan.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section id="how-it-works" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">
                        Cara Kerja Platform
                    </h2>
                    <p class="text-lg text-slate-600">
                        Proses mudah dalam 4 langkah sederhana
                    </p>
                </div>

                <div class="grid md:grid-cols-4 gap-8">
                    <!-- Step 1 -->
                    <div class="relative text-center">
                        <div class="w-20 h-20 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-blue-600/30">
                            <span class="text-3xl font-bold text-white">1</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">Cari Teknisi</h3>
                        <p class="text-slate-600 text-sm">
                            Masukkan lokasi dan jenis kerusakan untuk menemukan teknisi terdekat
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative text-center">
                        <div class="w-20 h-20 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-blue-600/30">
                            <span class="text-3xl font-bold text-white">2</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">Booking Online</h3>
                        <p class="text-slate-600 text-sm">
                            Pilih jadwal yang tersedia dan kirimkan detail kerusakan perangkat
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative text-center">
                        <div class="w-20 h-20 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-blue-600/30">
                            <span class="text-3xl font-bold text-white">3</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">Tracking Real-Time</h3>
                        <p class="text-slate-600 text-sm">
                            Pantau progres perbaikan dari aplikasi dengan notifikasi otomatis
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="relative text-center">
                        <div class="w-20 h-20 bg-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-green-600/30">
                            <span class="text-3xl font-bold text-white">4</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">Selesai & Review</h3>
                        <p class="text-slate-600 text-sm">
                            Ambil perangkat dan berikan rating untuk membantu pelanggan lain
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        {{-- <section class="py-20 bg-slate-900 relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 left-0 w-96 h-96 bg-blue-600 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-600 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
            </div>
            
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
                <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6">
                    Siap Memperbaiki Perangkat Anda?
                </h2>
                <p class="text-xl text-slate-300 mb-10 max-w-2xl mx-auto">
                    Bergabung dengan ribuan pelanggan yang telah mempercayakan perbaikan elektronik mereka kepada teknisi profesional kami.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-blue-600 bg-white rounded-xl hover:bg-blue-50 transition-all shadow-xl">
                        Mulai Sekarang - Gratis!
                    </a>
                    <a href="#features" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white border-2 border-white/30 rounded-xl hover:border-white transition-all">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
        </section> --}}

        <!-- Footer -->
        <footer class="bg-slate-50 border-t border-slate-200 pt-16 pb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8 mb-12">
                    <!-- Brand -->
                    <div class="col-span-2">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-slate-900">ServiceElektronik</span>
                        </div>
                        <p class="text-slate-600 max-w-sm">
                            Platform terpercaya untuk layanan perbaikan elektronik dengan teknisi profesional dan tracking real-time.
                        </p>
                    </div>

                    <!-- Links -->
                    <div>
                        <h4 class="font-semibold text-slate-900 mb-4">Platform</h4>
                        <ul class="space-y-2 text-slate-600">
                            <li><a href="#" class="hover:text-blue-600 transition-colors">Cari Teknisi</a></li>
                            <li><a href="#" class="hover:text-blue-600 transition-colors">Jadi Teknisi</a></li>
                            <li><a href="#" class="hover:text-blue-600 transition-colors">Cara Kerja</a></li>
                            <li><a href="#" class="hover:text-blue-600 transition-colors">Harga</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-semibold text-slate-900 mb-4">Bantuan</h4>
                        <ul class="space-y-2 text-slate-600">
                            <li><a href="#" class="hover:text-blue-600 transition-colors">Pusat Bantuan</a></li>
                            <li><a href="#" class="hover:text-blue-600 transition-colors">Syarat & Ketentuan</a></li>
                            <li><a href="#" class="hover:text-blue-600 transition-colors">Kebijakan Privasi</a></li>
                            <li><a href="#" class="hover:text-blue-600 transition-colors">Kontak</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-slate-500 text-sm">
                        © 2024 ServiceElektronik. All rights reserved.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="text-slate-400 hover:text-blue-600 transition-colors">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"/></svg>
                        </a>
                        <a href="#" class="text-slate-400 hover:text-blue-600 transition-colors">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>

    </body>
</html>