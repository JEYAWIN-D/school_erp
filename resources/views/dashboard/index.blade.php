@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

{{-- ═══════════════════════════════════════════════════════════════
     MANAGEMENT / ADMIN DASHBOARD
══════════════════════════════════════════════════════════════════ --}}
@if(($dashboardType ?? 'management') === 'management')

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Executive Dashboard</h1>
      <p class="page-subtitle">Welcome back, {{ auth()->user()->name }} &mdash; {{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <span class="badge-blue text-sm px-3 py-1">{{ $stats['academic_year'] }}</span>
      <a href="{{ route('reports.general-register') }}" class="btn-sm btn-secondary">General Register</a>
      <a href="{{ route('reports.attendance-register') }}" class="btn-sm btn-secondary">Attendance Register</a>
      <a href="{{ route('reports.fee-collection-register') }}" class="btn-sm btn-secondary">Fee Register</a>
    </div>
  </div>

  {{-- KPI Cards --}}
  <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
    <div class="card group">
      <div class="flex items-start justify-between">
        <div class="stat-icon bg-gradient-to-br from-blue-500 to-indigo-600">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <span class="badge-green text-xs">Active</span>
      </div>
      <div class="mt-3">
        <p class="stat-number" x-data="{ val: 0 }" x-init="$nextTick(() => { let t = setInterval(() => { val < {{ $stats['total_students'] }} ? val++ : clearInterval(t) }, 20) })" x-text="val">0</p>
        <p class="text-xs text-slate-500 mt-1">Total Students</p>
        @if($todayAttendance !== null)
          <span class="inline-flex items-center gap-1 mt-1.5 text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100" title="{{ $studentAttendanceStats->present ?? 0 }} present of {{ $studentAttendanceStats->total ?? 0 }} marked">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            {{ $todayAttendance }}% Attended Today
          </span>
        @endif
      </div>
      <a href="{{ route('students.index') }}" class="mt-2 text-xs text-blue-600 font-medium hover:text-blue-700">View all →</a>
    </div>
    <div class="card">
      <div class="flex items-start justify-between">
        <div class="stat-icon bg-gradient-to-br from-emerald-500 to-teal-600">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <span class="badge-green text-xs">Active</span>
      </div>
      <div class="mt-3">
        <p class="stat-number">{{ $stats['total_staff'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Staff</p>
      </div>
      <a href="{{ route('hr.employees') }}" class="mt-2 text-xs text-emerald-600 font-medium hover:text-emerald-700">View all →</a>
    </div>
    <div class="card">
      <div class="flex items-start justify-between">
        <div class="stat-icon bg-gradient-to-br from-violet-500 to-purple-600">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <span class="badge-blue text-xs">Month</span>
      </div>
      <div class="mt-3">
        <p class="stat-number">{{ $stats['new_admissions_month'] }}</p>
        <p class="text-xs text-slate-500 mt-1">New Admissions</p>
      </div>
      <a href="{{ route('admissions.index') }}" class="mt-2 text-xs text-violet-600 font-medium hover:text-violet-700">View all →</a>
    </div>
    <div class="card">
      <div class="flex items-start justify-between">
        <div class="stat-icon bg-gradient-to-br from-sky-500 to-blue-600">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <span class="badge-green text-xs">Today</span>
      </div>
      <div class="mt-3">
        <p class="stat-number">{{ $staffPresent }}</p>
        <p class="text-xs text-slate-500 mt-1">Staff Present</p>
      </div>
      <a href="{{ route('hr.index') }}" class="mt-2 text-xs text-sky-600 font-medium hover:text-sky-700">Details →</a>
    </div>
    <div class="card">
      <div class="flex items-start justify-between">
        <div class="stat-icon bg-gradient-to-br from-orange-400 to-amber-500">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <span class="badge-amber text-xs">Today</span>
      </div>
      <div class="mt-3">
        <p class="stat-number text-amber-600">₹{{ number_format($todayCollection) }}</p>
        <p class="text-xs text-slate-500 mt-1">Fee Collected</p>
      </div>
      <a href="{{ route('reports.fee') }}" class="mt-2 text-xs text-orange-600 font-medium hover:text-orange-700">View history →</a>
    </div>
    <div class="card">
      <div class="flex items-start justify-between">
        <div class="stat-icon bg-gradient-to-br from-rose-500 to-red-600">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <span class="badge-red text-xs">Due</span>
      </div>
      <div class="mt-3">
        <p class="stat-number text-rose-600">₹{{ number_format($outstanding) }}</p>
        <p class="text-xs text-slate-500 mt-1">Outstanding</p>
      </div>
      <a href="{{ route('reports.fee-collection-register') }}" class="mt-2 text-xs text-rose-600 font-medium hover:text-rose-700">Report →</a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="card lg:col-span-2">
      <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
        <div>
          <h3 class="font-semibold text-slate-800">Today's Attendance &amp; Class Strength</h3>
          @if($todayAttendance !== null && isset($studentAttendanceStats) && ($studentAttendanceStats->total ?? 0) > 0)
            <p class="text-xs text-slate-500 mt-0.5">
              <span class="font-bold text-emerald-600">{{ $studentAttendanceStats->present }} Present</span> &bull; 
              <span class="font-bold text-rose-500">{{ $studentAttendanceStats->absent }} Absent</span>
              @if(($studentAttendanceStats->late ?? 0) > 0)
                &bull; <span class="font-bold text-amber-500">{{ $studentAttendanceStats->late }} Late</span>
              @endif
              @if(($studentAttendanceStats->leave_count ?? 0) > 0)
                &bull; <span class="font-bold text-blue-500">{{ $studentAttendanceStats->leave_count }} Leave</span>
              @endif
              <span class="text-slate-400">({{ $studentAttendanceStats->total }} marked)</span>
            </p>
          @endif
        </div>
        @if($todayAttendance !== null)
          <div class="flex items-center gap-2">
            <span class="badge-green font-bold text-xs px-2.5 py-1">{{ $todayAttendance }}% present today</span>
            <a href="{{ route('attendance.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">View All &rarr;</a>
          </div>
        @else
          <span class="badge-slate">No attendance yet today</span>
        @endif
      </div>
      @if($classStrength->isNotEmpty())
        @php $maxCount = $classStrength->max('student_count') ?: 1; @endphp
        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
          @foreach($classStrength as $row)
          @php
            $classAtt = isset($todayClassAttendance) ? ($todayClassAttendance[$row->class_id] ?? null) : null;
          @endphp
          <div class="flex items-center gap-2 py-1">
            <div class="w-24 text-xs text-slate-600 truncate flex-shrink-0 font-medium">{{ $row->class_name }}</div>
            <div class="flex-1 bg-slate-100 rounded-full h-2.5 overflow-hidden">
              @if($classAtt && $classAtt->total > 0)
                @php $classPct = round(($classAtt->present / $classAtt->total) * 100); @endphp
                <div class="bg-emerald-500 h-2.5 rounded-full transition-all" style="width:{{ $classPct }}%" title="Today: {{ $classAtt->present }}/{{ $classAtt->total }} present ({{ $classPct }}%)"></div>
              @else
                <div class="bg-indigo-500 h-2.5 rounded-full transition-all" style="width:{{ round($row->student_count/$maxCount*100) }}%"></div>
              @endif
            </div>
            <div class="w-32 text-xs text-right text-slate-600 font-medium flex-shrink-0 flex items-center justify-end gap-1.5">
              <span>{{ $row->student_count }}</span>
              @if($classAtt && $classAtt->total > 0)
                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                  {{ round(($classAtt->present / $classAtt->total) * 100) }}% today
                </span>
              @else
                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-slate-100 text-slate-400">
                  Not marked
                </span>
              @endif
            </div>
          </div>
          @endforeach
        </div>
      @else
        <p class="text-slate-400 text-sm">No class data available.</p>
      @endif
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-800 mb-4">Quick Actions</h3>
      <div class="space-y-2">
        <a href="{{ route('admissions.create') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition group">
          <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          </div>
          <div><p class="text-sm font-medium text-slate-700">New Admission</p><p class="text-xs text-slate-400">Add new enquiry</p></div>
        </a>
        <a href="{{ route('attendance.mark') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-green-50 transition group">
          <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
          </div>
          <div><p class="text-sm font-medium text-slate-700">Mark Attendance</p><p class="text-xs text-slate-400">Today's attendance</p></div>
        </a>
        <a href="{{ route('fees.collect') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-orange-50 transition group">
          <div class="w-9 h-9 rounded-lg bg-orange-100 flex items-center justify-center group-hover:bg-orange-200 transition">
            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
          <div><p class="text-sm font-medium text-slate-700">Collect Fee</p><p class="text-xs text-slate-400">Record payment</p></div>
        </a>
        <a href="{{ route('library.issue') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-purple-50 transition group">
          <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          </div>
          <div><p class="text-sm font-medium text-slate-700">Issue Book</p><p class="text-xs text-slate-400">Library issue</p></div>
        </a>
      </div>
    </div>
  </div>

  @if($collectionTrend->isNotEmpty())
  <div class="card">
    <h3 class="font-semibold text-slate-800 mb-4">Fee Collection Trend (Last 6 Months)</h3>
    @php $maxVal = $collectionTrend->max('total') ?: 1; @endphp
    <div class="flex items-end gap-3 h-36">
      @foreach($collectionTrend as $row)
      @php $pct = round($row->total / $maxVal * 100); @endphp
      <div class="flex-1 flex flex-col items-center gap-1">
        <span class="text-xs text-slate-500">₹{{ number_format($row->total/1000,1) }}k</span>
        <div class="w-full bg-indigo-500 rounded-t transition-all" style="height:{{ max($pct,4) }}%"></div>
        <span class="text-xs text-slate-400">{{ \Carbon\Carbon::createFromFormat('Y-m',$row->month)->format('M y') }}</span>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="card">
      <h2 class="font-semibold text-slate-700 mb-3 flex items-center justify-between">
        <span>📢 Notices</span>
        <a href="{{ route('academics.notices') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
      </h2>
      @forelse($recentNotices as $n)
      <div class="py-2 border-b border-slate-100 last:border-0">
        <p class="text-sm font-medium text-slate-700 leading-tight line-clamp-2">{{ $n->title }}</p>
        <p class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($n->publish_date)->format('d M') }}</p>
      </div>
      @empty<p class="text-slate-400 text-sm">No recent notices.</p>@endforelse
    </div>
    <div class="card">
      <h2 class="font-semibold text-slate-700 mb-3 flex items-center justify-between">
        <span>🎉 Events</span>
        <a href="{{ route('events.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
      </h2>
      @forelse($upcomingEvents as $e)
      <div class="py-2 border-b border-slate-100 last:border-0">
        <p class="text-sm font-medium text-slate-700 leading-tight">{{ $e->name }}</p>
        <p class="text-xs text-slate-400 mt-0.5">{{ $e->event_date->format('d M Y') }} &middot; <span class="text-indigo-500">{{ $e->event_date->diffForHumans() }}</span></p>
      </div>
      @empty<p class="text-slate-400 text-sm">No upcoming events.</p>@endforelse
    </div>
    <div class="card">
      <h2 class="font-semibold text-slate-700 mb-3">🎂 Birthdays Today</h2>
      @forelse($todayBirthdays as $s)
      <div class="py-2 border-b border-slate-100 last:border-0 flex items-center gap-2">
        <div class="w-7 h-7 rounded-full bg-pink-100 flex items-center justify-center text-xs font-bold text-pink-600">{{ strtoupper(substr($s->first_name,0,1)) }}</div>
        <div>
          <p class="text-sm font-medium text-slate-700">{{ $s->first_name }} {{ $s->last_name }}</p>
          <p class="text-xs text-slate-400">Age {{ now()->diffInYears(\Carbon\Carbon::parse($s->dob)) }}</p>
        </div>
      </div>
      @empty<p class="text-slate-400 text-sm">No birthdays today.</p>@endforelse
    </div>
  </div>

  <div>
    <h2 class="text-lg font-bold text-slate-800 mb-4" style="font-family:'Plus Jakarta Sans',sans-serif;">All Modules</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
      @php
        $modules = [
          ['label' => 'Admissions',    'route' => 'admissions.index',  'color' => 'from-blue-500 to-indigo-600',    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
          ['label' => 'Students',      'route' => 'students.index',    'color' => 'from-sky-500 to-blue-600',       'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
          ['label' => 'Academics',     'route' => 'academics.index',   'color' => 'from-violet-500 to-purple-600', 'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055'],
          ['label' => 'Attendance',    'route' => 'attendance.index',  'color' => 'from-green-500 to-emerald-600', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
          ['label' => 'Examinations',  'route' => 'examinations.index','color' => 'from-amber-500 to-orange-600',  'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
          ['label' => 'Fees',          'route' => 'fees.index',        'color' => 'from-orange-500 to-red-500',    'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
          ['label' => 'HR & Payroll',  'route' => 'hr.index',          'color' => 'from-teal-500 to-cyan-600',     'icon' => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2'],
          ['label' => 'Library',       'route' => 'library.index',     'color' => 'from-pink-500 to-rose-600',     'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5'],
          ['label' => 'Transport',     'route' => 'transport.index',   'color' => 'from-indigo-500 to-blue-700',   'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z'],
          ['label' => 'Hostel',        'route' => 'hostel.index',      'color' => 'from-slate-600 to-slate-800',   'icon' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75'],
          ['label' => 'Inventory',     'route' => 'inventory.index',   'color' => 'from-orange-500 to-amber-600',  'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'],
          ['label' => 'Events',        'route' => 'events.index',      'color' => 'from-pink-500 to-rose-500',     'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
          ['label' => 'Gate/Visitors', 'route' => 'gate.index',        'color' => 'from-lime-500 to-green-600',    'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
          ['label' => 'Alumni',        'route' => 'alumni.index',      'color' => 'from-amber-500 to-yellow-600',  'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
          ['label' => 'Audit/Security','route' => 'system.audit-log',  'color' => 'from-gray-600 to-slate-700',    'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        ];
      @endphp
      @foreach($modules as $mod)
        <a href="{{ route($mod['route']) }}"
           class="card-flat flex flex-col items-center justify-center gap-3 py-5 text-center hover:shadow-card-md transition-all duration-200 hover:-translate-y-0.5 group">
          <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $mod['color'] }} flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-200">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $mod['icon'] }}"/>
            </svg>
          </div>
          <span class="text-xs font-semibold text-slate-600">{{ $mod['label'] }}</span>
        </a>
      @endforeach
    </div>
  </div>

{{-- ═══════════════════════════════════════════════════════════════
     TEACHER DASHBOARD
══════════════════════════════════════════════════════════════════ --}}
@elseif($dashboardType === 'teaching')

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">My Dashboard</h1>
      <p class="page-subtitle">Welcome back, {{ auth()->user()->name }} &mdash; {{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <span class="badge-blue text-sm px-3 py-1">{{ $currentYear?->name ?? '—' }}</span>
      <a href="{{ route('attendance.mark') }}" class="btn-sm btn-primary">Mark Attendance</a>
    </div>
  </div>

  {{-- Quick stats --}}
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-blue-500 to-indigo-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
      </div>
      <p class="stat-number">{{ $myClasses->pluck('class_name')->unique()->count() }}</p>
      <p class="text-xs text-slate-500 mt-1">My Classes</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>
      <p class="stat-number">{{ $myScheduleToday->count() }}</p>
      <p class="text-xs text-slate-500 mt-1">Classes Today</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
      </div>
      <p class="stat-number {{ $myAttendancePending > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ $myAttendancePending }}</p>
      <p class="text-xs text-slate-500 mt-1">Attendance Pending</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-violet-500 to-purple-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/></svg>
      </div>
      <p class="stat-number">{{ $pendingHomework }}</p>
      <p class="text-xs text-slate-500 mt-1">Active Homework</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Today's schedule --}}
    <div class="card lg:col-span-2">
      <h3 class="font-semibold text-slate-800 mb-4">Today's Schedule — {{ now()->format('l') }}</h3>
      @if($myScheduleToday->isEmpty())
        <div class="text-center py-10 text-slate-400">
          <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <p class="text-sm">No classes scheduled for today</p>
        </div>
      @else
        <div class="space-y-2">
          @foreach($myScheduleToday as $period)
          <div class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 border border-blue-100">
            <div class="flex-shrink-0 text-center w-20">
              <p class="text-xs font-bold text-blue-700">{{ \Carbon\Carbon::parse($period->start_time)->format('h:i A') }}</p>
              <p class="text-xs text-blue-400">{{ \Carbon\Carbon::parse($period->end_time)->format('h:i A') }}</p>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-semibold text-slate-800 text-sm">{{ $period->subject_name ?? 'Period' }}</p>
              <p class="text-xs text-slate-500">{{ $period->class_name }}</p>
            </div>
          </div>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Quick actions + notices --}}
    <div class="space-y-4">
      <div class="card">
        <h3 class="font-semibold text-slate-800 mb-3">Quick Actions</h3>
        <div class="space-y-2">
          <a href="{{ route('attendance.mark') }}" class="flex items-center gap-2.5 p-2.5 rounded-lg hover:bg-green-50 transition group text-sm">
            <div class="w-7 h-7 rounded-lg bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition flex-shrink-0">
              <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
            </div>
            <span class="font-medium text-slate-700">Mark Attendance</span>
          </a>
          <a href="{{ route('examinations.index') }}" class="flex items-center gap-2.5 p-2.5 rounded-lg hover:bg-amber-50 transition group text-sm">
            <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center group-hover:bg-amber-200 transition flex-shrink-0">
              <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <span class="font-medium text-slate-700">Examinations</span>
          </a>
          <a href="{{ route('lms.index') }}" class="flex items-center gap-2.5 p-2.5 rounded-lg hover:bg-purple-50 transition group text-sm">
            <div class="w-7 h-7 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition flex-shrink-0">
              <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/></svg>
            </div>
            <span class="font-medium text-slate-700">LMS / Lessons</span>
          </a>
        </div>
      </div>
      @if($recentNotices->isNotEmpty())
      <div class="card">
        <h3 class="font-semibold text-slate-800 mb-3">📢 Notices</h3>
        @foreach($recentNotices as $n)
        <div class="py-2 border-b border-slate-100 last:border-0">
          <p class="text-sm text-slate-700 line-clamp-2">{{ $n->title }}</p>
          <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($n->publish_date)->format('d M') }}</p>
        </div>
        @endforeach
      </div>
      @endif
    </div>
  </div>

  {{-- Upcoming exams --}}
  @if($upcomingExams->isNotEmpty())
  <div class="card">
    <h3 class="font-semibold text-slate-800 mb-4">Upcoming Examinations</h3>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="border-b border-slate-100">
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Exam</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Class</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">In</th>
        </tr></thead>
        <tbody>
          @foreach($upcomingExams as $ex)
          <tr class="border-b border-slate-50">
            <td class="py-2.5 font-medium text-slate-700">{{ $ex->title }}</td>
            <td class="py-2.5 text-slate-500">{{ $ex->class_name }}</td>
            <td class="py-2.5 text-slate-500">{{ $ex->exam_date->format('d M Y') }}</td>
            <td class="py-2.5"><span class="badge-blue text-xs">{{ $ex->exam_date->diffForHumans() }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

{{-- ═══════════════════════════════════════════════════════════════
     FINANCE / ACCOUNTANT DASHBOARD
══════════════════════════════════════════════════════════════════ --}}
@elseif($dashboardType === 'finance')

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Finance Dashboard</h1>
      <p class="page-subtitle">Fee collection & outstanding overview &mdash; {{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <span class="badge-blue text-sm px-3 py-1">{{ $stats['academic_year'] }}</span>
      <a href="{{ route('fees.collect') }}" class="btn-sm btn-primary">Collect Fee</a>
      <a href="{{ route('reports.fee-collection-register') }}" class="btn-sm btn-secondary">Fee Register</a>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <p class="stat-number text-green-600">₹{{ number_format($todayCollection) }}</p>
      <p class="text-xs text-slate-500 mt-1">Collected Today</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-blue-500 to-indigo-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
      </div>
      <p class="stat-number text-blue-600">₹{{ number_format($monthCollection) }}</p>
      <p class="text-xs text-slate-500 mt-1">This Month</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-rose-500 to-red-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      </div>
      <p class="stat-number text-rose-600">₹{{ number_format($outstanding) }}</p>
      <p class="text-xs text-slate-500 mt-1">Outstanding</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Payment mode breakdown --}}
    <div class="card">
      <h3 class="font-semibold text-slate-800 mb-4">Payment Modes — This Month</h3>
      @if($paymentModes->isNotEmpty())
        @php $modeTotal = $paymentModes->sum('total') ?: 1; @endphp
        <div class="space-y-3">
          @foreach($paymentModes as $mode)
          @php $pct = round($mode->total / $modeTotal * 100); @endphp
          <div>
            <div class="flex justify-between text-xs text-slate-600 mb-1">
              <span class="capitalize font-medium">{{ str_replace('_',' ',$mode->payment_mode ?? 'Other') }}</span>
              <span>₹{{ number_format($mode->total) }} ({{ $pct }}%)</span>
            </div>
            <div class="bg-slate-100 rounded-full h-2">
              <div class="bg-indigo-500 h-2 rounded-full" style="width:{{ $pct }}%"></div>
            </div>
          </div>
          @endforeach
        </div>
      @else
        <p class="text-slate-400 text-sm">No payments this month yet.</p>
      @endif
    </div>

    {{-- Recent payments --}}
    <div class="card">
      <h3 class="font-semibold text-slate-800 mb-4">Recent Payments</h3>
      <div class="space-y-2 max-h-72 overflow-y-auto">
        @forelse($recentPayments as $p)
        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
          <div class="min-w-0">
            <p class="text-sm font-medium text-slate-700 truncate">{{ $p->first_name }} {{ $p->last_name }}</p>
            <p class="text-xs text-slate-400">#{{ $p->receipt_number }} &middot; {{ \Carbon\Carbon::parse($p->payment_date)->format('d M') }} &middot; {{ ucfirst($p->payment_mode) }}</p>
          </div>
          <span class="text-sm font-bold text-green-600 ml-2 flex-shrink-0">₹{{ number_format($p->amount) }}</span>
        </div>
        @empty
        <p class="text-slate-400 text-sm">No recent payments.</p>
        @endforelse
      </div>
    </div>
  </div>

  @if($collectionTrend->isNotEmpty())
  <div class="card">
    <h3 class="font-semibold text-slate-800 mb-4">Collection Trend (Last 6 Months)</h3>
    @php $maxVal = $collectionTrend->max('total') ?: 1; @endphp
    <div class="flex items-end gap-3 h-36">
      @foreach($collectionTrend as $row)
      @php $pct = round($row->total / $maxVal * 100); @endphp
      <div class="flex-1 flex flex-col items-center gap-1">
        <span class="text-xs text-slate-500">₹{{ number_format($row->total/1000,1) }}k</span>
        <div class="w-full bg-emerald-500 rounded-t" style="height:{{ max($pct,4) }}%"></div>
        <span class="text-xs text-slate-400">{{ \Carbon\Carbon::createFromFormat('Y-m',$row->month)->format('M y') }}</span>
      </div>
      @endforeach
    </div>
  </div>
  @endif

{{-- ═══════════════════════════════════════════════════════════════
     HR DASHBOARD
══════════════════════════════════════════════════════════════════ --}}
@elseif($dashboardType === 'hr')

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">HR Dashboard</h1>
      <p class="page-subtitle">Staff management overview &mdash; {{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('hr.employees') }}" class="btn-sm btn-primary">All Employees</a>
      <a href="{{ route('hr.leave') }}" class="btn-sm btn-secondary">Leave Requests</a>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-teal-500 to-cyan-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <p class="stat-number">{{ $totalStaff }}</p>
      <p class="text-xs text-slate-500 mt-1">Total Staff</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <p class="stat-number text-green-600">{{ $staffPresent }}</p>
      <p class="text-xs text-slate-500 mt-1">Present Today</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      </div>
      <p class="stat-number {{ $pendingLeaves > 0 ? 'text-amber-600' : '' }}">{{ $pendingLeaves }}</p>
      <p class="text-xs text-slate-500 mt-1">Pending Leave Requests</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
      <h3 class="font-semibold text-slate-800 mb-4">Department Strength</h3>
      @if($departmentStrength->isNotEmpty())
        @php $maxDept = $departmentStrength->max('count') ?: 1; @endphp
        <div class="space-y-2">
          @foreach($departmentStrength as $dept)
          <div class="flex items-center gap-2">
            <div class="w-28 text-xs text-slate-600 truncate flex-shrink-0">{{ $dept->department ?? 'Unassigned' }}</div>
            <div class="flex-1 bg-slate-100 rounded-full h-2.5">
              <div class="bg-teal-500 h-2.5 rounded-full" style="width:{{ round($dept->count/$maxDept*100) }}%"></div>
            </div>
            <div class="w-6 text-xs text-right text-slate-600 font-medium flex-shrink-0">{{ $dept->count }}</div>
          </div>
          @endforeach
        </div>
      @else
        <p class="text-slate-400 text-sm">No department data.</p>
      @endif
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-800 mb-4">Recent Joinings</h3>
      @forelse($recentJoinings as $emp)
      <div class="flex items-center gap-3 py-2 border-b border-slate-100 last:border-0">
        <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-xs font-bold text-teal-600 flex-shrink-0">{{ strtoupper(substr($emp->name,0,1)) }}</div>
        <div class="min-w-0 flex-1">
          <p class="text-sm font-medium text-slate-700 truncate">{{ $emp->name }}</p>
          <p class="text-xs text-slate-400">{{ $emp->designation ?? '—' }} &middot; {{ \Carbon\Carbon::parse($emp->joining_date)->format('d M Y') }}</p>
        </div>
      </div>
      @empty
      <p class="text-slate-400 text-sm">No recent joinings.</p>
      @endforelse
    </div>
  </div>

{{-- ═══════════════════════════════════════════════════════════════
     LIBRARY DASHBOARD
══════════════════════════════════════════════════════════════════ --}}
@elseif($dashboardType === 'library')

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Library Dashboard</h1>
      <p class="page-subtitle">Books & issue management &mdash; {{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('library.issue') }}" class="btn-sm btn-primary">Issue Book</a>
      <a href="{{ route('library.index') }}" class="btn-sm btn-secondary">All Books</a>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-pink-500 to-rose-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/></svg>
      </div>
      <p class="stat-number">{{ $totalBooks }}</p>
      <p class="text-xs text-slate-500 mt-1">Total Books</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-blue-500 to-indigo-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
      </div>
      <p class="stat-number">{{ $issuedBooks }}</p>
      <p class="text-xs text-slate-500 mt-1">Currently Issued</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-rose-500 to-red-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <p class="stat-number text-rose-600">{{ $overdueBooks }}</p>
      <p class="text-xs text-slate-500 mt-1">Overdue Returns</p>
    </div>
  </div>

  <div class="card">
    <h3 class="font-semibold text-slate-800 mb-4">Recent Issues</h3>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="border-b border-slate-100">
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Book</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Student</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Issued</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Due</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
        </tr></thead>
        <tbody>
          @forelse($recentIssues as $issue)
          <tr class="border-b border-slate-50">
            <td class="py-2.5 font-medium text-slate-700 max-w-xs truncate">{{ $issue->title }}</td>
            <td class="py-2.5 text-slate-500">{{ $issue->first_name ?? '—' }} {{ $issue->last_name ?? '' }}</td>
            <td class="py-2.5 text-slate-500">{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M') }}</td>
            <td class="py-2.5 text-slate-500">{{ \Carbon\Carbon::parse($issue->due_date)->format('d M') }}</td>
            <td class="py-2.5">
              @if($issue->return_date)
                <span class="badge-green text-xs">Returned</span>
              @elseif(\Carbon\Carbon::parse($issue->due_date)->isPast())
                <span class="badge-red text-xs">Overdue</span>
              @else
                <span class="badge-blue text-xs">Issued</span>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="py-6 text-center text-slate-400 text-sm">No recent issues.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

{{-- ═══════════════════════════════════════════════════════════════
     TRANSPORT DASHBOARD
══════════════════════════════════════════════════════════════════ --}}
@elseif($dashboardType === 'transport')

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Transport Dashboard</h1>
      <p class="page-subtitle">Vehicles & routes overview &mdash; {{ now()->format('l, d M Y') }}</p>
    </div>
    <a href="{{ route('transport.index') }}" class="btn-sm btn-secondary">Manage Transport</a>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-indigo-500 to-blue-700 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
      </div>
      <p class="stat-number">{{ $totalVehicles }}</p>
      <p class="text-xs text-slate-500 mt-1">Active Vehicles</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4"/></svg>
      </div>
      <p class="stat-number">{{ $activeRoutes }}</p>
      <p class="text-xs text-slate-500 mt-1">Active Routes</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-sky-500 to-blue-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1"/></svg>
      </div>
      <p class="stat-number">{{ $studentsOnBus }}</p>
      <p class="text-xs text-slate-500 mt-1">Students Using Bus</p>
    </div>
  </div>

  @if($recentFuelLogs->isNotEmpty())
  <div class="card">
    <h3 class="font-semibold text-slate-800 mb-4">Recent Fuel Logs</h3>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="border-b border-slate-100">
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Vehicle</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Litres</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Amount</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Odometer</th>
        </tr></thead>
        <tbody>
          @foreach($recentFuelLogs as $log)
          <tr class="border-b border-slate-50">
            <td class="py-2.5 font-medium text-slate-700">{{ $log->registration_number }}</td>
            <td class="py-2.5 text-slate-500">{{ \Carbon\Carbon::parse($log->date)->format('d M Y') }}</td>
            <td class="py-2.5 text-slate-600">{{ $log->litres }}L</td>
            <td class="py-2.5 text-slate-600">₹{{ number_format($log->amount) }}</td>
            <td class="py-2.5 text-slate-500">{{ number_format($log->odometer) }} km</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

{{-- ═══════════════════════════════════════════════════════════════
     HOSTEL DASHBOARD
══════════════════════════════════════════════════════════════════ --}}
@elseif($dashboardType === 'hostel')

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Hostel Dashboard</h1>
      <p class="page-subtitle">Room occupancy & outpass overview &mdash; {{ now()->format('l, d M Y') }}</p>
    </div>
    <a href="{{ route('hostel.index') }}" class="btn-sm btn-secondary">Manage Hostel</a>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="card text-center">
      <p class="stat-number">{{ $totalRooms }}</p>
      <p class="text-xs text-slate-500 mt-1">Total Rooms</p>
    </div>
    <div class="card text-center">
      <p class="stat-number text-green-600">{{ $occupiedRooms }}</p>
      <p class="text-xs text-slate-500 mt-1">Occupied</p>
    </div>
    <div class="card text-center">
      <p class="stat-number text-blue-600">{{ $totalResidents }}</p>
      <p class="text-xs text-slate-500 mt-1">Residents</p>
    </div>
    <div class="card text-center">
      <p class="stat-number {{ $pendingOutpass > 0 ? 'text-amber-600' : '' }}">{{ $pendingOutpass }}</p>
      <p class="text-xs text-slate-500 mt-1">Pending Outpass</p>
    </div>
  </div>

  @if($recentOutpass->isNotEmpty())
  <div class="card">
    <h3 class="font-semibold text-slate-800 mb-4">Recent Outpass Requests</h3>
    <div class="space-y-2">
      @foreach($recentOutpass as $op)
      <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
        <div class="min-w-0">
          <p class="text-sm font-medium text-slate-700">{{ $op->first_name }} {{ $op->last_name }}</p>
          <p class="text-xs text-slate-400">{{ $op->reason }} &middot; {{ \Carbon\Carbon::parse($op->from_date)->format('d M') }} – {{ \Carbon\Carbon::parse($op->to_date)->format('d M Y') }}</p>
        </div>
        <span class="ml-2 flex-shrink-0 text-xs px-2 py-0.5 rounded-full font-medium
          {{ $op->status === 'approved' ? 'bg-green-100 text-green-700' : ($op->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
          {{ ucfirst($op->status) }}
        </span>
      </div>
      @endforeach
    </div>
  </div>
  @endif

{{-- ═══════════════════════════════════════════════════════════════
     ADMISSIONS DASHBOARD
══════════════════════════════════════════════════════════════════ --}}
@elseif($dashboardType === 'admissions')

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Admissions Dashboard</h1>
      <p class="page-subtitle">Enquiries & applications &mdash; {{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admissions.create') }}" class="btn-sm btn-primary">New Enquiry</a>
      <a href="{{ route('admissions.index') }}" class="btn-sm btn-secondary">All Enquiries</a>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-blue-500 to-indigo-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      </div>
      <p class="stat-number">{{ $totalEnquiries }}</p>
      <p class="text-xs text-slate-500 mt-1">Total Enquiries</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <p class="stat-number text-amber-600">{{ $pendingFollowups }}</p>
      <p class="text-xs text-slate-500 mt-1">Pending Followups</p>
    </div>
    <div class="card text-center">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mx-auto mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1"/></svg>
      </div>
      <p class="stat-number text-green-600">{{ $newAdmissionsMonth }}</p>
      <p class="text-xs text-slate-500 mt-1">Admitted This Month</p>
    </div>
  </div>

  <div class="card">
    <h3 class="font-semibold text-slate-800 mb-4">Recent Enquiries</h3>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="border-b border-slate-100">
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Student</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Class</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Phone</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
          <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
        </tr></thead>
        <tbody>
          @forelse($recentEnquiries as $enq)
          <tr class="border-b border-slate-50">
            <td class="py-2.5 font-medium text-slate-700">{{ $enq->student_name }}</td>
            <td class="py-2.5 text-slate-500">{{ $enq->class_applied ?? '—' }}</td>
            <td class="py-2.5 text-slate-500">{{ $enq->phone }}</td>
            <td class="py-2.5 text-slate-500">{{ \Carbon\Carbon::parse($enq->created_at)->format('d M Y') }}</td>
            <td class="py-2.5">
              <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize
                {{ $enq->status === 'converted' ? 'bg-green-100 text-green-700' : ($enq->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                {{ $enq->status }}
              </span>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="py-6 text-center text-slate-400 text-sm">No recent enquiries.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

{{-- ═══════════════════════════════════════════════════════════════
     OPERATIONS DASHBOARD (inventory / events / alumni)
══════════════════════════════════════════════════════════════════ --}}
@elseif($dashboardType === 'operations')

  @if(($subType ?? 'general') === 'inventory')
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h1 class="page-title">Inventory Dashboard</h1>
        <p class="page-subtitle">Stock & movements overview &mdash; {{ now()->format('l, d M Y') }}</p>
      </div>
      <a href="{{ route('inventory.index') }}" class="btn-sm btn-secondary">Manage Inventory</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="card text-center">
        <p class="stat-number">{{ $totalItems ?? 0 }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Items</p>
      </div>
      <div class="card text-center">
        <p class="stat-number text-rose-600">{{ $lowStockItems ?? 0 }}</p>
        <p class="text-xs text-slate-500 mt-1">Low Stock Items</p>
      </div>
    </div>
    @if(isset($recentMovements) && $recentMovements->isNotEmpty())
    <div class="card">
      <h3 class="font-semibold text-slate-800 mb-4">Recent Movements</h3>
      <div class="space-y-2">
        @foreach($recentMovements as $mov)
        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
          <p class="text-sm font-medium text-slate-700">{{ $mov->name }}</p>
          <div class="flex items-center gap-2">
            <span class="text-xs {{ $mov->type === 'in' ? 'text-green-600' : 'text-rose-600' }} font-medium">
              {{ $mov->type === 'in' ? '+' : '-' }}{{ $mov->quantity }}
            </span>
            <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($mov->created_at)->format('d M') }}</span>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

  @elseif(($subType ?? '') === 'events')
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h1 class="page-title">Events Dashboard</h1>
        <p class="page-subtitle">Upcoming events &mdash; {{ now()->format('l, d M Y') }}</p>
      </div>
      <a href="{{ route('events.index') }}" class="btn-sm btn-secondary">Manage Events</a>
    </div>
    <div class="card">
      <p class="stat-number mb-1">{{ $totalEvents ?? 0 }}</p>
      <p class="text-xs text-slate-500">Total Events</p>
    </div>
    @if(isset($upcomingEvents) && $upcomingEvents->isNotEmpty())
    <div class="card">
      <h3 class="font-semibold text-slate-800 mb-4">Upcoming Events</h3>
      <div class="space-y-3">
        @foreach($upcomingEvents as $ev)
        <div class="flex items-center gap-4 p-3 rounded-xl bg-slate-50">
          <div class="flex-shrink-0 w-12 text-center">
            <p class="text-lg font-bold text-indigo-600">{{ $ev->event_date->format('d') }}</p>
            <p class="text-xs text-slate-400">{{ $ev->event_date->format('M') }}</p>
          </div>
          <div class="min-w-0">
            <p class="font-semibold text-slate-700 text-sm">{{ $ev->name }}</p>
            <p class="text-xs text-slate-400">{{ $ev->event_date->diffForHumans() }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

  @elseif(($subType ?? '') === 'alumni')
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h1 class="page-title">Alumni Dashboard</h1>
        <p class="page-subtitle">Alumni network &mdash; {{ now()->format('l, d M Y') }}</p>
      </div>
      <a href="{{ route('alumni.index') }}" class="btn-sm btn-secondary">Manage Alumni</a>
    </div>
    <div class="card">
      <p class="stat-number mb-1">{{ $totalAlumni ?? 0 }}</p>
      <p class="text-xs text-slate-500">Total Alumni</p>
    </div>
    @if(isset($recentAlumni) && $recentAlumni->isNotEmpty())
    <div class="card">
      <h3 class="font-semibold text-slate-800 mb-4">Recent Alumni</h3>
      <div class="space-y-2">
        @foreach($recentAlumni as $al)
        <div class="flex items-center gap-3 py-2 border-b border-slate-100 last:border-0">
          <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-xs font-bold text-amber-600 flex-shrink-0">{{ strtoupper(substr($al->name,0,1)) }}</div>
          <div>
            <p class="text-sm font-medium text-slate-700">{{ $al->name }}</p>
            <p class="text-xs text-slate-400">Batch {{ $al->graduation_year }} &middot; {{ $al->current_occupation ?? '—' }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif
  @else
    <h1 class="page-title">Operations Dashboard</h1>
    <p class="page-subtitle">Welcome, {{ auth()->user()->name }}</p>
  @endif

@endif

</div>
@endsection

@push('scripts')
<script>
  const ctx = document.getElementById('attendanceChart');
  if (ctx) {
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Today'],
        datasets: [{
          label: 'Attendance %',
          data: [92, 88, 95, 91, 87, 93, 0],
          borderColor: '#3B82F6',
          backgroundColor: 'rgba(59,130,246,0.08)',
          borderWidth: 2.5,
          tension: 0.4,
          fill: true,
          pointBackgroundColor: '#3B82F6',
          pointRadius: 4,
          pointHoverRadius: 6,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: { backgroundColor: '#fff', titleColor: '#0f172a', bodyColor: '#64748b', borderColor: '#e2e8f0', borderWidth: 1 }
        },
        scales: {
          y: { min: 70, max: 100, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 11 } } },
          x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } }
        }
      }
    });
  }
</script>
@endpush
