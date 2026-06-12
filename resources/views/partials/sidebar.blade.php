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
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 bg-white/15 shadow-lg shadow-blue-900/30 text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </div>
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
        <p class="text-[10px] uppercase tracking-widest text-blue-200/40 font-semibold px-3 mb-2">Workspace</p>

        <a href="{{ route('dashboard') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
            Dashboard
        </a>
        <a href="{{ route('projects.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('projects.*') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
            Program
        </a>
        <a href="{{ route('subprojects.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('subprojects.*') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            List
        </a>
        <a href="{{ route('tasks.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('tasks.*') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
            Task
        </a>

        <p class="text-[10px] uppercase tracking-widest text-blue-200/40 font-semibold px-3 pt-4 mb-2">Pemantauan</p>
        <a href="{{ route('calendar') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('calendar') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            Kalender
        </a>
        <a href="{{ route('gantt') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('gantt') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
            Gantt Chart
        </a>
        <a href="{{ route('reports') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('reports') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            Progress Report
        </a>

        <p class="text-[10px] uppercase tracking-widest text-blue-200/40 font-semibold px-3 pt-4 mb-2">Personal</p>
        <a href="{{ route('reminders') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('reminders') ? 'bg-white/15 text-white border-l-2 border-white shadow-sm shadow-blue-900/20' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
            Reminder
        </a>

        @if(auth()->user()->isAdmin())
        <p class="text-[10px] uppercase tracking-widest text-blue-400/60 font-semibold px-3 pt-5 pb-2">Administrasi</p>
        <a href="{{ route('users.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('users.*') ? 'bg-white/15 text-white shadow-sm shadow-blue-950/30 border border-white/10' : 'text-blue-200/60 hover:bg-white/10 hover:text-blue-100' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            User Management
        </a>
        <a href="{{ route('activity-log.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('activity-log.*') ? 'bg-white/15 text-white shadow-sm shadow-blue-950/30 border border-white/10' : 'text-blue-200/60 hover:bg-white/10 hover:text-blue-100' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
            Activity Log
        </a>
        @endif
    </nav>
</aside>
