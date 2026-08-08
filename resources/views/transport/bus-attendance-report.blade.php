@extends('layouts.app')
@section('title', 'Bus Attendance Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Bus Attendance Report</h1>
    <div class="flex gap-2">
      @if($vehicleId)
      <a href="{{ route('transport.bus-attendance.report.excel', ['vehicle_id' => $vehicleId, 'from' => $from, 'to' => $to]) }}"
        class="btn-sm btn-secondary">Export Excel</a>
      @endif
      <a href="{{ route('transport.bus-attendance') }}" class="btn-sm btn-secondary">← Register</a>
    </div>
  </div>

  <form method="GET" class="card">
    <div class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Vehicle</label>
        <select name="vehicle_id" class="select w-44">
          <option value="">Select Vehicle</option>
          @foreach($vehicles as $v)
            <option value="{{ $v->id }}" {{ $vehicleId == $v->id ? 'selected' : '' }}>
              {{ $v->vehicle_number }} {{ $v->name ? '— '.$v->name : '' }}
            </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">From</label>
        <input type="date" name="from" class="input w-36" value="{{ $from }}">
      </div>
      <div>
        <label class="label">To</label>
        <input type="date" name="to" class="input w-36" value="{{ $to }}">
      </div>
      <button type="submit" class="btn-primary">Generate</button>
    </div>
  </form>

  @if($vehicleId && $records->isNotEmpty())
  <div class="card">
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">Student</th>
            <th class="th text-center">Total Days</th>
            <th class="th text-center text-green-700">Present</th>
            <th class="th text-center text-red-600">Absent</th>
            <th class="th text-center">Attendance %</th>
          </tr>
        </thead>
        <tbody>
          @foreach($records as $row)
          @php $pct = $row['total'] > 0 ? round($row['present'] / $row['total'] * 100) : 0; @endphp
          <tr class="tr">
            <td class="td">
              <div class="font-medium">{{ $row['student']?->full_name }}</div>
              <div class="text-xs text-slate-400">{{ $row['student']?->admission_number }}</div>
            </td>
            <td class="td text-center">{{ $row['total'] }}</td>
            <td class="td text-center text-green-700 font-semibold">{{ $row['present'] }}</td>
            <td class="td text-center text-red-600 font-semibold">{{ $row['absent'] }}</td>
            <td class="td text-center">
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-200 rounded-full h-2">
                  <div class="h-2 rounded-full {{ $pct >= 75 ? 'bg-green-500' : ($pct >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                    style="width: {{ $pct }}%"></div>
                </div>
                <span class="text-xs font-semibold w-10 text-right">{{ $pct }}%</span>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @elseif($vehicleId)
    <div class="card"><p class="text-slate-400 text-sm">No attendance records found for the selected vehicle and date range.</p></div>
  @endif
</div>
@endsection
