@extends('layouts.app')
@section('title', 'Class-wise Attendance Summary')
@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Class-wise Attendance Summary</h1>
      <p class="page-subtitle">{{ $currentYear?->name }} — Monthly class attendance breakdown</p>
    </div>
  </div>

  <form method="GET" class="card-flat py-4">
    <div class="flex items-center gap-3">
      <div>
        <label class="label">Month</label>
        <input type="month" name="month" value="{{ $month }}" class="input">
      </div>
      <button type="submit" class="btn btn-primary btn-sm mt-5">View</button>
    </div>
  </form>

  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">Class</th>
          <th class="th text-center">Present</th>
          <th class="th text-center">Absent</th>
          <th class="th text-center">Total Records</th>
          <th class="th text-center">Avg Attendance %</th>
          <th class="th">Rating</th>
        </tr>
      </thead>
      <tbody>
        @forelse($summary as $row)
          <tr class="tr">
            <td class="td font-semibold text-slate-800">{{ $row->class?->name ?? '—' }}</td>
            <td class="td text-center font-semibold text-green-600">{{ $row->present }}</td>
            <td class="td text-center text-red-500">{{ $row->absent }}</td>
            <td class="td text-center text-slate-500">{{ $row->total }}</td>
            <td class="td text-center">
              <span class="font-bold text-{{ $row->percentage >= 90 ? 'green' : ($row->percentage >= 75 ? 'amber' : 'red') }}-600">
                {{ $row->percentage }}%
              </span>
              <div class="w-20 bg-slate-200 rounded-full h-2 mt-1 mx-auto">
                <div class="h-2 rounded-full {{ $row->percentage >= 90 ? 'bg-green-500' : ($row->percentage >= 75 ? 'bg-amber-500' : 'bg-red-500') }}" style="width:{{ $row->percentage }}%"></div>
              </div>
            </td>
            <td class="td">
              @if($row->percentage >= 90)
                <span class="badge-green">Excellent</span>
              @elseif($row->percentage >= 75)
                <span class="badge-amber">Good</span>
              @else
                <span class="badge-red">Needs Attention</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="td text-center py-10 text-slate-400">No attendance data for this month.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>
@endsection
