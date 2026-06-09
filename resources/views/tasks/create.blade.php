@extends('layouts.app')
@section('title', 'Tambah Task')
@section('heading', 'Tambah Task')
@section('subheading', 'Buat task baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-8">
        <form method="POST" action="{{ route('tasks.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Project <span class="text-red-400">*</span></label>
                <select name="project_id" required
                        class="w-full bg-[#222535] border @error('project_id') border-red-500 @else border-[#333650] @enderror text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                    <option value="">-- Pilih Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }} ({{ $project->year }})
                        </option>
                    @endforeach
                </select>
                @error('project_id')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Nama Task <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama task"
                       class="w-full bg-[#222535] border @error('name') border-red-500 @else border-[#333650] @enderror text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                @error('name')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">PIC (Person In Charge)</label>
                <select name="pic_id" class="w-full bg-[#222535] border border-[#333650] text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                    <option value="">-- Pilih PIC --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('pic_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->role }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Rencana Mulai</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           class="w-full bg-[#222535] border border-[#333650] text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 ">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Rencana Selesai (Due Date)</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                           class="w-full bg-[#222535] border border-[#333650] text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 ">
                    @error('due_date')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Realisasi Mulai (Opsional)</label>
                    <input type="date" name="actual_start_date" value="{{ old('actual_start_date') }}"
                           class="w-full bg-[#222535] border border-[#333650] text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 ">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Realisasi Selesai (Opsional)</label>
                    <input type="date" name="actual_end_date" value="{{ old('actual_end_date') }}"
                           class="w-full bg-[#222535] border border-[#333650] text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 ">
                    @error('actual_end_date')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <div x-data="{ progress: {{ old('progress', 0) }} }">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Progress: <span class="text-blue-400 font-bold" x-text="progress + '%'"></span>
                </label>
                <input type="range" name="progress" min="0" max="100" x-model="progress"
                       class="w-full h-2 rounded-lg accent-blue-500 cursor-pointer">
                <div class="flex justify-between text-[10px] text-slate-300 mt-1"><span>0%</span><span>50%</span><span>100%</span></div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Status <span class="text-red-400">*</span></label>
                <select name="status" required class="w-full bg-[#222535] border border-[#333650] text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                    @foreach(\App\Models\Task::STATUSES as $s)
                        <option value="{{ $s }}" {{ old('status','Belum Mulai') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Deskripsi task (opsional)"
                          class="w-full bg-[#222535] border border-[#333650] text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-3 rounded-xl text-sm transition-all hover:-translate-y-0.5">
                    Simpan Task
                </button>
                <a href="{{ route('tasks.index') }}" class="bg-[#222535] border border-[#333650] text-slate-300 hover:text-white px-6 py-3 rounded-xl text-sm transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
