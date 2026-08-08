@extends('layouts.app')
@section('title', 'Course Progress')
@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">{{ $course->title }} — Progress</h1>
      <p class="text-sm text-slate-400">{{ $totalLessons }} published lessons total</p>
    </div>
    <a href="{{ route('lms.courses.show', $course->id) }}" class="btn-secondary btn-sm">← Course</a>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Student</th>
          <th class="th">Adm No.</th>
          <th class="th text-center">Lessons Done</th>
          <th class="th text-center">Progress</th>
        </tr></thead>
        <tbody>
          @forelse($enrollments as $e)
          @php
            $done = $completedCounts[$e->student_id] ?? 0;
            $pct  = $totalLessons > 0 ? round($done / $totalLessons * 100) : 0;
          @endphp
          <tr class="tr">
            <td class="td font-medium text-slate-700">{{ $e->student_name }}</td>
            <td class="td font-mono text-xs">{{ $e->admission_no }}</td>
            <td class="td text-center text-slate-700">{{ $done }}/{{ $totalLessons }}</td>
            <td class="td">
              <div class="flex items-center gap-2">
                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                  <div class="h-full rounded-full {{ $pct >= 80 ? 'bg-green-500' : ($pct >= 40 ? 'bg-amber-500' : 'bg-red-400') }}"
                       style="width: {{ $pct }}%"></div>
                </div>
                <span class="text-xs text-slate-500 w-8 text-right">{{ $pct }}%</span>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="4" class="td text-center text-slate-400 py-6">No enrolled students found for this class</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
