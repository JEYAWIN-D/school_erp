@extends('layouts.app')
@section('title', 'Bulk Rollover Progress Report')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Bulk Rollover Progress Report</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label">Academic Year</label>
        <select name="academic_year_id" class="select w-48">
          @foreach($years as $yr)
          <option value="{{ $yr->id }}" @selected(($selectedYear?->id) == $yr->id)>{{ $yr->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  @if($report->count())
  @php
    $totalEnrolled  = $report->sum('enrolled');
    $totalPromoted  = $report->sum('promoted');
    $totalDetained  = $report->sum('detained');
    $totalPending   = $report->sum('not_processed');
    $overallDone    = $report->where('complete', true)->count();
  @endphp

  <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    <div class="card text-center">
      <div class="text-2xl font-bold text-indigo-600">{{ $totalEnrolled }}</div>
      <div class="text-xs text-slate-500 mt-1">Total Students</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-green-600">{{ $totalPromoted }}</div>
      <div class="text-xs text-slate-500 mt-1">Promoted</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-red-500">{{ $totalDetained }}</div>
      <div class="text-xs text-slate-500 mt-1">Detained</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-amber-500">{{ $totalPending }}</div>
      <div class="text-xs text-slate-500 mt-1">Not Processed</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold {{ $overallDone === $report->count() ? 'text-green-600' : 'text-amber-500' }}">
        {{ $overallDone }}/{{ $report->count() }}
      </div>
      <div class="text-xs text-slate-500 mt-1">Classes Completed</div>
    </div>
  </div>

  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">Class</th>
          <th class="th text-center">Total Enrolled</th>
          <th class="th text-center">Promoted</th>
          <th class="th text-center">Detained</th>
          <th class="th text-center">Not Processed</th>
          <th class="th text-center">Promotion Rate</th>
          <th class="th text-center">Status</th>
          <th class="th text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($report as $row)
        <tr class="tr">
          <td class="td font-medium">{{ $row['class']->name }}</td>
          <td class="td text-center">{{ $row['enrolled'] }}</td>
          <td class="td text-center text-green-600 font-medium">{{ $row['promoted'] }}</td>
          <td class="td text-center text-red-500">{{ $row['detained'] }}</td>
          <td class="td text-center {{ $row['not_processed'] > 0 ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">{{ $row['not_processed'] }}</td>
          <td class="td text-center">
            <div class="flex items-center gap-2">
              <div class="flex-1 bg-slate-100 rounded-full h-2">
                <div class="h-2 rounded-full {{ $row['promotion_rate'] >= 90 ? 'bg-green-500' : ($row['promotion_rate'] >= 70 ? 'bg-yellow-400' : 'bg-red-400') }}"
                     style="width: {{ min($row['promotion_rate'], 100) }}%"></div>
              </div>
              <span class="text-xs font-medium w-10">{{ $row['promotion_rate'] }}%</span>
            </div>
          </td>
          <td class="td text-center">
            @if($row['complete'])
              <span class="badge-green text-xs">Complete</span>
            @else
              <span class="badge-amber text-xs">Pending</span>
            @endif
          </td>
          <td class="td text-center">
            @if(!$row['complete'])
            <a href="{{ route('students.promotions', ['class_id' => $row['class']->id]) }}"
               class="btn btn-secondary btn-xs">Process →</a>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  @if($totalPending === 0)
  <div class="alert-success text-sm">
    <strong>Rollover Complete!</strong> All students in {{ $selectedYear?->name }} have been processed.
  </div>
  @else
  <div class="alert-warning text-sm">
    <strong>{{ $totalPending }} students</strong> in {{ $report->where('complete', false)->count() }} class(es) have not been processed yet.
    Please complete the rollover before starting the new academic year.
  </div>
  @endif

  @else
  <div class="card text-center text-slate-400 py-10">Select an academic year to view the rollover progress.</div>
  @endif
</div>
@endsection
