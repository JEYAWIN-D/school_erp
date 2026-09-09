@extends('layouts.app')
@section('title', 'Mark Entry Progress')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Mark Entry Progress</h1>
      <p class="page-subtitle">Track % of marks entered per subject across classes and schedules</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('examinations.index') }}" class="btn btn-secondary btn-sm">Back to Exams</a>
    </div>
  </div>

  <div class="card bg-slate-50 border border-slate-200">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label font-semibold text-xs text-slate-700">Exam *</label>
        <select name="exam_id" class="select text-sm py-1.5 min-w-[260px]" required onchange="this.form.submit()">
          <option value="">— Select Exam (All Exam Names) —</option>
          @foreach($exams as $exam)
            <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->name }} ({{ str_replace('_',' ',$exam->type) }})</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">View Progress</button>
    </form>
  </div>

  @if(request('exam_id'))
  @if($data->isEmpty())
    <div class="card text-center py-12 text-slate-400">
      <p class="font-medium text-slate-600">No exam schedules found for this exam.</p>
    </div>
  @else
  @php
    $totalSchedules = $data->count();
    $completedCount = $data->where('pct', '>=', 100)->count();
    $inProgressCount = $data->whereBetween('pct', [1, 99])->count();
    $pendingCount = $data->where('pct', 0)->count();
    $overallPct = $totalSchedules > 0 ? round($data->avg('pct')) : 0;
  @endphp

  {{-- Progress KPIs --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card text-center py-4 border-t-4 border-slate-600">
      <p class="text-3xl font-extrabold text-slate-800">{{ $totalSchedules }}</p>
      <p class="text-xs uppercase tracking-wider text-slate-400 mt-1">Scheduled Subjects</p>
    </div>
    <div class="card text-center py-4 border-t-4 border-green-500">
      <p class="text-3xl font-extrabold text-green-600">{{ $completedCount }}</p>
      <p class="text-xs uppercase tracking-wider text-slate-400 mt-1">Completed (100%)</p>
    </div>
    <div class="card text-center py-4 border-t-4 border-amber-500">
      <p class="text-3xl font-extrabold text-amber-600">{{ $inProgressCount }}</p>
      <p class="text-xs uppercase tracking-wider text-slate-400 mt-1">In Progress</p>
    </div>
    <div class="card text-center py-4 border-t-4 border-indigo-500">
      <p class="text-3xl font-extrabold text-indigo-600">{{ $overallPct }}%</p>
      <p class="text-xs uppercase tracking-wider text-slate-400 mt-1">Overall Completion</p>
    </div>
  </div>

  <div class="card p-0 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
      <h3 class="font-bold text-slate-800 text-base">Class &amp; Subject Mark Entry Completion</h3>
      <span class="text-xs text-slate-500">{{ $completedCount }} of {{ $totalSchedules }} subjects complete</span>
    </div>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Class</th>
            <th class="th">Subject</th>
            <th class="th">Exam Date</th>
            <th class="th text-center">Students</th>
            <th class="th text-center">Marks Entered</th>
            <th class="th" style="min-width:200px">Entry Progress</th>
            <th class="th text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($data as $row)
          <tr class="tr hover:bg-slate-50 transition">
            <td class="td font-bold text-slate-800">{{ $row['class'] }}</td>
            <td class="td font-medium text-slate-700">{{ $row['subject'] }}</td>
            <td class="td text-slate-500 text-xs">{{ $row['date'] ? \Carbon\Carbon::parse($row['date'])->format('d M Y') : '—' }}</td>
            <td class="td text-center font-semibold text-slate-700">{{ $row['total'] }}</td>
            <td class="td text-center font-bold {{ $row['pct'] >= 100 ? 'text-green-600' : ($row['pct'] > 0 ? 'text-amber-600' : 'text-slate-400') }}">
              {{ $row['entered'] }}
            </td>
            <td class="td">
              <div class="flex items-center gap-2.5">
                <div class="flex-1 bg-slate-200 rounded-full h-2.5 overflow-hidden">
                  <div class="h-2.5 rounded-full transition-all {{ $row['pct'] >= 100 ? 'bg-green-500' : ($row['pct'] >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                       style="width: {{ $row['pct'] }}%"></div>
                </div>
                <span class="text-xs font-bold min-w-[40px] {{ $row['pct'] >= 100 ? 'text-green-600' : ($row['pct'] >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                  {{ $row['pct'] }}%
                </span>
              </div>
            </td>
            <td class="td text-right">
              <a href="{{ route('examinations.marks', request('exam_id')) }}" class="btn btn-secondary btn-sm text-xs">
                Enter Marks &rarr;
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
  @else
  <div class="card text-center py-16 text-slate-400">
    <p class="font-medium text-slate-600">Select an Exam above to view the Mark Entry Progress across classes and subjects.</p>
  </div>
  @endif
</div>
@endsection
