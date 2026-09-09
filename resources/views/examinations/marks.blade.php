@extends('layouts.app')
@section('title','Enter Marks — ' . $exam->name)
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-4">
      <a href="{{ route('examinations.index') }}" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
      <div>
        <h1 class="page-title">{{ $exam->name }} — Enter Marks</h1>
        @if($exam->marks_locked)
        <p class="text-sm text-red-600 font-medium mt-0.5">Locked on {{ $exam->marks_locked_at?->format('d M Y, h:i A') }}</p>
        @endif
      </div>
    </div>
    <div class="flex gap-2 flex-wrap">
      @if($exam->marks_locked)
      <form method="POST" action="{{ route('examinations.marks.unlock', $exam->id) }}">
        @csrf <button class="btn btn-secondary btn-sm">Unlock Marks</button>
      </form>
      @else
      <form method="POST" action="{{ route('examinations.marks.lock', $exam->id) }}">
        @csrf <button class="btn btn-primary btn-sm" onclick="return confirm('Lock marks?')">Lock Marks</button>
      </form>
      @endif
      @if(request('class_id'))
      <a href="{{ route('examinations.recheck-requests') }}" class="btn btn-secondary btn-sm">Recheck Requests</a>
      @endif
    </div>
  </div>

  @if($exam->marks_locked)
  <div class="alert-warning">Marks are locked. Unlock first to make changes.</div>
  @endif

  {{-- Exam and Class selector (All Exam Names Dropdown) --}}
  <div class="card bg-slate-50 border border-slate-200">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div class="flex flex-wrap items-end gap-3 flex-1">
        <div>
          <label class="label text-xs font-semibold text-slate-700">Exam (All Exam Names)</label>
          <select id="exam_switcher" class="select text-sm py-1.5 min-w-[240px]" onchange="if(this.value && this.value != '{{ $exam->id }}'){ window.location.href = '/examinations/' + this.value + '/marks' + (document.getElementById('marks_class_id').value ? '?class_id=' + document.getElementById('marks_class_id').value : ''); }">
            @foreach($allExams ?? [$exam] as $e)
              <option value="{{ $e->id }}" @selected($e->id == $exam->id)>{{ $e->name }} ({{ str_replace('_',' ',$e->type) }})</option>
            @endforeach
          </select>
        </div>
        <form method="GET" class="flex gap-2 items-end">
          <div>
            <label class="label text-xs font-semibold text-slate-700">Select Class</label>
            <select name="class_id" id="marks_class_id" class="select w-44 text-sm py-1.5">
              <option value="">— Choose Class —</option>
              @foreach($classes as $c)
              <option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-primary btn-sm">Load Students</button>
        </form>
      </div>
      @if($exam->schedules->count())
      <div class="text-right text-xs text-slate-500">
        <span class="font-semibold text-slate-700">{{ $exam->schedules->count() }}</span> scheduled subjects for this exam
      </div>
      @endif
    </div>
  </div>

  @if(request('class_id') && $schedules->count() && $enrollments->count())
  {{-- Grace marks section --}}
  @if(!$exam->marks_locked)
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-3 pb-2 border-b border-slate-100 text-sm">Grace Marks Configuration (per subject)</h3>
    <form method="POST" action="{{ route('examinations.grace-marks', $exam->id) }}" class="flex flex-wrap gap-4 items-end">
      @csrf
      @foreach($schedules as $sched)
      <div>
        <label class="label text-xs">{{ $sched->subject?->name }}</label>
        <input type="number" name="grace[{{ $sched->id }}]" class="input w-20 text-sm"
          value="{{ $sched->grace_marks ?? 0 }}" min="0" max="10" step="1">
      </div>
      @endforeach
      <button type="submit" class="btn btn-secondary btn-sm mt-4">Save Grace Marks</button>
    </form>
  </div>
  @endif

  {{-- Marks entry grid --}}
  @php
    $totalStudents = $enrollments->count();
    $markedPerSched = [];
    foreach($schedules as $sched) {
      $markedPerSched[$sched->id] = collect($existingMarks)->filter(
        fn($m,$k) => str_starts_with((string)$k, $sched->id . '_') && ($m->marks_obtained !== null || $m->is_absent)
      )->count();
    }
  @endphp
  <div class="card overflow-x-auto">
    <form method="POST" action="{{ route('examinations.marks.save', $exam->id) }}">
      @csrf
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase sticky left-0 bg-slate-50">Roll</th>
            <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase sticky left-12 bg-slate-50 min-w-40">Student</th>
            @foreach($schedules as $sched)
            @php $markedCount = $markedPerSched[$sched->id] ?? 0; $allDone = $markedCount >= $totalStudents; @endphp
            <th class="text-center px-3 py-3 text-slate-500 font-medium text-xs uppercase min-w-28">
              {{ $sched->subject?->name }}
              <div class="text-slate-400 font-normal">Max: {{ $sched->max_marks }}</div>
              @if($sched->grace_marks > 0)
              <div class="text-green-600 text-xs font-normal">+{{ $sched->grace_marks }} grace</div>
              @endif
              <div class="mt-1 text-xs font-normal {{ $allDone ? 'text-green-600' : 'text-amber-600' }}">
                {{ $markedCount }}/{{ $totalStudents }} marked{{ $allDone ? ' ✓' : '' }}
              </div>
            </th>
            @endforeach
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($enrollments as $enrollment)
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-2 text-slate-500 sticky left-0 bg-white">{{ $enrollment->roll_number }}</td>
            <td class="px-4 py-2 font-medium text-slate-800 sticky left-12 bg-white min-w-40">
              {{ $enrollment->student?->first_name }} {{ $enrollment->student?->last_name }}
            </td>
            @foreach($schedules as $sched)
            @php $key = $sched->id . '_' . $enrollment->student_id; $m = $existingMarks[$key] ?? null; @endphp
            <td class="px-2 py-1 text-center" x-data="{absent:{{ $m?->is_absent ? 'true' : 'false' }}}">
              <div class="flex flex-col items-center gap-1">
                <input type="number"
                  name="marks[{{ $sched->id }}][{{ $enrollment->student_id }}][marks]"
                  class="w-20 border border-slate-200 rounded px-2 py-1 text-center text-sm focus:ring-2 focus:ring-indigo-300"
                  value="{{ $m?->marks_obtained }}"
                  min="0" max="{{ $sched->max_marks }}" step="0.5"
                  :disabled="absent"
                  {{ $exam->marks_locked ? 'disabled' : '' }}>
                <label class="flex items-center gap-1 text-xs text-slate-400 cursor-pointer">
                  <input type="checkbox"
                    name="marks[{{ $sched->id }}][{{ $enrollment->student_id }}][absent]"
                    value="1"
                    x-model="absent"
                    {{ $m?->is_absent ? 'checked' : '' }}
                    {{ $exam->marks_locked ? 'disabled' : '' }}>
                  Absent
                </label>
              </div>
            </td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
      @if(!$exam->marks_locked)
      <div class="px-4 pb-4 pt-3 border-t border-slate-100">
        <button type="submit" class="btn btn-primary">Save Marks</button>
      </div>
      @endif
    </form>
  </div>
  @elseif(request('class_id') && !$schedules->count())
  <div class="card text-center py-8 text-slate-400">No exam schedules for this class.</div>
  @elseif(request('class_id') && !$enrollments->count())
  <div class="card text-center py-8 text-slate-400">No active enrollments for this class.</div>
  @elseif(!request('class_id'))
  <div class="card text-center py-8 text-slate-400">Select a class to start entering marks.</div>
  @endif
</div>
@endsection
