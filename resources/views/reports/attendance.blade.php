@extends('layouts.app')
@section('title', 'Attendance Report')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Attendance Report</h1>
      <p class="text-sm text-slate-500 mt-0.5">{{ $year?->name ?? 'All Years' }}</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('reports.index') }}" class="btn btn-outline">← Back</a>
      <a href="{{ route('reports.attendance.excel', request()->query()) }}" class="btn btn-secondary btn-sm">Export Excel</a>
    </div>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">Month</label>
        <input type="month" name="month" value="{{ $month }}" class="input">
      </div>
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select">
          <option value="">All Classes</option>
          @foreach($classes as $c)
          <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <button class="btn btn-primary">Filter</button>
      @if(request()->hasAny(['class_id']))
      <a href="{{ route('reports.attendance', ['month' => $month]) }}" class="btn btn-secondary btn-sm">Reset</a>
      @endif
    </form>
  </div>

  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4">Class-wise Attendance — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</h2>
    @if(count($classwise))
    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr><th>Class</th><th class="text-right">Present</th><th class="text-right">Absent</th><th class="text-right">Total</th><th class="text-right">%</th><th>Status</th></tr>
        </thead>
        <tbody>
          @foreach($classwise as $row)
          <tr>
            <td class="font-medium">{{ $row['class'] }}</td>
            <td class="text-right text-emerald-700">{{ $row['present'] }}</td>
            <td class="text-right text-rose-600">{{ $row['absent'] }}</td>
            <td class="text-right">{{ $row['total'] }}</td>
            <td class="text-right font-semibold {{ $row['pct'] >= 75 ? 'text-emerald-700' : 'text-rose-600' }}">{{ $row['pct'] }}%</td>
            <td><span class="badge {{ $row['pct'] >= 75 ? 'badge-success' : 'badge-danger' }}">{{ $row['pct'] >= 75 ? 'Good' : 'Low' }}</span></td>
          </tr>
          @endforeach
          @php
            $totalPresent = collect($classwise)->sum('present');
            $totalAbsent  = collect($classwise)->sum('absent');
            $totalAll     = collect($classwise)->sum('total');
            $overallPct   = $totalAll > 0 ? round($totalPresent / $totalAll * 100, 1) : 0;
          @endphp
          <tr class="font-bold bg-slate-50 border-t-2 border-slate-200">
            <td>School Total</td>
            <td class="text-right text-emerald-700">{{ $totalPresent }}</td>
            <td class="text-right text-rose-600">{{ $totalAbsent }}</td>
            <td class="text-right">{{ $totalAll }}</td>
            <td class="text-right {{ $overallPct >= 75 ? 'text-emerald-700' : 'text-rose-600' }}">{{ $overallPct }}%</td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </div>
    @else
      <p class="text-slate-400 text-center py-8">No attendance data for this month.</p>
    @endif
  </div>
</div>
@endsection
