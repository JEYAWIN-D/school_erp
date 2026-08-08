@extends('layouts.app')
@section('title','New Exam')
@section('content')
<div class="max-w-xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="{{ route('examinations.index') }}" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="page-title">Create Examination</h1>
  </div>
  <form method="POST" action="{{ route('examinations.store') }}" class="card space-y-4">
    @csrf
    <div><label class="label">Exam Name <span class="text-red-500">*</span></label><input type="text" name="name" value="{{ old('name') }}" class="input @error('name') input-error @enderror" placeholder="e.g. Term 1 Exam"></div>
    <div><label class="label">Type <span class="text-red-500">*</span></label>
      <select name="type" class="select">
        @foreach(['unit_test'=>'Unit Test','term'=>'Term Exam','final'=>'Final Exam','pre_board'=>'Pre-Board','practice'=>'Practice Test'] as $v=>$l)
        <option value="{{ $v }}" @selected(old('type')===$v)>{{ $l }}</option>
        @endforeach
      </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">Start Date <span class="text-red-500">*</span></label><input type="date" name="start_date" value="{{ old('start_date') }}" class="input"></div>
      <div><label class="label">End Date <span class="text-red-500">*</span></label><input type="date" name="end_date" value="{{ old('end_date') }}" class="input"></div>
    </div>
    <div><label class="label">Passing % (default 33)</label><input type="number" name="passing_percentage" value="{{ old('passing_percentage',33) }}" class="input" min="0" max="100"></div>
    <div x-data="{ isExternal: {{ old('is_external') ? 'true' : 'false' }} }">
      <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
        <input type="checkbox" name="is_external" value="1" x-model="isExternal" @checked(old('is_external'))
          class="w-4 h-4 text-indigo-600 rounded">
        External Examination (Conducted by external board/organisation)
      </label>
      <div x-show="isExternal" x-transition class="mt-2">
        <label class="label text-xs">Conducting Body</label>
        <input type="text" name="conducting_body" value="{{ old('conducting_body') }}"
          class="input" placeholder="e.g. CBSE, State Board, NEET, JEE">
      </div>
    </div>
    <div>
      <label class="label">Best-of-N Subjects (CBSE type)</label>
      <input type="number" name="best_of_n_subjects" value="{{ old('best_of_n_subjects') }}" class="input" min="1" placeholder="Leave blank to count all subjects">
      <p class="text-xs text-slate-400 mt-1">If set, only the top N subjects' marks are counted for total/percentage.</p>
    </div>
    <div class="border-t border-slate-100 pt-4">
      <h3 class="font-medium text-slate-700 mb-3 text-sm">Cumulative / Term Settings</h3>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="label">Term Label</label>
          <input type="text" name="term_label" value="{{ old('term_label') }}" class="input" placeholder="e.g. Term 1, Half-Yearly">
        </div>
        <div>
          <label class="label">Weightage %</label>
          <input type="number" name="weightage_percent" value="{{ old('weightage_percent') }}" class="input" min="0" max="100" step="0.01" placeholder="e.g. 30">
        </div>
      </div>
      <label class="flex items-center gap-2 mt-3 text-sm">
        <input type="checkbox" name="is_cumulative_component" value="1" @checked(old('is_cumulative_component'))>
        Include in Cumulative Marks calculation
      </label>
    </div>
    <div class="flex justify-end gap-3 pt-2">
      <a href="{{ route('examinations.index') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Create Exam</button>
    </div>
  </form>
</div>
@endsection
