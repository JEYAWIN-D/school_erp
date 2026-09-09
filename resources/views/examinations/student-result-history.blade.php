@extends('layouts.app')
@section('title', 'Consolidated Student Report')
@section('content')
<div class="space-y-6" x-data="{ search: '{{ request('search') }}' }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Consolidated Student Academic Report</h1>
      <p class="page-subtitle">Complete 360&deg; performance report across all terms, exams, subjects &amp; attendance</p>
    </div>
    <div class="flex items-center gap-2">
      @if($student)
      <button onclick="window.print()" class="btn btn-secondary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Report
      </button>
      @endif
      <a href="{{ route('examinations.index') }}" class="btn btn-secondary btn-sm">Back to Exams</a>
    </div>
  </div>

  {{-- Search / Selector Card --}}
  <div class="card bg-slate-50 border border-slate-200">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div class="flex-1 min-w-[280px]">
        <label class="label font-semibold text-xs text-slate-700">Find Student</label>
        <div class="relative">
          <input type="text" name="search" value="{{ request('search') }}"
                 placeholder="Search student by name, admission number, or roll…"
                 class="input w-full pl-10 text-sm">
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Search Student</button>
    </form>

    @if(isset($students) && $students->count() > 0 && !$student)
    <div class="mt-4 border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-100 bg-white shadow-sm">
      <div class="px-4 py-2 bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">Matching Students ({{ $students->count() }})</div>
      @foreach($students as $s)
      <a href="{{ route('examinations.student-result-history', ['student_id' => $s->id]) }}"
         class="flex items-center justify-between px-4 py-3 hover:bg-indigo-50 transition group">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
            {{ substr($s->first_name, 0, 1) }}{{ substr($s->last_name, 0, 1) }}
          </div>
          <div>
            <div class="font-bold text-slate-800 text-sm group-hover:text-indigo-600 transition">{{ $s->first_name }} {{ $s->last_name }}</div>
            <div class="text-xs text-slate-400">Adm No: {{ $s->admission_no ?? $s->admission_number ?? '—' }} &bull; Class: {{ $s->enrollments->where('status','active')->first()?->class?->name ?? '—' }}</div>
          </div>
        </div>
        <span class="text-xs font-semibold text-indigo-600 group-hover:underline">View Consolidated Report &rarr;</span>
      </a>
      @endforeach
    </div>
    @endif
  </div>

  @if($student)
  {{-- ── STUDENT PROFILE BANNER ───────────────────────────────────────── --}}
  <div class="card bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white shadow-lg">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-indigo-600/50 border-2 border-indigo-400/40 flex items-center justify-center text-2xl font-black text-white shadow-inner">
          {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h2 class="text-xl font-black text-white">{{ $student->first_name }} {{ $student->last_name }}</h2>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-500/20 text-green-300 border border-green-500/30">Active Student</span>
          </div>
          <div class="flex flex-wrap items-center gap-3 mt-1.5 text-xs text-slate-300">
            <span>Adm No: <strong class="text-white">{{ $student->admission_no ?? $student->admission_number ?? '—' }}</strong></span>
            <span>&bull;</span>
            <span>Class: <strong class="text-white">{{ $enrollment?->class?->name ?? '—' }} {{ $enrollment?->section?->name ? '('.$enrollment->section->name.')' : '' }}</strong></span>
            <span>&bull;</span>
            <span>Roll No: <strong class="text-white">{{ $enrollment?->roll_number ?? '—' }}</strong></span>
            @if($student->guardian_name || $student->father_name)
            <span>&bull;</span>
            <span>Guardian: <strong class="text-white">{{ $student->guardian_name ?: $student->father_name }}</strong></span>
            @endif
          </div>
        </div>
      </div>

      {{-- Overall Rating Badge --}}
      @if(isset($consolidatedData))
      <div class="flex items-center gap-4 md:border-l md:border-slate-700/60 md:pl-6">
        <div class="text-right">
          <span class="text-3xl font-black text-amber-400">{{ $consolidatedData['overall_pct'] }}%</span>
          <p class="text-xs uppercase tracking-wider text-slate-400 mt-0.5">Cumulative Score</p>
        </div>
        <div class="px-3 py-1.5 rounded-xl bg-white/10 border border-white/20 text-center">
          <span class="text-xs font-bold text-amber-300 block">{{ $consolidatedData['overall_grade'] }}</span>
          <span class="text-[10px] text-slate-400">Annual Standing</span>
        </div>
      </div>
      @endif
    </div>
  </div>

  @if(isset($consolidatedData))
  {{-- ── ANNUAL PERFORMANCE SUMMARY CARDS ───────────────────────────── --}}
  <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
    <div class="card text-center py-4 border-t-4 border-indigo-500">
      <p class="text-2xl font-extrabold text-slate-800">{{ $consolidatedData['total_exams'] }}</p>
      <p class="text-xs text-slate-500 mt-1">Exams Taken</p>
    </div>
    <div class="card text-center py-4 border-t-4 border-green-500">
      <p class="text-2xl font-extrabold text-green-600">{{ $consolidatedData['passed_exams'] }}</p>
      <p class="text-xs text-slate-500 mt-1">Exams Passed</p>
    </div>
    <div class="card text-center py-4 border-t-4 border-red-500">
      <p class="text-2xl font-extrabold text-red-600">{{ $consolidatedData['failed_exams'] }}</p>
      <p class="text-xs text-slate-500 mt-1">Remedial Exams</p>
    </div>
    <div class="card text-center py-4 border-t-4 border-purple-500">
      <p class="text-2xl font-extrabold text-purple-700">{{ $consolidatedData['total_obtained'] }} <span class="text-xs font-normal text-slate-400">/ {{ $consolidatedData['total_max'] }}</span></p>
      <p class="text-xs text-slate-500 mt-1">Total Marks Scored</p>
    </div>
    <div class="card text-center py-4 border-t-4 border-emerald-500">
      <p class="text-2xl font-extrabold text-emerald-600">{{ $consolidatedData['attendance_pct'] }}%</p>
      <p class="text-xs text-slate-500 mt-1">Attendance Rate</p>
    </div>
  </div>

  {{-- ── CONSOLIDATED SUBJECT-WISE PROGRESSION MATRIX ────────────────── --}}
  @if(!empty($consolidatedData['subject_matrix']))
  <div class="card p-0 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between flex-wrap gap-2">
      <div>
        <h3 class="font-bold text-slate-800 text-base">Consolidated Subject-wise Progression Matrix</h3>
        <p class="text-xs text-slate-500">Evolution of marks across each term/exam for the academic year</p>
      </div>
      <span class="text-xs text-slate-400">Passing Mark Threshold: 35%</span>
    </div>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Subject</th>
            @foreach($history as $h)
            <th class="th text-center">{{ $h['exam']->name }}</th>
            @endforeach
            <th class="th text-center">Subject Average</th>
            <th class="th text-center">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($consolidatedData['subject_matrix'] as $subjectName => $examScores)
          @php
            $subTotalObt = 0;
            $subTotalMax = 0;
            $allPassed = true;
            foreach($examScores as $eName => $val) {
              if($val['obtained'] !== null && !$val['is_absent']) {
                $subTotalObt += $val['obtained'];
                $subTotalMax += $val['max'];
              }
              if(!$val['passed']) $allPassed = false;
            }
            $subAvgPct = $subTotalMax > 0 ? round(($subTotalObt / $subTotalMax) * 100, 1) : 0;
          @endphp
          <tr class="tr hover:bg-slate-50 transition">
            <td class="td font-bold text-slate-800">{{ $subjectName }}</td>
            @foreach($history as $h)
            @php $score = $examScores[$h['exam']->name] ?? null; @endphp
            <td class="td text-center">
              @if($score)
                @if($score['is_absent'])
                  <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700">ABS</span>
                @else
                  <span class="font-bold text-sm {{ $score['passed'] ? 'text-slate-800' : 'text-red-600' }}">
                    {{ $score['obtained'] }}
                  </span>
                  <span class="text-xs text-slate-400">/ {{ $score['max'] }}</span>
                  @if(!$score['passed'])
                    <span class="block text-[10px] text-red-500 font-bold">Fail</span>
                  @endif
                @endif
              @else
                <span class="text-slate-300 text-xs">—</span>
              @endif
            </td>
            @endforeach
            <td class="td text-center font-extrabold text-slate-800">
              <span class="px-2 py-0.5 rounded text-xs {{ $subAvgPct >= 75 ? 'bg-green-100 text-green-700' : ($subAvgPct >= 50 ? 'bg-indigo-100 text-indigo-700' : 'bg-red-100 text-red-700') }}">
                {{ $subAvgPct }}%
              </span>
            </td>
            <td class="td text-center">
              @if($allPassed)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Clear</span>
              @else
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Remedial</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  {{-- ── EXAM-BY-EXAM BREAKDOWN CARDS ────────────────────────────────── --}}
  <div class="space-y-4">
    <h3 class="font-bold text-slate-800 text-base">Individual Exam Performance Cards</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @foreach($history as $item)
      <div class="card p-0 overflow-hidden border border-slate-200">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
          <div>
            <span class="font-bold text-slate-800">{{ $item['exam']->name }}</span>
            <span class="text-xs text-slate-500 ml-1">({{ $item['exam']->term_label ?: 'Exam' }})</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-xs font-extrabold text-slate-700">{{ $item['total'] }}/{{ $item['max'] }} ({{ $item['pct'] }}%)</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $item['passed'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
              {{ $item['passed'] ? 'Pass' : 'Fail' }}
            </span>
          </div>
        </div>
        <div class="table-wrap">
          <table class="w-full text-xs">
            <thead>
              <tr class="border-b border-slate-100 bg-slate-50/50">
                <th class="th py-2">Subject</th>
                <th class="th py-2 text-center">Max</th>
                <th class="th py-2 text-center">Pass</th>
                <th class="th py-2 text-center">Score</th>
                <th class="th py-2 text-center">Grade</th>
                <th class="th py-2 text-center">Result</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @foreach($item['marks'] as $m)
              @php
                $passVal = $m->examSchedule?->pass_marks ?? 35;
                $isPass = !$m->is_absent && $m->marks_obtained !== null && $m->marks_obtained >= $passVal;
              @endphp
              <tr class="tr">
                <td class="td py-2 font-medium text-slate-800">{{ $m->examSchedule?->subject?->name ?? '—' }}</td>
                <td class="td py-2 text-center text-slate-400">{{ $m->examSchedule?->max_marks }}</td>
                <td class="td py-2 text-center text-slate-400">{{ $passVal }}</td>
                <td class="td py-2 text-center font-extrabold {{ $isPass ? 'text-slate-800' : 'text-red-600' }}">
                  @if($m->is_absent) <span class="text-amber-600">ABS</span>
                  @else {{ $m->marks_obtained ?? '—' }}
                  @endif
                </td>
                <td class="td py-2 text-center font-semibold text-slate-700">{{ $m->grade ?: '—' }}</td>
                <td class="td py-2 text-center">
                  @if($isPass)
                    <span class="px-1.5 py-0.2 rounded bg-green-100 text-green-700 font-bold text-[10px]">P</span>
                  @elseif($m->is_absent)
                    <span class="px-1.5 py-0.2 rounded bg-amber-100 text-amber-700 font-bold text-[10px]">A</span>
                  @else
                    <span class="px-1.5 py-0.2 rounded bg-red-100 text-red-700 font-bold text-[10px]">F</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  @else
  <div class="card text-center py-16 text-slate-400">
    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    <p class="font-bold text-slate-700 text-base">Select a Student to View Consolidated Report</p>
    <p class="text-xs text-slate-400 mt-1">Use the search box above to find any student by name or admission number.</p>
  </div>
  @endif
</div>
@endsection
