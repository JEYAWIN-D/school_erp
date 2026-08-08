@extends('layouts.app')
@section('title','Results — ' . $exam->name)
@section('content')
<div class="space-y-6">
  <div class="flex items-center gap-4">
    <a href="{{ route('examinations.index') }}" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <div>
      <h1 class="page-title">{{ $exam->name }} — Results</h1>
      <p class="text-sm text-slate-500">{{ $exam->start_date?->format('d M') }} – {{ $exam->end_date?->format('d M Y') }} &nbsp;·&nbsp; Passing: {{ $exam->passing_percentage ?? 35 }}%</p>
    </div>
  </div>

  {{-- Schedules management panel --}}
  <div class="card" x-data="{open:false}">
    <div class="flex items-center justify-between cursor-pointer" @click="open=!open">
      <h3 class="font-semibold text-slate-700">Exam Schedules ({{ $exam->schedules->count() }})</h3>
      <button class="btn btn-primary btn-sm">+ Add Schedule</button>
    </div>
    @if($exam->schedules->count())
    <div class="mt-3 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100"><tr>
          @foreach(['Date','Subject','Class','Time','Max Marks','Min Marks','Room',''] as $h)
          <th class="text-left px-3 py-2 text-xs text-slate-500 font-medium uppercase tracking-wide">{{ $h }}</th>
          @endforeach
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($exam->schedules as $sch)
          <tr class="hover:bg-slate-50">
            <td class="px-3 py-2">{{ $sch->exam_date?->format('d M Y') }}</td>
            <td class="px-3 py-2 font-medium text-slate-800">{{ $sch->subject?->name }}</td>
            <td class="px-3 py-2 text-slate-600">{{ $sch->class?->name }}</td>
            <td class="px-3 py-2 text-slate-400 text-xs">{{ $sch->start_time ?? '—' }} – {{ $sch->end_time ?? '—' }}</td>
            <td class="px-3 py-2 text-center">{{ $sch->max_marks ?? 100 }}</td>
            <td class="px-3 py-2 text-center">{{ $sch->pass_marks ?? 35 }}</td>
            <td class="px-3 py-2 text-slate-400">{{ $sch->venue ?? '—' }}</td>
            <td class="px-3 py-2">
              <form method="POST" action="{{ route('examinations.schedules.destroy', $sch->id) }}" onsubmit="return confirm('Remove this schedule?')">@csrf @method('DELETE')
                <button class="text-red-400 hover:text-red-600 text-xs">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
    {{-- Add schedule form --}}
    <div class="mt-4 border-t border-slate-100 pt-4" x-show="open" style="display:none">
      <form method="POST" action="{{ route('examinations.schedules.store', $exam->id) }}" class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @csrf
        <div><label class="label">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select" required>
            <option value="">Select</option>
            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="label">Subject <span class="text-red-500">*</span></label>
          <select name="subject_id" class="select" required>
            <option value="">Select</option>
            @foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="label">Date <span class="text-red-500">*</span></label>
          <input type="date" name="exam_date" class="input" required value="{{ $exam->start_date?->toDateString() }}">
        </div>
        <div><label class="label">Start Time</label><input type="time" name="start_time" class="input"></div>
        <div><label class="label">End Time</label><input type="time" name="end_time" class="input"></div>
        <div><label class="label">Max Marks</label><input type="number" name="max_marks" class="input" value="100" min="1"></div>
        <div><label class="label">Pass Marks</label><input type="number" name="min_marks" class="input" value="35" min="0"></div>
        <div><label class="label">Venue/Room</label><input type="text" name="room_no" class="input" placeholder="e.g. A101"></div>
        <div class="col-span-2 md:col-span-4 flex gap-2">
          <button type="submit" class="btn btn-primary btn-sm">Add Schedule</button>
          <button type="button" @click="open=false" class="btn btn-secondary btn-sm">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert-danger">{{ session('error') }}</div>@endif

  @php
    // Build schedule index: class_id -> [subject_id -> schedule]
    $schedulesByClass = $exam->schedules->groupBy('class_id');
  @endphp

  @forelse($schedulesByClass as $classId => $schedules)
  @php
    $className = $schedules->first()?->class?->name ?? 'Class';
    // Collect all unique students who have any mark in this exam + class
    $studentIds = $schedules->flatMap(fn($s) => $s->marks->pluck('student_id'))->unique();
    $students   = \App\Models\Student::whereIn('id', $studentIds)->orderBy('first_name')->get()->keyBy('id');
    $passing    = $exam->passing_percentage ?? 35;
  @endphp
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-700">{{ $className }}</h3>
      <span class="text-xs text-slate-400">{{ $students->count() }} students · {{ $schedules->count() }} subjects</span>
    </div>
    @if($students->isEmpty())
      <div class="px-4 py-6 text-slate-400 text-sm text-center">No marks entered yet for this class.</div>
    @else
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="text-left px-3 py-2 text-xs text-slate-500 font-medium uppercase tracking-wide">Student</th>
            @foreach($schedules as $sch)
            <th class="text-center px-3 py-2 text-xs text-slate-500 font-medium uppercase tracking-wide" title="{{ $sch->subject?->name }}">
              {{ Str::limit($sch->subject?->name ?? '—', 10) }}<br>
              <span class="font-normal normal-case text-slate-400">/{{ $sch->max_marks ?? 100 }}</span>
            </th>
            @endforeach
            <th class="text-center px-3 py-2 text-xs text-slate-500 font-medium uppercase tracking-wide">Total</th>
            <th class="text-center px-3 py-2 text-xs text-slate-500 font-medium uppercase tracking-wide">%</th>
            <th class="text-center px-3 py-2 text-xs text-slate-500 font-medium uppercase tracking-wide">Result</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($students as $student)
          @php
            $totalObtained = 0;
            $totalMax      = 0;
            $anyAbsent     = false;
          @endphp
          <tr class="hover:bg-slate-50">
            <td class="px-3 py-2">
              <p class="font-medium text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</p>
              <p class="text-xs text-slate-400">{{ $student->admission_no }}</p>
            </td>
            @foreach($schedules as $sch)
            @php
              $mark = $sch->marks->firstWhere('student_id', $student->id);
              $max  = $sch->max_marks ?? 100;
              if ($mark) {
                $totalObtained += $mark->obtained_marks ?? 0;
                $totalMax += $max;
                if ($mark->is_absent ?? false) $anyAbsent = true;
              } else {
                $totalMax += $max;
              }
            @endphp
            <td class="px-3 py-2 text-center">
              @if(!$mark)
                <span class="text-slate-300">—</span>
              @elseif($mark->is_absent ?? false)
                <span class="badge-red text-xs">AB</span>
              @else
                @php $subPct = $max > 0 ? ($mark->obtained_marks / $max * 100) : 0; @endphp
                <span class="{{ $subPct < $passing ? 'text-red-600 font-semibold' : 'text-slate-700' }}">
                  {{ $mark->obtained_marks }}
                </span>
              @endif
            </td>
            @endforeach
            @php
              $pct = $totalMax > 0 ? round($totalObtained / $totalMax * 100, 1) : 0;
              $pass = !$anyAbsent && $pct >= $passing;
            @endphp
            <td class="px-3 py-2 text-center font-semibold text-slate-700">{{ $totalObtained }}/{{ $totalMax }}</td>
            <td class="px-3 py-2 text-center font-semibold {{ $pct < $passing ? 'text-red-600' : 'text-emerald-600' }}">{{ $pct }}%</td>
            <td class="px-3 py-2 text-center">
              @if($anyAbsent)
                <span class="badge-amber text-xs">AB</span>
              @elseif($pass)
                <span class="badge-green text-xs">PASS</span>
              @else
                <span class="badge-red text-xs">FAIL</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
  @empty
  <div class="card text-center py-12 text-slate-400">No exam schedules or marks entered for this exam yet.</div>
  @endforelse
</div>
@endsection
