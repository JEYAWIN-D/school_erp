@extends('layouts.app')
@section('title','CGPA Report')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">CGPA Report (CBSE)</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Exam</label>
        <select name="exam_id" class="select w-48">
          <option value="">Select Exam</option>
          @foreach($exams as $e)
          <option value="{{ $e->id }}" @selected(request('exam_id') == $e->id)>{{ $e->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label text-xs">Class</label>
        <select name="class_id" class="select w-36">
          <option value="">Select Class</option>
          @foreach($classes as $c)
          <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  @if($report->count())
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-700">CGPA Results — {{ $exam?->name }}</h3>
      <span class="text-xs text-slate-400">{{ $report->count() }} students</span>
    </div>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">Rank</th>
            <th class="th">Student Name</th>
            <th class="th">Adm No</th>
            <th class="th">Subjects</th>
            <th class="th">CGPA</th>
            <th class="th">Equiv %</th>
          </tr>
        </thead>
        <tbody>
          @foreach($report as $i => $row)
          <tr class="tr {{ $i < 3 ? 'bg-amber-50/30' : '' }}">
            <td class="td text-center font-semibold {{ $i === 0 ? 'text-amber-500' : ($i === 1 ? 'text-slate-500' : ($i === 2 ? 'text-orange-400' : '')) }}">
              {{ $i + 1 }}
            </td>
            <td class="td font-medium">{{ $row['student']?->full_name }}</td>
            <td class="td text-slate-400 text-xs">{{ $row['student']?->admission_number }}</td>
            <td class="td">
              <div class="flex flex-wrap gap-1">
                @foreach($row['subjects'] as $sub)
                <span class="text-xs bg-slate-100 rounded px-1.5 py-0.5" title="{{ $sub['marks'] }}/{{ $sub['max'] }}">
                  {{ $sub['subject'] }}: <strong>{{ $sub['grade'] }}</strong> ({{ $sub['gp'] }})
                </span>
                @endforeach
              </div>
            </td>
            <td class="td text-center">
              <span class="text-lg font-bold {{ $row['cgpa'] >= 9 ? 'text-green-600' : ($row['cgpa'] >= 7 ? 'text-blue-600' : ($row['cgpa'] >= 5 ? 'text-amber-500' : 'text-red-500')) }}">
                {{ number_format($row['cgpa'], 1) }}
              </span>
            </td>
            <td class="td text-center text-slate-600">
              {{ number_format($row['cgpa'] * 9.5, 1) }}%
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3 p-3 bg-slate-50 rounded-lg text-xs text-slate-500">
      <strong>Note:</strong> CGPA = Average of Grade Points across all scholastic subjects.
      Equivalent percentage = CGPA × 9.5 (CBSE formula).
      Grade points are based on the default grading scheme configured in the system.
    </div>
  </div>
  @elseif(request('exam_id'))
  <div class="card text-center py-10 text-slate-400">No marks data found for this exam and class.</div>
  @else
  <div class="card text-center py-10 text-slate-400">Select an exam and class to generate CGPA report.</div>
  @endif
</div>
@endsection
