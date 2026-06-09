@extends('layouts.app')
@section('title', 'Tambah Project')
@section('heading', 'Tambah Project')
@section('subheading', 'Buat project baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-8">
        <form method="POST" action="{{ route('projects.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Nama Project <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full bg-[#222535] border @error('name') border-red-500 @else border-[#333650] @enderror text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors"
                       placeholder="Nama project">
                @error('name')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Tahun <span class="text-red-400">*</span></label>
                    <input type="number" name="year" value="{{ old('year', date('Y')) }}" min="2020" max="2099" required
                           class="w-full bg-[#222535] border @error('year') border-red-500 @else border-[#333650] @enderror text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                    @error('year')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Status <span class="text-red-400">*</span></label>
                    <select name="status" required
                            class="w-full bg-[#222535] border @error('status') border-red-500 @else border-[#333650] @enderror text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                        @foreach(\App\Models\Project::STATUSES as $s)
                            <option value="{{ $s }}" {{ old('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Rencana Mulai</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           class="w-full bg-[#222535] border border-[#333650] text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 ">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Rencana Selesai</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                           class="w-full bg-[#222535] border border-[#333650] text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 ">
                    @error('end_date')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
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

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Deskripsi</label>
                <textarea name="description" rows="4" placeholder="Deskripsi project (opsional)"
                          class="w-full bg-[#222535] border border-[#333650] text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-3 rounded-xl text-sm transition-all hover:-translate-y-0.5">
                    Simpan Project
                </button>
                <a href="{{ route('projects.index') }}" class="bg-[#222535] border border-[#333650] text-slate-300 hover:text-white px-6 py-3 rounded-xl text-sm transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
