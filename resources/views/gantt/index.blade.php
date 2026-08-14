@extends('layouts.app')
@section('title', 'Rencana Tahunan')
@section('heading', 'Rencana Tahunan')
@section('subheading', 'Jadwal pelaksanaan program kerja selama satu tahun')

@section('content')

{{-- Filter Tahun --}}
<div class="mb-4 flex flex-wrap items-center gap-3">
    <form method="GET" action="{{ route('gantt') }}" class="flex items-center gap-2" id="yearForm">
        <label for="year" class="text-sm font-bold text-slate-700">Tahun:</label>
        <select name="year" id="year" onchange="document.getElementById('yearForm').submit()"
                class="bg-white border border-slate-200 text-slate-700 rounded-lg px-3 py-1.5 text-sm font-bold shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

{{--
==========================================================================
ENTERPRISE SCROLLABLE TABLE — Prinsip kerja:
  [A] .gt-wrap  → clip boundary (overflow: hidden, tidak bisa expand)
  [B] .gt-scroll → satu-satunya elemen yang BOLEH scroll (overflow: auto)
  [C] <table>   → width: max-content → lebar = lebar real semua kolom
  [D] thead th  → position: sticky top:0 (beku saat scroll ↑↓)
  [E] .gt-name  → position: sticky left:0 (beku saat scroll ←→)
  Sticky bekerja RELATIF terhadap .gt-scroll (scroll container mereka)
==========================================================================
--}}

