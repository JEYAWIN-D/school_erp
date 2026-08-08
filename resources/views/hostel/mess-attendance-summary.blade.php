@extends('layouts.app')
@section('title', 'Mess Attendance Summary')
@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Mess Attendance Summary</h1>
      <p class="page-subtitle">Monthly meal-wise attendance overview</p>
    </div>
    <a href="{{ route('hostel.mess-attendance') }}" class="btn btn-secondary btn-sm">← Daily Attendance</a>
  </div>

  <div class="card-flat py-4">
    <form method="GET" class="flex gap-3 items-end flex-wrap">
      <div>
        <label class="label">Month</label>
        <input type="month" name="month" value="{{ $month }}" class="input" onchange="this.form.submit()">
      </div>
      @if($hostels->count() > 1)
      <div>
        <label class="label">Hostel</label>
        <select name="hostel_id" class="select" onchange="this.form.submit()">
          <option value="">All</option>
          @foreach($hostels as $h)<option value="{{ $h->id }}" @selected(request('hostel_id')==$h->id)>{{ $h->name }}</option>@endforeach
        </select>
      </div>
      @endif
    </form>
  </div>

  @if(empty($summaryData))
    <div class="card text-center py-10 text-slate-400">No mess attendance data for this month.</div>
  @else
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr>
        <th class="th">Student</th>
        <th class="th">Room</th>
        <th class="th text-center">Total Records</th>
        <th class="th text-center">Present</th>
        <th class="th text-center">Absent</th>
        <th class="th text-center">Attendance %</th>
      </tr></thead>
      <tbody>
        @foreach($summaryData as $row)
        @php $pct = $row['total'] > 0 ? round($row['present']/$row['total']*100,1) : null; @endphp
        <tr class="tr">
          <td class="td font-medium">{{ $row['student']?->full_name ?? '—' }}</td>
          <td class="td text-slate-500 text-sm">{{ $row['allotment']->room?->room_number ?? '—' }}</td>
          <td class="td text-center">{{ $row['total'] }}</td>
          <td class="td text-center text-green-700 font-semibold">{{ $row['present'] }}</td>
          <td class="td text-center {{ $row['absent'] > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">{{ $row['absent'] ?: '—' }}</td>
          <td class="td text-center">
            @if($pct !== null)
              <span class="{{ $pct >= 80 ? 'badge-green' : ($pct >= 60 ? 'badge-yellow' : 'badge-red') }}">{{ $pct }}%</span>
            @else
              <span class="text-slate-400">—</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif

</div>
@endsection
