@extends('layouts.app')
@section('title', 'Staff Attendance Dashboard')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Staff Attendance Dashboard</h1>
    <div class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($today)->format('l, d M Y') }}</div>
  </div>

  {{-- Summary Cards --}}
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
    @php
      $cards = [
        ['label'=>'Total Staff',  'value'=>$stats['total'],      'color'=>'slate'],
        ['label'=>'Present',      'value'=>$stats['present'],     'color'=>'green'],
        ['label'=>'Absent',       'value'=>$stats['absent'],      'color'=>'red'],
        ['label'=>'On Leave',     'value'=>$stats['on_leave'],    'color'=>'amber'],
        ['label'=>'Half Day',     'value'=>$stats['half_day'],    'color'=>'blue'],
        ['label'=>'Not Marked',   'value'=>$stats['not_marked'],  'color'=>'purple'],
      ];
    @endphp
    @foreach($cards as $c)
    <div class="card text-center">
      <div class="text-2xl font-bold text-slate-800">{{ $c['value'] }}</div>
      <div class="text-xs text-slate-500 mt-1">{{ $c['label'] }}</div>
    </div>
    @endforeach
  </div>

  @php
    $attendancePct = $stats['total'] > 0 ? round($stats['present'] / $stats['total'] * 100) : 0;
  @endphp
  <div class="card">
    <div class="flex items-center justify-between mb-2">
      <span class="text-sm font-medium text-slate-700">Attendance Rate Today</span>
      <span class="font-bold {{ $attendancePct >= 90 ? 'text-green-600' : ($attendancePct >= 75 ? 'text-amber-600' : 'text-red-600') }}">{{ $attendancePct }}%</span>
    </div>
    <div class="w-full bg-slate-100 rounded-full h-3">
      <div class="h-3 rounded-full {{ $attendancePct >= 90 ? 'bg-green-500' : ($attendancePct >= 75 ? 'bg-amber-400' : 'bg-red-500') }}" style="width:{{ $attendancePct }}%"></div>
    </div>
  </div>

  {{-- Department-wise --}}
  <div class="card">
    <h2 class="font-semibold text-slate-800 mb-4">Department-wise Summary</h2>
    <div class="table-wrap">
      <table class="min-w-full text-sm">
        <thead><tr>
          <th class="th">Department</th>
          <th class="th text-right">Total</th>
          <th class="th text-right">Present</th>
          <th class="th text-right">Absent</th>
          <th class="th text-right">Not Marked</th>
          <th class="th">Rate</th>
        </tr></thead>
        <tbody>
          @forelse($departmentStats as $dept)
          @php $notMarkedDept = $dept->total_count - $dept->present_count - $dept->absent_count; $deptPct = $dept->total_count > 0 ? round($dept->present_count/$dept->total_count*100) : 0; @endphp
          <tr class="tr">
            <td class="td font-medium">{{ $dept->name }}</td>
            <td class="td text-right">{{ $dept->total_count }}</td>
            <td class="td text-right text-green-600 font-medium">{{ $dept->present_count }}</td>
            <td class="td text-right text-red-500">{{ $dept->absent_count }}</td>
            <td class="td text-right text-slate-400">{{ max(0,$notMarkedDept) }}</td>
            <td class="td w-32">
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-100 rounded-full h-2">
                  <div class="h-2 rounded-full bg-green-500" style="width:{{ $deptPct }}%"></div>
                </div>
                <span class="text-xs text-slate-500 w-8">{{ $deptPct }}%</span>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="td text-center text-slate-400">No departments.</td></tr>
          @endforelse
        </tbody>
      </table>
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
          <div class="text-xs text-slate-500">{{ $emp->department?->name }}</div>
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
          <div class="text-xs text-slate-500">{{ $emp->department?->name }}</div>
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
