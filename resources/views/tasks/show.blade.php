@extends('layouts.app')
@section('title', $task->name)
@section('heading', $task->name)
@section('subheading', 'Detail task dan diskusi')

@section('content')
@php
    // Logic untuk Smart Back Button
    $backUrl = route('tasks.index'); // Default ke Tasks
    if (request('source') === 'calendar') {
        // Jika dari kalender, kembalikan ke kalender dengan parameter bulan dan tahun
        $backUrl = route('calendar', [
            'month' => request('month', date('n')), 
            'year' => request('year', date('Y'))
        ]);
    }
@endphp

<div class="flex flex-wrap items-center gap-4 mb-8">
    <a href="{{ $backUrl }}" class="flex items-center gap-2 bg-white/70 hover:bg-white border border-slate-200 text-slate-600 hover:text-slate-900 shadow-sm hover:shadow font-bold px-4 py-2.5 rounded-xl text-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>

    @can('update', $task)
    <a href="{{ $task->project_id ? route('tasks.edit', $task->id) : '#' }}" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-amber-600/20 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit Task
    </a>
    @endcan
    
    {{-- Google Calendar --}}
    <a href="{{ $task->google_calendar_url }}" target="_blank"
       class="bg-white/80 backdrop-blur-md border border-slate-200/60 text-slate-700 hover:text-blue-600 hover:bg-white hover:border-blue-200 shadow-sm font-bold px-5 py-2.5 rounded-xl text-sm transition-all flex items-center gap-2">
        <span>📅</span> Tambah ke Google Calendar
    </a>
    
    @can('delete', $task)
    <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" onsubmit="return confirm('Hapus task ini? Semua data terkait (termasuk komentar) akan hilang.')" class="ml-auto">
        @csrf @method('DELETE')
        <button type="submit" class="bg-rose-100/50 hover:bg-rose-100 text-rose-700 border border-rose-200/60 font-bold px-5 py-2.5 rounded-xl text-sm transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Hapus
        </button>
    </form>
    @endcan
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">
    {{-- Background decorations --}}
    <div class="absolute -left-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute right-0 bottom-0 w-80 h-80 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>

    {{-- Left: Info --}}
    <div class="space-y-6 relative z-10">
        {{-- Info Card --}}
        <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-7 shadow-xl shadow-blue-900/5 relative overflow-hidden">
            <h2 class="font-black text-slate-800 mb-6 text-lg flex items-center gap-2">
                <span class="p-1.5 rounded-lg bg-blue-100 text-blue-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                Informasi Task
            </h2>
            
            <div class="space-y-4 text-sm font-medium">
                <div class="flex justify-between items-center py-2 border-b border-slate-100/80">
                    <span class="text-slate-500">Program</span>
                    <a href="{{ route('projects.show', $task->project_id) }}" class="text-blue-600 hover:text-blue-700 font-bold max-w-[150px] truncate bg-blue-50 px-2.5 py-1 rounded-lg">
                        {{ $task->project->name ?? '-' }}
                    </a>
                </div>
                @if($task->subproject)
                <div class="flex justify-between items-center py-2 border-b border-slate-100/80">
                    <span class="text-slate-500">List</span>
                    <span class="text-slate-700 font-bold max-w-[150px] truncate" title="{{ $task->subproject->name }}">
                        {{ $task->subproject->name }}
                    </span>
                </div>
                @endif
                <div class="flex justify-between items-start py-2 border-b border-slate-100/80">
                    <span class="text-slate-500 mt-1">PIC</span>
                    <div class="flex flex-col items-end gap-2.5">
                        @forelse($task->pics as $pic)
                            <div class="flex items-center gap-2 bg-slate-50 pl-1 pr-3 py-1 rounded-full border border-slate-100">
                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-[9px] font-black shadow-sm">
                                    {{ strtoupper(substr($pic->name, 0, 2)) }}
                                </div>
                                <span class="text-slate-700 text-xs font-bold">{{ $pic->name }}</span>
                            </div>
                        @empty
                            <span class="text-slate-400 text-xs italic">Belum ada PIC</span>
                        @endforelse
                    </div>
                </div>
                
                <div class="flex justify-between items-center py-2"><span class="text-slate-500">Rencana Mulai</span><span class="text-slate-800 font-bold">{{ $task->start_date?->format('d M Y') ?? '-' }}</span></div>
                <div class="flex justify-between items-center py-2"><span class="text-slate-500">Rencana Selesai</span>
                    <span class="{{ $task->isOverdue() ? 'text-rose-600 font-black bg-rose-50 px-2 py-0.5 rounded-lg' : 'text-slate-800 font-bold' }}">
                        {{ $task->due_date?->format('d M Y') ?? '-' }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center pt-4 mt-2 border-t border-slate-200/60">
                    <span class="text-slate-500">Realisasi Mulai</span>
                    <div class="text-right">
                        <span class="text-blue-600 font-black block">{{ $task->actual_start_date?->format('d M Y') ?? '-' }}</span>
                        @if($task->start_delay_days > 0)
                            <span class="text-[10px] text-rose-700 font-black bg-rose-100 border border-rose-200 px-2.5 py-1 rounded-md mt-1.5 inline-block shadow-sm">Telat Mulai {{ $task->start_delay_days }} Hari</span>
                        @elseif($task->start_delay_days < 0)
                            <span class="text-[10px] text-emerald-700 font-black bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-md mt-1.5 inline-block shadow-sm">Mulai Lebih Cepat {{ abs($task->start_delay_days) }} Hari</span>
                        @elseif($task->actual_start_date)
                            <span class="text-[10px] text-blue-700 font-black bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-md mt-1.5 inline-block shadow-sm">Tepat Waktu</span>
                        @endif
                    </div>
                </div>
                @if($task->actual_start_remarks)
                    <div class="bg-amber-50/80 border border-amber-200/50 rounded-xl p-3 text-xs text-amber-800 mt-2 shadow-sm">
                        <span class="font-bold block mb-0.5">Catatan Deviasi Mulai:</span> {{ $task->actual_start_remarks }}
                    </div>
                @endif

                <div class="flex justify-between items-center pt-3">
                    <span class="text-slate-500">Realisasi Selesai</span>
                    <div class="text-right">
                        <span class="text-blue-600 font-black block">{{ $task->actual_end_date?->format('d M Y') ?? '-' }}</span>
                        @if($task->delay_days > 0)
                            <span class="text-[10px] text-rose-700 font-black bg-rose-100 border border-rose-200 px-2.5 py-1 rounded-md mt-1.5 inline-block shadow-sm">Telat {{ $task->delay_days }} Hari</span>
                        @elseif($task->delay_days < 0)
                            <span class="text-[10px] text-emerald-700 font-black bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-md mt-1.5 inline-block shadow-sm">Maju {{ abs($task->delay_days) }} Hari</span>
                        @elseif($task->actual_end_date)
                            <span class="text-[10px] text-blue-700 font-black bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-md mt-1.5 inline-block shadow-sm">Tepat Waktu</span>
                        @endif
                    </div>
                </div>
                @if($task->actual_end_remarks)
                    <div class="bg-amber-50/80 border border-amber-200/50 rounded-xl p-3 text-xs text-amber-800 mt-2 shadow-sm">
                        <span class="font-bold block mb-0.5">Catatan Deviasi Selesai:</span> {{ $task->actual_end_remarks }}
                    </div>
                @endif
                
                <div class="flex justify-between items-center pt-4 mt-2 border-t border-slate-200/60">
                    <span class="text-slate-500">Status</span>
                    @php
                        $ts = match($task->status) {
                            'Berjalan'    => 'bg-blue-100 text-blue-700 border-blue-200 shadow-sm shadow-blue-500/10',
                            'Selesai'     => 'bg-emerald-100 text-emerald-700 border-emerald-200 shadow-sm shadow-emerald-500/10',
                            'Belum Mulai' => 'bg-slate-100 text-slate-600 border-slate-200 shadow-sm',
                            'Overdue'     => 'bg-rose-100 text-rose-700 border-rose-200 shadow-sm shadow-rose-500/10',
                            default       => 'bg-slate-100 text-slate-600 border-slate-200',
                        };
                    @endphp
                    <span class="px-3 py-1.5 rounded-xl text-xs font-black border {{ $ts }}">{{ $task->status }}</span>
                </div>
            </div>
            
            <div class="mt-6 pt-5 border-t border-slate-200/60">
                <div class="flex justify-between mb-3 items-end">
                    <span class="text-sm font-bold text-slate-700">Progress</span>
                    <span class="text-xl font-black text-blue-600 leading-none">{{ $task->progress }}%</span>
                </div>
                <div class="w-full bg-slate-100/80 rounded-full h-3 shadow-inner overflow-hidden">
                    @php $p = $task->progress; @endphp
                    <div class="h-full rounded-full transition-all duration-1000 ease-out" 
                         style="width:{{ $p }}%; background: {{ $p >= 75 ? 'linear-gradient(to right, #10b981, #059669)' : ($p >= 40 ? 'linear-gradient(to right, #3b82f6, #2563eb)' : 'linear-gradient(to right, #f59e0b, #d97706)') }}">
                    </div>
                </div>
            </div>
            
            @if($task->description)
            <div class="mt-6 pt-5 border-t border-slate-200/60">
                <p class="text-sm font-bold text-slate-700 mb-2">Deskripsi Detail</p>
                <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100 text-sm text-slate-600 leading-relaxed">
                    {{ $task->description }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Right: Comments --}}
    <div class="lg:col-span-2 relative z-10">
        <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-7 shadow-xl shadow-blue-900/5">
            <h2 class="font-black text-slate-800 mb-6 text-lg flex items-center gap-2">
                <span class="p-1.5 rounded-lg bg-indigo-100 text-indigo-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg></span>
                Komentar & Diskusi <span class="bg-slate-100 text-slate-500 text-xs px-2 py-0.5 rounded-full ml-1">{{ $task->comments->count() }}</span>
            </h2>

            @can('addComment', $task)
            <form method="POST" action="{{ route('tasks.comments.store', $task->id) }}" class="mb-8">
                @csrf
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-black shrink-0 shadow-md">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 bg-white/50 rounded-2xl border border-slate-200/60 p-2 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-400 transition-all">
                        <textarea name="comment" rows="3" required placeholder="Tambahkan komentar atau update progress disini..."
                                  class="w-full bg-transparent border-none text-slate-700 placeholder-slate-400 px-3 py-2 text-sm focus:ring-0 resize-none"></textarea>
                        <div class="flex justify-end pt-2 pr-1 pb-1">
                            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition-all shadow-md shadow-indigo-500/20 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Kirim
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @endcan

            <div class="space-y-5">
                @forelse($task->comments as $comment)
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-slate-600 text-xs font-black shrink-0 shadow-sm mt-1">
                        {{ strtoupper(substr($comment->user->name ?? '?', 0, 2)) }}
                    </div>
                    <div class="flex-1 bg-white/60 backdrop-blur-sm border border-slate-100 rounded-2xl p-4 shadow-sm group hover:shadow-md transition-shadow" id="comment-container-{{ $comment->id }}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-slate-800">{{ $comment->user->name ?? 'User' }}</span>
                                @if(auth()->id() === $comment->user_id)
                                    <span class="bg-indigo-100 text-indigo-700 text-[9px] px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">Anda</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[11px] font-medium text-slate-400">
                                    {{ $comment->created_at->diffForHumans() }}
                                    @if($comment->created_at->ne($comment->updated_at))
                                        <span class="italic text-slate-300 ml-1">(diedit)</span>
                                    @endif
                                </span>
                                
                                {{-- Actions --}}
                                @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity bg-white px-2 py-1 rounded-lg border border-slate-100 shadow-sm">
                                    @if(auth()->id() === $comment->user_id)
                                    <button type="button" onclick="editComment({{ $comment->id }})" class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded" title="Edit"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                    @endif
                                    <form method="POST" action="{{ route('tasks.comments.destroy', $comment->id) }}" onsubmit="return confirm('Hapus komentar ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded" title="Hapus"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Display text --}}
                        <div id="comment-text-{{ $comment->id }}">
                            <p class="text-sm text-slate-600 whitespace-pre-wrap leading-relaxed font-medium">{{ $comment->comment }}</p>
                        </div>
                        
                        {{-- Edit form (Hidden) --}}
                        @if(auth()->id() === $comment->user_id)
                        <form method="POST" action="{{ route('tasks.comments.update', $comment->id) }}" id="comment-form-{{ $comment->id }}" class="hidden mt-3">
                            @csrf @method('PUT')
                            <textarea name="comment" rows="2" required class="w-full bg-white border border-slate-200 text-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 resize-none">{{ $comment->comment }}</textarea>
                            <div class="flex gap-2 mt-2 justify-end">
                                <button type="button" onclick="cancelEdit({{ $comment->id }})" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-xs font-bold transition-colors">Batal</button>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors shadow-md">Simpan Perubahan</button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-12 bg-slate-50/50 rounded-2xl border border-slate-100 border-dashed">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">💭</div>
                    <p class="text-slate-500 text-sm font-bold">Belum ada diskusi.</p>
                    <p class="text-slate-400 text-xs mt-1">Jadilah yang pertama untuk memulai percakapan!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editComment(id) {
    document.getElementById('comment-text-' + id).classList.add('hidden');
    document.getElementById('comment-form-' + id).classList.remove('hidden');
}
function cancelEdit(id) {
    document.getElementById('comment-text-' + id).classList.remove('hidden');
    document.getElementById('comment-form-' + id).classList.add('hidden');
}
</script>
@endpush
