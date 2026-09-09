@extends('layouts.app')
@section('title', 'Subject-wise Performance Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Subject-wise Performance Report</h1>
      <p class="page-subtitle">Detailed subject analytics, grade distribution &amp; individual student score roster</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('examinations.class-result', request()->only('exam_id','class_id')) }}" class="btn btn-secondary btn-sm">Class Result Summary</a>
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
          <option value="{{ $e->id }}" @selected(request('exam_id') == $e->id)>{{ $e->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label font-semibold text-xs text-slate-700">Class *</label>
        <select name="class_id" class="select text-sm py-1.5" required onchange="this.form.submit()">
          <option value="">— Select Class —</option>
          @foreach($classes as $c)
          <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label font-semibold text-xs text-slate-700">Individual Subject</label>
        <select name="subject_id" class="select text-sm py-1.5 min-w-[220px]" onchange="this.form.submit()">
          <option value="">All Subjects (Comparative View)</option>
          @if(isset($availableSubjects) && $availableSubjects->isNotEmpty())
          @foreach($availableSubjects as $as)
          <option value="{{ $as->id }}" @selected(request('subject_id') == $as->id)>{{ $as->name }}</option>
          @endforeach
          @endif
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
      @if(request('subject_id'))
      <a href="{{ route('examinations.subject-performance', request()->only('exam_id','class_id')) }}" class="btn btn-secondary btn-sm">
        Reset to All Subjects
      </a>
      @endif
    </form>
  </div>

  @if($individualMode && isset($subjectStats))
  {{-- ── INDIVIDUAL SUBJECT PERFORMANCE MODE ──────────────────────────── --}}
  <div class="space-y-6">
    {{-- Subject Header Card --}}
    <div class="card bg-gradient-to-r from-purple-50 via-white to-indigo-50 border-purple-200">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-100 text-purple-800">Individual Subject Report</span>
            <span class="text-xs text-slate-400">Class {{ $class->name ?? '' }} &bull; {{ $exam->name ?? '' }}</span>
          </div>
          <h2 class="text-2xl font-black text-slate-800 mt-1">{{ $subjectStats['subject'] }}</h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Maximum Marks: <strong class="text-slate-700">{{ $subjectStats['max_marks'] }}</strong> &bull;
            Passing Threshold: <strong class="text-slate-700">{{ $subjectStats['pass_marks'] }}</strong> &bull;
            Top Scorer(s): <strong class="text-indigo-600">{{ $subjectStats['toppers'] }}</strong>
          </p>
        </div>
        <div class="text-right">
          <span class="text-3xl font-extrabold {{ $subjectStats['pass_pct'] >= 75 ? 'text-green-600' : ($subjectStats['pass_pct'] >= 50 ? 'text-amber-600' : 'text-red-600') }}">
            {{ $subjectStats['pass_pct'] }}%
          </span>
          <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pass Rate</p>
        </div>
      </div>
    </div>

    {{-- KPI Cards for this individual subject --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
      <div class="card text-center py-4 border-t-4 border-indigo-500">
        <p class="text-2xl font-bold text-slate-800">{{ $subjectStats['total'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Students Appeared</p>
      </div>
      <div class="card text-center py-4 border-t-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">{{ $subjectStats['passed'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Passed ({{ $subjectStats['pass_pct'] }}%)</p>
      </div>
      <div class="card text-center py-4 border-t-4 border-red-500">
        <p class="text-2xl font-bold text-red-600">{{ $subjectStats['failed'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Failed</p>
      </div>
      <div class="card text-center py-4 border-t-4 border-purple-500">
        <p class="text-2xl font-bold text-purple-700">{{ $subjectStats['avg'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Class Average</p>
      </div>
      <div class="card text-center py-4 border-t-4 border-amber-500">
        <div class="flex items-center justify-center gap-2">
          <span class="text-xl font-bold text-green-700">{{ $subjectStats['highest'] }}</span>
          <span class="text-xs text-slate-300">/</span>
          <span class="text-sm font-semibold text-red-600">{{ $subjectStats['lowest'] }}</span>
        </div>
        <p class="text-xs text-slate-500 mt-1">Highest / Lowest</p>
      </div>
    </div>

    {{-- Grade Distribution Breakdown --}}
    @if(!empty($gradeDistribution))
    <div class="card">
      <h3 class="font-bold text-slate-800 text-sm mb-3">Grade Distribution ({{ $subjectStats['subject'] }})</h3>
      <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2">
        @foreach($gradeDistribution as $gradeLabel => $gCount)
        <div class="p-2.5 rounded-xl border border-slate-200 text-center bg-slate-50/50">
          <span class="text-xs font-semibold text-slate-600 block">{{ $gradeLabel }}</span>
          <span class="text-xl font-extrabold text-slate-800 mt-1 block">{{ $gCount }}</span>
          <span class="text-[10px] text-slate-400">
            {{ $subjectStats['total'] > 0 ? round(($gCount / $subjectStats['total']) * 100) : 0 }}%
          </span>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Individual Student Marks Roster for this Subject --}}
    <div class="card p-0 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2 bg-slate-50/50">
        <h3 class="font-bold text-slate-800 text-base">
          Student Score Roster — {{ $subjectStats['subject'] }}
        </h3>
        <span class="text-xs text-slate-500">Ranked by score in this subject</span>
      </div>
      <div class="table-wrap">
        <table class="w-full">
          <thead>
            <tr>
              <th class="th text-center" style="width: 60px;">Rank</th>
              <th class="th">Student Name</th>
              <th class="th">Adm No</th>
              <th class="th text-center">Marks Obtained</th>
              <th class="th text-center">Max Marks</th>
              <th class="th text-center">Percentage</th>
              <th class="th text-center">Grade</th>
              <th class="th text-center">Status</th>
              <th class="th">Remarks</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($studentRoster as $idx => $sr)
            <tr class="tr hover:bg-slate-50 transition">
              <td class="td text-center">
                @if($idx < 3)
                  <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-white text-xs font-bold {{ ['bg-amber-500','bg-slate-400','bg-orange-700'][$idx] }}">{{ $idx+1 }}</span>
                @else
                  <span class="text-slate-400 font-semibold">{{ $idx+1 }}</span>
                @endif
              </td>
              <td class="td font-medium">
                <a href="{{ route('examinations.student-result-history', ['student_id' => $sr['student']->id]) }}" class="text-indigo-600 hover:underline font-semibold">
                  {{ $sr['student']->first_name }} {{ $sr['student']->last_name }}
                </a>
              </td>
              <td class="td text-slate-500 font-mono text-xs">{{ $sr['student']->admission_no ?? $sr['student']->admission_number ?? '—' }}</td>
              <td class="td text-center font-extrabold text-slate-800">
                @if($sr['is_absent'])
                  <span class="text-amber-600 font-bold">Absent</span>
                @else
                  {{ $sr['obtained'] ?? '—' }}
                @endif
              </td>
              <td class="td text-center text-slate-400">{{ $subjectStats['max_marks'] }}</td>
              <td class="td text-center font-semibold text-slate-700">{{ $sr['pct'] }}%</td>
              <td class="td text-center">
                <span class="px-2 py-0.5 rounded text-xs font-bold {{ $sr['status'] === 'Pass' ? 'bg-green-100 text-green-700' : ($sr['is_absent'] ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                  {{ $sr['grade'] }}
                </span>
              </td>
              <td class="td text-center">
                @if($sr['status'] === 'Pass')
                  <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Pass</span>
                @elseif($sr['is_absent'])
                  <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Absent</span>
                @else
                  <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Fail</span>
                @endif
              </td>
              <td class="td text-xs text-slate-500">{{ $sr['remarks'] ?? '—' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @elseif(!empty($data))
  {{-- ── ALL SUBJECTS COMPARATIVE VIEW ─────────────────────────────── --}}
  <div class="card p-0 overflow-hidden">
    <div class="p-4 border-b bg-slate-50 flex justify-between items-center flex-wrap gap-2">
      <div>
        <h2 class="font-bold text-slate-800 text-base">Class {{ $class->name ?? '' }} — {{ $exam->name ?? '' }}</h2>
        <p class="text-xs text-slate-500">Comparative Subject Performance Matrix</p>
      </div>
      <span class="text-xs text-slate-400">Click any subject row or "Inspect" to view individual report</span>
    </div>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Subject</th>
            <th class="th text-center">Max Marks</th>
            <th class="th text-center">Pass Marks</th>
            <th class="th text-center">Appeared</th>
            <th class="th text-center">Passed</th>
            <th class="th text-center">Failed</th>
            <th class="th text-center">Pass %</th>
            <th class="th text-center">Avg Marks</th>
            <th class="th text-center">Highest</th>
            <th class="th text-center">Lowest</th>
            <th class="th text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($data as $row)
          <tr class="tr hover:bg-slate-50 transition">
            <td class="td font-bold text-slate-800">
              <a href="{{ route('examinations.subject-performance', ['exam_id' => request('exam_id'), 'class_id' => request('class_id'), 'subject_id' => $row['subject_id']]) }}"
                 class="text-indigo-600 hover:underline">
                {{ $row['subject'] }}
              </a>
              @if(!empty($row['code']))
              <span class="text-xs text-slate-400 ml-1">({{ $row['code'] }})</span>
              @endif
            </td>
            <td class="td text-center text-slate-500">{{ $row['max_marks'] }}</td>
            <td class="td text-center text-slate-500">{{ $row['pass_marks'] }}</td>
            <td class="td text-center font-semibold text-slate-700">{{ $row['total'] }}</td>
            <td class="td text-center text-green-600 font-bold">{{ $row['passed'] }}</td>
            <td class="td text-center text-red-600 font-bold">{{ $row['failed'] }}</td>
            <td class="td text-center">
              <span class="inline-flex items-center gap-1">
                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $row['pass_pct'] >= 75 ? 'bg-green-100 text-green-700' : ($row['pass_pct'] >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                  {{ $row['pass_pct'] }}%
                </span>
              </span>
            </td>
            <td class="td text-center font-bold text-slate-800">{{ $row['avg'] }}</td>
            <td class="td text-center text-green-700 font-bold">{{ $row['highest'] }}</td>
            <td class="td text-center text-red-600 font-medium">{{ $row['lowest'] }}</td>
            <td class="td text-right">
              <a href="{{ route('examinations.subject-performance', ['exam_id' => request('exam_id'), 'class_id' => request('class_id'), 'subject_id' => $row['subject_id']]) }}"
                 class="btn btn-secondary btn-sm text-xs">
                Inspect Subject &rarr;
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @elseif(request('exam_id'))
  <div class="card text-center py-12 text-slate-400">
    <p class="font-medium text-slate-600">No examination data found for the selected filters.</p>
  </div>
  @else
  <div class="card text-center py-12 text-slate-400">
    <p class="font-medium text-slate-600">Select Exam and Class to view the Subject Performance report.</p>
  </div>
  @endif
</div>
@endsection
