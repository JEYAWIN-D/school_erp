@extends('layouts.app')
@section('title', 'Exam Result')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">

  <div class="card text-center py-8">
    @if($attempt->result === 'pass')
      <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      </div>
      <h2 class="text-2xl font-bold text-green-700 mb-1">Congratulations! You Passed</h2>
    @elseif($attempt->result === 'fail')
      <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </div>
      <h2 class="text-2xl font-bold text-red-600 mb-1">Better Luck Next Time</h2>
    @else
      <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <h2 class="text-2xl font-bold text-amber-700 mb-1">Exam Submitted</h2>
    @endif

    <p class="text-slate-500 text-sm mb-6">{{ $attempt->exam?->title }}</p>

    <div class="grid grid-cols-3 gap-4 max-w-md mx-auto">
      <div class="bg-slate-50 rounded-xl p-4">
        <p class="text-xs text-slate-400 mb-1">Score</p>
        <p class="text-2xl font-bold text-slate-800">{{ $attempt->final_score ?? '—' }}</p>
        <p class="text-xs text-slate-400">/ {{ $attempt->exam?->total_marks }}</p>
      </div>
      <div class="bg-slate-50 rounded-xl p-4">
        <p class="text-xs text-slate-400 mb-1">Pass Marks</p>
        <p class="text-2xl font-bold text-slate-800">{{ $attempt->exam?->pass_marks }}</p>
      </div>
      <div class="bg-slate-50 rounded-xl p-4">
        <p class="text-xs text-slate-400 mb-1">Percentage</p>
        @php $pct = $attempt->exam?->total_marks > 0 ? round(($attempt->final_score / $attempt->exam->total_marks) * 100, 1) : 0; @endphp
        <p class="text-2xl font-bold text-slate-800">{{ $pct }}%</p>
      </div>
    </div>

    @if($attempt->auto_submitted)
      <p class="text-xs text-amber-500 mt-4">Auto-submitted when time expired.</p>
    @endif

    @if($attempt->negative_marks > 0)
      <p class="text-xs text-red-400 mt-2">Negative marks deducted: {{ $attempt->negative_marks }}</p>
    @endif
  </div>

  {{-- Answer review --}}
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-3 pb-2 border-b border-slate-100">Answer Review</h3>
    <div class="space-y-3">
      @foreach($attempt->responses as $idx => $resp)
        @php $q = $resp->question; @endphp
        <div class="p-3 rounded-lg {{ $resp->is_correct === true ? 'bg-green-50 border border-green-200' : ($resp->is_correct === false ? 'bg-red-50 border border-red-200' : 'bg-slate-50 border border-slate-200') }}">
          <p class="text-sm font-medium text-slate-700 mb-1">{{ $idx + 1 }}. {{ $q?->question }}</p>
          <p class="text-sm">
            <span class="text-slate-500">Your answer: </span>
            <strong>{{ $resp->answer ?? 'Not answered' }}</strong>
          </p>
          @if($resp->is_correct !== null && $q?->correct_answer && in_array($q->question_type, ['mcq', 'true_false']))
            <p class="text-xs text-green-600 mt-1">Correct answer: {{ $q->correct_answer }}</p>
          @endif
          <div class="flex items-center gap-3 mt-1">
            @if($resp->is_correct === true)
              <span class="badge-green text-xs">+{{ $resp->marks_awarded }} marks</span>
            @elseif($resp->is_correct === false)
              <span class="badge-red text-xs">0 marks</span>
            @else
              <span class="badge-amber text-xs">Pending evaluation: {{ $resp->marks_awarded }} marks</span>
            @endif
            @if($resp->evaluator_remarks)
              <span class="text-xs text-slate-500 italic">{{ $resp->evaluator_remarks }}</span>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <div class="flex justify-center">
    <a href="{{ route('online-exams.student') }}" class="btn btn-secondary">Back to My Exams</a>
  </div>

</div>
@endsection
