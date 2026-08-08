@extends('layouts.app')
@section('title','Tabulation Register')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Tabulation Register</h1>
    @if(isset($enrollments) && $enrollments->count())
    <div class="flex gap-2">
      <a href="{{ route('examinations.tabulation.export', request()->query()) }}" class="btn btn-secondary btn-sm">Export Excel</a>
      <a href="{{ route('examinations.tabulation.pdf', request()->query()) }}" class="btn btn-secondary btn-sm">Tabulation PDF</a>
    </div>
    @endif
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="exam_id" class="select w-44">
      <option value="">Select Exam</option>
      @foreach($exams as $e)<option value="{{ $e->id }}" @selected(request('exam_id')==$e->id)>{{ $e->name }}</option>@endforeach
    </select>
    <select name="class_id" class="select w-36">
      <option value="">Select Class</option>
      @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <select name="section_id" class="select w-36">
      <option value="">Section</option>
      @foreach($sections as $s)<option value="{{ $s->id }}" @selected(request('section_id')==$s->id)>{{ $s->name }}</option>@endforeach
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </div></form>

  @if(isset($enrollments) && $enrollments->count())
  <div class="card overflow-x-auto">
    <table class="text-xs min-w-max">
      <thead class="bg-slate-50 border-b">
        <tr>
          <th class="sticky left-0 bg-slate-50 text-left px-4 py-3 text-slate-500 uppercase font-medium">Student</th>
          @foreach($subjects as $sub)
          <th class="px-3 py-3 text-center text-slate-500 font-medium min-w-[80px]">
            {{ $sub->name }}<br><span class="text-slate-300 font-normal">/ {{ $sub->pivot->max_marks }}</span>
          </th>
          @endforeach
          <th class="px-3 py-3 text-center text-slate-500 font-medium">Total</th>
          <th class="px-3 py-3 text-center text-slate-500 font-medium">%</th>
          <th class="px-3 py-3 text-center text-slate-500 font-medium">Grade</th>
          <th class="px-3 py-3 text-center text-slate-500 font-medium">Result</th>
          <th class="px-3 py-3 text-center text-slate-500 font-medium">Rank</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($enrollments as $enr)
        @php $sm = $marksMap[$enr->id] ?? []; @endphp
        <tr class="hover:bg-slate-50">
          <td class="sticky left-0 bg-white px-4 py-2 font-medium text-slate-800">{{ $enr->student?->full_name }}</td>
          @foreach($subjects as $sub)
          @php $m = $sm[$sub->id] ?? null; @endphp
          <td class="px-3 py-2 text-center">
            @if($m)
              <span class="{{ $m->marks_obtained < ($sub->pivot->pass_marks ?? 0) ? 'text-red-500 font-semibold' : 'text-slate-700' }}">{{ $m->marks_obtained }}</span>
              @if($m->is_absent)<span class="text-xs text-slate-400 ml-1">AB</span>@endif
            @else<span class="text-slate-300">—</span>@endif
          </td>
          @endforeach
          @php
            $total = collect($sm)->sum('marks_obtained');
            $maxTotal = $subjects->sum(fn($s)=>$s->pivot->max_marks??100);
            $pct = $maxTotal > 0 ? round($total/$maxTotal*100,1) : 0;
          @endphp
          <td class="px-3 py-2 text-center font-semibold">{{ $total }}</td>
          <td class="px-3 py-2 text-center font-semibold {{ $pct < 35 ? 'text-red-500' : '' }}">{{ $pct }}%</td>
          <td class="px-3 py-2 text-center font-bold text-indigo-600">{{ $gradingScheme?->gradeFor($pct) ?? '—' }}</td>
          <td class="px-3 py-2 text-center">
            @php $hasFail = collect($sm)->filter(fn($m)=>$m->marks_obtained < ($subjects->find($m->exam_schedule?->subject_id)?->pivot->pass_marks??35))->count(); @endphp
            <span class="{{ $hasFail ? 'text-red-500' : 'text-green-600' }} font-semibold">{{ $hasFail ? 'FAIL' : 'PASS' }}</span>
          </td>
          <td class="px-3 py-2 text-center text-slate-500">{{ $ranks[$enr->id] ?? '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  <div class="card text-center py-12 text-slate-400">Select exam and class to load tabulation register.</div>
  @endif
</div>
@endsection
