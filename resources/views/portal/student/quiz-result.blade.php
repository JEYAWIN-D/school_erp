@extends('portal.layout')
@section('title', 'Quiz Result')
@section('content')

@php
  $pct = $attempt->percentage();
  $passed = $pct >= 50;
  $badgeBg = $passed ? '#f0fdf4' : '#fef2f2';
  $badgeClr = $passed ? '#16a34a' : '#dc2626';
@endphp

<div class="portal-card" style="text-align:center;padding:2rem 1rem;margin-bottom:1rem;">
  <div style="width:5rem;height:5rem;border-radius:50%;background:{{ $badgeBg }};border:3px solid {{ $badgeClr }};display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
    <span style="font-size:1.75rem;font-weight:800;color:{{ $badgeClr }};">{{ $pct }}%</span>
  </div>
  <h2 style="font-size:1.25rem;font-weight:700;color:#0f172a;margin-bottom:.25rem;">{{ $passed ? 'Well done!' : 'Keep practising' }}</h2>
  <p style="font-size:.875rem;color:#64748b;">{{ $attempt->quiz->title }}</p>
  <p style="font-size:.875rem;color:#334155;margin-top:.5rem;">
    Score: <strong>{{ $attempt->score }}</strong> / {{ $attempt->total_marks }}
    &bull; {{ $attempt->timeUsedMinutes() }} min
  </p>
</div>

{{-- Answers review --}}
<div class="portal-card" style="margin-bottom:1rem;">
  <p style="font-size:.875rem;font-weight:700;color:#0f172a;margin-bottom:.875rem;">Answers Review</p>
  @foreach($attempt->quiz->questions as $i => $q)
  @php
    $ans = $attempt->answers->firstWhere('question_id', $q->id);
    $correct = $ans?->is_correct;
    $rowBg = $correct ? '#f0fdf4' : '#fef2f2';
    $icon  = $correct ? '✓' : '✗';
    $iconClr = $correct ? '#16a34a' : '#dc2626';
  @endphp
  <div style="padding:.75rem;background:{{ $rowBg }};border-radius:.5rem;margin-bottom:.5rem;">
    <p style="font-size:.8rem;font-weight:600;color:#1e293b;margin-bottom:.375rem;">
      <span style="color:{{ $iconClr }};margin-right:.25rem;">{{ $icon }}</span>
      Q{{ $i+1 }}. {{ $q->question }}
    </p>
    <p style="font-size:.75rem;color:#64748b;">Your answer: <strong>{{ $ans?->given_answer ?: '—' }}</strong></p>
    @if(!$correct)
      <p style="font-size:.75rem;color:#16a34a;">Correct: <strong>{{ $q->correct_answer }}</strong></p>
    @endif
  </div>
  @endforeach
</div>

<div style="text-align:center;">
  <a href="{{ route('portal.student.academics') }}" style="font-size:.875rem;color:#7c3aed;font-weight:600;">← Back to Academics</a>
</div>

@endsection
