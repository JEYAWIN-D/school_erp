@extends('layouts.app')
@section('title', 'Quiz Questions')
@section('content')
<div class="space-y-5" x-data="{ qtype: 'mcq_single' }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">{{ $quiz->title }}</h1>
      <p class="text-sm text-slate-400">{{ $quiz->questions->count() }} questions &bull; {{ $quiz->duration_minutes }} min</p>
    </div>
    <a href="{{ route('lms.quiz.builder', $quiz->course_id) }}" class="btn-secondary btn-sm">← Quiz Builder</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Add question --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Add Question</h3>
      <form method="POST" action="{{ route('lms.quiz.question.store', $quiz->id) }}" class="space-y-3">
        @csrf
        <div>
          <label class="label">Question Type</label>
          <select name="type" x-model="qtype" class="select w-full">
            <option value="mcq_single">MCQ — Single Correct</option>
            <option value="mcq_multi">MCQ — Multiple Correct</option>
            <option value="true_false">True / False</option>
            <option value="fill_blank">Fill in the Blank</option>
          </select>
        </div>
        <div>
          <label class="label">Question</label>
          <textarea name="question" rows="3" class="input w-full" required></textarea>
        </div>

        {{-- MCQ options --}}
        <div x-show="qtype === 'mcq_single' || qtype === 'mcq_multi'" class="space-y-2">
          <label class="label">Options (one per line)</label>
          <input type="text" name="options[]" placeholder="Option A" class="input w-full text-sm">
          <input type="text" name="options[]" placeholder="Option B" class="input w-full text-sm">
          <input type="text" name="options[]" placeholder="Option C" class="input w-full text-sm">
          <input type="text" name="options[]" placeholder="Option D" class="input w-full text-sm">
        </div>

        <div>
          <label class="label">
            Correct Answer
            <span x-show="qtype === 'mcq_single' || qtype === 'mcq_multi'" class="text-xs font-normal text-slate-400">(enter exact option text)</span>
            <span x-show="qtype === 'true_false'" class="text-xs font-normal text-slate-400">(True or False)</span>
          </label>
          <input type="text" name="correct_answer" class="input w-full" required>
        </div>
        <div>
          <label class="label">Marks</label>
          <input type="number" name="marks" value="{{ $quiz->marks_per_question }}" step="0.5" min="0" class="input w-full">
        </div>
        <button type="submit" class="btn-primary w-full">Add Question</button>
      </form>
    </div>

    {{-- Questions list --}}
    <div class="lg:col-span-2 space-y-3">
      @forelse($quiz->questions as $q)
      <div class="card">
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <span class="text-xs font-medium text-slate-400">Q{{ $loop->iteration }}</span>
              <span class="badge-blue text-xs">{{ str_replace('_', ' ', $q->type) }}</span>
              <span class="text-xs text-slate-400">{{ $q->marks }} marks</span>
            </div>
            <p class="text-sm font-medium text-slate-700">{{ $q->question }}</p>
            @if($q->options)
              <div class="mt-2 grid grid-cols-2 gap-1">
                @foreach($q->options as $opt)
                  <div class="text-xs px-2 py-1 rounded {{ $opt === $q->correct_answer ? 'bg-green-50 text-green-700 font-medium' : 'bg-slate-50 text-slate-500' }}">
                    {{ $opt }}
                    @if($opt === $q->correct_answer) ✓ @endif
                  </div>
                @endforeach
              </div>
            @else
              <p class="text-xs text-green-600 mt-1">Answer: {{ $q->correct_answer }}</p>
            @endif
          </div>
          <form method="POST" action="{{ route('lms.quiz.question.delete', $q->id) }}">
            @csrf @method('DELETE')
            <button type="submit" onclick="return confirm('Delete question?')" class="text-red-400 hover:text-red-600 text-lg leading-none px-1">×</button>
          </form>
        </div>
      </div>
      @empty
      <div class="card text-center py-10">
        <p class="text-slate-400 text-sm">No questions yet. Add questions on the left.</p>
      </div>
      @endforelse
    </div>

  </div>
</div>
@endsection
