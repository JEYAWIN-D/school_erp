@extends('layouts.app')
@section('title', 'My Online Exams')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">

  <div>
    <h1 class="page-title">My Online Exams</h1>
    <p class="page-subtitle">Upcoming and past online examinations for your class</p>
  </div>

  @if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
  @endif
  @if(session('info'))
    <div class="alert-info">{{ session('info') }}</div>
  @endif

  @forelse($exams as $exam)
    @php $attempt = $exam->attempt; $live = $exam->isLive(); @endphp
    <div class="card flex flex-col sm:flex-row sm:items-center gap-4">
      <div class="flex-1">
        <div class="flex items-center gap-2 mb-1">
          <h3 class="font-semibold text-slate-800">{{ $exam->title }}</h3>
          @if($live)
            <span class="badge-green">LIVE</span>
          @elseif($exam->start_time->isFuture())
            <span class="badge-blue">Upcoming</span>
          @else
            <span class="badge-slate">Ended</span>
          @endif
        </div>
        <p class="text-sm text-slate-500">{{ $exam->subject?->name ?? $exam->class?->name }}</p>
        <p class="text-xs text-slate-400 mt-1">
          {{ $exam->start_time->format('d M Y, g:i A') }} – {{ $exam->end_time->format('g:i A') }}
          | {{ $exam->duration_minutes }} min | {{ $exam->total_marks }} marks
        </p>
      </div>
      <div class="text-right flex-shrink-0">
        @if($attempt?->submitted_at)
          <p class="text-sm font-semibold text-slate-700 mb-1">{{ $attempt->final_score ?? '?' }} / {{ $exam->total_marks }}</p>
          @if($attempt->result === 'pass')
            <span class="badge-green">Pass</span>
          @elseif($attempt->result === 'fail')
            <span class="badge-red">Fail</span>
          @else
            <span class="badge-amber">Pending</span>
          @endif
          <div class="mt-2">
            <a href="{{ route('online-exams.result', $attempt->id) }}" class="btn btn-secondary btn-xs">View Result</a>
          </div>
        @elseif($live)
          <a href="{{ route('online-exams.start', $exam->id) }}" class="btn btn-primary">
            {{ $attempt ? 'Continue Exam' : 'Start Exam' }}
          </a>
        @elseif($exam->start_time->isFuture())
          <p class="text-xs text-slate-400">Starts {{ $exam->start_time->diffForHumans() }}</p>
        @else
          <p class="text-xs text-red-400">Exam ended</p>
        @endif
      </div>
    </div>
  @empty
    <div class="card text-center py-12 text-slate-400">
      <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      No online exams scheduled for your class yet.
    </div>
  @endforelse
</div>
@endsection
