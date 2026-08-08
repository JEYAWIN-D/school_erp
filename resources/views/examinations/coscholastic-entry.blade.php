@extends('layouts.app')
@section('title', 'Co-Scholastic Grading')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Co-Scholastic Grading</h1>
      <p class="text-slate-500 text-sm mt-0.5">{{ $exam->name }}</p>
    </div>
    <a href="{{ route('examinations.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <form method="GET" class="card-flat py-3">
    <input type="hidden" name="exam_id" value="{{ $exam->id }}">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label">Class</label>
        <select name="class_id" required class="select w-40">
          <option value="">Select Class</option>
          @foreach($classes as $c)
            <option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Load</button>
    </div>
  </form>

  @if($students->count() && $subjects->count())
  <form method="POST" action="{{ route('examinations.coscholastic.save', $exam->id) }}">
    @csrf
    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
    <div class="card overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr>
            <th class="th sticky left-0 bg-white z-10">Student</th>
            @foreach($subjects as $s)
            <th class="th text-center">{{ $s->subject?->name }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($students as $student)
          <tr class="hover:bg-slate-50">
            <td class="td sticky left-0 bg-white z-10 font-medium">
              {{ $student->full_name }}
              <div class="text-xs text-slate-400">{{ $student->admission_number }}</div>
            </td>
            @foreach($subjects as $schedule)
            @php
              $existingGrade = $entries[$student->id][$schedule->id]['grade'] ?? ($entries[$student->id]?->where('exam_schedule_id', $schedule->id)->first()?->grade ?? '');
            @endphp
            <td class="td text-center">
              <select name="grades[{{ $student->id }}][{{ $schedule->id }}]" class="select text-center w-20 py-1 text-sm">
                <option value="">—</option>
                @foreach(['A1','A2','B1','B2','C1','C2','D','E'] as $g)
                  <option value="{{ $g }}" @selected($existingGrade===$g)>{{ $g }}</option>
                @endforeach
              </select>
            </td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="flex justify-end mt-4">
      <button type="submit" class="btn btn-primary">Save Co-Scholastic Grades</button>
    </div>
  </form>
  @elseif(request('class_id'))
    <div class="card text-center py-10 text-slate-400">
      @if(!$subjects->count())
        No co-scholastic subjects found for this class in this exam.
        <p class="text-xs mt-1">Mark subjects as "Co-Scholastic" in Subject Management to enable grade entry.</p>
      @else
        No students found for this class.
      @endif
    </div>
  @endif
</div>
@endsection
