@extends('layouts.app')
@section('title', 'Tambah Program')
@section('heading', 'Tambah Program')
@section('subheading', 'Buat program baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-8 shadow-xl shadow-blue-900/5 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <form method="POST" action="{{ route('projects.store') }}" class="space-y-6 relative z-10"
              x-data="{
                  startDate: '{{ old('start_date') }}',
                  endDate: '{{ old('end_date') }}',
                  actualStartDate: '{{ old('actual_start_date') }}',
                  actualEndDate: '{{ old('actual_end_date') }}',
                  isStartDeviated() {
                      return !!(this.startDate && this.actualStartDate && this.startDate !== this.actualStartDate);
                  },
                  isEndDeviated() {
                      return !!(this.endDate && this.actualEndDate && this.endDate !== this.actualEndDate);
                  }
              }">
            @csrf

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Program <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full bg-white/50 border @error('name') border-rose-400 focus:border-rose-500 focus:ring-rose-500/20 @else border-slate-200/60 focus:border-blue-500 focus:ring-blue-500/20 @enderror text-slate-900 placeholder-slate-400 rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-sm"
                       placeholder="Misal: Redesain UI Taskflow">
                @error('name')<p class="text-rose-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tahun <span class="text-rose-500">*</span></label>
                <input type="number" name="year" value="{{ old('year', date('Y')) }}" min="2020" max="2099" required
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
                          :required="isStartDeviated()" :disabled="!isStartDeviated()">{{ old('actual_start_remarks') }}</textarea>
                @error('actual_start_remarks')<p class="text-rose-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
            </div>

            {{-- Remarks Deviasi Selesai --}}
            <div x-show="isEndDeviated()" x-transition class="bg-amber-50/50 border border-amber-100/50 rounded-2xl p-5" style="display: none;">
                <label class="block text-sm font-bold text-amber-800 mb-2">Keterangan Deviasi Selesai <span class="text-rose-500">*</span></label>
                <p class="text-[11px] text-amber-700/70 mb-3">Jelaskan alasan perbedaan tanggal realisasi selesai dengan rencana.</p>
                <textarea name="actual_end_remarks" rows="2"
                          class="w-full bg-white/80 border @error('actual_end_remarks') border-rose-400 focus:border-rose-500 focus:ring-rose-500/20 @else border-amber-200/60 focus:border-amber-400 focus:ring-amber-400/20 @enderror text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 transition-all shadow-sm"
                          :required="isEndDeviated()" :disabled="!isEndDeviated()">{{ old('actual_end_remarks') }}</textarea>
                @error('actual_end_remarks')<p class="text-rose-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="4" placeholder="Tuliskan deskripsi lengkap program di sini..."
                          class="w-full bg-white/50 border border-slate-200/60 text-slate-900 placeholder-slate-400 rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:bg-white resize-none transition-all shadow-sm">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-slate-100/80">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold px-8 py-3.5 rounded-2xl text-sm transition-all hover:-translate-y-0.5 shadow-lg shadow-blue-900/20">
                    Simpan Program
                </button>
                <a href="{{ route('projects.index') }}" class="bg-slate-50 border border-slate-200/60 text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-bold px-8 py-3.5 rounded-2xl text-sm transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
