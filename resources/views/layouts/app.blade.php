<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SENTIMEN | @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="/images/logo.png"  media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/png" href="/images/logo1.png" media="(prefers-color-scheme: light)">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#f0f4ff] text-slate-900 font-sans antialiased" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
<div class="flex min-h-screen">

    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main Content --}}
    <div :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'" class="flex-1 flex flex-col min-h-screen min-w-0 overflow-hidden transition-all duration-300">

        {{-- Topbar --}}
        <header class="sticky top-0 z-40 h-16 bg-white/80 backdrop-blur-md border-b border-[#e0e7ff] flex items-center justify-between px-4 sm:px-8">
            <div class="flex items-center gap-3">
                {{-- Hamburger Button --}}
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="p-2 -ml-2 rounded-xl text-[#1e3a8a]/60 hover:text-[#1e3a8a] hover:bg-[#e0e7ff]/50 transition-colors"
                        aria-label="Toggle Sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="font-bold text-[#1e3a8a] text-base sm:text-lg leading-tight">@yield('heading', 'Dashboard')</h1>
                    <p class="text-xs text-[#2563eb]/60">@yield('subheading', '')</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                {{-- Notification Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative text-[#1e3a8a]/50 hover:text-[#1e3a8a] transition-colors p-1.5 rounded-lg hover:bg-[#e0e7ff]/50 cursor-pointer flex items-center justify-center">
                        🔔
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-0 right-0 w-4.5 h-4.5 bg-red-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center translate-x-1 -translate-y-1">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2.5 w-80 bg-white border border-[#e0e7ff] rounded-2xl shadow-xl shadow-blue-900/8 z-50 p-4"
                         style="display: none;">
                        <div class="flex justify-between items-center pb-2 mb-2 border-b border-[#e0e7ff]">
                            <span class="text-xs font-bold text-[#1e3a8a]">Notifikasi</span>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <form method="POST" action="{{ route('notifications.markAllRead') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-[#2563eb] hover:text-[#1e40af] font-semibold cursor-pointer">Tandai semua dibaca</button>
                                </form>
                            @endif
                        </div>
                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                <a href="{{ route('notifications.read', $notification->id) }}" class="block p-2.5 hover:bg-[#f0f4ff] rounded-xl transition-colors text-left border border-transparent hover:border-[#e0e7ff]">
                                    <p class="text-xs text-slate-700 leading-normal">
                                        {{ $notification->data['message'] ?? '' }}
                                    </p>
                                    <span class="text-[9px] text-[#2563eb]/50 block mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </a>
                            @empty
                                <div class="text-center py-6 text-[#2563eb]/40 text-xs">
                                    Tidak ada notifikasi baru
                                </div>
                            @endforelse
                        </div>
                        @if(auth()->user()->notifications->count() > 0)
                            <div class="border-t border-[#e0e7ff] pt-2 mt-2 text-center">
                                <a href="{{ route('reminders') }}" class="text-[10px] text-[#2563eb]/60 hover:text-[#1e3a8a] font-semibold">Lihat Semua Riwayat</a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <span class="text-[#e0e7ff]">|</span>
                <div class="relative" x-data="{ profileOpen: false }">
                    <button @click="profileOpen = !profileOpen" class="flex items-center gap-2.5 hover:bg-[#e0e7ff]/50 p-1.5 rounded-xl transition-colors text-left">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#1e3a8a] to-[#2563eb] flex items-center justify-center font-bold text-white text-xs shadow-sm shadow-blue-900/20">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="hidden sm:block">
                            <div class="text-xs font-semibold text-[#1e3a8a]">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-[#2563eb]/60">{{ auth()->user()->role }}</div>
                        </div>
                        <svg class="w-4 h-4 text-[#1e3a8a]/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    
                    <div x-show="profileOpen" @click.outside="profileOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2.5 w-48 bg-white border border-[#e0e7ff] rounded-2xl shadow-xl shadow-blue-900/8 z-50 p-2"
                         style="display: none;">
                        <a href="{{ route('profile.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-[#1e3a8a] hover:bg-[#f0f4ff] rounded-xl transition-colors font-medium">
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Profil Saya
                        </a>
                        <div class="h-px bg-[#e0e7ff] my-1"></div>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-xl transition-colors font-medium">
                                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h6a2 2 0 012 2v1" /></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-4 sm:p-8">
            @include('partials.flash')
            @yield('content')
        </main>

        <footer class="py-4 px-8 border-t border-[#e0e7ff] text-center text-xs text-[#1e3a8a]/50">
            SENTIMEN &copy; {{ date('Y') }} — Sistem Evaluasi dan Notifikasi Terintegrasi Monitoring
        </footer>
    </div>
</div>

@stack('scripts')

<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-messaging.js";

    const firebaseConfig = {
        apiKey: "AIzaSyAy3CM2I2OREwSWHPkWVQ0_sVk-3e415WM",
        authDomain: "sentimen-notif.firebaseapp.com",
        projectId: "sentimen-notif",
        storageBucket: "sentimen-notif.firebasestorage.app",
        messagingSenderId: "441496587129",
        appId: "1:441496587129:web:3cd9cd4ac13acf87083da8"
    };

    const app = initializeApp(firebaseConfig);
    
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then((registration) => {
                const messaging = getMessaging(app);
                
                Notification.requestPermission().then((permission) => {
                    if (permission === 'granted') {
                        // NOTE: vapidKey is highly recommended by Firebase for production push notifications
                        // To get this: Firebase Console -> Project Settings -> Cloud Messaging -> Web Push certificates -> Generate
                        getToken(messaging, { 
                            serviceWorkerRegistration: registration,
                            vapidKey: "BGxhGPzxTiwLRNVzrJWRdSTMdK8T1JT6nwJIfcNJD0VnQFaLvWkwNuSUb7XpBjSyleormihjzPmyunDGjLgqLuc"
                        }).then((currentToken) => {
                            if (currentToken) {
                                // Save token to Laravel backend
                                fetch("{{ route('profile.fcm_token') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ token: currentToken })
                                });
                            }
                        }).catch((err) => {
                            console.error('Error retrieving FCM token: ', err);
                        });
                    }
                });

                // Foreground message handler
                onMessage(messaging, (payload) => {
                    console.log('Foreground message received: ', payload);
                    
                    // Munculkan notifikasi sistem meskipun web sedang dibuka
                    const title = payload.notification?.title || 'Pemberitahuan Baru';
                    const options = {
                        body: payload.notification?.body || '',
                        icon: '/images/logo.png' // Ganti jika logo berbeda
                    };
                    new Notification(title, options);
                    
                    // Opsional: Reload otomatis halaman (atau perbarui angka lonceng pakai JS)
                    // Biar simpel dan logo lonceng langsung update, kita reload aja saat notif masuk
                    // Tapi lebih aman nggak ngereload paksa kalau user lagi ngetik. Kita cuma alert.
                });
            });
    }
</script>
</body>
</html>

