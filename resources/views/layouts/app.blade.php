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
<body class="bg-[#0f1117] text-white font-sans antialiased">
<div class="flex min-h-screen">

    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main Content --}}
    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        {{-- Topbar --}}
        <header class="sticky top-0 z-40 h-16 bg-[#0f1117]/80 backdrop-blur-md border-b border-[#333650] flex items-center justify-between px-8">
            <div>
                <h1 class="font-bold text-white text-lg leading-tight">@yield('heading', 'Dashboard')</h1>
                <p class="text-xs text-slate-400">@yield('subheading', '')</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('reminders') }}" class="relative text-slate-400 hover:text-white transition-colors">
                    🔔
                </a>
                <span class="text-slate-300">|</span>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-950 flex items-center justify-center font-bold text-blue-400 text-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-white">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-400">{{ auth()->user()->role }}</div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-8">
            @include('partials.flash')
            @yield('content')
        </main>

        <footer class="py-4 px-8 border-t border-[#333650] text-center text-xs text-slate-300">
            TaskFlow &copy; {{ date('Y') }} — Sistem Manajemen Project
        </footer>
    </div>
</div>

@stack('scripts')
</body>
</html>
