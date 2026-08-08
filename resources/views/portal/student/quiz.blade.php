@extends('portal.layout')
@section('title', $quiz->title)
@section('content')

@if($existing)
<div class="portal-card" style="margin-bottom:1rem;background:#fffbeb;border:1px solid #fcd34d;">
  <p style="font-size:.875rem;color:#92400e;font-weight:600;">You already completed this quiz.</p>
  <p style="font-size:.8rem;color:#78350f;margin-top:.25rem;">Score: {{ $existing->score }}/{{ $existing->total_marks }} ({{ $existing->percentage() }}%)</p>
  <a href="{{ route('portal.quiz.result', $existing->id) }}" style="display:inline-block;margin-top:.75rem;font-size:.8rem;color:#7c3aed;font-weight:600;">View Result →</a>
</div>
@endif

<div class="portal-card" style="margin-bottom:1rem;">
  <h2 style="font-size:1.1rem;font-weight:700;color:#0f172a;margin-bottom:.25rem;">{{ $quiz->title }}</h2>
  @if($quiz->course)
    <p style="font-size:.8rem;color:#64748b;margin-bottom:.5rem;">{{ $quiz->course->title }}</p>
  @endif
  <div style="display:flex;gap:1rem;flex-wrap:wrap;">
    <span style="font-size:.75rem;color:#64748b;">{{ $questions->count() }} questions</span>
    @if($quiz->duration_minutes)
      <span style="font-size:.75rem;color:#64748b;">⏱ {{ $quiz->duration_minutes }} min</span>
    @endif
    @if($quiz->marks_per_question)
      <span style="font-size:.75rem;color:#64748b;">{{ $quiz->marks_per_question }} mark(s) per question</span>
    @endif
    @if($quiz->negative_marks)
      <span style="font-size:.75rem;color:#dc2626;">-{{ $quiz->negative_marks }} for wrong</span>
    @endif
  </div>
</div>

<form method="POST" action="{{ route('portal.quiz.submit', $quiz->id) }}"
      onsubmit="return confirm('Submit quiz? You cannot change answers after submission.');">
  @csrf

  @foreach($questions as $i => $q)
  <div class="portal-card" style="margin-bottom:.75rem;">
    <p style="font-size:.875rem;font-weight:600;color:#1e293b;margin-bottom:.75rem;">
      <span style="color:#7c3aed;">Q{{ $i+1 }}.</span> {{ $q->question }}
    </p>

    @if($q->type === 'mcq' && $q->options)
      @foreach($q->options as $opt)
      <label style="display:flex;align-items:center;gap:.5rem;padding:.5rem .625rem;margin-bottom:.375rem;border:1px solid #e2e8f0;border-radius:.5rem;cursor:pointer;font-size:.85rem;color:#334155;">
        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}"
               style="width:1rem;height:1rem;accent-color:#7c3aed;">
        {{ $opt }}
      </label>
      @endforeach
    @elseif($q->type === 'true_false')
      @foreach(['True', 'False'] as $opt)
      <label style="display:flex;align-items:center;gap:.5rem;padding:.5rem .625rem;margin-bottom:.375rem;border:1px solid #e2e8f0;border-radius:.5rem;cursor:pointer;font-size:.85rem;color:#334155;">
        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}"
               style="width:1rem;height:1rem;accent-color:#7c3aed;">
        {{ $opt }}
      </label>
      @endforeach
    @else
      <input type="text" name="answers[{{ $q->id }}]" placeholder="Type your answer…"
             style="width:100%;padding:.5rem .75rem;border:1px solid #e2e8f0;border-radius:.5rem;font-size:.875rem;color:#1e293b;background:#fff;box-sizing:border-box;">
    @endif
  </div>
  @endforeach

  <div style="text-align:center;margin-top:1.25rem;">
    <button type="submit" style="background:#7c3aed;color:#fff;border:none;padding:.75rem 2.5rem;border-radius:.625rem;font-size:.9rem;font-weight:700;cursor:pointer;">
      Submit Quiz
    </button>
  </div>
</form>

@endsection
