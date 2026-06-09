@extends('layouts.app')
@section('title', $task->name)
@section('heading', $task->name)
@section('subheading', 'Detail task dan komentar')

@section('content')
<div class="flex flex-wrap items-center gap-3 mb-6">
    <a href="{{ route('tasks.index') }}" class="text-slate-500 hover:text-slate-950 text-sm font-semibold transition-colors">← Kembali</a>
    @can('update', $task)
    <a href="{{ route('tasks.edit', $task->id) }}" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all">✏️ Edit</a>
    @endcan
    {{-- Google Calendar --}}
    <a href="{{ $task->google_calendar_url }}" target="_blank"
       class="bg-white border border-slate-200 text-slate-700 hover:text-slate-950 hover:border-slate-300 shadow-sm font-semibold px-4 py-2 rounded-xl text-sm transition-all flex items-center gap-2">
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
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h2 class="font-bold text-slate-900 mb-4">Informasi Task</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-start">
                    <span class="text-slate-500">Project</span>
                    <a href="{{ route('projects.show', $task->project_id) }}" class="text-blue-600 hover:text-blue-700 text-right font-medium max-w-[150px] truncate">
                        {{ $task->project->name ?? '-' }}
                    </a>
                </div>
                @if($task->subproject)
                <div class="flex justify-between items-start">
                    <span class="text-slate-500">Sub-Project</span>
                    <span class="text-slate-800 font-medium text-right max-w-[150px] truncate" title="{{ $task->subproject->name }}">
                        {{ $task->subproject->name }}
                    </span>
                </div>
                @endif
                <div class="flex justify-between items-start"><span class="text-slate-500 mt-1">PIC</span>
                    <div class="flex flex-col items-end gap-2">
                        @forelse($task->pics as $pic)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-[9px] font-bold">
                                    {{ strtoupper(substr($pic->name, 0, 2)) }}
                                </div>
                                <span class="text-slate-800 text-xs font-semibold">{{ $pic->name }}</span>
                            </div>
                        @empty
                            <span class="text-slate-500 text-xs">-</span>
                        @endforelse
                    </div>
                </div>
                <div class="flex justify-between"><span class="text-slate-500">Rencana Mulai</span><span class="text-slate-800 font-medium">{{ $task->start_date?->format('d M Y') ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Rencana Selesai</span>
                    <span class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : 'text-slate-800 font-medium' }}">
                        {{ $task->due_date?->format('d M Y') ?? '-' }}
                    </span>
                </div>
                
                <div class="flex justify-between border-t border-slate-100 pt-3 mt-1"><span class="text-slate-500">Realisasi Mulai</span><span class="text-blue-600 font-semibold">{{ $task->actual_start_date?->format('d M Y') ?? '-' }}</span></div>
                <div class="flex justify-between items-start"><span class="text-slate-500 mt-0.5">Realisasi Selesai</span>
                    <div class="text-right">
                        <span class="text-blue-600 font-semibold block">{{ $task->actual_end_date?->format('d M Y') ?? '-' }}</span>
                        @if($task->delay_days > 0)
                            <span class="text-[10px] text-red-700 font-bold bg-red-50 border border-red-100 px-2 py-0.5 rounded-md mt-1 inline-block">Telat {{ $task->delay_days }} Hari</span>
                        @elseif($task->delay_days < 0)
                            <span class="text-[10px] text-green-700 font-bold bg-green-50 border border-green-100 px-2 py-0.5 rounded-md mt-1 inline-block">Maju {{ abs($task->delay_days) }} Hari</span>
                        @elseif($task->actual_end_date)
                            <span class="text-[10px] text-blue-700 font-bold bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-md mt-1 inline-block">Sesuai Target</span>
                        @endif
                    </div>
                </div>
                <div class="flex justify-between items-center"><span class="text-slate-500">Status</span>
                    @php
                        $ts = match($task->status) {
                            'Berjalan'    => 'bg-blue-50 text-blue-600 border-blue-100',
                            'Selesai'     => 'bg-green-50 text-green-600 border-green-100',
                            'Belum Mulai' => 'bg-slate-50 text-slate-500 border-slate-200',
                            'Overdue'     => 'bg-red-50 text-red-600 border-red-100',
                            default       => 'bg-slate-50 text-slate-500 border-slate-200',
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $ts }}">{{ $task->status }}</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-slate-500">Progress</span>
                    <span class="text-sm font-bold text-blue-600">{{ $task->progress }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5">
                    @php $p = $task->progress; @endphp
                    <div class="h-2.5 rounded-full" style="width:{{ $p }}%; background: {{ $p >= 75 ? '#22c55e' : ($p >= 40 ? '#4f80ff' : '#f59e0b') }}"></div>
                </div>
            </div>
            @if($task->description)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-2">Deskripsi</p>
                <p class="text-sm text-slate-700">{{ $task->description }}</p>
            </div>
            @endif
        </div>


    </div>

    {{-- Right: Comments --}}
    <div class="lg:col-span-2">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h2 class="font-bold text-slate-900 mb-5">Komentar ({{ $task->comments->count() }})</h2>

            @can('addComment', $task)
            <form method="POST" action="{{ route('tasks.comments.store', $task->id) }}" class="mb-6">
                @csrf
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold shrink-0 mt-0.5">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <textarea name="comment" rows="3" required placeholder="Tulis komentar..."
                                  class="w-full bg-white border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none"></textarea>
                        <button type="submit" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-xl text-sm transition-all">
                            Kirim Komentar
                        </button>
                    </div>
                </div>
            </form>
            @endcan

            <div class="space-y-4">
                @forelse($task->comments as $comment)
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 text-xs font-bold shrink-0 mt-0.5">
                        {{ strtoupper(substr($comment->user->name ?? '?', 0, 2)) }}
                    </div>
                    <div class="flex-1 bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 group" id="comment-container-{{ $comment->id }}">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-semibold text-slate-800">{{ $comment->user->name ?? 'User' }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] text-slate-500">
                                    {{ $comment->created_at->diffForHumans() }}
                                    @if($comment->created_at->ne($comment->updated_at))
                                        <span class="italic ml-1">(diedit)</span>
                                    @endif
                                </span>
                                
                                {{-- Actions --}}
                                @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if(auth()->id() === $comment->user_id)
                                    <button type="button" onclick="editComment({{ $comment->id }})" class="text-[10px] text-blue-600 hover:text-blue-700 font-semibold">Edit</button>
                                    @endif
                                    <form method="POST" action="{{ route('tasks.comments.destroy', $comment->id) }}" onsubmit="return confirm('Hapus komentar ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[10px] text-red-600 hover:text-red-700 font-semibold">Hapus</button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Display text --}}
                        <div id="comment-text-{{ $comment->id }}">
                            <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $comment->comment }}</p>
                        </div>
                        
                        {{-- Edit form (Hidden) --}}
                        @if(auth()->id() === $comment->user_id)
                        <form method="POST" action="{{ route('tasks.comments.update', $comment->id) }}" id="comment-form-{{ $comment->id }}" class="hidden mt-2">
                            @csrf @method('PUT')
                            <textarea name="comment" rows="2" required class="w-full bg-white border border-slate-200 text-slate-900 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none">{{ $comment->comment }}</textarea>
                            <div class="flex gap-2 mt-2">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">Simpan</button>
                                <button type="button" onclick="cancelEdit({{ $comment->id }})" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-3 py-1.5 rounded-lg text-xs font-semibold">Batal</button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <div class="text-3xl mb-2">💬</div>
                    <p class="text-slate-500 text-sm">Belum ada komentar. Jadilah yang pertama!</p>
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
