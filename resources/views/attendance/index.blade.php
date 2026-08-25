@extends('layouts.app')
@section('title', 'Attendance')
@section('content')
<div class="space-y-5">

  {{-- Page Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h1 class="page-title">Attendance Management</h1>
      <p class="page-subtitle">{{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
      @if($pendingLeaves > 0)
      <a href="{{ route('attendance.leave') }}" class="btn btn-secondary btn-sm relative">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Leave Requests
        <span class="ml-0.5 inline-flex items-center justify-center w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full">{{ $pendingLeaves }}</span>
      </a>
      @endif
      <a href="{{ route('attendance.staff') }}" class="btn btn-secondary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Staff Attendance
      </a>
      <a href="{{ route('attendance.mark') }}" class="btn btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Mark Attendance
      </a>
    </div>
  </div>

  {{-- Today's Stat Cards --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    {{-- Present --}}
    <div class="card border-l-4 border-l-emerald-500">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Present</p>
          <p class="text-2xl font-bold text-emerald-700 leading-none" style="font-family:'Plus Jakarta Sans',sans-serif;letter-spacing:-0.03em">
            {{ $todayStats['present'] }}
          </p>
        </div>
        <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
          <svg class="w-4.5 h-4.5 w-[18px] h-[18px] text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
      </div>
    </div>

    {{-- Absent --}}
    <div class="card border-l-4 border-l-red-500">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Absent</p>
          <p class="text-2xl font-bold text-red-600 leading-none" style="font-family:'Plus Jakarta Sans',sans-serif;letter-spacing:-0.03em">
            {{ $todayStats['absent'] }}
          </p>
        </div>
        <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
          <svg class="w-[18px] h-[18px] text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
      </div>
    </div>

    {{-- Late --}}
    <div class="card border-l-4 border-l-amber-500">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Late</p>
          <p class="text-2xl font-bold text-amber-600 leading-none" style="font-family:'Plus Jakarta Sans',sans-serif;letter-spacing:-0.03em">
            {{ $todayStats['late'] }}
          </p>
        </div>
        <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
          <svg class="w-[18px] h-[18px] text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>
    </div>

    {{-- Rate --}}
    <div class="card border-l-4 {{ $todayStats['total'] > 0 ? ($todayStats['pct'] >= 75 ? 'border-l-indigo-500' : 'border-l-red-500') : 'border-l-slate-300' }}">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Today's Rate</p>
          @if($todayStats['total'] > 0)
            <p class="text-2xl font-bold leading-none {{ $todayStats['pct'] >= 75 ? 'text-indigo-700' : 'text-red-600' }}"
               style="font-family:'Plus Jakarta Sans',sans-serif;letter-spacing:-0.03em">
              {{ $todayStats['pct'] }}%
            </p>
          @else
            <p class="text-2xl font-bold text-slate-300 leading-none" style="font-family:'Plus Jakarta Sans',sans-serif">—</p>
          @endif
        </div>
        <div class="w-9 h-9 rounded-lg {{ $todayStats['total'] > 0 ? ($todayStats['pct'] >= 75 ? 'bg-indigo-50' : 'bg-red-50') : 'bg-slate-100' }} flex items-center justify-center">
          <svg class="w-[18px] h-[18px] {{ $todayStats['total'] > 0 ? ($todayStats['pct'] >= 75 ? 'text-indigo-600' : 'text-red-500') : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
      </div>
    </div>
  </div>

  {{-- Attendance Progress --}}
  @if($todayStats['total'] > 0)
  <div class="card py-4">
    <div class="flex items-center justify-between mb-2.5">
      <span class="text-sm font-semibold text-slate-700">Overall Attendance Rate — Today</span>
      <span class="text-sm font-bold {{ $todayStats['pct'] >= 75 ? 'text-emerald-700' : 'text-red-600' }}">
        {{ $todayStats['pct'] }}%
      </span>
    </div>
    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
      <div class="h-2 rounded-full transition-all duration-500 {{ $todayStats['pct'] >= 75 ? 'bg-emerald-500' : 'bg-red-500' }}"
           style="width: {{ $todayStats['pct'] }}%"></div>
    </div>
    <p class="text-xs text-slate-400 mt-2">
      <span class="font-semibold text-slate-600">{{ $todayStats['present'] }}</span> present out of
      <span class="font-semibold text-slate-600">{{ $todayStats['total'] }}</span> students marked today
    </p>
  </div>
  @endif

  {{-- Alerts --}}
  @if($unmarkedClasses->count() > 0)
  <div class="alert-warning">
    <div class="flex-shrink-0 mt-0.5">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <div class="flex-1 min-w-0">
      <p class="font-semibold">{{ $unmarkedClasses->count() }} class{{ $unmarkedClasses->count() > 1 ? 'es have' : ' has' }} not marked attendance today</p>
      <div class="mt-2 flex flex-wrap gap-2">
        @foreach($unmarkedClasses as $cls)
        <a href="{{ route('attendance.mark', ['class_id' => $cls->id, 'date' => today()->toDateString()]) }}"
           class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-lg text-xs font-semibold text-amber-900 transition-colors">
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
          {{ $cls->name }}
        </a>
        @endforeach
      </div>
    </div>
  </div>
  @elseif($todayStats['total'] > 0)
  <div class="alert-success">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="font-semibold">All classes have marked attendance today</p>
  </div>
  @endif

  {{-- ── Today's Section-Wise Attendance Status Grid ──────────── --}}
  <div class="card p-5 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div>
        <h3 class="font-extrabold text-slate-900 text-sm">Today's Section-Wise Attendance Register</h3>
        <p class="text-xs text-slate-400">Click on any section to mark, update, or review attendance</p>
      </div>
      <a href="{{ route('attendance.mark') }}" class="btn btn-primary btn-xs font-bold flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Manual Marking Studio
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
      @foreach($classes as $cls)
        @foreach($cls->sections->sortBy('name') as $sec)
          @php
            $key = $cls->id . '-' . $sec->id;
            $info = $todaySectionAttendance[$key] ?? ['is_marked' => false, 'total' => 0, 'present' => 0, 'absent' => 0, 'rate' => 0];
          @endphp
          <a href="{{ route('attendance.mark', ['class_id' => $cls->id, 'section_id' => $sec->id, 'date' => today()->toDateString()]) }}"
             class="p-3.5 rounded-2xl border transition-all duration-200 group flex flex-col justify-between {{ $info['is_marked'] ? 'bg-emerald-50/40 border-emerald-200 hover:border-emerald-400 hover:shadow-xs' : 'bg-slate-50/70 border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/30' }}">
            <div class="flex items-center justify-between">
              <div>
                <span class="font-extrabold text-slate-900 text-sm group-hover:text-indigo-600 transition-colors">Class {{ $cls->name }}</span>
                <span class="text-xs font-bold text-slate-500 ml-1">Sec {{ $sec->name }}</span>
              </div>
              @if($info['is_marked'])
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                  {{ $info['rate'] }}% Marked
                </span>
              @else
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                  Pending
                </span>
              @endif
            </div>

            <div class="mt-3 flex items-center justify-between text-xs font-medium text-slate-500">
              @if($info['is_marked'])
                <span><b class="text-emerald-700 font-mono">{{ $info['present'] }}</b> Present / <b class="text-rose-600 font-mono">{{ $info['absent'] }}</b> Absent</span>
                <span class="text-[11px] font-bold text-indigo-600 group-hover:underline">Edit &rarr;</span>
              @else
                <span class="text-slate-400">Attendance not taken</span>
                <span class="text-[11px] font-bold text-indigo-600 group-hover:underline">Mark Now &rarr;</span>
              @endif
            </div>
          </a>
        @endforeach
      @endforeach
    </div>
  </div>

  {{-- Quick Links --}}
  <div>
    <p class="section-title">Attendance Modules</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
      @foreach([
        ['Mark Attendance',     'attendance.mark',          'bg-emerald-50 text-emerald-700 border-emerald-200', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'],
        ['Monthly Report',      'attendance.report',        'bg-blue-50 text-blue-700 border-blue-200',          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
        ['Class Summary',       'attendance.class-summary', 'bg-indigo-50 text-indigo-700 border-indigo-200',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>'],
        ['Shortage Report',     'attendance.shortage',      'bg-red-50 text-red-700 border-red-200',             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
        ['Chronic Absentees',   'attendance.chronic',       'bg-rose-50 text-rose-700 border-rose-200',          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>'],
        ['Date-wise Strength',  'attendance.date-strength', 'bg-amber-50 text-amber-700 border-amber-200',       '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
        ['Leave Requests',      'attendance.leave',         'bg-teal-50 text-teal-700 border-teal-200',          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
        ['Staff Attendance',    'attendance.staff',         'bg-slate-100 text-slate-700 border-slate-200',      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'],
        ['Attendance Register', 'attendance.register',      'bg-violet-50 text-violet-700 border-violet-200',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
      ] as [$label,$route,$color,$icon])
      <a href="{{ route($route) }}"
         class="card-flat flex items-center gap-3 py-3.5 px-4 border hover:shadow-sm hover:border-slate-300 transition-all group {{ $color }}">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-white/60 group-hover:bg-white transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
        </div>
        <div class="flex-1 min-w-0">
          <span class="font-semibold text-sm leading-tight">{{ $label }}</span>
        </div>
        @if($label === 'Leave Requests' && $pendingLeaves > 0)
          <span class="inline-flex items-center justify-center w-5 h-5 bg-red-500 text-white text-[10px] rounded-full font-bold flex-shrink-0">{{ $pendingLeaves }}</span>
        @endif
      </a>
      @endforeach
    </div>
  </div>

</div>
@endsection
