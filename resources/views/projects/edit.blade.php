@extends('layouts.app')
@section('title', 'Edit Project')
@section('heading', 'Edit Project')
@section('subheading', 'Perbarui informasi project')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-8 shadow-xl shadow-blue-900/5 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <form method="POST" action="{{ route('projects.update', $project->id) }}" class="space-y-6 relative z-10"
              x-data="{
                  startDate: '{{ old('start_date', $project->start_date?->format('Y-m-d') ?? '') }}',
                  endDate: '{{ old('end_date', $project->end_date?->format('Y-m-d') ?? '') }}',
                  actualStartDate: '{{ old('actual_start_date', $project->actual_start_date?->format('Y-m-d') ?? '') }}',
                  actualEndDate: '{{ old('actual_end_date', $project->actual_end_date?->format('Y-m-d') ?? '') }}',
                  isStartDeviated() {
                      return !!(this.startDate && this.actualStartDate && this.startDate !== this.actualStartDate);
                  },
                  isEndDeviated() {
                      return !!(this.endDate && this.actualEndDate && this.endDate !== this.actualEndDate);
                  }
              }">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Project <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" required
                       class="w-full bg-white/50 border @error('name') border-rose-400 focus:border-rose-500 focus:ring-rose-500/20 @else border-slate-200/60 focus:border-blue-500 focus:ring-blue-500/20 @enderror text-slate-900 placeholder-slate-400 rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-sm">
                @error('name')<p class="text-rose-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tahun <span class="text-rose-500">*</span></label>
                <input type="number" name="year" value="{{ old('year', $project->year) }}" min="2020" max="2099" required
                       class="w-full bg-white/50 border @error('year') border-rose-400 focus:border-rose-500 focus:ring-rose-500/20 @else border-slate-200/60 focus:border-blue-500 focus:ring-blue-500/20 @enderror text-slate-900 rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-sm">
                @error('year')<p class="text-rose-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Rencana Mulai</label>
                    <input type="date" name="start_date" x-model="startDate"
                           class="w-full bg-white/50 border border-slate-200/60 text-slate-900 rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-all shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Rencana Selesai</label>
                    <input type="date" name="end_date" x-model="endDate"
                           class="w-full bg-white/50 border border-slate-200/60 text-slate-900 rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-all shadow-sm">
                    @error('end_date')<p class="text-rose-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Realisasi Mulai <span class="text-xs font-normal text-slate-400">(Opsional)</span></label>
                    <input type="date" name="actual_start_date" x-model="actualStartDate"
                           class="w-full bg-white/50 border border-slate-200/60 text-slate-900 rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-all shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Realisasi Selesai <span class="text-xs font-normal text-slate-400">(Opsional)</span></label>
                    <input type="date" name="actual_end_date" x-model="actualEndDate"
                           class="w-full bg-white/50 border border-slate-200/60 text-slate-900 rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-all shadow-sm">
                    @error('actual_end_date')<p class="text-rose-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Remarks Deviasi Mulai --}}
            <div x-show="isStartDeviated()" x-transition class="bg-amber-50/50 border border-amber-100/50 rounded-2xl p-5" style="display: none;">
                <label class="block text-sm font-bold text-amber-800 mb-2">Keterangan Deviasi Mulai <span class="text-rose-500">*</span></label>
                <p class="text-[11px] text-amber-700/70 mb-3">Jelaskan alasan perbedaan tanggal realisasi mulai dengan rencana.</p>
                <textarea name="actual_start_remarks" rows="2"
                          class="w-full bg-white/80 border @error('actual_start_remarks') border-rose-400 focus:border-rose-500 focus:ring-rose-500/20 @else border-amber-200/60 focus:border-amber-400 focus:ring-amber-400/20 @enderror text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 transition-all shadow-sm"
                          :required="isStartDeviated()" :disabled="!isStartDeviated()">{{ old('actual_start_remarks', $project->actual_start_remarks) }}</textarea>
                @error('actual_start_remarks')<p class="text-rose-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
            </div>

            {{-- Remarks Deviasi Selesai --}}
            <div x-show="isEndDeviated()" x-transition class="bg-amber-50/50 border border-amber-100/50 rounded-2xl p-5" style="display: none;">
                <label class="block text-sm font-bold text-amber-800 mb-2">Keterangan Deviasi Selesai <span class="text-rose-500">*</span></label>
                <p class="text-[11px] text-amber-700/70 mb-3">Jelaskan alasan perbedaan tanggal realisasi selesai dengan rencana.</p>
                <textarea name="actual_end_remarks" rows="2"
                          class="w-full bg-white/80 border @error('actual_end_remarks') border-rose-400 focus:border-rose-500 focus:ring-rose-500/20 @else border-amber-200/60 focus:border-amber-400 focus:ring-amber-400/20 @enderror text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 transition-all shadow-sm"
                          :required="isEndDeviated()" :disabled="!isEndDeviated()">{{ old('actual_end_remarks', $project->actual_end_remarks) }}</textarea>
                @error('actual_end_remarks')<p class="text-rose-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="4"
                          class="w-full bg-white/50 border border-slate-200/60 text-slate-900 placeholder-slate-400 rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:bg-white resize-none transition-all shadow-sm">{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-slate-100/80">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold px-8 py-3.5 rounded-2xl text-sm transition-all hover:-translate-y-0.5 shadow-lg shadow-blue-900/20">
                    Perbarui Project
                </button>
                <a href="{{ route('projects.show', $project->id) }}" class="bg-slate-50 border border-slate-200/60 text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-bold px-8 py-3.5 rounded-2xl text-sm transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Card: Convert to Sub-Project --}}
    @if(auth()->user()->isAdminOrManager())
    <div class="mt-8 bg-white/80 backdrop-blur-md border border-rose-100/50 rounded-3xl p-8 shadow-xl shadow-rose-900/5 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-rose-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <h3 class="text-lg font-black text-rose-700 mb-2 relative z-10">⚠️ Ubah Menjadi Sub-Project</h3>
        <p class="text-sm text-slate-500 mb-6 leading-relaxed relative z-10">
            Ingin mengubah Project ini menjadi Sub-Project dari Project lain? Seluruh tugas (Tasks) dan sub-project di dalam Project ini akan secara otomatis dipindahkan ke bawah Project induk yang baru. Setelah pemindahan selesai, Project ini akan dihapus secara otomatis.
        </p>
        
        <form method="POST" action="{{ route('projects.convert', $project->id) }}" class="space-y-5 relative z-10" onsubmit="return confirm('Apakah Anda yakin ingin mengubah project ini menjadi sub-project? Project ini akan dihapus setelah seluruh tugasnya dipindahkan.')">
            @csrf
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Project Induk Baru</label>
                <div class="relative">
                    <select name="target_project_id" required
                            class="w-full appearance-none bg-white/50 border border-slate-200/60 text-slate-900 font-semibold rounded-2xl pl-4 pr-10 py-3.5 text-sm focus:outline-none focus:border-rose-400 focus:ring-2 focus:ring-rose-400/20 focus:bg-white transition-all shadow-sm cursor-pointer">
                        <option value="">-- Pilih Project Sasaran --</option>
                        @foreach(\App\Models\Project::where('id', '!=', $project->id)->orderBy('name')->get() as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
                </div>
            </div>
            
            <button type="submit" class="bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-400 hover:to-red-500 text-white font-bold px-6 py-3 rounded-2xl text-sm transition-all hover:-translate-y-0.5 shadow-lg shadow-rose-900/20">
                Konversi & Pindahkan
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
