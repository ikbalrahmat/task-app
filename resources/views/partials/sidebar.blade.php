{{-- Sidebar Backdrop for Mobile --}}
<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm lg:hidden"
     @click="sidebarOpen = false"
     style="display: none;"></div>

{{-- Sidebar Partial --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-[#1e3a8a] via-[#1e40af] to-[#2563eb] flex flex-col z-50 transition-transform duration-300 shadow-xl shadow-blue-900/20">
    {{-- Logo --}}
    <div class="flex items-center justify-between px-6 pt-6 pb-5 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 bg-white/15 shadow-lg shadow-blue-900/30">⚡</div>
            <div>
                <div class="font-bold text-white text-sm leading-tight">TaskFlow</div>
                <div class="text-[10px] text-blue-200/60 uppercase tracking-widest font-semibold">Project Manager</div>
            </div>
        </div>
        {{-- Close Button for Mobile --}}
        <button @click="sidebarOpen = false" 
                class="p-1.5 rounded-lg text-white/50 hover:text-white hover:bg-white/10 transition-colors lg:hidden"
                aria-label="Close Sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
        <p class="text-[10px] uppercase tracking-widest text-blue-200/40 font-semibold px-3 mb-2">Menu Utama</p>

        <a href="{{ route('dashboard') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <span class="text-base">📊</span> Dashboard
        </a>
        <a href="{{ route('projects.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('projects.*') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <span class="text-base">📁</span> Project
        </a>
        <a href="{{ route('subprojects.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('subprojects.*') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <span class="text-base">📂</span> Sub-Project
        </a>
        <a href="{{ route('tasks.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('tasks.*') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <span class="text-base">✅</span> Task
        </a>
        <a href="{{ route('calendar') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('calendar') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <span class="text-base">📅</span> Kalender
        </a>
        <a href="{{ route('gantt') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('gantt') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <span class="text-base">📉</span> Gantt Chart
        </a>

        <p class="text-[10px] uppercase tracking-widest text-blue-200/40 font-semibold px-3 pt-4 mb-2">Laporan</p>
        <a href="{{ route('reports') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('reports') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <span class="text-base">📈</span> Progress Report
        </a>
        <a href="{{ route('reminders') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('reminders') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <span class="text-base">🔔</span> Reminder
        </a>

        @if(auth()->user()->isAdmin())
        <p class="text-[10px] uppercase tracking-widest text-blue-200/40 font-semibold px-3 pt-4 mb-2">Administrasi</p>
        <a href="{{ route('users.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('users.*') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <span class="text-base">👥</span> Pengguna
        </a>
        <a href="{{ route('activity-log.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('activity-log.*') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <span class="text-base">🛡️</span> Log Aktivitas
        </a>
        @endif

    </nav>

    {{-- Change Password & Logout --}}
    <div class="px-4 py-4 border-t border-white/10 space-y-2">
        <a href="{{ route('change-password') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('change-password') ? 'bg-white/15 text-white border-l-2 border-white' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <span class="text-base">🔑</span> Ganti Password
        </a>
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium text-blue-100/70 hover:bg-red-500/20 hover:text-red-200 transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h6a2 2 0 012 2v1"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

