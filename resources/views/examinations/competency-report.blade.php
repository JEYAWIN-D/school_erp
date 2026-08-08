@extends('layouts.app')
@section('title', 'Competency Progress Report')
@section('content')
<div class="space-y-6">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Competency Progress Report</h1>
      <p class="page-subtitle">Student-wise competency progress across terms</p>
    </div>
    <a href="{{ route('examinations.competency-assessment') }}" class="btn btn-secondary btn-sm">Enter Assessment</a>
  </div>

  <form method="GET" class="card-flat py-3 flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Student (Admission No)</label>
      <input type="text" name="student_id" value="{{ request('student_id') }}" placeholder="Student ID…" class="input w-44">
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </form>

  @if($student && $report->count())
    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="font-semibold text-slate-700">{{ $student->full_name }}</h3>
          <p class="text-xs text-slate-400">{{ $student->currentEnrollment?->class?->name }} | {{ $academicYear?->name }}</p>
        </div>
      </div>

      <div class="table-wrap">
        <table class="w-full">
          <thead>
            <tr>
              <th class="th">Subject</th>
              <th class="th">Competency</th>
              <th class="th">Term 1</th>
              <th class="th">Term 2</th>
              <th class="th">Annual</th>
            </tr>
          </thead>
          <tbody>
            @foreach($report as $row)
              <tr class="tr">
                <td class="td text-xs text-slate-500">{{ $row['subject'] }}</td>
                <td class="td font-medium text-slate-700 text-sm">{{ $row['competency']->name }}</td>
                @foreach(['term1', 'term2', 'annual'] as $t)
                  <td class="td">
                    @if($row[$t])
                      <span class="{{ $row[$t]->level_color }}">{{ $row[$t]->level_label }}</span>
                      @if($row[$t]->remarks)
                        <p class="text-xs text-slate-400 mt-0.5">{{ $row[$t]->remarks }}</p>
                      @endif
                    @else
                      <span class="text-slate-300 text-xs">—</span>
                    @endif
                  </td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @elseif($student)
    <div class="card text-center py-10 text-slate-400">No competency assessments found for this student.</div>
  @else
    <div class="card text-center py-10 text-slate-400">Enter a Student ID above to view their competency progress report.</div>
  @endif

</div>
@endsection