<div class="gt-wrap">
    <div class="gt-scroll">

        @php
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            // Bangun matrix bulan → minggu
            $matrix = [];
            foreach ($months as $m => $mname) {
                $days = cal_days_in_month(CAL_GREGORIAN, $m, $year);
                $weeks = [
                    ['label' => 'Mg-1', 'range' => '1-7',    's' => 1,  'e' => 7],
                    ['label' => 'Mg-2', 'range' => '8-14',   's' => 8,  'e' => 14],
                    ['label' => 'Mg-3', 'range' => '15-21',  's' => 15, 'e' => 21],
                    ['label' => 'Mg-4', 'range' => '22-28',  's' => 22, 'e' => 28],
                ];
                if ($days > 28) $weeks[] = ['label' => 'Mg-5', 'range' => "29-{$days}", 's' => 29, 'e' => $days];
                $matrix[$m] = ['name' => $mname, 'weeks' => $weeks];
            }

            function weekOverlap($a, $b, $c, $d) {
                if (!$a || !$b) return false;
                return $a <= $d && $b >= $c;
            }
        @endphp

        <table class="gt-table">
            <thead>
                {{-- Baris 1: Nama kolom kiri + Nama Bulan --}}
                <tr class="gt-row-month">
                    <th class="gt-th gt-name" rowspan="2">Nama Program / Kegiatan</th>
                    <th class="gt-th gt-date-hdr" rowspan="2">Tanggal</th>
                    @foreach($matrix as $m => $month)
                        <th class="gt-th gt-month" colspan="{{ count($month['weeks']) }}">
                            {{ $month['name'] }}
                        </th>
                    @endforeach
                </tr>
                {{-- Baris 2: Nama Minggu --}}
                <tr class="gt-row-week">
                    @foreach($matrix as $m => $month)
                        @foreach($month['weeks'] as $w)
                            <th class="gt-th gt-week">
                                {{ $w['label'] }}<br>
                                <span class="gt-week-range">({{ $w['range'] }})</span>
                            </th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @forelse($projects as $idx => $project)
                    @php
                        $pColor = match($project->status) {
                            'Selesai' => 'bar-green',
                            'Overdue' => 'bar-red',
                            default   => 'bar-blue',
                        };
                        $pS = $project->start_date ? $project->start_date->startOfDay() : null;
                        $pE = $project->end_date   ? $project->end_date->endOfDay()     : null;
                    @endphp

                    {{-- ROW: PROJECT --}}
                    <tr class="gt-row-project">
                        <td class="gt-td gt-name gt-name-project">
                        <div class="gt-nwrap">
                                <span class="gt-badge-num">{{ $idx + 1 }}</span>
                                <a href="{{ route('projects.show', $project->id) }}" class="gt-link-project">{{ $project->name }}</a>
                            </div>
                        </td>
                        <td class="gt-td gt-date-cell">
                            @if($pS && $pE) {{ $pS->format('d/m') }}&ndash;{{ $pE->format('d/m') }} @else &mdash; @endif
                        </td>
                        @foreach($matrix as $m => $month)
                            @foreach($month['weeks'] as $w)
                                @php
                                    $wS = \Carbon\Carbon::create($year, $m, $w['s'])->startOfDay();
                                    $wE = \Carbon\Carbon::create($year, $m, $w['e'])->endOfDay();
                                    $hit = weekOverlap($pS, $pE, $wS, $wE);
                                @endphp
                                <td class="gt-td gt-cell {{ $hit ? 'gt-cell-active' : '' }}">
                                    @if($hit)<div class="gt-bar {{ $pColor }} gt-bar-lg" title="{{ $project->name }}"></div>@endif
                                </td>
                            @endforeach
                        @endforeach
                    </tr>

                    {{-- SUBPROJECTS --}}
                    @foreach($project->subprojects as $sub)
                        @php
                            // Prioritas 1: tanggal milik subproject sendiri
                            $subS = $sub->start_date;
                            $subE = $sub->end_date;

                            // Prioritas 2: fallback range dari task di dalamnya
                            if (!$subS || !$subE) {
                                $subTaskDates = $sub->tasks->filter(fn($t) => $t->start_date && $t->due_date);
                                $subS = $subS ?? $subTaskDates->min('start_date');
                                $subE = $subE ?? $subTaskDates->max('due_date');
                            }

                            $subSObj = $subS ? \Carbon\Carbon::parse($subS) : null;
                            $subEObj = $subE ? \Carbon\Carbon::parse($subE) : null;
                        @endphp
                        <tr class="gt-row-sub">
                            <td class="gt-td gt-name gt-name-sub">
                            <div class="gt-nwrap">
                                    <span class="gt-arrow">↳</span>
                                    <a href="{{ route('subprojects.show', $sub->id) }}" class="gt-link-sub">{{ $sub->name }}</a>
                                    <span class="gt-tag">LIST</span>
                                </div>
                            </td>
                            <td class="gt-td gt-date-cell gt-date-muted">
                                @if($subSObj && $subEObj)
                                    {{ $subSObj->format('d/m') }}&ndash;{{ $subEObj->format('d/m') }}
                                @else
                                    &mdash;
                                @endif
                            </td>
                            @foreach($matrix as $m => $month)
                                @foreach($month['weeks'] as $w)
                                    <td class="gt-td gt-cell"></td>
                                @endforeach
                            @endforeach
                        </tr>

                        @foreach($sub->tasks as $task)
                            @php
                                $tColor = match($task->status) { 'Selesai' => 'bar-green', 'Overdue' => 'bar-red', default => 'bar-slate' };
                                $tS = $task->start_date ? $task->start_date->startOfDay() : null;
                                $tE = $task->due_date   ? $task->due_date->endOfDay()     : null;
                            @endphp
                            <tr class="gt-row-task">
                                <td class="gt-td gt-name gt-name-task">
                                <div class="gt-nwrap">
                                        <span class="gt-dot">▪</span>
                                        <a href="{{ route('tasks.show', $task->id) }}" class="gt-link-task">{{ $task->name }}</a>
                                    </div>
                                </td>
                                <td class="gt-td gt-date-cell gt-date-sm">
                                    @if($tS && $tE) {{ $tS->format('d/m') }}&ndash;{{ $tE->format('d/m') }} @else &mdash; @endif
                                </td>
                                @foreach($matrix as $m => $month)
                                    @foreach($month['weeks'] as $w)
                                        @php
                                            $wS = \Carbon\Carbon::create($year, $m, $w['s'])->startOfDay();
                                            $wE = \Carbon\Carbon::create($year, $m, $w['e'])->endOfDay();
                                            $hit = weekOverlap($tS, $tE, $wS, $wE);
                                        @endphp
                                        <td class="gt-td gt-cell">
                                            @if($hit)<div class="gt-bar {{ $tColor }} gt-bar-sm" title="{{ $task->name }}"></div>@endif
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach

                    {{-- DIRECT TASKS --}}
                    @foreach($project->tasks->whereNull('subproject_id') as $task)
                        @php
                            $tColor = match($task->status) { 'Selesai' => 'bar-green', 'Overdue' => 'bar-red', default => 'bar-slate' };
                            $tS = $task->start_date ? $task->start_date->startOfDay() : null;
                            $tE = $task->due_date   ? $task->due_date->endOfDay()     : null;
                        @endphp
                        <tr class="gt-row-task">
                            <td class="gt-td gt-name gt-name-task" style="padding-left: 20px;">
                            <div class="gt-nwrap">
                                <div class="gt-nwrap">
                                        <span class="gt-dot" style="color:#64748b;">▪</span>
                                        <a href="{{ route('tasks.show', $task->id) }}" class="gt-link-task">{{ $task->name }}</a>
                                    </div>
                            </td>
                            <td class="gt-td gt-date-cell gt-date-sm">
                                @if($tS && $tE) {{ $tS->format('d/m') }}&ndash;{{ $tE->format('d/m') }} @else &mdash; @endif
                            </td>
                            @foreach($matrix as $m => $month)
                                @foreach($month['weeks'] as $w)
                                    @php
                                        $wS = \Carbon\Carbon::create($year, $m, $w['s'])->startOfDay();
                                        $wE = \Carbon\Carbon::create($year, $m, $w['e'])->endOfDay();
                                        $hit = weekOverlap($tS, $tE, $wS, $wE);
                                    @endphp
                                    <td class="gt-td gt-cell">
                                        @if($hit)<div class="gt-bar {{ $tColor }} gt-bar-sm" title="{{ $task->name }}"></div>@endif
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @endforeach

                @empty
                    @php $cols = 2; foreach($matrix as $m) $cols += count($m['weeks']); @endphp
                    <tr>
                        <td colspan="{{ $cols }}" class="gt-empty">Belum ada program kerja di tahun {{ $year }}.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>{{-- /.gt-scroll --}}
