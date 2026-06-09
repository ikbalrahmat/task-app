<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TaskFlow — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
<div class="flex min-h-screen">

    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main Content --}}
    <div :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'" class="flex-1 flex flex-col min-h-screen w-full transition-all duration-300">

        {{-- Topbar --}}
        <header class="sticky top-0 z-40 h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 sm:px-8">
            <div class="flex items-center gap-3">
                {{-- Hamburger Button --}}
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="p-2 -ml-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors"
                        aria-label="Toggle Sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="font-bold text-slate-900 text-base sm:text-lg leading-tight">@yield('heading', 'Dashboard')</h1>
                    <p class="text-xs text-slate-500">@yield('subheading', '')</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                {{-- Notification Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative text-slate-500 hover:text-slate-900 transition-colors p-1.5 rounded-lg hover:bg-slate-100 cursor-pointer flex items-center justify-center">
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
                         class="absolute right-0 mt-2.5 w-80 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-4"
                         style="display: none;">
                        <div class="flex justify-between items-center pb-2 mb-2 border-b border-slate-100">
                            <span class="text-xs font-bold text-slate-800">Notifikasi</span>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <form method="POST" action="{{ route('notifications.markAllRead') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-blue-600 hover:text-blue-700 font-semibold cursor-pointer">Tandai semua dibaca</button>
                                </form>
                            @endif
                        </div>
                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                <a href="{{ route('notifications.read', $notification->id) }}" class="block p-2.5 hover:bg-slate-50 rounded-xl transition-colors text-left border border-slate-50 hover:border-slate-100">
                                    <p class="text-xs text-slate-700 leading-normal">
                                        {{ $notification->data['message'] ?? '' }}
                                    </p>
                                    <span class="text-[9px] text-slate-400 block mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </a>
                            @empty
                                <div class="text-center py-6 text-slate-400 text-xs">
                                    Tidak ada notifikasi baru
                                </div>
                            @endforelse
                        </div>
                        @if(auth()->user()->notifications->count() > 0)
                            <div class="border-t border-slate-100 pt-2 mt-2 text-center">
                                <a href="{{ route('reminders') }}" class="text-[10px] text-slate-500 hover:text-slate-700 font-semibold">Lihat Semua Riwayat</a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <span class="text-slate-200">|</span>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center font-bold text-blue-600 text-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="hidden sm:block">
                        <div class="text-xs font-semibold text-slate-800">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-500">{{ auth()->user()->role }}</div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-4 sm:p-8">
            @include('partials.flash')
            @yield('content')
        </main>

        <footer class="py-4 px-8 border-t border-slate-200 text-center text-xs text-slate-500">
            TaskFlow &copy; {{ date('Y') }} — Sistem Manajemen Project
        </footer>
    </div>
</div>

@stack('scripts')
</body>
</html>
