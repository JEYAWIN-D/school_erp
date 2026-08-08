@extends('layouts.app')
@section('title', 'Late Arrival Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Late Arrival Tracking — Staff</h1>
    <a href="{{ route('attendance.teacher-report') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  {{-- Filters --}}
  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Month</label>
        <input type="month" name="month" value="{{ $month }}" class="input text-sm">
      </div>
      <div>
        <label class="label text-xs">Department</label>
        <select name="department_id" class="select text-sm">
          <option value="">All Departments</option>
          @foreach($departments as $dept)
            <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </form>
  </div>

  {{-- Summary --}}
  <div class="grid grid-cols-3 gap-4">
    <div class="card text-center">
      <p class="text-2xl font-bold text-amber-600">{{ $byEmployee->count() }}</p>
      <p class="text-xs text-slate-500 mt-1">Staff with Late Arrivals</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-red-600">{{ $records->count() }}</p>
      <p class="text-xs text-slate-500 mt-1">Total Late Incidents</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-slate-700">{{ $lateThreshold }}</p>
      <p class="text-xs text-slate-500 mt-1">Late Threshold (HH:MM)</p>
    </div>
  </div>

  {{-- By Employee Summary --}}
  @if($byEmployee->isEmpty())
    <div class="card text-center py-12 text-slate-400">No late arrivals recorded for this period.</div>
  @else
  <div class="card overflow-x-auto">
    <h2 class="font-semibold text-slate-700 mb-3">Employee-wise Summary</h2>
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Employee</th>
          <th class="th">Department</th>
          <th class="th">Late Count</th>
          <th class="th">Total Late Mins</th>
          <th class="th">Late Dates</th>
        </tr>
      </thead>
      <tbody>
        @foreach($byEmployee as $empId => $data)
        <tr class="tr {{ $data['late_count'] >= 5 ? 'bg-red-50' : '' }}">
          <td class="td text-slate-400">{{ $loop->iteration }}</td>
          <td class="td font-medium">{{ $data['employee']->name }}</td>
          <td class="td text-slate-500">{{ $data['employee']->department?->name ?? '—' }}</td>
          <td class="td text-center">
            <span class="badge-{{ $data['late_count'] >= 5 ? 'red' : ($data['late_count'] >= 3 ? 'amber' : 'blue') }}">
              {{ $data['late_count'] }}
            </span>
          </td>
          <td class="td text-center">{{ $data['total_mins'] }} min</td>
          <td class="td">
            <div class="flex flex-wrap gap-1">
              @foreach($data['records'] as $rec)
                <span class="text-xs bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded" title="{{ $rec->late_minutes }} min late">
                  {{ $rec->date->format('d') }}
                  @if($rec->check_in)
                    <span class="text-amber-600">{{ \Carbon\Carbon::createFromTimeString($rec->check_in)->format('H:i') }}</span>
                  @endif
                </span>
              @endforeach
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Detail Table --}}
  <div class="card overflow-x-auto">
    <h2 class="font-semibold text-slate-700 mb-3">All Late Arrivals — {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}</h2>
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">Date</th>
          <th class="th">Employee</th>
          <th class="th">Department</th>
          <th class="th">Check-in Time</th>
          <th class="th">Late By</th>
          <th class="th">Remarks</th>
        </tr>
      </thead>
      <tbody>
        @foreach($records as $rec)
        <tr class="tr">
          <td class="td">{{ $rec->date->format('D, d M') }}</td>
          <td class="td font-medium">{{ $rec->employee->name }}</td>
          <td class="td text-slate-500">{{ $rec->employee->department?->name ?? '—' }}</td>
          <td class="td font-mono text-amber-700">{{ $rec->check_in ? \Carbon\Carbon::createFromTimeString($rec->check_in)->format('h:i A') : '—' }}</td>
          <td class="td text-red-600 font-semibold">{{ $rec->late_minutes }} min</td>
          <td class="td text-slate-500 text-xs">{{ $rec->remarks ?? '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