</div>{{-- /.gt-wrap --}}

<style>
/* ==============================================
   ENTERPRISE GANTT — PREMIUM DESIGN
   Scroll hanya di .gt-scroll, sticky bekerja
   relatif ke .gt-scroll (scroll container).
   ============================================== */

/* [A] WRAPPER */
.gt-wrap {
    width: 100%;
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 12px rgba(15,23,42,0.06), 0 1px 3px rgba(15,23,42,0.04);
    overflow: hidden;
}

/* [B] SCROLL CONTAINER */
.gt-scroll {
    width: 100%;
    overflow-x: auto;
    overflow-y: auto;
    max-height: calc(100vh - 230px);
    scrollbar-width: thin;
    scrollbar-color: #94a3b8 #f1f5f9;
}
.gt-scroll::-webkit-scrollbar { height: 10px; width: 8px; }
.gt-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 99px; }
.gt-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; border: 2px solid #f1f5f9; }
.gt-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

/* [C] TABLE */
.gt-table {
    border-collapse: separate;
    border-spacing: 0;
    width: max-content;
    min-width: 100%;
    font-family: 'Inter', sans-serif;
}

/* ============================================
   STICKY HEADERS [D]
   ============================================ */
.gt-row-month .gt-th {
    position: sticky;
    top: 0;
    z-index: 10;
}
.gt-row-week .gt-week {
    position: sticky;
    /* top set via JS */
    z-index: 10;
}

/* ============================================
   STICKY LEFT COLUMNS [E]
   ============================================ */
