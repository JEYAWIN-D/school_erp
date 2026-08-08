@extends('layouts.app')
@section('title', 'Evaluate — ' . $attempt->student?->full_name)
@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  <div class="flex items-center gap-4">
    <a href="{{ route('online-exams.attempts', $attempt->online_exam_id) }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">Evaluate: {{ $attempt->student?->full_name }}</h1>
      <p class="page-subtitle">{{ $attempt->exam?->title }} | Submitted {{ $attempt->submitted_at?->format('d M Y, g:i A') }}</p>
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('online-exams.evaluate.save', $attempt->id) }}">
    @csrf
    <div class="space-y-4">
      @foreach($attempt->responses as $idx => $resp)
        @php $q = $resp->question; @endphp
        <div class="card">
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
              <p class="text-sm font-semibold text-slate-700 mb-2">{{ $idx + 1 }}. {{ $q?->question }}</p>
              <div class="text-sm text-slate-500 mb-2">
                <strong>Student Answer:</strong>
                <span class="ml-1 {{ $resp->is_correct === true ? 'text-green-600 font-semibold' : ($resp->is_correct === false ? 'text-red-500' : 'text-amber-600') }}">
                  {{ $resp->answer ?? '—' }}
                </span>
              </div>
              @if(in_array($q?->question_type, ['mcq', 'true_false']) && $q?->correct_answer)
                <p class="text-xs text-green-600"><strong>Correct:</strong> {{ $q->correct_answer }}</p>
              @endif
            </div>
            <div class="flex-shrink-0 text-right">
              @if($resp->is_correct !== null)
                <span class="{{ $resp->is_correct ? 'badge-green' : 'badge-red' }}">{{ $resp->is_correct ? 'Correct' : 'Wrong' }}</span>
              @else
                <span class="badge-amber">Not Evaluated</span>
              @endif
              <p class="text-xs text-slate-400 mt-1">Max: {{ $q?->marks }} marks</p>
            </div>
          </div>

          @if(in_array($q?->question_type, ['short_answer', 'fill_blank']))
            <div class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-slate-100">
              <div>
                <label class="label">Marks Awarded (0–{{ $q?->marks }})</label>
                <input type="number" name="marks[{{ $resp->id }}]" class="input"
                       value="{{ $resp->marks_awarded }}" step="0.5" min="0" max="{{ $q?->marks }}">
              </div>
              <div>
                <label class="label">Remarks</label>
                <input type="text" name="remarks[{{ $resp->id }}]" class="input"
                       value="{{ $resp->evaluator_remarks }}" placeholder="Optional feedback…">
              </div>
            </div>
          @endif
        </div>
      @endforeach
    </div>

    @if($pending->count())
      <div class="flex justify-end mt-4">
        <button type="submit" class="btn btn-primary">Save Evaluation</button>
      </div>
    @else
      <div class="card-flat py-3 mt-4 text-center text-sm text-green-700">
        All responses evaluated.
        <strong>Final Score: {{ $attempt->final_score }} / {{ $attempt->exam?->total_marks }}</strong>
        — <span class="{{ $attempt->result === 'pass' ? 'text-green-600' : 'text-red-500' }} font-bold uppercase">{{ $attempt->result }}</span>
      </div>
    @endif
  </form>

</div>
@endsection
