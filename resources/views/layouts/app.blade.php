<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name', 'Servisio') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <style>
            @keyframes shrink {
                from { width: 100%; }
                to { width: 0%; }
            }
            .animate-shrink-width {
                animation: shrink 3s linear backwards;
            }
        </style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @auth
            <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignal.js" async></script>
            <script>
                window.OneSignal = window.OneSignal || [];
        
                function initOneSignal() {
                    // Cek apakah SDK sudah load (bukan array lagi)
                    if (Array.isArray(window.OneSignal)) {
                        // Belum siap, coba lagi 500ms lagi
                        setTimeout(initOneSignal, 500);
                        return;
                    }
                    
                    OneSignal.init({
                        appId: "{{ env('ONESIGNAL_APP_ID') }}",
                        allowLocalhostAsSecureOrigin: true,
                    });

                    window._oneSignalReady = true;
                    window.dispatchEvent(new CustomEvent('onesignal-ready'));
                    console.log('✅ [OneSignal] Init selesai, keys:', Object.keys(OneSignal));
                }

                // Mulai cek setelah halaman load
                window.addEventListener('load', function() {
                    initOneSignal();
                });
            </script>
        @endauth
    </head>
    <body class="font-sans antialiased">
        <div 
            class="min-h-screen bg-gray-100 dark:bg-gray-900 overflow-hidden relative"
            x-data="{
                isSubscribed: false,
                userHasInteracted: false,
                playNotification() {
                    if (!this.userHasInteracted) {
                        console.log('🔇 [Servisio] Diabaikan — belum ada interaksi pengguna.');
                        return;
                    }
                    const sound = new Audio('{{ asset('assets/sound_notifikasi/notifikasi.mp3') }}');
                    sound.play().then(() => {
                        console.log('✅ [Servisio] Suara diputar.');
                    }).catch(err => {
                        console.warn('⚠️ [Servisio] Autoplay diblokir:', err);
                    });
                },
                initInteractionListener() {
                    var self = this;
                    var handler = function() {
                        self.userHasInteracted = true;
                        ['click','keydown','touchstart','scroll'].forEach(function(e) {
                            document.removeEventListener(e, handler, { capture: true });
                        });
                        console.log('👆 [Servisio] Interaksi terdeteksi.');
                    };
                    ['click','keydown','touchstart','scroll'].forEach(function(e) {
                        document.addEventListener(e, handler, { capture: true, passive: true });
                    });
                }
            }"
            x-init="initInteractionListener()"
            @play-notif-sound.window="playNotification()"
            @onesignal-ready.window="
                if (window.OneSignal && OneSignal.Notifications) {
                    isSubscribed = (OneSignal.Notifications.permission === 'granted');
                }
            "
        >
            <livewire:layout.navigation />
            <header class="flex sm:hidden">
                {{ $header ?? '' }}
            </header>
            <main class="{{ request()->routeIs('pesan') || request()->routeIs('lacak') ? 'mt-20' : 'mt-10' }}">
                {{ $slot }}
            </main>

            @auth
                <div 
                    x-show="!isSubscribed" 
                    class="fixed bottom-24 left-4 right-4 sm:left-auto sm:right-5 sm:max-w-sm bg-indigo-600 text-white p-3.5 rounded-2xl shadow-2xl z-[9999] flex items-center justify-between gap-3 border border-indigo-500/30 backdrop-blur-md"
                >
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                            <i class="bi bi-bell-fill text-amber-300 animate-bounce"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold truncate">Aktifkan Notifikasi HP</p>
                            <p class="text-[10px] text-indigo-200 truncate">Agar infonya realtime saat browser ditutup</p>
                        </div>
                    </div>
                    <button 
                        type="button"
                        @click="window.aktifkanNotifikasi()"
                        @onesignal-subscribed.window="isSubscribed = true"
                        class="px-3 py-1.5 bg-white text-indigo-700 text-[11px] font-bold rounded-xl hover:bg-indigo-50 active:scale-95 transition-all shadow-sm shrink-0"
                    >
                        Aktifkan
                    </button>
                </div>
            @endauth

            <div class="fixed right-5 bottom-20 flex flex-col gap-3 z-50">
                @if (session()->has('success'))
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
                    class="flex items-center gap-3 bg-slate-900 dark:bg-blue-600 rounded-xl py-3 px-5 shadow-2xl border-b-4 border-green-500 overflow-hidden"
                >
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-green-500">
                        <i class="bi bi-check-lg text-white"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-white text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 bg-green-400 animate-shrink-width"></div>
                </div>
                @endif

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
        @auth
            <script>

                window.aktifkanNotifikasi = function() {
                    if (!window._oneSignalReady) {
                        console.warn('OneSignal belum siap');
                        return;
                    }
                    OneSignal.Notifications.requestPermission().then(function(permission) {
                        console.log('Permission:', permission);
                        if (permission === 'granted') {
                            OneSignal.login('{{ Auth::id() }}').then(function() {
                                console.log('✅ Login OneSignal berhasil');
                                // Update Alpine state
                                window.dispatchEvent(new CustomEvent('onesignal-subscribed'));
                            });
                        }
                    }).catch(function(err) {
                        console.error('Error:', err);
                    });
                };

                function pingOnline() {
                    fetch('/ping-online', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });
                }

                document.addEventListener('livewire:initialized', function() {
                    pingOnline();
                    setInterval(pingOnline, 60000);
                });
            </script>
        @endauth
    </body>
</html>