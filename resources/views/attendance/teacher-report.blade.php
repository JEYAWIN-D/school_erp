@extends('layouts.app')
@section('title', 'Teacher Attendance Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Teacher Attendance Report</h1>
    @if($report->count())
    <a href="{{ request()->fullUrlWithQuery(['format' => 'print']) }}" target="_blank" class="btn btn-secondary flex items-center gap-1">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
      Print
    </a>
    @endif
  </div>

  {{-- Filters --}}
  <form method="GET" class="card grid grid-cols-2 md:grid-cols-4 gap-4">
    <div>
      <label class="label">Staff Role / Category</label>
      <select name="category" class="select font-bold">
        <option value="all">All Staff (Teaching, Drivers, etc.)</option>
        <option value="teaching" @selected(($selectedCategory ?? '') === 'teaching')>Teaching Staff</option>
        <option value="non_teaching" @selected(($selectedCategory ?? '') === 'non_teaching')>Non-Teaching Staff</option>
        <option value="driver" @selected(($selectedCategory ?? '') === 'driver')>Drivers</option>
        <option value="nanny" @selected(($selectedCategory ?? '') === 'nanny')>Nannies (Naani)</option>
        <option value="cleaner" @selected(($selectedCategory ?? '') === 'cleaner')>Cleaners / Support</option>
      </select>
    </div>
    <div>
      <label class="label">Month</label>
      <input type="month" name="month" value="{{ $month }}" class="input font-mono">
    </div>
    <div>
      <label class="label">Department</label>
      <select name="department_id" class="select">
        <option value="">All Departments</option>
        @foreach($departments as $dept)
        <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="flex items-end gap-2">
      <button type="submit" name="action" value="1" class="btn btn-primary">Generate Report</button>
      <a href="{{ route('attendance.teacher-report') }}" class="btn btn-secondary">Reset</a>
    </div>
  </form>

  @if($report->count())
  {{-- Summary Cards --}}
  @php
    $avgPct    = $report->avg('percentage');
    $below75   = $report->where('percentage', '<', 75)->count();
    $perfect   = $report->where('percentage', '>=', 95)->count();
  @endphp
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="card text-center">
      <div class="text-2xl font-bold text-indigo-600">{{ $report->count() }}</div>
      <div class="text-xs text-slate-500 mt-1">Total Staff in Report</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-emerald-600">{{ number_format($avgPct, 1) }}%</div>
      <div class="text-xs text-slate-500 mt-1">Avg Attendance</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-red-500">{{ $below75 }}</div>
      <div class="text-xs text-slate-500 mt-1">Below 75%</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-green-600">{{ $perfect }}</div>
      <div class="text-xs text-slate-500 mt-1">Above 95%</div>
    </div>
  </div>

  {{-- Report Table --}}
  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Staff Member &amp; Category</th>
          <th class="th">Department</th>
          <th class="th">Designation</th>
          <th class="th text-center">Working Days</th>
          <th class="th text-center">Present</th>
          <th class="th text-center">Late</th>
          <th class="th text-center">Half Day</th>
          <th class="th text-center">Overtime (Extra Pay)</th>
          <th class="th text-center">On Leave</th>
          <th class="th text-center">Absent</th>
          <th class="th text-center">Attendance %</th>
        </tr>
      </thead>
      <tbody>
        @foreach($report as $i => $row)
        @php
          $pct = $row['percentage'];
          $color = $pct >= 90 ? 'text-green-600' : ($pct >= 75 ? 'text-yellow-600' : 'text-red-600');
          $bg    = $pct < 75 ? 'bg-red-50' : '';
        @endphp
        <tr class="tr {{ $bg }}">
          <td class="td text-slate-400">{{ $i + 1 }}</td>
          <td class="td">
            <div class="flex items-center gap-1.5 flex-wrap">
              <a href="{{ route('hr.employees.show', $row['employee']->id) }}" class="font-bold text-slate-800 hover:text-indigo-600">{{ $row['employee']->full_name }}</a>
              <span class="{{ $row['employee']->category_badge_class }} text-[9px] px-1.5 py-0.2 font-bold uppercase tracking-wider rounded">
                {{ $row['employee']->category_label }}
              </span>
            </div>
            <div class="text-xs text-slate-400 font-mono">{{ $row['employee']->employee_code ?? 'EMP-' . $row['employee']->id }}</div>
          </td>
          <td class="td text-sm">{{ $row['employee']->department_name }}</td>
          <td class="td text-sm">{{ $row['employee']->designation_name }}</td>
          <td class="td text-center font-mono">{{ $row['working'] }}</td>
          <td class="td text-center text-green-600 font-bold font-mono">{{ $row['present'] }}</td>
          <td class="td text-center text-amber-600 font-mono">{{ $row['late'] ?? 0 }}</td>
          <td class="td text-center text-blue-500 font-mono">{{ $row['half_day'] }}</td>
          <td class="td text-center">
            @if(($row['overtime'] ?? 0) > 0)
              <span class="px-2 py-0.5 rounded-full font-bold text-xs bg-amber-100 text-amber-900 border border-amber-300 font-mono">
                {{ $row['overtime'] }} d ({{ $row['overtime_duration'] }})
              </span>
            @else
              <span class="text-slate-300">—</span>
            @endif
          </td>
          <td class="td text-center text-yellow-600 font-mono">{{ $row['on_leave'] }}</td>
          <td class="td text-center text-red-500 font-bold font-mono">{{ $row['absent'] }}</td>
          <td class="td text-center">
            <span class="font-mono font-bold text-sm {{ $color }}">{{ $pct }}%</span>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1">
              <div class="h-1.5 rounded-full {{ $pct >= 90 ? 'bg-green-500' : ($pct >= 75 ? 'bg-yellow-400' : 'bg-red-400') }}"
                   style="width: {{ min($pct, 100) }}%"></div>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="bg-slate-50 font-semibold">
          <td class="td" colspan="4">Total / Average</td>
          <td class="td text-center">—</td>
          <td class="td text-center">{{ $report->sum('present') }}</td>
          <td class="td text-center">{{ $report->sum('half_day') }}</td>
          <td class="td text-center">{{ $report->sum('on_leave') }}</td>
          <td class="td text-center">{{ $report->sum('absent') }}</td>
          <td class="td text-center">{{ $report->sum('unmarked') }}</td>
          <td class="td text-center">{{ number_format($avgPct, 1) }}%</td>
        </tr>
      </tfoot>
    </table>
  </div>

  @if($below75 > 0)
  <div class="alert-warning text-sm">
    <strong>{{ $below75 }} employee(s)</strong> have attendance below 75% for {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}.
    Consider issuing leave shortage notices.
  </div>
  @endif

  @else
    @if(request()->has('action') || request()->has('month'))
    <div class="card text-center text-slate-500 py-10">No attendance data found for the selected filters.</div>
    @else
    <div class="card text-center text-slate-400 py-10">Select a month and click <strong>Generate Report</strong> to view teacher attendance.</div>
    @endif
  @endif
</div>
@endsection
