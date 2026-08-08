@extends('layouts.app')
@section('title', 'Bus Attendance Register')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Bus Attendance Register</h1>
    <a href="{{ route('transport.bus-attendance.report') }}" class="btn-sm btn-secondary">Attendance Report</a>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  {{-- Filters --}}
  <form method="GET" class="card">
    <div class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Vehicle</label>
        <select name="vehicle_id" class="select w-44" required>
          <option value="">Select Vehicle</option>
          @foreach($vehicles as $v)
            <option value="{{ $v->id }}" {{ $vehicleId == $v->id ? 'selected' : '' }}>
              {{ $v->vehicle_number }} {{ $v->name ? '— '.$v->name : '' }}
            </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Date</label>
        <input type="date" name="date" class="input w-36" value="{{ $date }}">
      </div>
      <div>
        <label class="label">Trip</label>
        <select name="trip_type" class="select w-36">
          <option value="morning" {{ $tripType=='morning'?'selected':'' }}>Morning</option>
          <option value="afternoon" {{ $tripType=='afternoon'?'selected':'' }}>Afternoon</option>
          <option value="both" {{ $tripType=='both'?'selected':'' }}>Both</option>
        </select>
      </div>
      <button type="submit" class="btn-primary">Load Students</button>
    </div>
  </form>

  @if($vehicleId && $students->isNotEmpty())
  <form method="POST" action="{{ route('transport.bus-attendance.save') }}" class="card">
    @csrf
    <input type="hidden" name="vehicle_id" value="{{ $vehicleId }}">
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="trip_type" value="{{ $tripType }}">

    <div class="flex items-center justify-between mb-4">
      <h2 class="text-sm font-semibold text-slate-700">
        {{ $students->count() }} students — {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
        <span class="badge-blue ml-1">{{ ucfirst($tripType) }}</span>
      </h2>
      <div class="flex gap-2">
        <button type="button" onclick="markAll('present')" class="btn-xs btn-secondary text-green-700">Mark All Present</button>
        <button type="button" onclick="markAll('absent')" class="btn-xs btn-secondary text-red-700">Mark All Absent</button>
      </div>
    </div>

    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">Student</th>
            <th class="th">Boarding Stop</th>
            <th class="th">Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($students as $i => $row)
          @php
            $stu = $row['student'];
            $markedStatus = $existing->has($stu->id) ? $existing[$stu->id] : null;
          @endphp
          <tr class="tr {{ is_null($markedStatus) ? 'bg-amber-50/40' : '' }}">
            <td class="td text-slate-400 text-xs">{{ $i + 1 }}</td>
            <td class="td">
              <div class="font-medium">{{ $stu->full_name }}</div>
              <div class="text-xs text-slate-400">{{ $stu->admission_number }}</div>
            </td>
            <td class="td text-slate-500 text-xs">{{ $row['boarding_stop'] ?? '—' }}</td>
            <td class="td">
              <div class="flex gap-2 items-center attendance-row">
                @if(is_null($markedStatus))
                  <span class="text-xs text-amber-600 font-medium mr-1">Not marked</span>
                @endif
                <label class="flex items-center gap-1 text-sm cursor-pointer">
                  <input type="radio" name="attendance[{{ $stu->id }}]" value="present"
                    class="w-4 h-4 text-green-600" {{ $markedStatus === 'present' ? 'checked' : '' }}>
                  <span class="text-green-700">Present</span>
                </label>
                <label class="flex items-center gap-1 text-sm cursor-pointer">
                  <input type="radio" name="attendance[{{ $stu->id }}]" value="absent"
                    class="w-4 h-4 text-red-500" {{ $markedStatus === 'absent' ? 'checked' : '' }}>
                  <span class="text-red-600">Absent</span>
                </label>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="flex justify-end pt-4">
      <button type="submit" class="btn-primary">Save Attendance</button>
    </div>
  </form>
  @elseif($vehicleId)
    <div class="card"><p class="text-slate-500 text-sm">No students allotted to this vehicle. Go to <a href="{{ route('transport.allotment') }}" class="text-indigo-600">Transport Allotment</a> to assign students.</p></div>
  @else
    <div class="card"><p class="text-slate-400 text-sm">Select a vehicle and date to load the attendance register.</p></div>
  @endif
</div>

<script>
function markAll(status) {
  document.querySelectorAll('.attendance-row input[value="' + status + '"]').forEach(r => r.checked = true);
}
</script>
@endsection
