@extends('layouts.app')
@section('title', 'Manage Questions — ' . $exam->title)
@section('content')
<div class="space-y-6">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('online-exams.index') }}" class="btn-icon">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h1 class="page-title">{{ $exam->title }}</h1>
        <p class="page-subtitle">{{ $exam->class?->name }} | {{ $exam->start_time->format('d M Y, g:i A') }} | {{ $exam->duration_minutes }} min</p>
      </div>
    </div>
    <div class="flex gap-2">
      <span class="badge-slate">Total: {{ $totalMarks }} marks</span>
      @if($exam->status === 'draft')
        <form method="POST" action="{{ route('online-exams.publish', $exam->id) }}">
          @csrf
          <button type="submit" class="btn btn-primary btn-sm">Publish Exam</button>
        </form>
      @else
        <span class="badge-green capitalize">{{ $exam->status }}</span>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Questions in exam --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-3 pb-2 border-b border-slate-100">
        Questions in Exam ({{ $exam->examQuestions->count() }})
      </h3>
      @forelse($exam->examQuestions->sortBy('sort_order') as $eq)
        <div class="flex items-start gap-3 py-2 border-b border-slate-50">
          <span class="text-xs text-slate-400 w-5 text-right mt-1">{{ $loop->iteration }}.</span>
          <div class="flex-1 min-w-0">
            <p class="text-sm text-slate-700">{{ Str::limit($eq->question?->question, 120) }}</p>
            <div class="flex items-center gap-2 mt-1 flex-wrap">
              <span class="badge-slate text-xs capitalize">{{ $eq->question?->question_type }}</span>
              <span class="text-xs text-slate-400">{{ $eq->question?->marks }} marks</span>
              @if($eq->question?->difficulty)
                <span class="text-xs text-slate-400 capitalize">{{ $eq->question->difficulty }}</span>
              @endif
            </div>
          </div>
          <form method="POST" action="{{ route('online-exams.question.remove', [$exam->id, $eq->question_bank_id]) }}">
            @csrf @method('DELETE')
            <button type="submit" class="btn-icon text-red-400 hover:text-red-600 mt-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </form>
        </div>
      @empty
        <p class="text-slate-400 text-sm py-4 text-center">No questions added yet.</p>
      @endforelse
    </div>

    {{-- Add from question bank --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-3 pb-2 border-b border-slate-100">Add from Question Bank</h3>

      <form method="GET" class="flex flex-wrap gap-2 mb-4">
        <input type="hidden" name="id" value="{{ $exam->id }}">
        <select name="subject_id" class="select w-36 text-sm">
          <option value="">All Subjects</option>
          @foreach($subjects as $sub)
            <option value="{{ $sub->id }}" @selected(request('subject_id') == $sub->id)>{{ $sub->name }}</option>
          @endforeach
        </select>
        <select name="question_type" class="select w-32 text-sm">
          <option value="">All Types</option>
          @foreach(['mcq' => 'MCQ', 'true_false' => 'True/False', 'fill_blank' => 'Fill Blank', 'short_answer' => 'Short Answer'] as $k => $v)
            <option value="{{ $k }}" @selected(request('question_type') === $k)>{{ $v }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      </form>

      <form method="POST" action="{{ route('online-exams.question.add', $exam->id) }}">
        @csrf
        <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
          @forelse($bank as $q)
            <label class="flex items-start gap-3 py-2 border border-transparent hover:border-slate-200 rounded-lg px-2 cursor-pointer">
              <input type="checkbox" name="question_ids[]" value="{{ $q->id }}" class="mt-1 rounded">
              <div class="flex-1 min-w-0">
                <p class="text-sm text-slate-700">{{ Str::limit($q->question, 100) }}</p>
                <span class="badge-slate text-xs capitalize">{{ $q->question_type }}</span>
                <span class="text-xs text-slate-400 ml-1">{{ $q->marks }} marks</span>
              </div>
            </label>
          @empty
            <p class="text-slate-400 text-sm text-center py-4">All questions from this class/filter are already added or none exist.</p>
          @endforelse
        </div>

        @if($bank->count())
          <div class="mt-4 flex justify-between items-center">
            <button type="button" onclick="this.closest('form').querySelectorAll('input[type=checkbox]').forEach(c=>c.checked=true)" class="text-xs text-blue-600 hover:underline">Select All</button>
            <button type="submit" class="btn btn-primary btn-sm">Add Selected</button>
          </div>
        @endif
      </form>

      @if($bank->hasPages())
        <div class="mt-3 text-xs text-slate-400 flex justify-between">
          <span>{{ $bank->firstItem() }}–{{ $bank->lastItem() }} of {{ $bank->total() }}</span>
          {{ $bank->links() }}
        </div>
      @endif
    </div>

  </div>
</div>
@endsection
