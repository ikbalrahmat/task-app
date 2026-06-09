@extends('layouts.app')
@section('title', $task->name)
@section('heading', $task->name)
@section('subheading', 'Detail task dan komentar')

@section('content')
<div class="flex flex-wrap items-center gap-3 mb-6">
    <a href="{{ route('tasks.index') }}" class="text-slate-400 hover:text-white text-sm transition-colors">← Kembali</a>
    @can('update', $task)
    <a href="{{ route('tasks.edit', $task->id) }}" class="bg-amber-600 hover:bg-amber-9500 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all">✏️ Edit</a>
    @endcan
    {{-- Google Calendar --}}
    <a href="{{ $task->google_calendar_url }}" target="_blank"
       class="bg-[#222535] border border-[#333650] text-slate-300 hover:text-white hover:border-blue-500 font-semibold px-4 py-2 rounded-xl text-sm transition-all flex items-center gap-2">
        📅 Tambah ke Google Calendar
    </a>
    @can('delete', $task)
    <form method="POST" action="{{ route('tasks.destroy', $task->id) }}"
          onsubmit="return confirm('Hapus task ini?')">
        @csrf @method('DELETE')
        <button type="submit" class="bg-red-700 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all">🗑 Hapus</button>
    </form>
    @endcan
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: Info + Attachments --}}
    <div class="space-y-5">
        {{-- Info Card --}}
        <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6">
            <h2 class="font-bold text-white mb-4">Informasi Task</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-start">
                    <span class="text-slate-400">Project</span>
                    <a href="{{ route('projects.show', $task->project_id) }}" class="text-blue-400 hover:text-blue-300 text-right font-medium max-w-[150px] truncate">
                        {{ $task->project->name ?? '-' }}
                    </a>
                </div>
                <div class="flex justify-between items-center"><span class="text-slate-400">PIC</span>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-blue-950 flex items-center justify-center text-blue-400 text-[10px] font-bold">
                            {{ strtoupper(substr($task->pic->name ?? '?', 0, 2)) }}
                        </div>
                        <span class="text-white">{{ $task->pic->name ?? '-' }}</span>
                    </div>
                </div>
                <div class="flex justify-between"><span class="text-slate-400">Rencana Mulai</span><span class="text-white">{{ $task->start_date?->format('d M Y') ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Rencana Selesai</span>
                    <span class="{{ $task->isOverdue() ? 'text-red-400 font-semibold' : 'text-white' }}">
                        {{ $task->due_date?->format('d M Y') ?? '-' }}
                    </span>
                </div>
                
                <div class="flex justify-between border-t border-[#333650] pt-3 mt-1"><span class="text-slate-400">Realisasi Mulai</span><span class="text-blue-300 font-semibold">{{ $task->actual_start_date?->format('d M Y') ?? '-' }}</span></div>
                <div class="flex justify-between items-start"><span class="text-slate-400 mt-0.5">Realisasi Selesai</span>
                    <div class="text-right">
                        <span class="text-blue-300 font-semibold block">{{ $task->actual_end_date?->format('d M Y') ?? '-' }}</span>
                        @if($task->delay_days > 0)
                            <span class="text-[10px] text-red-400 font-bold bg-red-950/50 px-2 py-0.5 rounded-md mt-1 inline-block">Telat {{ $task->delay_days }} Hari</span>
                        @elseif($task->delay_days < 0)
                            <span class="text-[10px] text-green-400 font-bold bg-green-950/50 px-2 py-0.5 rounded-md mt-1 inline-block">Maju {{ abs($task->delay_days) }} Hari</span>
                        @elseif($task->actual_end_date)
                            <span class="text-[10px] text-blue-400 font-bold bg-blue-950/50 px-2 py-0.5 rounded-md mt-1 inline-block">Sesuai Target</span>
                        @endif
                    </div>
                </div>
                <div class="flex justify-between items-center"><span class="text-slate-400">Status</span>
                    @php
                        $ts = match($task->status) {
                            'Berjalan'    => 'bg-blue-950 text-blue-400',
                            'Selesai'     => 'bg-green-950 text-green-400',
                            'Belum Mulai' => 'bg-[#222535] text-slate-400',
                            'Overdue'     => 'bg-red-950 text-red-400',
                            default       => 'bg-[#222535] text-slate-400',
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $ts }}">{{ $task->status }}</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-[#333650]">
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-slate-400">Progress</span>
                    <span class="text-sm font-bold text-blue-400">{{ $task->progress }}%</span>
                </div>
                <div class="w-full bg-[#222535] rounded-full h-2.5">
                    @php $p = $task->progress; @endphp
                    <div class="h-2.5 rounded-full" style="width:{{ $p }}%; background: {{ $p >= 75 ? '#22c55e' : ($p >= 40 ? '#4f80ff' : '#f59e0b') }}"></div>
                </div>
            </div>
            @if($task->description)
            <div class="mt-4 pt-4 border-t border-[#333650]">
                <p class="text-xs text-slate-400 mb-2">Deskripsi</p>
                <p class="text-sm text-slate-300">{{ $task->description }}</p>
            </div>
            @endif
        </div>


    </div>

    {{-- Right: Comments --}}
    <div class="lg:col-span-2">
        <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6">
            <h2 class="font-bold text-white mb-5">Komentar ({{ $task->comments->count() }})</h2>

            @can('addComment', $task)
            <form method="POST" action="{{ route('tasks.comments.store', $task->id) }}" class="mb-6">
                @csrf
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-950 flex items-center justify-center text-blue-400 text-xs font-bold shrink-0 mt-0.5">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <textarea name="comment" rows="3" required placeholder="Tulis komentar..."
                                  class="w-full bg-[#222535] border border-[#333650] text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
                        <button type="submit" class="mt-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2 rounded-xl text-sm transition-all">
                            Kirim Komentar
                        </button>
                    </div>
                </div>
            </form>
            @endcan

            <div class="space-y-4">
                @forelse($task->comments as $comment)
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#222535] flex items-center justify-center text-slate-300 text-xs font-bold shrink-0 mt-0.5">
                        {{ strtoupper(substr($comment->user->name ?? '?', 0, 2)) }}
                    </div>
                    <div class="flex-1 bg-[#222535] rounded-xl px-4 py-3 group" id="comment-container-{{ $comment->id }}">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-semibold text-white">{{ $comment->user->name ?? 'User' }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] text-slate-400">
                                    {{ $comment->created_at->diffForHumans() }}
                                    @if($comment->created_at->ne($comment->updated_at))
                                        <span class="italic ml-1">(diedit)</span>
                                    @endif
                                </span>
                                
                                {{-- Actions --}}
                                @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if(auth()->id() === $comment->user_id)
                                    <button type="button" onclick="editComment({{ $comment->id }})" class="text-[10px] text-blue-400 hover:text-blue-300">Edit</button>
                                    @endif
                                    <form method="POST" action="{{ route('tasks.comments.destroy', $comment->id) }}" onsubmit="return confirm('Hapus komentar ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[10px] text-red-400 hover:text-red-300">Hapus</button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Display text --}}
                        <div id="comment-text-{{ $comment->id }}">
                            <p class="text-sm text-slate-300 whitespace-pre-wrap">{{ $comment->comment }}</p>
                        </div>
                        
                        {{-- Edit form (Hidden) --}}
                        @if(auth()->id() === $comment->user_id)
                        <form method="POST" action="{{ route('tasks.comments.update', $comment->id) }}" id="comment-form-{{ $comment->id }}" class="hidden mt-2">
                            @csrf @method('PUT')
                            <textarea name="comment" rows="2" required class="w-full bg-[#1a1d27] border border-[#333650] text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 resize-none">{{ $comment->comment }}</textarea>
                            <div class="flex gap-2 mt-2">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">Simpan</button>
                                <button type="button" onclick="cancelEdit({{ $comment->id }})" class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">Batal</button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <div class="text-3xl mb-2">💬</div>
                    <p class="text-slate-400 text-sm">Belum ada komentar. Jadilah yang pertama!</p>
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
