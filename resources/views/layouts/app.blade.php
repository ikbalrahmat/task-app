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
                <a href="{{ route('reminders') }}" class="relative text-slate-500 hover:text-slate-900 transition-colors">
                    🔔
                </a>
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
