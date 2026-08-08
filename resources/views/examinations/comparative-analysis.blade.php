@extends('layouts.app')
@section('title', 'Comparative Analysis')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Comparative Analysis</h1>
    <div class="flex gap-2 items-center">
      @if(request('exam_id'))
      <a href="{{ route('examinations.comparative-analysis.pdf', request()->only('exam_id')) }}"
         target="_blank" class="btn btn-secondary btn-sm">Export PDF</a>
      @endif
      <a href="{{ route('examinations.index') }}" class="btn btn-secondary btn-sm">Back to Exams</a>
    </div>
  </div>

  {{-- Filter --}}
  <div class="card">
    <form method="GET" class="flex gap-3 items-end flex-wrap">
      <div class="flex-1 min-w-[200px]">
        <label class="label text-xs">Select Exam <span class="text-red-500">*</span></label>
        <select name="exam_id" class="select text-sm" onchange="this.form.submit()">
          <option value="">-- Choose Exam --</option>
          @foreach($exams as $e)
            <option value="{{ $e->id }}" @selected(request('exam_id') == $e->id)>{{ $e->name }}</option>
          @endforeach
        </select>
      </div>
    </form>
  </div>

  @if($exam)
  {{-- Summary Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="card text-center">
      <p class="text-xs text-slate-500 uppercase tracking-wide">Exam</p>
      <p class="font-bold text-slate-800 text-lg mt-1">{{ $exam->name }}</p>
    </div>
    <div class="card text-center">
      <p class="text-xs text-slate-500 uppercase tracking-wide">Classes Analysed</p>
      <p class="font-bold text-slate-800 text-3xl mt-1">{{ $classData->count() }}</p>
    </div>
    <div class="card text-center">
      <p class="text-xs text-slate-500 uppercase tracking-wide">School Overall Average</p>
      <p class="font-bold text-{{ $schoolAvg >= 60 ? 'green' : ($schoolAvg >= 40 ? 'amber' : 'red') }}-600 text-3xl mt-1">
        {{ $schoolAvg ?? '—' }}%
      </p>
    </div>
  </div>

  @if($classData->isEmpty())
    <div class="card text-center py-12 text-slate-400">No marks data found for this exam.</div>
  @else

  {{-- Overall Comparison Table --}}
  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4">Class vs School Average</h2>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th text-left">Class</th>
            <th class="th text-center">Class Average</th>
            <th class="th text-center">School Average</th>
            <th class="th text-center">Difference</th>
            <th class="th text-center">Performance Bar</th>
          </tr>
        </thead>
        <tbody>
          @foreach($classData as $row)
          @php
            $diff     = round($row['overall_avg'] - ($schoolAvg ?? 0), 1);
            $aboveAvg = $diff >= 0;
            $barPct   = min(100, (int) $row['overall_avg']);
          @endphp
          <tr class="tr">
            <td class="td font-medium text-slate-800">{{ $row['class']->name }}</td>
            <td class="td text-center font-semibold text-slate-700">{{ $row['overall_avg'] }}%</td>
            <td class="td text-center text-slate-500">{{ $schoolAvg ?? '—' }}%</td>
            <td class="td text-center">
              <span class="inline-flex items-center gap-1 font-medium {{ $aboveAvg ? 'text-green-600' : 'text-red-500' }}">
                @if($aboveAvg)
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                  +{{ $diff }}%
                @else
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                  {{ $diff }}%
                @endif
              </span>
            </td>
            <td class="td">
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-100 rounded-full h-2">
                  <div class="h-2 rounded-full {{ $aboveAvg ? 'bg-green-500' : 'bg-amber-400' }}"
                       style="width: {{ $barPct }}%"></div>
                </div>
                <span class="text-xs text-slate-500 w-8 text-right">{{ $barPct }}%</span>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Subject-wise Breakdown --}}
  @if(!empty($subjectSchoolAvg))
  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4">Subject-wise: Class Average vs School Average</h2>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th text-left">Class</th>
            @foreach(array_keys($subjectSchoolAvg) as $subj)
              <th class="th text-center" colspan="2">{{ $subj }}</th>
            @endforeach
          </tr>
          <tr>
            <th class="th"></th>
            @foreach(array_keys($subjectSchoolAvg) as $subj)
              <th class="th text-center text-xs text-slate-500 font-normal">Class</th>
              <th class="th text-center text-xs text-slate-500 font-normal">School</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($classData as $row)
          <tr class="tr">
            <td class="td font-medium text-slate-800">{{ $row['class']->name }}</td>
            @foreach(array_keys($subjectSchoolAvg) as $subj)
            @php
              $classSubjAvg  = $row['subject_avgs'][$subj] ?? null;
              $schoolSubjAvg = $subjectSchoolAvg[$subj];
              $above         = $classSubjAvg !== null && $classSubjAvg >= $schoolSubjAvg;
            @endphp
            <td class="td text-center {{ $classSubjAvg !== null ? ($above ? 'text-green-600 font-semibold' : 'text-red-500 font-semibold') : 'text-slate-300' }}">
              {{ $classSubjAvg ?? '—' }}
            </td>
            <td class="td text-center text-slate-400 text-xs">{{ $schoolSubjAvg }}</td>
            @endforeach
          </tr>
          @endforeach
          {{-- School Average Row --}}
          <tr class="bg-slate-50 border-t-2 border-slate-200">
            <td class="td font-bold text-slate-700">School Average</td>
            @foreach($subjectSchoolAvg as $avg)
            <td class="td text-center font-bold text-blue-600">{{ $avg }}</td>
            <td class="td"></td>
            @endforeach
          </tr>
        </tbody>
      </table>
    </div>
    <p class="text-xs text-slate-400 mt-2">
      <span class="text-green-600 font-medium">Green</span> = above school average &nbsp;|&nbsp;
      <span class="text-red-500 font-medium">Red</span> = below school average
    </p>
  </div>
  @endif

  @endif {{-- classData not empty --}}
  @endif {{-- exam selected --}}
</div>
@endsection
