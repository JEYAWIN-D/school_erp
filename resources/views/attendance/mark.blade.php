@extends('layouts.app')
@section('title', 'Mark Attendance')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="{{ route('attendance.index') }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <h1 class="page-title">Mark Attendance</h1>
  </div>

  {{-- Class + Date selector --}}
  <form method="GET" class="card py-4">
    <div class="flex flex-wrap gap-3">
      <select name="class_id" class="select w-36" onchange="this.form.submit()">
        <option value="">Select Class</option>
        @foreach($classes as $cls)
          <option value="{{ $cls->id }}" @selected($classId == $cls->id)>{{ $cls->name }}</option>
        @endforeach
      </select>
      @if($sections->count())
        <select name="section_id" class="select w-32" onchange="this.form.submit()">
          <option value="">All Sections</option>
          @foreach($sections as $sec)
            <option value="{{ $sec->id }}" @selected($sectionId == $sec->id)>{{ $sec->name }}</option>
          @endforeach
        </select>
      @endif
      <input type="date" name="date" value="{{ $date }}" max="{{ today()->toDateString() }}" class="input w-44" onchange="this.form.submit()">
    </div>
  </form>

  @if($students->count())
    <form method="POST" action="{{ route('attendance.save') }}">
      @csrf
      <input type="hidden" name="class_id" value="{{ $classId }}">
      <input type="hidden" name="section_id" value="{{ $sectionId }}">
      <input type="hidden" name="date" value="{{ $date }}">

      @php
        $cutoff   = \App\Models\SchoolSetting::get('attendance_cutoff_time', '12:00');
        $lateTime = \App\Models\SchoolSetting::get('late_arrival_time', '09:30');
        $isPastCutoff = ($date === today()->toDateString()) && (now()->format('H:i') > $cutoff);
      @endphp

      {{-- Toolbar --}}
      <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex items-center justify-between gap-3 flex-wrap">
        <p class="font-semibold text-slate-700 text-sm">{{ $students->count() }} Students</p>
        <div class="flex gap-2 text-xs">
          <button type="button" onclick="markAll('present')" class="btn btn-ghost btn-sm text-green-600">✓ All Present</button>
          <button type="button" onclick="markAll('absent')" class="btn btn-ghost btn-sm text-red-600">✗ All Absent</button>
        </div>
      </div>

      {{-- ── MOBILE card view (hidden on md+) ── --}}
      <div class="space-y-2 md:hidden">
        @foreach($students as $student)
          @php $current = $existing[$student->id]?->status ?? 'present'; @endphp
          <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 space-y-3" id="card-{{ $student->id }}">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-semibold text-slate-800 text-sm">{{ $student->full_name }}</p>
                <p class="text-xs text-slate-400 font-mono">Roll {{ $student->currentEnrollment?->roll_number ?? '—' }}</p>
              </div>
              <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-slate-100 text-slate-600" id="badge-{{ $student->id }}">
                {{ ucfirst(str_replace('_',' ',$current)) }}
              </span>
            </div>
            <div class="grid grid-cols-5 gap-1.5">
              @foreach([
                ['present','P','green'],
                ['absent','A','red'],
                ['late','L','amber'],
                ['half_day','H','orange'],
                ['leave','Le','blue'],
              ] as [$status,$lbl,$clr])
              <label class="flex flex-col items-center gap-1 cursor-pointer">
                <input type="radio" name="attendance[{{ $student->id }}]" value="{{ $status }}"
                  @checked($current === $status)
                  class="sr-only peer"
                  onchange="highlightRow({{ $student->id }}, '{{ $status }}'); document.getElementById('badge-{{ $student->id }}').textContent='{{ ucfirst(str_replace('_',' ',$status)) }}'">
                <span class="w-10 h-10 rounded-xl border-2 border-slate-200 flex items-center justify-center text-sm font-bold text-slate-400 peer-checked:border-{{ $clr }}-500 peer-checked:bg-{{ $clr }}-50 peer-checked:text-{{ $clr }}-600 transition-all">{{ $lbl }}</span>
                <span class="text-[10px] text-slate-400">{{ str_replace('_',' ',ucfirst($status)) }}</span>
              </label>
              @endforeach
            </div>
            <input type="text" name="remarks[{{ $student->id }}]"
              value="{{ $existing[$student->id]?->remark ?? '' }}"
              placeholder="Reason (optional)"
              class="input text-xs py-1.5 w-full" id="remark-{{ $student->id }}">
          </div>
        @endforeach
      </div>

      {{-- ── DESKTOP table view (hidden on mobile) ── --}}
      <div class="hidden md:block table-wrap">
        <table class="w-full">
          <thead><tr>
            <th class="th">Roll</th><th class="th">Student</th>
            <th class="th text-center">Present</th><th class="th text-center">Absent</th>
            <th class="th text-center">Late</th><th class="th text-center">Half Day</th><th class="th text-center">Leave</th>
            <th class="th">Arrival Time</th>
            <th class="th">Reason</th>
          </tr></thead>
          <tbody>
            @foreach($students as $student)
              @php $current = $existing[$student->id]?->status ?? 'present'; $existingRemark = $existing[$student->id]?->remark ?? ''; @endphp
              <tr class="tr" id="row-{{ $student->id }}">
                <td class="td font-mono">{{ $student->currentEnrollment?->roll_number ?? '—' }}</td>
                <td class="td font-medium text-slate-800">{{ $student->full_name }}</td>
                @foreach(['present','absent','late','half_day','leave'] as $status)
                  <td class="td text-center">
                    <input type="radio" name="attendance[{{ $student->id }}]" value="{{ $status }}"
                      @checked($current === $status)
                      class="w-4 h-4"
                      onchange="highlightRow({{ $student->id }}, '{{ $status }}')">
                  </td>
                @endforeach
                <td class="td">
                    <input type="time" name="arrival_time[{{ $student->id }}]"
                        value="{{ $existing[$student->id]?->arrival_time ?? '' }}"
                        class="input text-xs py-1 w-28">
                </td>
                <td class="td">
                    <input type="text" name="remarks[{{ $student->id }}]"
                        value="{{ $existingRemark }}"
                        placeholder="Optional reason"
                        class="input text-xs py-1 w-36 @if($current === 'present') opacity-40 @endif"
                        id="remark-{{ $student->id }}">
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if($isPastCutoff)
      <div class="alert-warning mt-4">
          Attendance cutoff time ({{ $cutoff }}) has passed.
          <label class="ml-3 flex items-center gap-2 cursor-pointer inline-flex">
              <input type="checkbox" name="override_cutoff" value="1" class="w-4 h-4">
              <span class="text-sm font-medium">Override cutoff (requires justification)</span>
          </label>
      </div>
      @endif

      <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-4">
        <a href="{{ route('attendance.index') }}" class="btn btn-secondary w-full sm:w-auto text-center">Cancel</a>
        <button type="submit" class="btn btn-primary w-full sm:w-auto">Save Attendance</button>
      </div>
    </form>
  @elseif($classId)
    <div class="card text-center py-12 text-slate-400">No students found for this class.</div>
  @else
    <div class="card text-center py-12 text-slate-400">Select a class to mark attendance.</div>
  @endif
</div>
@endsection
@push('scripts')
<script>
function markAll(status) {
  document.querySelectorAll(`input[type=radio][value=${status}]`).forEach(r => {
    r.checked = true;
    highlightRow(r.name.match(/\d+/)[0], status);
  });
}
function highlightRow(id, status) {
  const row = document.getElementById('row-' + id);
  row.className = 'tr ' + (status === 'absent' ? 'bg-red-50' : status === 'late' ? 'bg-amber-50' : status === 'half_day' ? 'bg-orange-50' : status === 'leave' ? 'bg-blue-50' : '');
}
</script>
@endpush
