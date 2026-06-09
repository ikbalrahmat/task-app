@extends('layouts.app')
@section('title', 'Tambah Task')
@section('heading', 'Tambah Task')
@section('subheading', 'Buat task baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
        <form method="POST" action="{{ route('tasks.store') }}" class="space-y-5">
            @csrf

            <div x-data="{ 
                selectedProject: '{{ old('project_id', request('project_id', '')) }}',
                selectedSubproject: '{{ old('subproject_id', request('subproject_id', '')) }}',
                subprojectsByProject: {{ json_encode($projects->mapWithKeys(fn($p) => [$p->id => $p->subprojects->map(fn($sp) => ['id' => $sp->id, 'name' => $sp->name])])->toArray()) }}
            }" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Project <span class="text-red-600">*</span></label>
                    <select name="project_id" required x-model="selectedProject" @change="selectedSubproject = ''"
                            class="w-full bg-white border @error('project_id') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none transition-colors">
                        <option value="">-- Pilih Project --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">
                                {{ $project->name }} ({{ $project->year }})
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                {{-- Subprojects Dropdown (Only show if project has subprojects) --}}
                <div x-show="selectedProject && subprojectsByProject[selectedProject] && subprojectsByProject[selectedProject].length > 0" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-2"
                     style="display: none;">
                    <label class="block text-sm font-medium text-slate-700">Sub-Project (Opsional)</label>
                    <select name="subproject_id" x-model="selectedSubproject"
                            class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        <option value="">-- Tugas Langsung (Tidak masuk Sub-Project) --</option>
                        <template x-for="sub in subprojectsByProject[selectedProject]" :key="sub.id">
                            <option :value="sub.id" x-text="sub.name" :selected="selectedSubproject == sub.id"></option>
                        </template>
                    </select>
                    @error('subproject_id')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Task <span class="text-red-600">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama task"
                       class="w-full bg-white border @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror text-slate-900 placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none transition-colors">
                @error('name')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div x-data="{ 
                open: false, 
                selectedPics: {{ json_encode(array_map('intval', old('pic_ids', []))) }},
                allUsers: {{ json_encode($users->map(fn($u) => ['id' => (int) $u->id, 'name' => $u->name, 'role' => $u->role])->toArray()) }},
                toggleAll() {
                    if (this.selectedPics.length === this.allUsers.length) {
                        this.selectedPics = [];
                    } else {
                        this.selectedPics = this.allUsers.map(u => u.id);
                    }
                },
                getSelectedText() {
                    if (this.selectedPics.length === 0) return '-- Pilih PIC --';
                    if (this.selectedPics.length === this.allUsers.length) return 'Semua PIC Terpilih (ALL)';
                    return this.selectedPics.length + ' PIC Terpilih';
                }
            }" class="relative">
                <label class="block text-sm font-medium text-slate-700 mb-2">PIC (Person In Charge)</label>
                
                <!-- Trigger Button -->
                <button type="button" @click="open = !open" 
                        class="w-full bg-white border border-slate-200 text-left text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors flex items-center justify-between shadow-xs cursor-pointer">
                    <span x-text="getSelectedText()" class="font-semibold text-slate-700"></span>
                    <svg class="w-4 h-4 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown Content -->
                <div x-show="open" @click.outside="open = false" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute z-50 left-0 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-lg p-3 max-h-60 overflow-y-auto"
                     style="display: none;">
                    
                    <!-- Toggle All (Select All) -->
                    <div class="border-b border-slate-100 pb-2 mb-2">
                        <button type="button" @click="toggleAll()" 
                                class="w-full flex items-center justify-between text-left px-2 py-1.5 hover:bg-slate-50 rounded-lg text-xs font-semibold text-blue-600 transition-colors cursor-pointer">
                            <span x-text="selectedPics.length === allUsers.length ? 'Hapus Semua Pilihan' : 'Pilih Semua (ALL)'"></span>
                            <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded text-[10px]" x-text="selectedPics.length + '/' + allUsers.length"></span>
                        </button>
                    </div>

                    <!-- Options -->
                    <div class="space-y-1">
                        @foreach($users as $user)
                            <label class="flex items-center gap-2.5 p-2 hover:bg-slate-50 rounded-lg cursor-pointer transition-colors">
                                <input type="checkbox" name="pic_ids[]" value="{{ $user->id }}"
                                       x-model="selectedPics"
                                       :value="{{ (int) $user->id }}"
                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <div class="w-6 h-6 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-[10px] font-bold shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-slate-800 font-semibold truncate">{{ $user->name }}</p>
                                    <p class="text-[9px] text-slate-500 truncate">{{ $user->role }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                @error('pic_ids')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div x-data="{
                startDate: '{{ old('start_date') }}',
                dueDate: '{{ old('due_date') }}',
                actualStartDate: '{{ old('actual_start_date') }}',
                actualEndDate: '{{ old('actual_end_date') }}',
                isStartDeviated() {
                    return this.startDate && this.actualStartDate && this.startDate !== this.actualStartDate;
                },
                isEndDeviated() {
                    return this.dueDate && this.actualEndDate && this.dueDate !== this.actualEndDate;
                }
            }" class="space-y-5">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Rencana Mulai</label>
                        <input type="date" name="start_date" x-model="startDate"
                               class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Rencana Selesai (Due Date)</label>
                        <input type="date" name="due_date" x-model="dueDate"
                               class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        @error('due_date')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
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
                <div x-show="isStartDeviated()" x-transition class="bg-slate-50 border border-slate-200 rounded-xl p-4" style="display: none;">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Keterangan Realisasi Mulai <span class="text-red-600">*</span></label>
                    <textarea name="actual_start_remarks" rows="2" placeholder="Jelaskan alasan perbedaan tanggal realisasi mulai dengan rencana..."
                              class="w-full bg-white border @error('actual_start_remarks') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror text-slate-900 placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none transition-colors"
                              :required="isStartDeviated()">{{ old('actual_start_remarks') }}</textarea>
                    @error('actual_start_remarks')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                {{-- Remarks Deviasi Selesai --}}
                <div x-show="isEndDeviated()" x-transition class="bg-slate-50 border border-slate-200 rounded-xl p-4" style="display: none;">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Keterangan Realisasi Selesai <span class="text-red-600">*</span></label>
                    <textarea name="actual_end_remarks" rows="2" placeholder="Jelaskan alasan perbedaan tanggal realisasi selesai dengan rencana..."
                              class="w-full bg-white border @error('actual_end_remarks') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror text-slate-900 placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none transition-colors"
                              :required="isEndDeviated()">{{ old('actual_end_remarks') }}</textarea>
                    @error('actual_end_remarks')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <div x-data="{ progress: {{ old('progress', 0) }} }">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Progress: <span class="text-blue-600 font-bold" x-text="progress + '%'"></span>
                </label>
                <input type="range" name="progress" min="0" max="100" x-model="progress"
                       class="w-full h-2 rounded-lg accent-blue-500 cursor-pointer">
                <div class="flex justify-between text-[10px] text-slate-500 mt-1"><span>0%</span><span>50%</span><span>100%</span></div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Status <span class="text-red-600">*</span></label>
                <select name="status" required class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @foreach(\App\Models\Task::STATUSES as $s)
                        <option value="{{ $s }}" {{ old('status','Belum Mulai') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Deskripsi task (opsional)"
                          class="w-full bg-white border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none transition-colors">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl text-sm transition-all hover:-translate-y-0.5 shadow-sm hover:shadow-md">
                    Simpan Task
                </button>
                <a href="{{ route('tasks.index') }}" class="bg-slate-100 border border-slate-200 text-slate-700 hover:bg-slate-200 hover:text-slate-900 px-6 py-3 rounded-xl text-sm transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
