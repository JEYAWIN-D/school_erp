@extends('layouts.app')
@section('title', 'Failed Students')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Failed Students</h1>
      <p class="page-subtitle">Students who failed in one or more subjects</p>
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
        <label class="label">Class (Optional)</label>
        <select name="class_id" class="select">
          <option value="">All Classes</option>
          @foreach($classes as $class)
          <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Show Failed</button>
      @if(request('exam_id'))
      <a href="{{ route('examinations.failed.pdf', request()->only('exam_id','class_id')) }}"
         target="_blank" class="btn btn-secondary btn-sm">PDF</a>
      <a href="{{ route('examinations.failed.excel', request()->only('exam_id','class_id')) }}"
         class="btn btn-secondary btn-sm">Excel</a>
      @endif
    </form>
  </div>

  @if($failed->isNotEmpty())
  @php $suppCount = $failed->where('supp_eligible', true)->count(); @endphp
  <div class="card">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
      <div>
        <h2 class="font-semibold text-slate-800">
          Failed Students
          <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 text-sm rounded-full">{{ $failed->count() }}</span>
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">
          Supplementary Eligible (≤{{ $threshold }} subjects failed):
          <span class="font-medium text-amber-600">{{ $suppCount }}</span>
        </p>
      </div>
    </div>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">Student Name</th>
            <th class="th">Adm No</th>
            <th class="th">Failed Subjects</th>
            <th class="th">Count</th>
            <th class="th">Supp. Eligible</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($failed as $i => $row)
          <tr class="tr">
            <td class="td text-slate-500">{{ $i + 1 }}</td>
            <td class="td font-medium">
              <a href="{{ route('students.show', $row['student']->id) }}" class="text-indigo-600 hover:underline">
                {{ $row['student']->first_name }} {{ $row['student']->last_name }}
              </a>
            </td>
            <td class="td text-slate-500">{{ $row['student']->admission_no ?? $row['student']->admission_number ?? '—' }}</td>
            <td class="td">
              <div class="flex flex-wrap gap-1">
                @foreach($row['subjects'] as $subject)
                  <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full">{{ $subject }}</span>
                @endforeach
              </div>
            </td>
            <td class="td">
              <span class="font-semibold text-red-600">{{ $row['subjects']->count() }}</span>
            </td>
            <td class="td">
              @if($row['supp_eligible'])
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-medium rounded-full">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  Eligible
                </span>
              @else
                <span class="text-slate-400 text-xs">—</span>
              @endif
            </td>
            <td class="td">
              <a href="{{ route('students.show', $row['student']->id) }}" class="text-sm text-indigo-600 hover:underline">View Profile</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @elseif(request('exam_id'))
  <div class="alert-success">No failed students found for the selected filters.</div>
  @endif
</div>
@endsection
