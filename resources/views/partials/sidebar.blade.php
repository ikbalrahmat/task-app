{{-- Sidebar Partial --}}
<aside class="fixed top-0 left-0 h-full w-64 bg-[#1a1d27] border-r border-[#333650] flex flex-col z-50">
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 pt-6 pb-5 border-b border-[#333650]">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0"
             style="background: linear-gradient(135deg, #4f80ff, #a78bfa);">⚡</div>
        <div>
            <div class="font-bold text-white text-sm leading-tight">TaskFlow</div>
            <div class="text-[10px] text-slate-400 uppercase tracking-widest">Project Manager</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
        <p class="text-[10px] uppercase tracking-widest text-slate-300 font-semibold px-3 mb-2">Menu Utama</p>

        <a href="{{ route('dashboard') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('dashboard') ? 'bg-blue-950 text-blue-400 border border-blue-900/60' : 'text-slate-400 hover:bg-[#222535] hover:text-white' }}">
            <span class="text-base">📊</span> Dashboard
        </a>
        <a href="{{ route('projects.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('projects.*') ? 'bg-blue-950 text-blue-400 border border-blue-900/60' : 'text-slate-400 hover:bg-[#222535] hover:text-white' }}">
            <span class="text-base">📁</span> Project
        </a>
        <a href="{{ route('tasks.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('tasks.*') ? 'bg-blue-950 text-blue-400 border border-blue-900/60' : 'text-slate-400 hover:bg-[#222535] hover:text-white' }}">
            <span class="text-base">✅</span> Task
        </a>
        <a href="{{ route('calendar') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('calendar') ? 'bg-blue-950 text-blue-400 border border-blue-900/60' : 'text-slate-400 hover:bg-[#222535] hover:text-white' }}">
            <span class="text-base">📅</span> Kalender
        </a>
        <a href="{{ route('gantt') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('gantt') ? 'bg-blue-950 text-blue-400 border border-blue-900/60' : 'text-slate-400 hover:bg-[#222535] hover:text-white' }}">
            <span class="text-base">📉</span> Gantt Chart
        </a>

        <p class="text-[10px] uppercase tracking-widest text-slate-300 font-semibold px-3 pt-4 mb-2">Laporan</p>
        <a href="{{ route('reports') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('reports') ? 'bg-blue-950 text-blue-400 border border-blue-900/60' : 'text-slate-400 hover:bg-[#222535] hover:text-white' }}">
            <span class="text-base">📈</span> Progress Report
        </a>
        <a href="{{ route('reminders') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('reminders') ? 'bg-blue-950 text-blue-400 border border-blue-900/60' : 'text-slate-400 hover:bg-[#222535] hover:text-white' }}">
            <span class="text-base">🔔</span> Reminder
        </a>

        @if(auth()->user()->isAdmin())
        <p class="text-[10px] uppercase tracking-widest text-slate-300 font-semibold px-3 pt-4 mb-2">Administrasi</p>
        <a href="{{ route('users.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                  {{ request()->routeIs('users.*') ? 'bg-blue-950 text-blue-400 border border-blue-900/60' : 'text-slate-400 hover:bg-[#222535] hover:text-white' }}">
            <span class="text-base">👥</span> Pengguna
        </a>
        @endif
    </nav>

    {{-- Logout --}}
    <div class="px-4 py-4 border-t border-[#333650]">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-red-950 hover:text-red-400 transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h6a2 2 0 012 2v1"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>
