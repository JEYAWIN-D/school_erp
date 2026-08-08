@extends('layouts.app')
@section('title','Edit Question')
@section('content')
<div class="space-y-6 max-w-2xl">
  <h1 class="page-title">Edit Question</h1>
  <form method="POST" action="{{ route('examinations.qbank.update',$question->id) }}" class="card space-y-4">
    @csrf @method('PUT')
    <div class="grid grid-cols-2 gap-3">
      <div><label class="label">Subject <span class="text-red-500">*</span></label>
        <select name="subject_id" class="select" required>
          @foreach($subjects as $s)<option value="{{ $s->id }}" @selected($question->subject_id==$s->id)>{{ $s->name }}</option>@endforeach
        </select>
      </div>
      <div><label class="label">Class</label>
        <select name="class_id" class="select">
          <option value="">All</option>
          @foreach($classes as $c)<option value="{{ $c->id }}" @selected($question->class_id==$c->id)>{{ $c->name }}</option>@endforeach
        </select>
      </div>
    </div>
    <div><label class="label">Question Text <span class="text-red-500">*</span></label>
      <textarea name="question_text" class="input h-24" required>{{ old('question_text',$question->question_text) }}</textarea>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div><label class="label">Type</label>
        <select name="question_type" class="select">
          @foreach(['mcq'=>'MCQ','short_answer'=>'Short Answer','long_answer'=>'Long Answer','true_false'=>'True/False','fill_blank'=>'Fill Blank'] as $k=>$v)
          <option value="{{ $k }}" @selected($question->question_type===$k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div><label class="label">Difficulty</label>
        <select name="difficulty" class="select">
          @foreach(['easy'=>'Easy','medium'=>'Medium','hard'=>'Hard'] as $k=>$v)
          <option value="{{ $k }}" @selected($question->difficulty===$k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div><label class="label">Marks</label>
        <input type="number" name="marks" class="input" value="{{ old('marks',$question->marks) }}" min="0.5" step="0.5">
      </div>
    </div>
    <div><label class="label">Answer / Correct Option</label>
      <input type="text" name="answer" class="input" value="{{ old('answer',$question->answer) }}">
    </div>
    <div><label class="label">Chapter / Unit</label>
      <input type="text" name="chapter" class="input" value="{{ old('chapter',$question->chapter) }}">
    </div>
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="{{ route('examinations.qbank') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
