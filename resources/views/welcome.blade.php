<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Platform jasa service elektronik terpercaya dengan tracking real-time dan teknisi verified">
    <title>Servisio - Platform Jasa Service Terpercaya</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @auth
        <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
        <script>
            window.OneSignalDeferred = window.OneSignalDeferred || [];
            OneSignalDeferred.push(async function(OneSignal) {
                await OneSignal.init({
                    appId: "{{ env('ONESIGNAL_APP_ID') }}",
                    allowLocalhostAsSecureOrigin: true,
                });
                if (OneSignal.Notifications.permission === 'granted') {
                    await OneSignal.login('{{ Auth::id() }}');
                }
            });
        </script>
    @endauth

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    animation: {
                        'ping-slow': 'ping 1.8s ease-out infinite',
                        'pulse-ring': 'pulseRing 2s ease-in-out infinite',
                    },
                    keyframes: {
                        pulseRing: {
                            '0%, 100%': { boxShadow: '0 0 0 0 rgba(37,99,235,0.4)' },
                            '50%': { boxShadow: '0 0 0 8px rgba(37,99,235,0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom utilities not covered by Tailwind */
        .gradient-text {
            background: linear-gradient(135deg, #2563eb, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .logo-gradient {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .tl-active-dot {
            animation: pulseRing 2s ease-in-out infinite;
        }
        @keyframes pulseRing {
            0%, 100% { box-shadow: 0 0 0 0 rgba(37,99,235,0.4); }
            50% { box-shadow: 0 0 0 8px rgba(37,99,235,0); }
        }
    </style>
</head>
<body class="antialiased font-sans bg-white text-slate-900">

<!-- ============================================================
     NAVIGATION
     ============================================================ -->
<nav class="fixed top-0 left-0 right-0 z-50 h-16 bg-white/85 backdrop-blur-md border-b border-slate-200/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between gap-4">

        <!-- Logo -->
        <a href="#" class="flex items-center gap-2.5 flex-shrink-0 no-underline">
            <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/25">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-xl font-extrabold logo-gradient tracking-tight">Servisio</span>
        </a>

        <!-- Desktop links -->
        <ul class="hidden md:flex items-center gap-8 list-none">
            <li><a href="#features" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors no-underline">Fitur</a></li>
            <li><a href="#how-it-works" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors no-underline">Cara Kerja</a></li>
            <li><a href="{{ route('register.technician') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors no-underline">Teknisi</a></li>
        </ul>

        <!-- CTA -->
        @auth
            <div class="flex items-center">
                @if(auth()->user()->role === 'technician')
                    <a href="{{ route('dashboard_technician') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg shadow-blue-600/25 hover:shadow-blue-600/35 hover:-translate-y-0.5 transition-all no-underline">
                        Dashboard
                    </a>
                @elseif(auth()->user()->role === 'customer')
                    <a href="{{ route('beranda') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg shadow-blue-600/25 hover:shadow-blue-600/35 hover:-translate-y-0.5 transition-all no-underline">
                        Beranda
                    </a>
                @elseif(auth()->user()->role === 'admin')
                    <a href="{{ route('dashboard_admin') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg shadow-blue-600/25 hover:shadow-blue-600/35 hover:-translate-y-0.5 transition-all no-underline">
                        Beranda
                    </a>
                @endauth
            </div>
        @endauth

        @guest
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="hidden sm:inline-flex text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 px-3 py-2 rounded-lg transition-all no-underline">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg shadow-blue-600/25 hover:shadow-blue-600/35 hover:-translate-y-0.5 transition-all no-underline">
                    Daftar Sekarang
                </a>
                <!-- Hamburger -->
                <button onclick="toggleMenu()" class="md:hidden flex flex-col gap-1.5 p-2 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Menu">
                    <span class="block w-5 h-0.5 bg-slate-700 rounded"></span>
                    <span class="block w-5 h-0.5 bg-slate-700 rounded"></span>
                    <span class="block w-5 h-0.5 bg-slate-700 rounded"></span>
                </button>
            </div>
        @endguest
    </div>
</nav>

<!-- Mobile menu -->
<div id="mobileMenu" class="hidden fixed top-16 left-0 right-0 z-40 bg-white border-b border-slate-200 shadow-lg px-5 py-4 md:hidden">
    <a href="#features" onclick="toggleMenu()" class="block py-3 text-sm font-medium text-slate-700 border-b border-slate-100 no-underline">Fitur</a>
    <a href="#how-it-works" onclick="toggleMenu()" class="block py-3 text-sm font-medium text-slate-700 border-b border-slate-100 no-underline">Cara Kerja</a>
    <a href="{{ route('register.technician') }}" onclick="toggleMenu()" class="block py-3 text-sm font-medium text-slate-700 border-b border-slate-100 no-underline">Teknisi</a>
    <a href="{{ route('login') }}" onclick="toggleMenu()" class="block py-3 text-sm font-medium text-slate-700 border-b border-slate-100 no-underline">Masuk</a>
    <a href="{{ route('register') }}" class="mt-3 w-full flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-lg no-underline">
        Daftar Sekarang
    </a>
</div>


<!-- ============================================================
     HERO
     ============================================================ -->
<section class="relative pt-28 pb-20 lg:pt-32 lg:pb-28 overflow-hidden">
    <!-- Background blobs -->
    <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 right-0 w-[600px] h-[600px] bg-blue-50 rounded-full blur-3xl opacity-80"></div>
        <div class="absolute bottom-0 -left-1/4 w-[500px] h-[500px] bg-indigo-50 rounded-full blur-3xl opacity-70"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            <!-- Content -->
            <div class="text-center lg:text-left">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-full text-blue-700 text-xs font-bold tracking-wide uppercase mb-6">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                    </span>
                    500+ Teknisi Terverifikasi
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-[3.25rem] font-extrabold tracking-tight leading-[1.15] text-slate-900 mb-5">
                    Service Elektronik<br>
                    <span class="gradient-text">Cepat & Terpercaya</span>
                </h1>

                <p class="text-lg text-slate-500 leading-relaxed mb-8 max-w-lg mx-auto lg:mx-0">
                    Platform digital untuk layanan perbaikan elektronik dengan tracking real-time, teknisi terverifikasi, dan garansi pelayanan terbaik. Hemat waktu, transparan, dan aman.
                </p>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start mb-12">
                    <a href="{{ route('beranda') }}" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 text-base font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xl shadow-blue-600/25 hover:shadow-blue-600/35 hover:-translate-y-0.5 transition-all no-underline">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari Teknisi Sekarang
                    </a>
                    <a href="#how-it-works" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 text-base font-semibold text-slate-700 bg-white border-2 border-slate-200 hover:border-blue-300 hover:text-blue-600 rounded-xl hover:-translate-y-0.5 transition-all no-underline">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Lihat Cara Kerja
                    </a>
                </div>

                <!-- Stats -->
                {{-- <div class="grid grid-cols-3 gap-0 border-t border-slate-200 pt-8 max-w-sm mx-auto lg:mx-0 lg:max-w-none">
                    <div class="pr-6">
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">10K+</div>
                        <div class="text-xs text-slate-500 mt-1 font-medium">Service Selesai</div>
                    </div>
                    <div class="px-6 border-l border-slate-200">
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">4.8★</div>
                        <div class="text-xs text-slate-500 mt-1 font-medium">Rating Rata-rata</div>
                    </div>
                    <div class="pl-6 border-l border-slate-200">
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">30min</div>
                        <div class="text-xs text-slate-500 mt-1 font-medium">Respon Cepat</div>
                    </div>
                </div> --}}
            </div>

            <!-- Visual card -->
            <div class="relative order-first lg:order-last">
                <div class="relative bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-7 shadow-2xl shadow-blue-600/30 max-w-md mx-auto lg:max-w-none">
                    <!-- Decorative circles -->
                    <div class="absolute -top-16 -right-16 w-56 h-56 bg-white/10 rounded-full pointer-events-none"></div>
                    <div class="absolute -bottom-12 -left-12 w-44 h-44 bg-white/8 rounded-full pointer-events-none"></div>

                    <!-- Status card -->
                    <div class="relative bg-white rounded-2xl p-6 shadow-xl z-10">
                        <!-- Header -->
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-11 h-11 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-bold text-slate-800">Status Service</div>
                                <div class="text-xs font-semibold text-green-600 mt-0.5">Sedang Dikerjakan</div>
                            </div>
                            <span class="bg-blue-50 text-blue-700 text-[11px] font-bold px-2.5 py-1 rounded-full flex-shrink-0">#SRV-001</span>
                        </div>

                        <!-- Timeline -->
                        <div class="space-y-4">
                            <!-- Done -->
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-slate-800">Diterima</div>
                                    <div class="text-xs text-slate-400">10:30 WIB</div>
                                </div>
                            </div>
                            <!-- Done -->
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-slate-800">Diagnosa</div>
                                    <div class="text-xs text-slate-400">11:15 WIB</div>
                                </div>
                            </div>
                            <!-- Active -->
                            <div class="flex items-center gap-3">
                                <div class="tl-active-dot w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="w-2.5 h-2.5 bg-white rounded-full"></span>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-blue-700">Perbaikan</div>
                                    <div class="text-xs font-medium text-blue-500">Sedang berlangsung…</div>
                                </div>
                            </div>
                            <!-- Pending -->
                            <div class="flex items-center gap-3 opacity-40">
                                <div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="w-2.5 h-2.5 bg-slate-400 rounded-full"></span>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-slate-700">Selesai</div>
                                    <div class="text-xs text-slate-400">Estimasi 14:00</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating tech badge -->
                    <div class="absolute -bottom-5 -right-3 bg-white rounded-2xl px-4 py-3 shadow-xl border border-slate-100 flex items-center gap-3 z-20">
                        <img src="https://ui-avatars.com/api/?name=Budi+S&background=2563EB&color=fff&bold=true" alt="Teknisi" class="w-10 h-10 rounded-full flex-shrink-0">
                        <div>
                            <div class="text-sm font-bold text-slate-800">Budi Santoso</div>
                            <div class="flex items-center gap-1 text-xs text-slate-500 mt-0.5">
                                <span class="text-amber-400">★</span>
                                4.9 · 128 ulasan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ============================================================
     FEATURES
     ============================================================ -->
<section id="features" class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-14">
            <div class="text-xs font-bold tracking-widest uppercase text-blue-600 mb-3">Keunggulan Platform</div>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mb-4">Kenapa Memilih Servisio?</h2>
            <p class="text-slate-500 text-base leading-relaxed">Platform modern dengan fitur lengkap untuk pengalaman service elektronik terbaik</p>
        </div>

        <!-- Grid -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">

            <div class="bg-white rounded-2xl p-7 border border-slate-200 hover:shadow-xl hover:-translate-y-1 hover:border-blue-200 transition-all duration-300">
                <div class="w-13 h-13 w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-2">Tracking Real-Time</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Pantau status perbaikan perangkat Anda secara langsung dari penerimaan hingga selesai, tanpa perlu menelepon berulang kali.</p>
            </div>

            <div class="bg-white rounded-2xl p-7 border border-slate-200 hover:shadow-xl hover:-translate-y-1 hover:border-indigo-200 transition-all duration-300">
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-2">Teknisi Terverifikasi</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Semua teknisi telah melalui proses verifikasi ketat dengan sertifikasi dan latar belakang terpercaya.</p>
            </div>

            <div class="bg-white rounded-2xl p-7 border border-slate-200 hover:shadow-xl hover:-translate-y-1 hover:border-purple-200 transition-all duration-300">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-2">Lokasi Terdekat</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Temukan teknisi terdekat dari lokasi Anda dengan fitur pencarian berbasis radius dan integrasi maps.</p>
            </div>

            <div class="bg-white rounded-2xl p-7 border border-slate-200 hover:shadow-xl hover:-translate-y-1 hover:border-green-200 transition-all duration-300">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-2">Estimasi Transparan</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Dapatkan estimasi biaya dan waktu perbaikan yang jelas sebelum menyetujui service, tanpa biaya tersembunyi.</p>
            </div>

            <div class="bg-white rounded-2xl p-7 border border-slate-200 hover:shadow-xl hover:-translate-y-1 hover:border-amber-200 transition-all duration-300">
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-2">Review & Rating</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Lihat ulasan dan rating dari pelanggan sebelumnya untuk memilih teknisi terbaik sesuai kebutuhan Anda.</p>
            </div>

            <div class="bg-white rounded-2xl p-7 border border-slate-200 hover:shadow-xl hover:-translate-y-1 hover:border-rose-200 transition-all duration-300">
                <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-2">Garansi Service</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Nikmati garansi perbaikan hingga 30 hari. Jika ada masalah yang sama, kami perbaiki tanpa biaya tambahan.</p>
            </div>

        </div>
    </div>
</section>


<!-- ============================================================
     HOW IT WORKS
     ============================================================ -->
<section id="how-it-works" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-14">
            <div class="text-xs font-bold tracking-widest uppercase text-blue-600 mb-3">Cara Kerja</div>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mb-4">Mudah dalam 4 Langkah</h2>
            <p class="text-slate-500 text-base leading-relaxed">Proses yang simpel dari pencarian hingga perangkat Anda selesai diperbaiki</p>
        </div>

        <!-- Steps -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 relative">
            <!-- Connector line (desktop only) -->
            <div class="hidden lg:block absolute top-9 left-[calc(12.5%+44px)] right-[calc(12.5%+44px)] h-0.5 bg-gradient-to-r from-blue-200 via-blue-400 to-green-400 z-0"></div>

            <!-- Step 1 -->
            <div class="flex flex-col items-center text-center sm:items-center sm:text-center relative z-10">
                <div class="w-[72px] h-[72px] bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-blue-600/25">
                    <span class="text-2xl font-extrabold text-white">1</span>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-2">Cari Teknisi</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Masukkan lokasi dan jenis kerusakan untuk menemukan teknisi terdekat</p>
            </div>

            <!-- Step 2 -->
            <div class="flex flex-col items-center text-center relative z-10">
                <div class="w-[72px] h-[72px] bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-blue-600/25">
                    <span class="text-2xl font-extrabold text-white">2</span>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-2">Booking Online</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Pilih jadwal yang tersedia dan kirimkan detail kerusakan perangkat</p>
            </div>

            <!-- Step 3 -->
            <div class="flex flex-col items-center text-center relative z-10">
                <div class="w-[72px] h-[72px] bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-blue-600/25">
                    <span class="text-2xl font-extrabold text-white">3</span>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-2">Tracking Real-Time</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Pantau progres perbaikan dari aplikasi dengan notifikasi otomatis</p>
            </div>

            <!-- Step 4 -->
            <div class="flex flex-col items-center text-center relative z-10">
                <div class="w-[72px] h-[72px] bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-green-500/25">
                    <span class="text-2xl font-extrabold text-white">4</span>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-2">Selesai & Review</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Ambil perangkat dan berikan rating untuk membantu pelanggan lain</p>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     CTA BAND
     ============================================================ -->
<section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="relative bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl px-8 py-16 sm:px-16 text-center overflow-hidden shadow-2xl shadow-blue-600/30">
        <!-- Decorative -->
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-52 h-52 bg-white/8 rounded-full pointer-events-none"></div>

        <div class="relative z-10 max-w-xl mx-auto">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight mb-4">
                Siap Memperbaiki Perangkat Anda?
            </h2>
            <p class="text-white/75 text-base leading-relaxed mb-8">
                Bergabung dengan ribuan pelanggan yang telah mempercayakan perbaikan elektronik mereka kepada teknisi profesional kami.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="#" class="inline-flex items-center justify-center px-7 py-3 text-sm font-bold text-blue-600 bg-white hover:bg-blue-50 rounded-xl hover:-translate-y-0.5 transition-all shadow-lg no-underline">
                    Mulai Sekarang — Gratis!
                </a>
                <a href="#features" class="inline-flex items-center justify-center px-7 py-3 text-sm font-semibold text-white border-2 border-white/30 hover:border-white/70 hover:bg-white/10 rounded-xl transition-all no-underline">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     FOOTER
     ============================================================ -->
<footer class="bg-slate-50 border-t border-slate-200 pt-14 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Bottom bar -->
        <div class="border-t border-slate-200 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-slate-400">© 2026 Servisio. All rights reserved.</p>
            <div class="flex items-center gap-4">
                <a href="#" class="text-slate-400 hover:text-blue-600 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"/></svg>
                </a>
                <a href="#" class="text-slate-400 hover:text-blue-600 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                </a>
            </div>
        </div>
    </div>
</footer>

<script>
    function toggleMenu() {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    }
</script>

</body>
</html>