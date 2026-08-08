@extends('layouts.app')
@section('title', 'Quiz Attempts')
@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">{{ $quiz->title }} — Attempts</h1>
      <p class="text-sm text-slate-400">{{ $attempts->total() }} attempt(s)</p>
    </div>
    <a href="{{ route('lms.quiz.builder', $quiz->course_id) }}" class="btn-secondary btn-sm">← Quiz Builder</a>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Student</th>
          <th class="th">Adm No.</th>
          <th class="th text-center">Score</th>
          <th class="th text-center">%</th>
          <th class="th text-center">Time Used</th>
          <th class="th">Submitted</th>
          <th class="th">Status</th>
        </tr></thead>
        <tbody>
          @forelse($attempts as $a)
          <tr class="tr">
            <td class="td font-medium text-slate-700">{{ $a->student_name }}</td>
            <td class="td font-mono text-xs">{{ $a->admission_no }}</td>
            <td class="td text-center font-semibold">{{ $a->score }}/{{ $a->total_marks }}</td>
            <td class="td text-center">
              @php $pct = $a->total_marks > 0 ? round($a->score/$a->total_marks*100,1) : 0; @endphp
              <span class="{{ $pct >= 50 ? 'text-green-600' : 'text-red-600' }} font-medium">{{ $pct }}%</span>
            </td>
            <td class="td text-center text-slate-500 text-xs">
              {{ $a->submitted_at && $a->started_at ? \Carbon\Carbon::parse($a->started_at)->diffInMinutes($a->submitted_at) . ' min' : '—' }}
            </td>
            <td class="td text-xs text-slate-400">
              {{ $a->submitted_at ? \Carbon\Carbon::parse($a->submitted_at)->format('d M Y H:i') : '—' }}
            </td>
            <td class="td">
              <span class="badge-{{ $a->status === 'submitted' ? 'green' : ($a->status === 'in_progress' ? 'blue' : 'purple') }}">
                {{ ucfirst(str_replace('_', ' ', $a->status)) }}
              </span>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="td text-center text-slate-400 py-6">No attempts yet</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $attempts->links() }}</div>
  </div>
</div>
@endsection
