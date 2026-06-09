@extends('layouts.app')
@section('title', 'Edit Project')
@section('heading', 'Edit Project')
@section('subheading', 'Perbarui informasi project')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
        <form method="POST" action="{{ route('projects.update', $project->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Project <span class="text-red-600">*</span></label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" required
                       class="w-full bg-white border @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror text-slate-900 placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none transition-colors">
                @error('name')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tahun <span class="text-red-600">*</span></label>
                    <input type="number" name="year" value="{{ old('year', $project->year) }}" min="2020" max="2099" required
                           class="w-full bg-white border @error('year') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none transition-colors">
                    @error('year')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status <span class="text-red-600">*</span></label>
                    <select name="status" required
                            class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        @foreach(\App\Models\Project::STATUSES as $s)
                            <option value="{{ $s }}" {{ old('status', $project->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Rencana Mulai</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                           class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Rencana Selesai</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                           class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('end_date')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Realisasi Mulai (Opsional)</label>
                    <input type="date" name="actual_start_date" value="{{ old('actual_start_date', $project->actual_start_date?->format('Y-m-d')) }}"
                           class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Realisasi Selesai (Opsional)</label>
                    <input type="date" name="actual_end_date" value="{{ old('actual_end_date', $project->actual_end_date?->format('Y-m-d')) }}"
                           class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('actual_end_date')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="4"
                          class="w-full bg-white border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none transition-colors">{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl text-sm transition-all hover:-translate-y-0.5 shadow-sm hover:shadow-md">
                    Perbarui Project
                </button>
                <a href="{{ route('projects.show', $project->id) }}" class="bg-slate-100 border border-slate-200 text-slate-700 hover:bg-slate-200 hover:text-slate-900 px-6 py-3 rounded-xl text-sm transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Card: Convert to Sub-Project --}}
    @if(auth()->user()->isAdminOrManager())
    <div class="mt-8 bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
        <h3 class="text-base font-bold text-red-700 mb-2">⚠️ Ubah Menjadi Sub-Project</h3>
        <p class="text-xs text-slate-500 mb-5 leading-relaxed">
            Ingin mengubah Project ini menjadi Sub-Project dari Project lain? Seluruh tugas (Tasks) dan sub-project di dalam Project ini akan secara otomatis dipindahkan ke bawah Project induk yang baru. Setelah pemindahan selesai, Project ini akan dihapus secara otomatis.
        </p>
        
        <form method="POST" action="{{ route('projects.convert', $project->id) }}" class="space-y-4" onsubmit="return confirm('Apakah Anda yakin ingin mengubah project ini menjadi sub-project? Project ini akan dihapus setelah seluruh tugasnya dipindahkan.')">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Project Induk Baru</label>
                <select name="target_project_id" required
                        class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <option value="">-- Pilih Project Sasaran --</option>
                    @foreach(\App\Models\Project::where('id', '!=', $project->id)->orderBy('name')->get() as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-semibold px-5 py-2.5 rounded-xl text-sm transition-all cursor-pointer">
                Konversi & Pindahkan
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
