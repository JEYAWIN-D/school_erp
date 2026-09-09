@extends('layouts.app')
@section('title', 'Class Result Summary')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Class Result Summary</h1>
      <p class="page-subtitle">Rank-wise breakdown &amp; subject performance for an exam and class</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('examinations.subject-performance', request()->only('exam_id','class_id')) }}" class="btn btn-secondary btn-sm">
        Subject Performance Report
      </a>
      <a href="{{ route('examinations.index') }}" class="btn btn-secondary btn-sm">Back to Exams</a>
    </div>
  </div>

  {{-- Filters --}}
  <div class="card bg-slate-50 border border-slate-200">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label font-semibold text-xs text-slate-700">Exam *</label>
        <select name="exam_id" class="select text-sm py-1.5" required onchange="this.form.submit()">
          <option value="">— Select Exam —</option>
          @foreach($exams as $e)
          <option value="{{ $e->id }}" {{ request('exam_id') == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label font-semibold text-xs text-slate-700">Class *</label>
        <select name="class_id" class="select text-sm py-1.5" required onchange="this.form.submit()">
          <option value="">— Select Class —</option>
          @foreach($classes as $c)
          <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label font-semibold text-xs text-slate-700">Subject Filter</label>
        <select name="subject_id" class="select text-sm py-1.5 min-w-[200px]" onchange="this.form.submit()">
          <option value="">All Subjects (Overall Rank)</option>
          @if(isset($subjects) && $subjects->isNotEmpty())
          @foreach($subjects as $sub)
          <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
          @endforeach
          @endif
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      @if(request('exam_id') && request('class_id'))
      <a href="{{ route('examinations.class-result.pdf', request()->only('exam_id','class_id','subject_id')) }}"
         target="_blank" class="btn btn-secondary btn-sm">PDF Export</a>
      <a href="{{ route('examinations.class-result.excel', request()->only('exam_id','class_id','subject_id')) }}"
         class="btn btn-secondary btn-sm">Excel Export</a>
      @endif
    </form>
  </div>

  @if($summary->isNotEmpty())
  @php
    $passCount    = $summary->where('hasFail', false)->count();
    $failCount    = $summary->where('hasFail', true)->count();
    $classAverage = round($summary->avg('percentage'), 1);
  @endphp

  {{-- KPI Overview --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card text-center py-5 border-t-4 border-slate-600">
      <p class="text-3xl font-bold text-slate-700">{{ $summary->count() }}</p>
      <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mt-1">Total Students</p>
    </div>
    <div class="card text-center py-5 border-t-4 border-green-500">
      <p class="text-3xl font-bold text-green-600">{{ $passCount }}</p>
      <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mt-1">Passed ({{ $summary->count() > 0 ? round(($passCount/$summary->count())*100) : 0 }}%)</p>
    </div>
    <div class="card text-center py-5 border-t-4 border-red-500">
      <p class="text-3xl font-bold text-red-500">{{ $failCount }}</p>
      <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mt-1">Failed</p>
    </div>
    <div class="card text-center py-5 border-t-4 border-indigo-500">
      <p class="text-3xl font-bold text-indigo-600">{{ $classAverage }}%</p>
      <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mt-1">{{ $selectedSubject ? $selectedSubject->name . ' Avg' : 'Class Average' }}</p>
    </div>
  </div>

  {{-- Subject-wise Summary Matrix Strip --}}
  @if(isset($subjectSummary) && $subjectSummary->isNotEmpty())
  <div class="card">
    <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
        <h3 class="font-bold text-slate-800 text-sm">Subject Performance Summary for Class {{ $class->name ?? '' }}</h3>
      </div>
      <p class="text-xs text-slate-400">Click a subject card to view subject-specific ranking</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
      @foreach($subjectSummary as $ss)
      @php $isCurrent = request('subject_id') == $ss['subject_id']; @endphp
      <a href="{{ route('examinations.class-result', ['exam_id' => request('exam_id'), 'class_id' => request('class_id'), 'subject_id' => $ss['subject_id']]) }}"
         class="p-3 rounded-xl border transition {{ $isCurrent ? 'bg-indigo-50 border-indigo-300 ring-2 ring-indigo-200' : 'bg-white hover:bg-slate-50 border-slate-200 hover:border-slate-300' }}">
        <div class="flex items-center justify-between">
          <span class="font-bold text-xs {{ $isCurrent ? 'text-indigo-800' : 'text-slate-800' }} truncate">{{ $ss['subject_name'] }}</span>
          @if($ss['failed'] > 0)
          <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">{{ $ss['failed'] }} Fail</span>
          @else
          <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700">100% Pass</span>
          @endif
        </div>
        <div class="mt-2 flex items-baseline justify-between">
          <span class="text-lg font-extrabold text-slate-700">{{ $ss['avg'] }}</span>
          <span class="text-[11px] text-slate-400">Max {{ $ss['max_marks'] }}</span>
        </div>
        <div class="mt-1 flex items-center justify-between text-[11px] text-slate-500">
          <span>Pass: <strong class="text-slate-700">{{ $ss['pass_pct'] }}%</strong></span>
          <span>Top: <strong class="text-green-600">{{ $ss['highest'] }}</strong></span>
        </div>
      </a>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Results Table --}}
  <div class="card p-0 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3 bg-slate-50/50">
      <div>
        <h2 class="font-bold text-slate-800 text-base">
          {{ $selectedSubject ? $selectedSubject->name . ' — Result List' : 'Overall Class Rank List' }}
          @if($selectedSubject)
          <span class="ml-2 text-xs font-medium text-indigo-700 bg-indigo-100 px-2.5 py-0.5 rounded-full">Subject Mode</span>
          @endif
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">
          {{ $exam->name ?? '' }} &bull; Class {{ $class->name ?? '' }}
          @if($selectedSubject)
          &bull; <a href="{{ route('examinations.class-result', ['exam_id' => request('exam_id'), 'class_id' => request('class_id')]) }}" class="text-indigo-600 hover:underline">View All Subjects</a>
          @endif
        </p>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('examinations.tabulation.pdf', request()->only(['exam_id','class_id'])) }}" class="btn btn-secondary btn-sm">
          Tabulation PDF
        </a>
      </div>
    </div>

    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th text-center" style="width: 70px;">Rank</th>
            <th class="th">Student Name</th>
            <th class="th">Adm No</th>
            @if($selectedSubject)
            <th class="th">Subject</th>
            <th class="th text-center">Marks Obtained</th>
            <th class="th text-center">Max Marks</th>
            <th class="th text-center">Percentage</th>
            <th class="th text-center">Grade</th>
            @else
            <th class="th text-center">Marks Obtained</th>
            <th class="th text-center">Total Marks</th>
            <th class="th text-center">Percentage</th>
            <th class="th">Subject Breakdown</th>
            @endif
            <th class="th text-center">Result</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($summary as $i => $row)
          <tr class="tr hover:bg-slate-50 transition">
            <td class="td text-center">
              @if($i < 3)
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white text-xs font-bold {{ ['bg-amber-500','bg-slate-400','bg-orange-700'][$i] }}">{{ $i+1 }}</span>
              @else
                <span class="text-slate-500 font-semibold">{{ $i+1 }}</span>
              @endif
            </td>
            <td class="td font-medium">
              <a href="{{ route('examinations.student-result-history', ['student_id' => $row['student']->id]) }}" class="text-indigo-600 hover:underline font-semibold">
                {{ $row['student']->first_name }} {{ $row['student']->last_name }}
              </a>
            </td>
            <td class="td text-slate-500 font-mono text-xs">{{ $row['student']->admission_no ?? $row['student']->admission_number ?? '—' }}</td>

            @if($selectedSubject)
            <td class="td font-semibold text-slate-700">{{ $selectedSubject->name }}</td>
            <td class="td text-center font-bold text-slate-800">{{ $row['obtained'] }}</td>
            <td class="td text-center text-slate-500">{{ $row['totalMarks'] }}</td>
            <td class="td text-center font-semibold text-slate-700">{{ $row['percentage'] }}%</td>
            <td class="td text-center">
              <span class="px-2 py-0.5 rounded text-xs font-bold {{ $row['percentage'] >= 75 ? 'bg-green-100 text-green-700' : ($row['percentage'] >= 50 ? 'bg-indigo-100 text-indigo-700' : ($row['hasFail'] ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')) }}">
                {{ $row['grade'] ?? '—' }}
              </span>
            </td>
            @else
            <td class="td text-center font-bold text-slate-800">{{ $row['obtained'] }}</td>
            <td class="td text-center text-slate-500">{{ $row['totalMarks'] }}</td>
            <td class="td text-center">
              <div class="flex items-center justify-center gap-2">
                <div class="w-16 bg-slate-200 rounded-full h-2 overflow-hidden">
                  <div class="h-2 rounded-full {{ $row['percentage'] >= 75 ? 'bg-green-500' : ($row['percentage'] >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width:{{ min(100, $row['percentage']) }}%"></div>
                </div>
                <span class="text-xs font-semibold text-slate-700">{{ $row['percentage'] }}%</span>
              </div>
            </td>
            <td class="td">
              <div class="flex flex-wrap gap-1 max-w-md">
                @if(isset($row['subject_scores']))
                  @foreach($row['subject_scores'] as $subId => $sScore)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium {{ $sScore['passed'] ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-red-100 text-red-700 border border-red-200 font-bold' }}" title="{{ $sScore['name'] }}: {{ $sScore['obtained'] }}/{{ $sScore['max'] }} (Pass: {{ $sScore['pass'] }})">
                    <span>{{ $sScore['code'] ?: substr($sScore['name'], 0, 4) }}:</span>
                    <strong>{{ $sScore['obtained'] }}</strong>
                  </span>
                  @endforeach
                @else
                  <span class="text-slate-400 text-xs">—</span>
                @endif
              </div>
            </td>
            @endif

            <td class="td text-center">
              @if($row['hasFail'])
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Fail</span>
              @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Pass</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @elseif(request('exam_id') && request('class_id'))
  <div class="card text-center py-12 text-slate-400">
    <p class="font-medium text-base text-slate-600">No student enrollment or marks records found.</p>
    <p class="text-xs text-slate-400 mt-1">Ensure students are enrolled in this class and exam schedules have been marked.</p>
  </div>
  @else
  <div class="card text-center py-12 text-slate-400">
    <p class="font-medium text-base text-slate-600">Please select an Exam and Class to view the Result Summary.</p>
  </div>
  @endif
</div>
@endsection
