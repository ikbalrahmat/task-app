@extends('layouts.app')
@section('title', 'Gantt Chart')
@section('heading', 'Gantt Chart')
@section('subheading', 'Visualisasi timeline program dan task')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css">
<style>
    #gantt-container .gantt-container { background: #ffffff !important; }
    
    /* Planned Bar (Baseline) */
    .bar-planned .bar { fill: #f8fafc !important; stroke: #94a3b8 !important; stroke-width: 1.5px; stroke-dasharray: 4; opacity: 0.9; }
    .bar-planned .bar-progress { fill: #cbd5e1 !important; opacity: 0.6; }
    .bar-planned .bar-label { fill: #475569 !important; font-weight: 600 !important; font-size: 11px !important; }
    
    /* Actual Bar (Realization) */
    .bar-actual .bar { fill: #3b82f6 !important; }
    .bar-actual .bar-progress { fill: #1d4ed8 !important; }
    .bar-actual .bar-label { fill: #ffffff !important; font-weight: 600 !important; font-size: 11px !important; }
    
    .gantt .grid-header { fill: #f8fafc !important; }
    .gantt .grid-row { fill: #ffffff !important; }
    .gantt .grid-row:nth-child(even) { fill: #f8fafc !important; }
    .gantt .row-line, .gantt .tick { stroke: #e2e8f0 !important; }
    .gantt .upper-text, .gantt .lower-text { fill: #475569 !important; font-weight: 600 !important; }
    .gantt-container { border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
</style>
@endpush

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="w-24 h-24 bg-blue-50 border border-blue-100 rounded-3xl flex items-center justify-center mb-6 shadow-inner text-blue-500">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
    </div>
    <h2 class="text-2xl font-bold text-slate-800 mb-2">Fitur Dalam Pengembangan 🚀</h2>
    <p class="text-slate-500 max-w-md mx-auto leading-relaxed">
        Kami sedang meracik fitur Gantt Chart yang super canggih untuk membantu abang memantau jadwal proyek dengan lebih mudah. Harap bersabar ya!
    </p>
    <a href="{{ route('dashboard') }}" class="mt-8 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors shadow-sm shadow-blue-600/30">
        Kembali ke Dashboard
    </a>
</div>
@endsection
