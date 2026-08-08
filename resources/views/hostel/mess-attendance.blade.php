@extends('layouts.app')
@section('title', 'Mess Attendance')
@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Mess Attendance</h1>
      <p class="page-subtitle">Mark daily meal attendance for hostel students</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('hostel.mess-attendance-summary') }}" class="btn btn-secondary btn-sm">Monthly Summary</a>
      <a href="{{ route('hostel.index') }}" class="btn btn-secondary btn-sm">← Hostel</a>
    </div>
  </div>

  {{-- Filters --}}
  <div class="card-flat py-4">
    <form method="GET" class="flex gap-3 flex-wrap items-end">
      <div>
        <label class="label">Date</label>
        <input type="date" name="date" value="{{ $date->toDateString() }}" class="input" onchange="this.form.submit()">
      </div>
      <div>
        <label class="label">Meal</label>
        <select name="meal" class="select" onchange="this.form.submit()">
          @foreach(['breakfast'=>'Breakfast','lunch'=>'Lunch','snacks'=>'Evening Snacks','dinner'=>'Dinner'] as $v=>$l)
            <option value="{{ $v }}" @selected($meal===$v)>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      @if($hostels->count() > 1)
      <div>
        <label class="label">Hostel</label>
        <select name="hostel_id" class="select" onchange="this.form.submit()">
          <option value="">All Hostels</option>
          @foreach($hostels as $h)<option value="{{ $h->id }}" @selected(request('hostel_id')==$h->id)>{{ $h->name }}</option>@endforeach
        </select>
      </div>
      @endif
    </form>
  </div>

  @if($allotments->isEmpty())
    <div class="card text-center py-12 text-slate-400">No active hostel allotments found.</div>
  @else
  <form method="POST" action="{{ route('hostel.mess-attendance.save') }}">
    @csrf
    <input type="hidden" name="date" value="{{ $date->toDateString() }}">
    <input type="hidden" name="meal" value="{{ $meal }}">

    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-700">
          {{ ucfirst($meal) }} — {{ $date->format('l, d M Y') }}
          <span class="badge-slate ml-2">{{ $allotments->count() }} students</span>
        </h3>
        <div class="flex gap-2">
          <button type="button" onclick="toggleAll(true)" class="btn-xs btn-secondary">Mark All Present</button>
          <button type="button" onclick="toggleAll(false)" class="btn-xs bg-red-50 text-red-600 hover:bg-red-100">Mark All Absent</button>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($allotments as $allotment)
        @php $isPresent = $existingAttendance->get($allotment->id, true); @endphp
        <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer hover:bg-slate-50 transition
                      {{ $isPresent ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}"
               x-data="{ checked: {{ $isPresent ? 'true' : 'false' }} }"
               :class="checked ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'">
          <input type="checkbox" name="present_ids[]" value="{{ $allotment->id }}"
                 @checked($isPresent) x-model="checked" class="w-4 h-4 text-green-600">
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-slate-800 truncate">{{ $allotment->student?->full_name }}</p>
            <p class="text-xs text-slate-400">Room {{ $allotment->room?->room_number ?? '—' }}</p>
          </div>
          <span x-show="checked" class="text-xs text-green-700 font-medium">Present</span>
          <span x-show="!checked" class="text-xs text-red-500 font-medium">Absent</span>
        </label>
        @endforeach
      </div>

      <div class="flex justify-end mt-4 pt-4 border-t border-slate-100">
        <button type="submit" class="btn btn-primary">Save Attendance</button>
      </div>
    </div>
  </form>
  @endif

</div>

<script>
function toggleAll(val) {
  document.querySelectorAll('input[name="present_ids[]"]').forEach(cb => {
    cb.checked = val;
    cb.dispatchEvent(new Event('change'));
  });
}
</script>
@endsection
