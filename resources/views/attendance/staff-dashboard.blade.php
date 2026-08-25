@extends('layouts.app')
@section('title', 'Staff Attendance Dashboard')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Staff Attendance Dashboard</h1>
    <div class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($today)->format('l, d M Y') }}</div>
  </div>

  @if($stats['isHoliday'])
    {{-- Clean Minimalist Holiday Notice Banner --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center shrink-0">
          <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"/></svg>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h4 class="font-bold text-xs text-slate-900">{{ $stats['holidayName'] }}</h4>
            <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">Holiday Off</span>
          </div>
          <p class="text-[11px] text-slate-500 mt-0.5">All staff are set to holiday status by default. Overtime duties are recorded automatically upon card tap.</p>
        </div>
      </div>
      <div class="flex items-center gap-2 self-start sm:self-auto">
        <span class="text-xs font-mono font-bold text-slate-700 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl">
          {{ $stats['overtime'] }} Overtime Today
        </span>
      </div>
    </div>
  @endif

  {{-- Summary Cards --}}
  <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
    @php
      $cards = [
        ['label'=>'Total Staff',  'value'=>$stats['total'],      'color'=>'slate'],
        ['label'=>'Present',      'value'=>$stats['present'],    'color'=>'green'],
        ['label'=>'Late Arrival', 'value'=>$stats['late'],       'color'=>'amber'],
        ['label'=>'Half Day',     'value'=>$stats['half_day'],   'color'=>'blue'],
        ['label'=>'Overtime',     'value'=>$stats['overtime'],   'color'=>'yellow'],
        ['label'=>'On Leave',     'value'=>$stats['on_leave'],   'color'=>'purple'],
        ['label'=>'Absent',       'value'=>$stats['absent'],     'color'=>'red'],
      ];
    @endphp
    @foreach($cards as $c)
    <div class="card text-center p-3.5 border border-slate-200">
      <div class="text-2xl font-bold font-mono text-slate-800">{{ $c['value'] }}</div>
      <div class="text-xs font-semibold text-slate-500 mt-1">{{ $c['label'] }}</div>
    </div>
    @endforeach
  </div>

  {{-- Attendance Percentage Metrics (Day-Wise & Month-Wise) --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Day-Wise Percentage Meter --}}
    <div class="card p-4">
      <div class="flex items-center justify-between mb-2">
        <div>
          <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Day-Wise Attendance Rate</span>
          <p class="text-xs text-slate-500 font-mono">{{ \Carbon\Carbon::parse($today)->format('d M Y (l)') }}</p>
        </div>
        <span class="text-2xl font-black font-mono {{ $stats['dayRate'] >= 90 ? 'text-green-600' : ($stats['dayRate'] >= 75 ? 'text-blue-600' : 'text-amber-600') }}">{{ $stats['dayRate'] }}%</span>
      </div>
      <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
        <div class="h-3 rounded-full {{ $stats['dayRate'] >= 90 ? 'bg-green-500' : ($stats['dayRate'] >= 75 ? 'bg-blue-500' : 'bg-amber-500') }}" style="width:{{ min(100, $stats['dayRate']) }}%"></div>
      </div>
    </div>

    {{-- Month-Wise Percentage Meter --}}
    <div class="card p-4">
      <div class="flex items-center justify-between mb-2">
        <div>
          <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Month-Wise Attendance Average</span>
          <p class="text-xs text-slate-500 font-mono">{{ $stats['monthName'] }} ({{ $stats['monthWorkingDays'] }} Working Days)</p>
        </div>
        <span class="text-2xl font-black font-mono {{ $stats['monthRate'] >= 90 ? 'text-indigo-600' : ($stats['monthRate'] >= 75 ? 'text-blue-600' : 'text-amber-600') }}">{{ $stats['monthRate'] }}%</span>
      </div>
      <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
        <div class="h-3 rounded-full {{ $stats['monthRate'] >= 90 ? 'bg-indigo-500' : ($stats['monthRate'] >= 75 ? 'bg-blue-500' : 'bg-amber-500') }}" style="width:{{ min(100, $stats['monthRate']) }}%"></div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Staff Role / Category Breakdown (Teaching, Non-Teaching, Drivers, Nannies, Cleaners) --}}
    <div class="card">
      <h2 class="font-bold text-slate-800 mb-4">
        Staff Category &amp; Role Summary
      </h2>
      <div class="table-wrap">
        <table class="min-w-full text-sm">
          <thead><tr>
            <th class="th">Staff Category</th>
            <th class="th text-right">Total</th>
            <th class="th text-right">Present</th>
            <th class="th text-right">Absent</th>
            <th class="th">Attendance Rate</th>
          </tr></thead>
          <tbody>
            @foreach($categoryStats as $cat)
            <tr class="tr">
              <td class="td font-bold">
                {{ $cat->label }}
              </td>
              <td class="td text-right font-mono font-medium">{{ $cat->total_count }}</td>
              <td class="td text-right text-emerald-600 font-mono font-bold">{{ $cat->present_count }}</td>
              <td class="td text-right text-rose-500 font-mono font-bold">{{ $cat->absent_count }}</td>
              <td class="td w-32">
                <div class="flex items-center gap-2">
                  <div class="flex-1 bg-slate-100 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $cat->rate >= 90 ? 'bg-emerald-500' : ($cat->rate >= 75 ? 'bg-blue-500' : 'bg-amber-500') }}" style="width:{{ $cat->rate }}%"></div>
                  </div>
                  <span class="text-xs font-mono font-bold text-slate-600 w-10 text-right">{{ $cat->rate }}%</span>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- Department-wise Summary --}}
    <div class="card">
      <h2 class="font-bold text-slate-800 mb-4">
        Department-wise Summary
      </h2>
      <div class="table-wrap">
        <table class="min-w-full text-sm">
          <thead><tr>
            <th class="th">Department</th>
            <th class="th text-right">Total</th>
            <th class="th text-right">Present</th>
            <th class="th text-right">Absent</th>
            <th class="th">Rate</th>
          </tr></thead>
          <tbody>
            @forelse($departmentStats as $dept)
            @php $notMarkedDept = $dept->total_count - $dept->present_count - $dept->absent_count; $deptPct = $dept->total_count > 0 ? round($dept->present_count/$dept->total_count*100) : 0; @endphp
            <tr class="tr">
              <td class="td font-medium">{{ $dept->name }}</td>
              <td class="td text-right font-mono">{{ $dept->total_count }}</td>
              <td class="td text-right text-emerald-600 font-mono font-bold">{{ $dept->present_count }}</td>
              <td class="td text-right text-rose-500 font-mono">{{ $dept->absent_count }}</td>
              <td class="td w-32">
                <div class="flex items-center gap-2">
                  <div class="flex-1 bg-slate-100 rounded-full h-2">
                    <div class="h-2 rounded-full bg-emerald-500" style="width:{{ $deptPct }}%"></div>
                  </div>
                  <span class="text-xs font-mono font-bold text-slate-500 w-8">{{ $deptPct }}%</span>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="td text-center text-slate-400">No departments.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Absent today --}}
    <div class="card">
      <h2 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
        Absent Today <span class="badge-red">{{ $absentToday->count() }}</span>
      </h2>
      @forelse($absentToday as $emp)
      <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
        <div>
          <div class="font-medium text-sm text-slate-800">{{ $emp->full_name }}</div>
          <div class="text-xs text-slate-500">{{ is_string($emp->department) ? $emp->department : ($emp->department?->name ?? 'General') }}</div>
        </div>
        <span class="badge-red text-xs">Absent</span>
      </div>
      @empty
      <p class="text-slate-400 text-sm">No one is absent today.</p>
      @endforelse
    </div>

    {{-- Not marked --}}
    <div class="card">
      <h2 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
        Attendance Not Marked <span class="badge-amber">{{ $notMarked->count() }}</span>
      </h2>
      @forelse($notMarked->take(15) as $emp)
      <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
        <div>
          <div class="font-medium text-sm text-slate-800">{{ $emp->full_name }}</div>
          <div class="text-xs text-slate-500">{{ is_string($emp->department) ? $emp->department : ($emp->department?->name ?? 'General') }}</div>
        </div>
        <span class="badge-slate text-xs">Pending</span>
      </div>
      @empty
      <p class="text-slate-400 text-sm">All attendance marked!</p>
      @endforelse
      @if($notMarked->count() > 15)
      <p class="text-xs text-slate-400 mt-2">...and {{ $notMarked->count() - 15 }} more</p>
      @endif
    </div>
  </div>

  <div class="flex gap-3">
    <a href="{{ route('attendance.staff') }}" class="btn btn-primary btn-sm">Mark Staff Attendance</a>
    <a href="{{ route('attendance.staff.register') }}" class="btn btn-secondary btn-sm">Monthly Register</a>
  </div>
</div>
@endsection
