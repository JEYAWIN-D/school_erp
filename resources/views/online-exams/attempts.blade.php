@extends('layouts.app')
@section('title', 'Attempt Log — ' . $exam->title)
@section('content')
<div class="space-y-6">

  <div class="flex items-center gap-4">
    <a href="{{ route('online-exams.index') }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">Student Attempts</h1>
      <p class="page-subtitle">{{ $exam->title }} — {{ $exam->class?->name }}</p>
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">Student</th>
          <th class="th">Started</th>
          <th class="th">Submitted</th>
          <th class="th">Score</th>
          <th class="th">Final Score</th>
          <th class="th">Result</th>
          <th class="th text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($attempts as $att)
          <tr class="tr">
            <td class="td font-medium text-slate-800">
              {{ $att->student?->full_name }}
              <p class="text-xs text-slate-400">{{ $att->student?->admission_number }}</p>
            </td>
            <td class="td text-xs text-slate-500">
              {{ $att->started_at?->format('d M Y, g:i A') ?? '—' }}
            </td>
            <td class="td text-xs text-slate-500">
              @if($att->submitted_at)
                {{ $att->submitted_at->format('d M Y, g:i A') }}
                @if($att->auto_submitted)
                  <span class="badge-amber text-xs ml-1">Auto</span>
                @endif
              @else
                <span class="text-amber-500">In Progress</span>
              @endif
            </td>
            <td class="td text-sm">{{ $att->score ?? '—' }}</td>
            <td class="td text-sm font-semibold">
              {{ $att->final_score ?? '—' }} / {{ $exam->total_marks }}
              @if($att->negative_marks > 0)
                <p class="text-xs text-red-400">-{{ $att->negative_marks }} negative</p>
              @endif
            </td>
            <td class="td">
              @if($att->result === 'pass')
                <span class="badge-green">Pass</span>
              @elseif($att->result === 'fail')
                <span class="badge-red">Fail</span>
              @else
                <span class="badge-slate">Pending</span>
              @endif
            </td>
            <td class="td text-right">
              @if($att->submitted_at)
                <a href="{{ route('online-exams.evaluate', $att->id) }}" class="btn btn-secondary btn-xs">Evaluate</a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="td text-center py-10 text-slate-400">No attempts yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($attempts->hasPages())
    <div class="flex justify-between items-center text-sm text-slate-500">
      <span>Showing {{ $attempts->firstItem() }}–{{ $attempts->lastItem() }} of {{ $attempts->total() }}</span>
      {{ $attempts->links() }}
    </div>
  @endif
</div>
@endsection
