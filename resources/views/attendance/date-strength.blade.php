@extends('layouts.app')
@section('title', 'Date-wise School Strength')
@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Date-wise School Strength</h1>
      <p class="page-subtitle">{{ $currentYear?->name }} — Daily attendance summary</p>
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
          <th class="th">Date</th>
          <th class="th">Day</th>
          <th class="th text-center">Present</th>
          <th class="th text-center">Absent</th>
          <th class="th text-center">Total Marked</th>
          <th class="th text-center">Attendance %</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $row)
          @php
            $pct = $row->total > 0 ? round($row->present / $row->total * 100, 1) : 0;
          @endphp
          <tr class="tr">
            <td class="td font-medium">{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
            <td class="td text-slate-500">{{ \Carbon\Carbon::parse($row->date)->format('D') }}</td>
            <td class="td text-center font-semibold text-green-600">{{ $row->present }}</td>
            <td class="td text-center text-red-500">{{ $row->absent }}</td>
            <td class="td text-center text-slate-500">{{ $row->total }}</td>
            <td class="td text-center">
              <span class="font-semibold {{ $pct >= 90 ? 'text-green-600' : ($pct >= 75 ? 'text-amber-600' : 'text-red-600') }}">{{ $pct }}%</span>
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
