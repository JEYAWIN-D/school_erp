@extends('layouts.app')
@section('title', 'Quiz Builder')
@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Quiz Builder — {{ $course->title }}</h1>
    <a href="{{ route('lms.courses.show', $course->id) }}" class="btn-secondary btn-sm">← Course</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Create quiz --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">New Quiz</h3>
      <form method="POST" action="{{ route('lms.quiz.store', $course->id) }}" class="space-y-3">
        @csrf
        <div>
          <label class="label">Quiz Title</label>
          <input type="text" name="title" value="{{ old('title') }}" class="input w-full" required>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="label">Duration (min)</label>
            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 30) }}" class="input w-full" min="1">
          </div>
          <div>
            <label class="label">Marks/Question</label>
            <input type="number" name="marks_per_question" value="{{ old('marks_per_question', 1) }}" class="input w-full" step="0.5" min="0">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="label">Available From</label>
            <input type="datetime-local" name="available_from" value="{{ old('available_from') }}" class="input w-full text-sm">
          </div>
          <div>
            <label class="label">Available To</label>
            <input type="datetime-local" name="available_to" value="{{ old('available_to') }}" class="input w-full text-sm">
          </div>
        </div>
        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" name="randomise" value="1" checked class="rounded">
          Randomise question order
        </label>
        <button type="submit" class="btn-primary w-full">Create Quiz</button>
      </form>
    </div>

    {{-- Existing quizzes --}}
    <div class="lg:col-span-2 space-y-4">
      @forelse($quizzes as $quiz)
      <div class="card">
        <div class="flex items-center justify-between mb-3">
          <div>
            <h3 class="font-semibold text-slate-700">{{ $quiz->title }}</h3>
            <p class="text-xs text-slate-400">{{ $quiz->duration_minutes }} min &bull; {{ $quiz->questions->count() }} questions &bull; {{ $quiz->marks_per_question }} marks each</p>
          </div>
          <div class="flex gap-2">
            <a href="{{ route('lms.quiz.questions', $quiz->id) }}" class="btn-xs btn-primary">Manage Questions</a>
            <a href="{{ route('lms.quiz.attempts', $quiz->id) }}" class="btn-xs btn-secondary">Attempts</a>
          </div>
        </div>
        @if($quiz->available_from || $quiz->available_to)
        <p class="text-xs text-slate-400">
          Available: {{ $quiz->available_from?->format('d M Y H:i') ?? 'Always' }}
          → {{ $quiz->available_to?->format('d M Y H:i') ?? 'No end' }}
        </p>
        @endif
        <div class="mt-2 flex items-center gap-2">
          @if($quiz->isAvailable())
            <span class="badge-green text-xs">Open</span>
          @elseif($quiz->available_from && now()->lt($quiz->available_from))
            <span class="badge-blue text-xs">Upcoming</span>
          @else
            <span class="badge-red text-xs">Closed</span>
          @endif
        </div>
      </div>
      @empty
      <div class="card text-center py-10">
        <p class="text-slate-400 text-sm">No quizzes yet. Create one on the left.</p>
      </div>
      @endforelse
    </div>

  </div>
</div>
@endsection
