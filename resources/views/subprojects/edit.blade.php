@extends('layouts.app')
@section('title', 'Edit Sub-Project')
@section('heading', 'Edit Sub-Project')
@section('subheading', 'Perbarui informasi sub-project')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
        <form method="POST" action="{{ route('subprojects.update', $subproject->id) }}" class="space-y-5"
              x-data="{
                  startDate: '{{ old('start_date', $subproject->start_date?->format('Y-m-d') ?? '') }}',
                  endDate: '{{ old('end_date', $subproject->end_date?->format('Y-m-d') ?? '') }}',
                  actualStartDate: '{{ old('actual_start_date', $subproject->actual_start_date?->format('Y-m-d') ?? '') }}',
                  actualEndDate: '{{ old('actual_end_date', $subproject->actual_end_date?->format('Y-m-d') ?? '') }}',
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
                <label class="block text-sm font-medium text-slate-700 mb-2">Project Induk <span class="text-red-600">*</span></label>
                <select name="project_id" required
                        class="w-full bg-white border @error('project_id') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none transition-colors">
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id', $subproject->project_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Sub-Project <span class="text-red-600">*</span></label>
                <input type="text" name="name" value="{{ old('name', $subproject->name) }}" required
                       class="w-full bg-white border @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror text-slate-900 placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none transition-colors"
                       placeholder="Nama sub-project">
                @error('name')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Rencana Mulai</label>
                    <input type="date" name="start_date" x-model="startDate"
                           class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Rencana Selesai</label>
                    <input type="date" name="end_date" x-model="endDate"
                           class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('end_date')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Realisasi Mulai (Opsional)</label>
                    <input type="date" name="actual_start_date" x-model="actualStartDate"
                           class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Realisasi Selesai (Opsional)</label>
                    <input type="date" name="actual_end_date" x-model="actualEndDate"
                           class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('actual_end_date')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Remarks Deviasi Mulai --}}
            <div x-show="isStartDeviated()" x-transition class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Keterangan Realisasi Mulai <span class="text-red-600">*</span></label>
                <textarea name="actual_start_remarks" rows="2" placeholder="Jelaskan alasan perbedaan tanggal realisasi mulai dengan rencana..."
                          class="w-full bg-white border @error('actual_start_remarks') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror text-slate-900 placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none transition-colors"
                          :required="isStartDeviated()" :disabled="!isStartDeviated()">{{ old('actual_start_remarks', $subproject->actual_start_remarks) }}</textarea>
                @error('actual_start_remarks')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            {{-- Remarks Deviasi Selesai --}}
            <div x-show="isEndDeviated()" x-transition class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Keterangan Realisasi Selesai <span class="text-red-600">*</span></label>
                <textarea name="actual_end_remarks" rows="2" placeholder="Jelaskan alasan perbedaan tanggal realisasi selesai dengan rencana..."
                          class="w-full bg-white border @error('actual_end_remarks') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror text-slate-900 placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none transition-colors"
                          :required="isEndDeviated()" :disabled="!isEndDeviated()">{{ old('actual_end_remarks', $subproject->actual_end_remarks) }}</textarea>
                @error('actual_end_remarks')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="4" placeholder="Deskripsi sub-project (opsional)"
                          class="w-full bg-white border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none transition-colors">{{ old('description', $subproject->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl text-sm transition-all hover:-translate-y-0.5 shadow-sm hover:shadow-md">
                    Perbarui Sub-Project
                </button>
                <a href="{{ route('projects.show', $subproject->project_id) }}" class="bg-slate-100 border border-slate-200 text-slate-700 hover:bg-slate-200 hover:text-slate-900 px-6 py-3 rounded-xl text-sm transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
