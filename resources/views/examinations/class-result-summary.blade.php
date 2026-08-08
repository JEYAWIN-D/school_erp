@extends('layouts.app')
@section('title', 'Class Result Summary')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Class Result Summary</h1>
      <p class="page-subtitle">Rank-wise breakdown for an exam &amp; class</p>
    </div>
    <a href="{{ route('examinations.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Exam</label>
        <select name="exam_id" class="select" required>
          <option value="">Select Exam</option>
          @foreach($exams as $exam)
          <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select" required>
          <option value="">Select Class</option>
          @foreach($classes as $class)
          <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">View Summary</button>
      @if(request('exam_id') && request('class_id'))
      <a href="{{ route('examinations.class-result.pdf', request()->only('exam_id','class_id')) }}"
         target="_blank" class="btn btn-secondary btn-sm">PDF</a>
      <a href="{{ route('examinations.class-result.excel', request()->only('exam_id','class_id')) }}"
         class="btn btn-secondary btn-sm">Excel</a>
      @endif
    </form>
  </div>

  @if($summary->isNotEmpty())
  @php
    $passCount    = $summary->where('hasFail', false)->count();
    $failCount    = $summary->where('hasFail', true)->count();
    $classAverage = round($summary->avg('percentage'), 1);
    $topPct       = $summary->first()['percentage'];
  @endphp
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-slate-700">{{ $summary->count() }}</p>
      <p class="text-sm text-slate-500 mt-1">Total Students</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-green-600">{{ $passCount }}</p>
      <p class="text-sm text-slate-500 mt-1">Passed</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-red-500">{{ $failCount }}</p>
      <p class="text-sm text-slate-500 mt-1">Failed</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-indigo-600">{{ $classAverage }}%</p>
      <p class="text-sm text-slate-500 mt-1">Class Average</p>
    </div>
  </div>

  <div class="card">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
      <h2 class="font-semibold text-slate-800">Result Rank List</h2>
      <a href="{{ route('examinations.tabulation.pdf', request()->only(['exam_id','class_id'])) }}" class="btn btn-secondary text-sm">
        Download Tabulation PDF
      </a>
    </div>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Rank</th>
            <th class="th">Student Name</th>
            <th class="th">Adm No</th>
            <th class="th">Marks Obtained</th>
            <th class="th">Total Marks</th>
            <th class="th">Percentage</th>
            <th class="th">Result</th>
          </tr>
        </thead>
        <tbody>
          @foreach($summary as $i => $row)
          <tr class="tr">
            <td class="td">
              @if($i < 3)
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white text-xs font-bold {{ ['bg-amber-500','bg-slate-400','bg-orange-700'][$i] }}">{{ $i+1 }}</span>
              @else
                <span class="text-slate-500">{{ $i+1 }}</span>
              @endif
            </td>
            <td class="td font-medium">
              <a href="{{ route('students.show', $row['student']->id) }}" class="text-indigo-600 hover:underline">
                {{ $row['student']->first_name }} {{ $row['student']->last_name }}
              </a>
            </td>
            <td class="td text-slate-500">{{ $row['student']->admission_no ?? $row['student']->admission_number ?? '—' }}</td>
            <td class="td">{{ $row['obtained'] }}</td>
            <td class="td text-slate-500">{{ $row['totalMarks'] }}</td>
            <td class="td">
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-200 rounded-full h-2 max-w-[80px]">
                  <div class="h-2 rounded-full {{ $row['percentage'] >= 75 ? 'bg-green-500' : ($row['percentage'] >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width:{{ $row['percentage'] }}%"></div>
                </div>
                <span class="text-sm font-medium">{{ $row['percentage'] }}%</span>
              </div>
            </td>
            <td class="td">
              <span class="badge-{{ $row['hasFail'] ? 'red' : 'green' }}">{{ $row['hasFail'] ? 'Fail' : 'Pass' }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @elseif(request('exam_id') && request('class_id'))
  <div class="alert-info">No enrollment records found for the selected exam and class.</div>
  @endif
</div>
@endsection