th.gt-name {
    position: sticky; top: 0; left: 0;
    z-index: 40 !important;
    min-width: 310px; max-width: 310px;
    background: #f0f4ff !important;
    border-right: 2px solid #c7d7fe;
    box-shadow: 4px 0 12px -2px rgba(30,58,138,0.10);
    text-align: left;
    padding: 12px 16px;
    font-weight: 800;
    font-size: 12px;
    color: #1e3a8a;
    vertical-align: middle;
    letter-spacing: 0.01em;
}
th.gt-date-hdr {
    position: sticky; top: 0; left: 310px;
    z-index: 39 !important;
    min-width: 105px;
    background: #f8fafc !important;
    border-right: 2px solid #e2e8f0;
    box-shadow: 3px 0 8px -2px rgba(0,0,0,0.06);
    text-align: center;
    font-weight: 700; font-size: 11px; color: #64748b;
    white-space: nowrap; padding: 8px;
}
td.gt-name {
    position: sticky; left: 0;
    z-index: 20;
    min-width: 310px; max-width: 310px;
    background: #fff;
    border-right: 2px solid #e2e8f0;
    box-shadow: 4px 0 12px -2px rgba(30,58,138,0.07);
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    padding: 0;
}
td.gt-date-cell {
    position: sticky; left: 310px;
    z-index: 19;
    min-width: 105px;
    background: #fff;
    border-right: 2px solid #e2e8f0;
    box-shadow: 3px 0 8px -2px rgba(0,0,0,0.05);
    text-align: center;
    font-size: 10px; color: #64748b;
    white-space: nowrap; padding: 4px 6px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

/* ============================================
   HEADER BULAN & MINGGU
   ============================================ */
.gt-month {
    padding: 8px 6px;
    text-align: center;
    font-weight: 800; font-size: 11px;
    color: #1e3a8a;
    background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%) !important;
    border-right: 2px solid #bfdbfe;
    border-bottom: 1px solid #bfdbfe;
    white-space: nowrap;
    letter-spacing: 0.03em;
}
.gt-week {
    padding: 4px 2px;
    text-align: center;
    font-size: 9.5px; font-weight: 600; color: #475569;
    white-space: nowrap;
    background: #f8fafc !important;
    border-right: 1px solid #e8edf5;
    border-bottom: 2px solid #e2e8f0;
    min-width: 60px; width: 60px;
}
.gt-week-range { font-size: 8.5px; color: #94a3b8; font-weight: 400; }

/* ============================================
   ROW TYPES
   ============================================ */
.gt-row-project {
    background: linear-gradient(90deg, #f0f7ff 0%, #f8fbff 100%);
}
.gt-row-project:hover { background: linear-gradient(90deg,#dbeafe,#eff6ff); }
.gt-row-project:hover td.gt-name  { background: #dbeafe; }
.gt-row-project:hover td.gt-date-cell { background: #dbeafe; }

.gt-row-sub { background: #fafbfd; }
.gt-row-sub:hover { background: #f0f4ff; }
.gt-row-sub:hover td.gt-name { background: #f0f4ff; }
.gt-row-sub:hover td.gt-date-cell { background: #f0f4ff; }

.gt-row-task { background: #fff; }
.gt-row-task:hover { background: #f8fafc; }
.gt-row-task:hover td.gt-name { background: #f8fafc; }
.gt-row-task:hover td.gt-date-cell { background: #f8fafc; }

/* Background sticky cell ikut row */
.gt-row-project td.gt-name  { background: #f0f7ff; }
.gt-row-sub td.gt-name      { background: #fafbfd; }

/* ============================================
   GRID CELLS (td.gt-cell)
   ============================================ */
.gt-th { white-space: nowrap; border-bottom: 1px solid #e2e8f0; }
.gt-td  { vertical-align: middle; }

td.gt-cell {
    min-width: 60px; width: 60px;
    padding: 4px 3px;
    text-align: center;
    vertical-align: middle;
    border-right: 1px solid #f0f4f8;
    border-bottom: 1px solid #f0f4f8;
}
.gt-cell-active { background: rgba(219,234,254,0.3); }

/* ============================================
   NAME COLUMN INNER CONTENT (.gt-nwrap)
   ============================================ */
.gt-nwrap {
    display: flex;
    align-items: center;
    gap: 6px;
    overflow: hidden;
    padding: 0 12px;
    min-height: 34px;
}
.gt-name-project .gt-nwrap {
    padding: 8px 16px;
    min-height: 38px;
    gap: 7px;
}
.gt-name-sub    { padding-left: 0; }
.gt-name-sub .gt-nwrap   { padding-left: 26px; min-height: 32px; }
.gt-name-task   { padding-left: 0; }
.gt-name-task .gt-nwrap  { padding-left: 38px; min-height: 30px; }

/* Number badge */
.gt-badge-num {
    flex-shrink: 0;
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #fff;
    font-size: 9px; font-weight: 900;
    border-radius: 5px;
    padding: 2px 6px;
    letter-spacing: 0.02em;
    box-shadow: 0 1px 4px rgba(37,99,235,0.3);
}
/* Date badge on project row */
.gt-badge-date {
    flex-shrink: 0;
    background: #e0e7ff;
    color: #3730a3;
    font-size: 9px; font-weight: 700;
    border-radius: 5px;
    padding: 2px 6px;
    white-space: nowrap;
    margin-left: auto;
}
/* LIST badge on subproject */
.gt-tag {
    flex-shrink: 0;
    background: linear-gradient(135deg,#eef2ff,#e0e7ff);
    color: #6366f1;
    font-size: 8px; font-weight: 800;
    border-radius: 4px;
    padding: 2px 5px;
    margin-left: auto;
    border: 1px solid #c7d2fe;
    letter-spacing: 0.05em;
}

/* Icons */
.gt-arrow { color: #818cf8; font-size: 12px; flex-shrink: 0; }
.gt-dot   { color: #cbd5e1; font-size: 10px; flex-shrink: 0; }

/* Small date shown inside name cell */
.gt-date-inline {
    flex-shrink: 0; margin-left: auto;
    font-size: 9px; color: #94a3b8;
    white-space: nowrap;
    background: #f8fafc;
    border-radius: 3px;
    padding: 1px 4px;
}
.gt-date-muted  { color: #cbd5e1; }
.gt-date-sm     { font-size: 10px; color: #64748b; }

/* Links */
.gt-link-project {
    color: #1e40af; font-weight: 800; font-size: 11.5px;
    text-decoration: none; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis; min-width: 0;
    transition: color .15s;
}
.gt-link-project:hover { color: #2563eb; text-decoration: underline; }
.gt-link-sub {
    color: #4f46e5; font-weight: 600; font-size: 11px;
    text-decoration: none; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis; min-width: 0;
    transition: color .15s;
}
.gt-link-sub:hover { color: #6366f1; }
.gt-link-task {
    color: #475569; font-size: 10.5px;
    text-decoration: none; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis; min-width: 0;
    transition: color .15s;
}
.gt-link-task:hover { color: #1d4ed8; }

/* ============================================
   GANTT BARS
   ============================================ */
.gt-bar {
    border-radius: 4px;
    width: 88%;
    margin: 0 auto;
    display: block;
    transition: opacity .15s;
}
.gt-bar:hover { opacity: .85; }
.gt-bar-lg { height: 16px; }
.gt-bar-sm { height: 8px; }
.bar-blue  { background: linear-gradient(90deg,#60a5fa,#2563eb); box-shadow: 0 1px 4px rgba(37,99,235,.35); }
.bar-green { background: linear-gradient(90deg,#34d399,#059669); box-shadow: 0 1px 4px rgba(5,150,105,.35); }
.bar-red   { background: linear-gradient(90deg,#f87171,#dc2626); box-shadow: 0 1px 4px rgba(220,38,38,.35); }
.bar-slate { background: linear-gradient(90deg,#94a3b8,#475569); }

/* ============================================
   EMPTY STATE
   ============================================ */
.gt-empty {
    text-align: center;
    padding: 56px 24px;
    color: #94a3b8;
    font-size: 13px;
}

/* Tailwind ml-auto helper */
.ml-auto { margin-left: auto !important; }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Ukur tinggi baris BULAN secara akurat
    // lalu set baris MINGGU tepat di bawahnya
    function fixWeekTop() {
        var monthRow = document.querySelector('.gt-row-month');
        if (!monthRow) return;
        var h = monthRow.getBoundingClientRect().height;
        document.querySelectorAll('.gt-row-week .gt-week').forEach(function(th) {
            th.style.top = Math.round(h) + 'px';
        });
    }
    fixWeekTop();
    window.addEventListener('resize', fixWeekTop);
});
</script>
@endpush
