@extends('layouts.app')
@section('title','Staff Attendance')

@section('content')
<div class="space-y-5 w-full" x-data="staffAttendanceKiosk()">

  {{-- ── Page Header ───────────────────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('attendance.index') }}" class="btn-icon w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition shadow-2xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h1 class="page-title text-xl font-extrabold text-slate-900">Staff Attendance</h1>
        <p class="text-xs text-slate-500 mt-0.5">Card tapping terminal and daily staff register</p>
      </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      {{-- Download Day-Wise Report --}}
      <a href="{{ route('attendance.staff.export.day', ['date' => $date, 'category' => $category, 'department_id' => $deptId]) }}"
         class="btn bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 btn-sm flex items-center gap-1.5 text-xs font-bold shadow-2xs transition-all hover:border-slate-300"
         title="Download Excel spreadsheet for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download Day-Wise Report
      </a>

      {{-- Download Monthly Report --}}
      <a href="{{ route('attendance.staff.export.month', ['month' => \Carbon\Carbon::parse($date)->format('Y-m'), 'category' => $category, 'department_id' => $deptId]) }}"
         class="btn bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 btn-sm flex items-center gap-1.5 text-xs font-bold shadow-2xs transition-all"
         title="Download Excel monthly attendance & overtime summary for {{ \Carbon\Carbon::parse($date)->format('F Y') }}">
        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"/></svg>
        Download Monthly Report
      </a>
    </div>
  </div>

  {{-- ── Top Section: 2-Column Hub ──────────────────────────────── --}}
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    {{-- Column 1: Card Tap Terminal Kiosk --}}
    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-xs p-5 space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
          <h2 class="text-sm font-extrabold text-slate-900">Smart Card Tap Terminal</h2>
        </div>

        <div class="flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span class="font-mono text-xs font-bold text-slate-700" x-text="liveTime">00:00:00 AM</span>
        </div>
      </div>

      {{-- Terminal Input Box --}}
      <div class="space-y-2.5">
        <form @submit.prevent="submitTap()" class="relative flex items-center gap-2">
          <div class="flex-1 flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus-within:bg-white focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
            <svg class="w-4 h-4 text-indigo-600 shrink-0 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <input type="text" x-ref="cardScanner" x-model="cardInput"
                   placeholder="Tap RFID card or enter Employee ID / Name..."
                   style="outline: none !important; border: none !important; box-shadow: none !important; padding-left: 0.75rem !important;"
                   class="w-full bg-transparent border-none outline-none ring-0 focus:ring-0 focus:outline-none text-xs font-medium text-slate-800 placeholder-slate-400 py-0">
          </div>
          <button type="submit" :disabled="isProcessing"
                  class="btn btn-primary text-xs font-bold px-4 py-2.5 rounded-xl cursor-pointer shrink-0 shadow-2xs">
            <span x-show="!isProcessing">Punch &rarr;</span>
            <span x-show="isProcessing">Saving...</span>
          </button>
        </form>

        {{-- Error Banner --}}
        <div x-show="punchError" x-cloak class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs font-semibold flex items-center gap-2">
          <span>⚠️</span>
          <span x-text="punchError"></span>
        </div>

        {{-- Instant Success Punch Popup --}}
        <div x-show="lastPunch" x-cloak class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-950 text-xs flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
            ✓
          </div>
          <div class="flex-1">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="font-bold text-slate-900" x-text="lastPunch?.employee?.name"></span>
              <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800" x-text="lastPunch?.employee?.code || 'STAFF'"></span>
              <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full"
                    :class="lastPunch?.type === 'check_in' ? 'bg-emerald-600 text-white' : 'bg-blue-600 text-white'"
                    x-text="lastPunch?.type === 'check_in' ? 'ENTRY LOGGED' : 'EXIT LOGGED'"></span>
            </div>
            <p class="text-[11px] text-emerald-800 mt-0.5" x-text="lastPunch?.message"></p>
          </div>
        </div>
      </div>

      {{-- Shift Rules Guide --}}
      @if($isHoliday)
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-slate-100 text-xs">
          <div class="p-2.5 rounded-xl bg-indigo-50/70 border border-indigo-100">
            <span class="font-bold text-indigo-800 text-xs block">Sunday Shift</span>
            <span class="text-indigo-700 text-[11px] font-medium">Special Duty: 09:15 AM &ndash; 03:00 PM</span>
          </div>
          <div class="p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-100">
            <span class="font-bold text-emerald-800 text-xs block">Full Overtime</span>
            <span class="text-emerald-700 text-[11px] font-medium">Entry &le; 09:30 AM &amp; Exit &ge; 03:00 PM</span>
          </div>
          <div class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-100">
            <span class="font-bold text-amber-800 text-xs block">Half Overtime</span>
            <span class="text-amber-700 text-[11px] font-medium">Exit before 03:00 PM (min 3 hrs worked)</span>
          </div>
          <div class="p-2.5 rounded-xl bg-purple-50/70 border border-purple-100">
            <span class="font-bold text-purple-800 text-xs block">Extra Pay</span>
            <span class="text-purple-700 text-[11px] font-medium">Extra compensation for Sunday work</span>
          </div>
        </div>
      @else
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-slate-100 text-xs">
          <div class="p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-100">
            <span class="font-bold text-emerald-800 text-xs block">Present</span>
            <span class="text-emerald-700 text-[11px] font-medium">Entry before 08:45 AM &amp; Exit &ge; 04:30 PM</span>
          </div>
          <div class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-100">
            <span class="font-bold text-amber-800 text-xs block">Late Arrival</span>
            <span class="text-amber-700 text-[11px] font-medium">Entry 08:45 – 10:30 AM &amp; Exit &ge; 04:30 PM</span>
          </div>
          <div class="p-2.5 rounded-xl bg-orange-50/70 border border-orange-100">
            <span class="font-bold text-orange-800 text-xs block">Half Day</span>
            <span class="text-orange-700 text-[11px] font-medium">Entry after 10:30 AM or Exit before 04:30 PM</span>
          </div>
          <div class="p-2.5 rounded-xl bg-rose-50/70 border border-rose-100">
            <span class="font-bold text-rose-800 text-xs block">Absent</span>
            <span class="text-rose-700 text-[11px] font-medium">Entry after 12:30 PM or left in &lt; 1 hr</span>
          </div>
        </div>
      @endif
    </div>

    {{-- Column 2: Live Activity Feed --}}
    <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between space-y-3">
      <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
        <h3 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider">Today's Punch Feed</h3>
        <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-full font-mono">
          {{ $stats['checkedIn'] }} Punches
        </span>
      </div>

      <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1 flex-1">
        @forelse($recentTaps as $tap)
          <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100 text-xs">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[11px] shrink-0">
                @if($tap->employee?->photo)
                  <img src="{{ asset('storage/' . $tap->employee->photo) }}" class="w-full h-full object-cover rounded-lg" alt="">
                @else
                  {{ strtoupper(substr($tap->employee?->first_name ?? 'S', 0, 1)) }}
                @endif
              </div>
              <div>
                <p class="font-bold text-slate-900 text-xs leading-tight">{{ $tap->employee?->full_name }}</p>
                <p class="text-[10px] text-slate-400 font-mono">{{ $tap->employee?->department?->name ?? 'Staff' }} &bull; {{ $tap->employee?->employee_code ?? 'EMP-' . $tap->employee_id }}</p>
              </div>
            </div>
            <div class="text-right font-mono">
              @if($tap->check_out)
                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                  OUT {{ \Carbon\Carbon::parse($tap->check_out)->format('h:i A') }}
                </span>
              @else
                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                  IN {{ \Carbon\Carbon::parse($tap->check_in)->format('h:i A') }}
                </span>
              @endif
            </div>
          </div>
        @empty
          <div class="py-8 text-center text-slate-400">
            <p class="text-xs font-semibold">No card punches logged today.</p>
          </div>
        @endforelse
      </div>

      <div class="pt-2 border-t border-slate-100 text-[10px] text-slate-400 flex items-center justify-between">
        <span>Date: {{ today()->format('d M Y (D)') }}</span>
        <span>Auto-synced</span>
      </div>
    </div>

  </div>

  @if($isHoliday)
    {{-- Clean Minimalist Holiday Notice Banner --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center shrink-0">
          <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"/></svg>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h4 class="font-bold text-xs text-slate-900">{{ $holidayName }}</h4>
            <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">Holiday Off</span>
          </div>
          <p class="text-[11px] text-slate-500 mt-0.5">Staff tapping in today will be logged as overtime duty for extra compensation.</p>
        </div>
      </div>
      <div class="flex items-center gap-2 self-start sm:self-auto">
        <span class="text-xs font-mono font-bold text-slate-700 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl">
          {{ $stats['overtime'] }} Overtime Today
        </span>
      </div>
    </div>
  @endif

  {{-- ── Sleek KPI Summary Bar (Day/Month % + Counts) ────────────── --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
    <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-2xs text-center">
      <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Staff</span>
      <p class="text-2xl font-black font-mono text-slate-900 mt-1">{{ $stats['totalStaff'] }}</p>
    </div>

    <div class="bg-white p-3.5 rounded-2xl border border-emerald-200 shadow-2xs text-center">
      <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700">Checked In</span>
      <p class="text-2xl font-black font-mono text-emerald-600 mt-1">{{ $stats['checkedIn'] }}</p>
    </div>

    <div class="bg-white p-3.5 rounded-2xl border border-amber-200 shadow-2xs text-center">
      <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700">Late</span>
      <p class="text-2xl font-black font-mono text-amber-600 mt-1">{{ $stats['late'] }}</p>
    </div>

    <div class="bg-white p-3.5 rounded-2xl border border-orange-200 shadow-2xs text-center">
      <span class="text-[10px] font-extrabold uppercase tracking-wider text-orange-700">Half-Day</span>
      <p class="text-2xl font-black font-mono text-orange-600 mt-1">{{ $stats['halfDay'] }}</p>
    </div>

    <div class="bg-white p-3.5 rounded-2xl border border-yellow-200 bg-amber-50/30 shadow-2xs text-center">
      <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800">Overtime</span>
      <p class="text-2xl font-black font-mono text-amber-700 mt-1">{{ $stats['overtime'] }}</p>
    </div>

    @if($isHoliday)
      <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-2xs text-center">
        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Holiday Off</span>
        <p class="text-2xl font-black font-mono text-slate-600 mt-1">{{ $stats['holidayCount'] }}</p>
      </div>
    @else
      <div class="bg-white p-3.5 rounded-2xl border border-rose-200 shadow-2xs text-center">
        <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-700">Absent</span>
        <p class="text-2xl font-black font-mono text-rose-600 mt-1">{{ $stats['absent'] }}</p>
      </div>
    @endif

    <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-2xs text-center">
      <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Day Rate</span>
      <p class="text-2xl font-black font-mono {{ $stats['dayAttendanceRate'] >= 90 ? 'text-emerald-600' : ($stats['dayAttendanceRate'] >= 75 ? 'text-blue-600' : 'text-amber-600') }} mt-1">
        {{ $stats['dayAttendanceRate'] }}%
      </p>
    </div>

    <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-2xs text-center">
      <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Month Rate</span>
      <p class="text-2xl font-black font-mono {{ $monthStats['monthAvgRate'] >= 90 ? 'text-indigo-600' : ($monthStats['monthAvgRate'] >= 75 ? 'text-blue-600' : 'text-amber-600') }} mt-1">
        {{ $monthStats['monthAvgRate'] }}%
      </p>
    </div>
  </div>

  {{-- ── Staff Category Filter Tabs (Teaching, Non-Teaching, Drivers, Nannies, Cleaners) ── --}}
  <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
    @foreach($categories as $cat)
      @php $isActive = ($category === $cat['key'] || (!$category && $cat['key'] === 'all')); @endphp
      <a href="{{ route('attendance.staff', array_merge(request()->query(), ['category' => $cat['key']])) }}"
         class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2
         {{ $isActive
            ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200 ring-2 ring-indigo-500/20'
            : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
        <span>{{ $cat['label'] }}</span>
        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
          {{ $cat['count'] }}
        </span>
      </a>
    @endforeach
  </div>

  {{-- ── Filter & Search Toolbar ───────────────────────────────── --}}
  <div class="bg-white rounded-2xl border border-slate-200 p-3.5 shadow-2xs">
    <form method="GET" action="{{ route('attendance.staff') }}" class="flex flex-wrap items-center justify-between gap-3">
      <input type="hidden" name="category" value="{{ $category }}">

      <div class="flex flex-wrap items-center gap-3">
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Date</label>
          <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                 class="input input-sm border-slate-200 rounded-xl font-bold text-xs bg-slate-50 text-slate-800">
        </div>

        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Department</label>
          <select name="department_id" onchange="this.form.submit()"
                  class="select select-sm border-slate-200 rounded-xl font-semibold text-xs bg-slate-50 text-slate-800">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
              <option value="{{ $dept->id }}" @selected($deptId == $dept->id)>{{ $dept->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="flex items-center gap-3 w-full sm:w-auto">
        <div class="w-full sm:w-72 relative flex items-center">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 z-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          <input type="text" x-model="searchQuery" placeholder="Search staff, role, driver, code..."
                 style="padding-left: 2.5rem !important;"
                 class="input input-sm text-xs w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl focus:bg-white focus:border-indigo-500 transition-all">
        </div>
      </div>
    </form>
  </div>

  {{-- ── Main Attendance Table ───────────────────────────────────── --}}
  <form action="{{ route('attendance.staff.save') }}" method="POST">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="category" value="{{ $category }}">
    <input type="hidden" name="department_id" value="{{ $deptId }}">

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-black uppercase tracking-wider text-slate-400">
              <th class="py-3 px-4 w-[28%]">Staff Member & Role</th>
              <th class="py-3 px-4 w-[12%]">Department</th>
              <th class="py-3 px-4 text-center w-[16%]">Status</th>
              <th class="py-3 px-4 w-[12%]">In-Time</th>
              <th class="py-3 px-4 w-[12%]">Out-Time</th>
              <th class="py-3 px-4 text-center w-[8%]">Duration</th>
              <th class="py-3 px-4 w-[12%]">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs">
            @forelse($employees as $emp)
              @php
                $existing = $attendances[$emp->id] ?? null;
                $hasCheckIn = !empty($existing?->check_in);
                if ($isHoliday) {
                    $status = $hasCheckIn ? 'overtime' : 'holiday';
                } else {
                    $status = $existing?->status ?? ($hasCheckIn ? 'present' : 'absent');
                }
                $inTime = $existing?->check_in ? \Carbon\Carbon::parse($existing->check_in) : null;
                $outTime = $existing?->check_out ? \Carbon\Carbon::parse($existing->check_out) : null;
                $diffMins = ($inTime && $outTime) ? abs($outTime->diffInMinutes($inTime)) : 0;
                $hrs = floor($diffMins / 60);
                $mins = $diffMins % 60;
                $duration = ($inTime && $outTime) ? ($hrs > 0 ? "{$hrs}h {$mins}m" : "{$mins}m") : ($inTime ? 'In Progress' : '—');
              @endphp
              <tr class="hover:bg-slate-50/60 transition-colors" id="staff-row-{{ $emp->id }}"
                  x-show="!searchQuery || '{{ strtolower($emp->full_name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($emp->employee_code ?? '') }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($emp->department_name ?? '') }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($emp->category_label ?? '') }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($emp->designation_name ?? '') }}'.includes(searchQuery.toLowerCase())">

                {{-- Staff Member --}}
                <td class="py-3 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-200 overflow-hidden flex items-center justify-center font-bold text-indigo-700 shrink-0">
                      @if($emp->photo)
                        <img src="{{ asset('storage/' . $emp->photo) }}" class="w-full h-full object-cover" alt="">
                      @else
                        {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1)) }}
                      @endif
                    </div>
                    <div>
                      <div class="flex items-center gap-1.5 flex-wrap">
                        <a href="{{ route('hr.employees.show', $emp->id) }}" target="_blank" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                          {{ $emp->full_name }}
                        </a>
                        <span class="{{ $emp->category_badge_class }} text-[9px] px-1.5 py-0.2 font-bold uppercase tracking-wider rounded">
                          {{ $emp->category_label }}
                        </span>
                      </div>
                      <p class="text-[11px] text-slate-400 font-mono">{{ $emp->employee_code ?? 'EMP-' . $emp->id }} &bull; {{ $emp->designation_name }}</p>
                    </div>
                  </div>
                </td>

                {{-- Department --}}
                <td class="py-3 px-4 font-semibold text-slate-600 text-xs">
                  {{ $emp->department?->name ?? 'General' }}
                </td>

                {{-- Status Selector --}}
                <td class="py-3 px-4 text-center" id="status-cell-{{ $emp->id }}">
                  @if($isHoliday && empty($existing?->check_in))
                    <input type="hidden" name="attendance[{{ $emp->id }}][status]" value="holiday">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200/90 shadow-2xs whitespace-nowrap">
                      <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                      <span>Holiday</span>
                    </span>
                  @elseif($isHoliday && !empty($existing?->check_in))
                    <input type="hidden" name="attendance[{{ $emp->id }}][status]" value="overtime">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold bg-amber-100 text-amber-900 border border-amber-300 shadow-2xs whitespace-nowrap">
                      <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                      <span>Overtime / Extra Pay</span>
                    </span>
                  @else
                    <select name="attendance[{{ $emp->id }}][status]"
                            class="select select-xs text-xs font-bold rounded-lg border-slate-200 py-1 px-2 w-full
                            {{ $status === 'present' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : ($status === 'late' ? 'bg-amber-50 text-amber-800 border-amber-300' : ($status === 'half_day' ? 'bg-orange-50 text-orange-800 border-orange-300' : ($status === 'overtime' ? 'bg-yellow-100 text-amber-900 border-amber-400' : ($status === 'holiday' ? 'bg-slate-100 text-slate-700 border-slate-300' : 'bg-rose-50 text-rose-800 border-rose-300')))) }}">
                      <option value="present" @selected($status === 'present')>Present</option>
                      <option value="late" @selected($status === 'late')>Late</option>
                      <option value="half_day" @selected($status === 'half_day')>Half-Day</option>
                      <option value="overtime" @selected($status === 'overtime')>Overtime / Extra Pay</option>
                      <option value="absent" @selected($status === 'absent')>Absent</option>
                      <option value="leave" @selected($status === 'leave')>On Leave</option>
                      <option value="holiday" @selected($status === 'holiday')>Holiday</option>
                    </select>
                  @endif
                </td>

                {{-- In Time --}}
                <td class="py-3 px-4" id="intime-cell-{{ $emp->id }}">
                  @if($isHoliday && empty($existing?->check_in))
                    <span class="text-xs font-mono text-slate-400 bg-slate-50 px-2 py-1 rounded-lg border border-slate-200 block text-center cursor-not-allowed">—</span>
                  @else
                    <input type="time" name="attendance[{{ $emp->id }}][in_time]"
                           value="{{ $existing?->check_in ? substr($existing->check_in, 0, 5) : '' }}"
                           class="input text-xs py-1 px-2 w-28 bg-white border-slate-200 rounded-lg font-mono">
                  @endif
                </td>

                {{-- Out Time --}}
                <td class="py-3 px-4" id="outtime-cell-{{ $emp->id }}">
                  @if($isHoliday && empty($existing?->check_in))
                    <span class="text-xs font-mono text-slate-400 bg-slate-50 px-2 py-1 rounded-lg border border-slate-200 block text-center cursor-not-allowed">—</span>
                  @else
                    <input type="time" name="attendance[{{ $emp->id }}][out_time]"
                           value="{{ $existing?->check_out ? substr($existing->check_out, 0, 5) : '' }}"
                           class="input text-xs py-1 px-2 w-28 bg-white border-slate-200 rounded-lg font-mono">
                  @endif
                </td>

                {{-- Working Duration --}}
                <td class="py-3 px-4 text-center font-mono font-bold text-slate-700">
                  <span class="staff-duration-badge px-2 py-0.5 rounded-md text-[11px] {{ ($inTime && $outTime) ? 'bg-slate-100 text-slate-800' : 'text-slate-400' }}">
                    {{ $duration }}
                  </span>
                </td>

                {{-- Instant Card Tap Button --}}
                <td class="py-3 px-4">
                  <div class="flex items-center gap-2">
                    <button type="button" @click="submitTap('{{ $emp->id }}')"
                            class="staff-tap-btn btn btn-xs {{ empty($existing?->check_in) ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : (empty($existing?->check_out) ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'btn-secondary text-slate-700') }} font-bold text-[11px] px-2 py-0.5 rounded-md shadow-2xs cursor-pointer flex items-center gap-1">
                      @if(empty($existing?->check_in))
                        <span>Tap In</span>
                      @elseif(empty($existing?->check_out))
                        <span>Tap Out</span>
                      @else
                        <span>Update</span>
                      @endif
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="py-12 text-center text-slate-400">
                  No staff members found for the selected department.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Save & Bulk Actions Footer --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-4">
      <div class="flex items-center gap-2 flex-wrap">
        @if($isHoliday)
          <button type="button" onclick="document.querySelectorAll('select[name*=\'[status]\']').forEach(s=>s.value='overtime')"
                  class="btn btn-secondary btn-xs font-bold">
            Mark All Overtime
          </button>
          <button type="button" onclick="document.querySelectorAll('input[name*=\'[in_time]\']').forEach(i=>i.value='09:15'); document.querySelectorAll('input[name*=\'[out_time]\']').forEach(o=>o.value='15:00'); document.querySelectorAll('select[name*=\'[status]\']').forEach(s=>s.value='overtime');"
                  class="btn btn-secondary btn-xs font-bold">
            Auto-fill Sunday Shift (09:15 - 15:00)
          </button>
        @else
          <button type="button" onclick="document.querySelectorAll('select[name*=\'[status]\']').forEach(s=>s.value='present')"
                  class="btn btn-secondary btn-xs font-bold">
            Mark All Present
          </button>
          <button type="button" onclick="document.querySelectorAll('input[name*=\'[in_time]\']').forEach(i=>i.value='08:30'); document.querySelectorAll('input[name*=\'[out_time]\']').forEach(o=>o.value='16:30');"
                  class="btn btn-secondary btn-xs font-bold">
            Auto-fill Shift (08:30 - 16:30)
          </button>
        @endif
      </div>

      <div class="flex items-center gap-3">
        <a href="{{ route('attendance.index') }}" class="btn btn-secondary text-xs font-bold px-4 py-2">
          Cancel
        </a>
        <button type="submit" class="btn btn-primary text-xs font-bold px-6 py-2 shadow-sm cursor-pointer flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          Save Staff Attendance
        </button>
      </div>
    </div>
  </form>

</div>
@endsection

@push('scripts')
<script>
function staffAttendanceKiosk() {
  return {
    searchQuery: '',
    cardInput: '',
    isProcessing: false,
    lastPunch: null,
    punchError: null,
    liveTime: '',

    init() {
      this.updateClock();
      setInterval(() => this.updateClock(), 1000);
      this.$nextTick(() => {
        if (this.$refs.cardScanner) this.$refs.cardScanner.focus();
      });
    },
    updateClock() {
      const now = new Date();
      this.liveTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
    },
    async submitTap(code = null) {
      let input = code || this.cardInput;
      if (!input || !input.trim()) return;

      this.isProcessing = true;
      this.punchError = null;

      const now = new Date();
      const localHHMM = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

      try {
        let res = await fetch('{{ route('attendance.staff.tap') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            card_input: input.trim(),
            date: '{{ $date }}',
            punch_time: localHHMM
          })
        });

        let data = await res.json();
        if (res.ok && data.success) {
          this.lastPunch = data;
          this.cardInput = '';

          // Instant in-place row update without scrolling or reloading!
          const empId = data.employee?.id;
          if (empId) {
            const row = document.getElementById(`staff-row-${empId}`);
            if (row) {
              const inVal = data.check_in ? data.check_in.substring(0, 5) : '';
              const outVal = data.check_out ? data.check_out.substring(0, 5) : '';

              // Update In-Time Cell
              const inCell = document.getElementById(`intime-cell-${empId}`);
              if (inCell) {
                inCell.innerHTML = `<input type="time" name="attendance[${empId}][in_time]" value="${inVal}" class="input text-xs py-1 px-2 w-28 bg-white border-slate-200 rounded-lg font-mono">`;
              }

              // Update Out-Time Cell
              const outCell = document.getElementById(`outtime-cell-${empId}`);
              if (outCell) {
                outCell.innerHTML = `<input type="time" name="attendance[${empId}][out_time]" value="${outVal}" class="input text-xs py-1 px-2 w-28 bg-white border-slate-200 rounded-lg font-mono">`;
              }

              // Update Status Cell
              const statusCell = document.getElementById(`status-cell-${empId}`);
              if (statusCell) {
                if ({{ $isHoliday ? 'true' : 'false' }}) {
                  statusCell.innerHTML = `<input type="hidden" name="attendance[${empId}][status]" value="overtime">
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-amber-100 text-amber-900 border border-amber-300 shadow-2xs whitespace-nowrap">
                      <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                      <span>Overtime / Extra Pay</span>
                    </span>`;
                } else {
                  const statusSelect = statusCell.querySelector(`select[name="attendance[${empId}][status]"]`);
                  if (statusSelect && data.status) {
                    statusSelect.value = data.status;
                    statusSelect.className = `select select-xs text-xs font-bold rounded-lg border-slate-200 py-1 px-2 w-full ` +
                      (data.status === 'present' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' :
                      (data.status === 'late' ? 'bg-amber-50 text-amber-800 border-amber-300' :
                      (data.status === 'half_day' ? 'bg-orange-50 text-orange-800 border-orange-300' :
                      (data.status === 'overtime' ? 'bg-yellow-100 text-amber-900 border-amber-400' :
                      (data.status === 'holiday' ? 'bg-slate-100 text-slate-700 border-slate-300' :
                      'bg-rose-50 text-rose-800 border-rose-300')))));
                  }
                }
              }

              // Update Duration display
              const durEl = row.querySelector('.staff-duration-badge');
              if (durEl) {
                if (outVal || data.check_out) {
                  if (data.duration) {
                    durEl.textContent = data.duration;
                  } else {
                    const [inH, inM] = inVal.split(':').map(Number);
                    const [outH, outM] = outVal.split(':').map(Number);
                    const diff = Math.abs((outH * 60 + outM) - (inH * 60 + inM));
                    const h = Math.floor(diff / 60);
                    const m = diff % 60;
                    durEl.textContent = h > 0 ? `${h}h ${m}m` : `${m}m`;
                  }
                  durEl.className = 'staff-duration-badge px-2 py-0.5 rounded-md text-[11px] bg-slate-100 text-slate-800 font-bold';
                } else if (inVal || data.check_in) {
                  durEl.textContent = 'In Progress';
                  durEl.className = 'staff-duration-badge px-2 py-0.5 rounded-md text-[11px] bg-emerald-50 text-emerald-700 font-bold';
                }
              }

              // Update Tap Button state
              const btn = row.querySelector('.staff-tap-btn');
              if (btn) {
                if (outVal || data.check_out) {
                  btn.className = 'staff-tap-btn btn btn-xs btn-secondary text-slate-700 font-bold text-[11px] px-2 py-0.5 rounded-md shadow-2xs cursor-pointer flex items-center gap-1';
                  btn.innerHTML = '<span>Update</span>';
                } else {
                  btn.className = 'staff-tap-btn btn btn-xs bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] px-2 py-0.5 rounded-md shadow-2xs cursor-pointer flex items-center gap-1';
                  btn.innerHTML = '<span>Tap Out</span>';
                }
              }

              // Flash highlight animation on the modified row
              row.classList.add('bg-emerald-50/90', 'transition-all');
              setTimeout(() => {
                row.classList.remove('bg-emerald-50/90');
              }, 1800);
            }
          }
        } else {
          this.punchError = data.message || 'No staff member found for this card or ID.';
        }
      } catch (e) {
        this.punchError = 'Network error occurred while recording punch.';
      } finally {
        this.isProcessing = false;
        if (!code && this.$refs.cardScanner) {
          this.$refs.cardScanner.focus({ preventScroll: true });
        }
      }
    }
  };
}
</script>
@endpush
