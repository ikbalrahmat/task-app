@extends('layouts.app')
@section('title', 'Agenda Tahunan')
@section('heading', 'Agenda Tahunan')
@section('subheading', 'Perencanaan dan monitoring kegiatan ' . $tahun)

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css' rel='stylesheet' />
<style>
/* ===== GANTT PROPORTIONAL ===== */
.gantt-wrap       { overflow-x: auto; }
.gantt-grid       { display: grid; grid-template-columns: 220px 1fr; min-width: 600px; }
.gantt-header-row { display: contents; }
.gantt-name-cell  { position: sticky; left: 0; z-index: 20; background: #fff; border-right: 1px solid #e0e7ff; }
.gantt-row        { display: contents; }
.gantt-row:hover .gantt-name-cell,
.gantt-row:hover .gantt-bar-cell { background: #f0f4ff !important; }
.gantt-bar-cell   { position: relative; border-bottom: 1px solid #f1f5f9; background: #fff; }
.gantt-bar        { position: absolute; top: 50%; transform: translateY(-50%);
                    height: 18px; border-radius: 4px; cursor: pointer;
                    transition: filter .15s; z-index: 2; min-width: 4px; }
.gantt-bar:hover  { filter: brightness(1.1); }
.gantt-today-line { position: absolute; top: 0; bottom: 0; width: 2px;
                    background: #2563eb; opacity: .6; z-index: 3;
                    pointer-events: none; }
/* Month header cells */
.month-hdr        { border-right: 1px solid #e0e7ff; text-align: center;
                    font-size: 11px; font-weight: 600; color: #1e3a8a;
                    padding: 4px 2px; white-space: nowrap; overflow: hidden; }
/* Alternating month shade */
.month-shade-even { background: #f8faff; }
.month-shade-odd  { background: #fff; }

/* ===== FULLCALENDAR ===== */
.fc .fc-toolbar-title    { font-size: 1rem; font-weight: 700; color: #1e3a8a; }
.fc .fc-button-primary   { background:#2563eb; border-color:#1e3a8a; font-size:.75rem; padding:.35rem .7rem; border-radius:.5rem; }
.fc .fc-button-primary:hover { background:#1e40af; }
.fc .fc-button-primary:not(:disabled).fc-button-active { background:#1e3a8a; }
.fc .fc-daygrid-day-number { font-size:.72rem; padding:4px 6px; font-weight:500; }
.fc .fc-col-header-cell  { background:#f0f4ff; font-size:.7rem; font-weight:600; color:#1e3a8a; }
.fc .fc-daygrid-day.fc-day-today { background:#eff6ff !important; }
.fc-event { border-radius:4px!important; border:none!important; font-size:.68rem!important; padding:1px 4px!important; }
</style>
@endpush

@section('content')
@php
    $totalDays  = (new DateTime("{$tahun}-12-31"))->diff(new DateTime("{$tahun}-01-01"))->days + 1;
    $todayRatio = null;
    if (date('Y') == $tahun) {
        $dayOfYear  = (int)(new DateTime('today'))->diff(new DateTime("{$tahun}-01-01"))->days + 1;
        $todayRatio = ($dayOfYear - 1) / $totalDays * 100;
    }

    $mappedAgendas = $agendas->map(function($a) {
        return [
            'id' => $a->id,
            'nama_kegiatan' => $a->nama_kegiatan,
            'tanggal_mulai' => $a->tanggal_mulai ? $a->tanggal_mulai->format('Y-m-d') : null,
            'tanggal_selesai' => $a->tanggal_selesai ? $a->tanggal_selesai->format('Y-m-d') : null,
            'kategori' => $a->kategori,
            'keterangan' => $a->keterangan,
            'warna' => $a->warna
        ];
    })->toArray();
@endphp

<div x-data="agendaApp()" x-init="init()">

    {{-- ============================================================
         TOOLBAR
    ============================================================ --}}
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-2xl px-5 py-3 shadow-sm flex flex-wrap items-center gap-3 mb-4">

        {{-- Navigasi Tahun --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('agenda.index', ['tahun' => $tahun - 1]) }}"
               class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <span class="font-bold text-[#1e3a8a] text-lg px-2">{{ $tahun }}</span>
            <a href="{{ route('agenda.index', ['tahun' => $tahun + 1]) }}"
               class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="h-5 w-px bg-slate-200"></div>

        {{-- Toggle View --}}
        <div class="flex bg-slate-100 rounded-xl p-1 gap-1">
            <button @click="activeView = 'gantt'"
                    :class="activeView === 'gantt' ? 'bg-white shadow text-[#1e3a8a]' : 'text-slate-500 hover:text-slate-700'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Gantt
            </button>
            <button @click="activeView = 'kalender'; initCalendar()"
                    :class="activeView === 'kalender' ? 'bg-white shadow text-[#1e3a8a]' : 'text-slate-500 hover:text-slate-700'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Kalender
            </button>
        </div>

        <div class="flex-1"></div>

        {{-- Legend --}}
        <div class="hidden md:flex items-center gap-3 text-[11px]">
            @foreach(\App\Models\AgendaKegiatan::WARNA_KATEGORI as $kat => $warna)
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-sm flex-shrink-0" style="background:{{ $warna }}"></span>
                <span class="text-slate-500">{{ $kat }}</span>
            </div>
            @endforeach
        </div>

        <div class="h-5 w-px bg-slate-200 hidden md:block"></div>

        <button @click="showFormModal = true"
                class="flex items-center gap-2 bg-gradient-to-r from-[#1e3a8a] to-[#2563eb] text-white px-4 py-2 rounded-xl text-sm font-semibold shadow hover:shadow-md transition-all hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kegiatan
        </button>
    </div>

    {{-- ============================================================
         GANTT VIEW — Proportional Year (fit window)
    ============================================================ --}}
    <div x-show="activeView === 'gantt'" x-transition
         class="bg-white/80 backdrop-blur-md border border-white/60 rounded-2xl shadow-sm overflow-hidden">

        <div class="gantt-wrap">
            <div class="gantt-grid">

                {{-- === HEADER ROW === --}}
                {{-- Left cell --}}
                <div class="gantt-name-cell bg-[#1e3a8a] px-4 py-2.5 font-semibold text-white text-xs flex items-center">
                    Nama Kegiatan
                </div>
                {{-- Month headers (proportional) --}}
                <div class="flex border-b border-slate-200" style="background:#1e3a8a;">
                    @foreach($bulanData as $i => $bulan)
                    @php $pct = $bulan['jumlah_hari'] / $totalDays * 100; @endphp
                    <div class="month-hdr text-white border-r border-blue-700/50"
                         style="width:{{ number_format($pct,4) }}%; flex-shrink:0;">
                        {{ Str::limit($bulan['nama'], 3, '') }}
                    </div>
                    @endforeach
                </div>

                {{-- === DATA ROWS === --}}
                @forelse($agendas as $agenda)
                @php
                    $mulai    = $agenda->tanggal_mulai;
                    $selesai  = $agenda->tanggal_selesai;
                    $warna    = $agenda->warna;
                    $barLeft  = null;
                    $barWidth = null;
                    if ($mulai && $selesai) {
                        $startDay  = (int)(new DateTime($mulai->format('Y-m-d')))->diff(new DateTime("{$tahun}-01-01"))->days;
                        $endDay    = (int)(new DateTime($selesai->format('Y-m-d')))->diff(new DateTime("{$tahun}-01-01"))->days;
                        $barLeft   = max(0, $startDay / $totalDays * 100);
                        $barWidth  = min(100 - $barLeft, ($endDay - $startDay + 1) / $totalDays * 100);
                    }
                @endphp
                {{-- Name cell --}}
                <div class="gantt-name-cell px-3 py-2 border-b border-slate-100 group flex items-center justify-between gap-2"
                     style="min-height:44px;">
                    <div class="min-w-0">
                        <div class="font-semibold text-slate-800 truncate text-xs">{{ $agenda->nama_kegiatan }}</div>
                        @if($agenda->kategori)
                        <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:{{ $warna }}"></span>
                            {{ $agenda->kategori }}
                        </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                        <button @click="openDatePicker({{ $agenda->id }}, '{{ addslashes($agenda->nama_kegiatan) }}', '{{ $mulai?->format('Y-m-d') }}', '{{ $selesai?->format('Y-m-d') }}')"
                                title="Set tanggal"
                                class="p-1 rounded text-blue-500 hover:bg-blue-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </button>
                        <button @click="openEdit({{ $agenda->id }}, '{{ addslashes($agenda->nama_kegiatan) }}', '{{ $agenda->kategori }}', '{{ addslashes($agenda->keterangan ?? '') }}')"
                                title="Edit" class="p-1 rounded text-slate-400 hover:bg-slate-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        @if($mulai)
                        <button @click="resetMapping({{ $agenda->id }})"
                                title="Reset tanggal" class="p-1 rounded text-amber-400 hover:bg-amber-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                        @endif
                        <button @click="deleteAgenda({{ $agenda->id }})"
                                title="Hapus" class="p-1 rounded text-red-400 hover:bg-red-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Bar cell --}}
                <div class="gantt-bar-cell border-b border-slate-100 cursor-pointer"
                     style="min-height:44px;"
                     @click="openDatePicker({{ $agenda->id }}, '{{ addslashes($agenda->nama_kegiatan) }}', '{{ $mulai?->format('Y-m-d') }}', '{{ $selesai?->format('Y-m-d') }}')" >

                    {{-- Month alternating background stripes --}}
                    @php $dayOffset = 0; @endphp
                    @foreach($bulanData as $i => $bulan)
                    @php
                        $pct = $bulan['jumlah_hari'] / $totalDays * 100;
                        $off = $dayOffset / $totalDays * 100;
                        $dayOffset += $bulan['jumlah_hari'];
                    @endphp
                    <div class="absolute top-0 bottom-0 {{ $i % 2 === 0 ? 'bg-slate-50/50' : '' }}"
                         style="left:{{ number_format($off,4) }}%; width:{{ number_format($pct,4) }}%; pointer-events:none;"></div>
                    @endforeach

                    {{-- Today line --}}
                    @if($todayRatio !== null)
                    <div class="gantt-today-line" style="left:{{ number_format($todayRatio,4) }}%;"></div>
                    @endif

                    {{-- Bar --}}
                    @if($barLeft !== null)
                    <div class="gantt-bar" title="{{ $agenda->nama_kegiatan }}: {{ $mulai->format('d M') }} – {{ $selesai->format('d M Y') }}"
                         style="left:{{ number_format($barLeft,4) }}%; width:{{ number_format($barWidth,4) }}%; background:{{ $warna }};">
                        <span class="absolute inset-0 flex items-center px-2 text-white text-[10px] font-semibold truncate" style="font-size:9px;">
                            {{ $agenda->nama_kegiatan }}
                        </span>
                    </div>
                    @endif
                </div>
                @empty
                <div class="gantt-name-cell py-12"></div>
                <div class="flex items-center justify-center py-12 text-slate-400">
                    <div class="text-center">
                        <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm font-medium">Belum ada kegiatan {{ $tahun }}</p>
                        <button @click="showFormModal = true" class="text-blue-600 text-xs font-semibold hover:underline mt-1">+ Tambah Kegiatan Pertama</button>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Footer info --}}
        <div class="px-4 py-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
            <span>Klik baris untuk mengatur jadwal kegiatan</span>
            @if($todayRatio !== null)
            <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                Hari ini: {{ now()->isoFormat('D MMMM Y') }}
            </span>
            @endif
        </div>
    </div>

    {{-- ============================================================
         KALENDER VIEW
    ============================================================ --}}
    <div x-show="activeView === 'kalender'" x-transition
         class="bg-white/80 backdrop-blur-md border border-white/60 rounded-2xl shadow-sm p-5">
        <div id="fullcalendar"></div>
    </div>

    {{-- ============================================================
         MODAL: FORM TAMBAH / EDIT
    ============================================================ --}}
    <div x-show="showFormModal" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         style="display:none;" @click.self="showFormModal = false; resetForm()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>
            <h3 class="font-bold text-[#1e3a8a] mb-4 text-sm" x-text="editId ? 'Edit Kegiatan' : 'Tambah Kegiatan Baru'"></h3>
            <form @submit.prevent="submitForm()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.nama_kegiatan" required
                           class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                           placeholder="Contoh: Audit ISO 27001 DC Sentul">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Kategori</label>
                    <select x-model="form.kategori"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <option value="">— Pilih Kategori —</option>
                        @foreach(\App\Models\AgendaKegiatan::KATEGORI as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Keterangan</label>
                    <textarea x-model="form.keterangan" rows="3" placeholder="Opsional..."
                              class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-[#1e3a8a] to-[#2563eb] text-white font-bold py-2.5 rounded-xl text-sm">
                        <span x-text="editId ? 'Simpan Perubahan' : 'Tambah Kegiatan'"></span>
                    </button>
                    <button type="button" @click="showFormModal = false; resetForm()"
                            class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================
         MODAL: SET TANGGAL (Date Picker)
    ============================================================ --}}
    <div x-show="showDateModal" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         style="display:none;" @click.self="showDateModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6" @click.stop>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Jadwalkan Kegiatan</h3>
                    <p class="text-xs text-slate-500 truncate max-w-[200px]" x-text="dateForm.nama"></p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" x-model="dateForm.tanggal_mulai"
                           :min="'{{ $tahun }}-01-01'" :max="'{{ $tahun }}-12-31'"
                           class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" x-model="dateForm.tanggal_selesai"
                           :min="dateForm.tanggal_mulai || '{{ $tahun }}-01-01'" :max="'{{ $tahun }}-12-31'"
                           class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button @click="submitMapping()"
                        :disabled="!dateForm.tanggal_mulai || !dateForm.tanggal_selesai"
                        class="flex-1 bg-gradient-to-r from-[#1e3a8a] to-[#2563eb] text-white font-bold py-2.5 rounded-xl text-sm disabled:opacity-50">
                    Simpan & Buat Program
                </button>
                <button @click="showDateModal = false"
                        class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script>
function agendaApp() {
    return {
        activeView: 'gantt',
        showFormModal: false,
        showDateModal: false,
        editId: null,
        form: { nama_kegiatan: '', kategori: '', keterangan: '' },
        dateForm: { id: null, nama: '', tanggal_mulai: '', tanggal_selesai: '' },
        agendas: @json($mappedAgendas),
        hariLibur: @json($hariLibur),
        tahun: {{ $tahun }},
        calendarInitialized: false,

        init() {},

        // ===== DATE PICKER MODAL =====
        openDatePicker(id, nama, mulai, selesai) {
            this.dateForm = { id, nama, tanggal_mulai: mulai || '', tanggal_selesai: selesai || '' };
            this.showDateModal = true;
        },

        async submitMapping() {
            if (!this.dateForm.tanggal_mulai || !this.dateForm.tanggal_selesai) return;
            if (this.dateForm.tanggal_selesai < this.dateForm.tanggal_mulai) {
                alert('Tanggal selesai harus setelah tanggal mulai.');
                return;
            }
            const res = await fetch(`/agenda/${this.dateForm.id}/mapping`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    tanggal_mulai:   this.dateForm.tanggal_mulai,
                    tanggal_selesai: this.dateForm.tanggal_selesai,
                }),
            });
            const data = await res.json();
            if (data.success) {
                this.showDateModal = false;
                window.location.reload();
            } else {
                alert('Gagal: ' + (data.message || JSON.stringify(data.errors)));
            }
        },

        async resetMapping(id) {
            if (!confirm('Reset tanggal kegiatan ini? Program terkait juga akan dihapus.')) return;
            const res = await fetch(`/agenda/${id}/mapping`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            });
            if ((await res.json()).success) window.location.reload();
        },

        // ===== FORM MODAL =====
        openEdit(id, nama, kategori, keterangan) {
            this.editId = id;
            this.form   = { nama_kegiatan: nama, kategori: kategori ?? '', keterangan: keterangan ?? '' };
            this.showFormModal = true;
        },
        resetForm() { this.editId = null; this.form = { nama_kegiatan: '', kategori: '', keterangan: '' }; },

        async submitForm() {
            const url    = this.editId ? `/agenda/${this.editId}` : '/agenda';
            const method = this.editId ? 'PUT' : 'POST';
            const res    = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ ...this.form, tahun: this.tahun }),
            });
            const data = await res.json();
            if (data.success) { this.showFormModal = false; this.resetForm(); window.location.reload(); }
            else alert('Gagal: ' + JSON.stringify(data.errors ?? data.message));
        },

        async deleteAgenda(id) {
            if (!confirm('Hapus kegiatan ini?')) return;
            const res = await fetch(`/agenda/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            });
            if ((await res.json()).success) window.location.reload();
        },

        // ===== FULLCALENDAR =====
        initCalendar() {
            if (this.calendarInitialized) return;
            this.calendarInitialized = true;
            this.$nextTick(() => {
                const el = document.getElementById('fullcalendar');
                if (!el) return;

                // Build libur lookup — FIXED: pakai local date bukan ISO (hindari UTC shift)
                const liburDates = {};
                this.hariLibur.forEach(h => { liburDates[h.tanggal] = h.nama_libur; });

                const events = this.agendas
                    .filter(a => a.tanggal_mulai && a.tanggal_selesai)
                    .map(a => ({
                        id:              a.id,
                        title:           a.nama_kegiatan,
                        start:           a.tanggal_mulai,
                        end:             this.addDay(a.tanggal_selesai),
                        backgroundColor: this.getWarna(a.kategori),
                        borderColor:     'transparent',
                        extendedProps:   { agenda: a },
                    }));

                new FullCalendar.Calendar(el, {
                    initialView:  'dayGridMonth',
                    locale:       'id',
                    firstDay:     0,  // Minggu di kolom pertama
                    initialDate:  `${this.tahun}-01-01`,
                    height:       'auto',
                    headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth' },
                    events,
                    dayCellDidMount: (info) => {
                        // ✅ FIX: Get the exact YYYY-MM-DD rendered by FullCalendar
                        const dateStr = info.el.getAttribute('data-date');
                        if (!dateStr) return;
                        
                        const numEl = info.el.querySelector('.fc-daygrid-day-number');

                        // Sunday is always Day 0 in getDay() but we can check Date object just to be sure
                        if (info.date.getDay() === 0 && numEl) {
                            numEl.style.color = '#ef4444';
                        }

                        if (liburDates[dateStr]) {
                            if (numEl) { numEl.style.color = '#ef4444'; numEl.title = liburDates[dateStr]; }
                            const lbl = document.createElement('div');
                            lbl.style.cssText = 'font-size:8px; color:#ef4444; line-height:1.2; padding:0 4px; opacity:.85; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;';
                            lbl.textContent   = liburDates[dateStr];
                            info.el.appendChild(lbl);
                        }
                    },
                    eventClick: (info) => {
                        const a = info.event.extendedProps.agenda;
                        this.openDatePicker(a.id, a.nama_kegiatan, a.tanggal_mulai, a.tanggal_selesai);
                        this.activeView = 'kalender';
                    },
                }).render();
            });
        },

        // ===== HELPERS =====
        getWarna(kat) {
            return { 'Audit Internal':'#3b82f6','Audit Eksternal':'#8b5cf6','Assessment':'#10b981','Training':'#f59e0b','Monitoring':'#06b6d4','Lainnya':'#6b7280' }[kat] ?? '#6b7280';
        },
        addDay(dateStr) {
            if (!dateStr) return null;
            const parts = dateStr.split('-');
            if (parts.length !== 3) return null;
            // Parse array string ke angka untuk hindari UTC offset
            const d = new Date(parts[0], parts[1] - 1, parts[2]);
            d.setDate(d.getDate() + 1);
            return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        },
    };
}
</script>
@endpush
