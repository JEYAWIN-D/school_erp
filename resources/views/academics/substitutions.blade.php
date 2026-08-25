@extends('layouts.app')
@section('title', 'Substitution Management')
@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Substitution Register</h1>
      <p class="page-subtitle">Track teacher substitutions and replacements</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- Add form --}}
    <div class="lg:col-span-2">
      <form method="POST" action="{{ route('academics.substitutions.store') }}" class="card space-y-4">
        @csrf
        <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Record Substitution</h3>

        <div>
          <label class="label">Date <span class="text-red-500">*</span></label>
          <input type="date" name="date" value="{{ old('date', $date) }}" class="input">
        </div>
        <div>
          <label class="label">Absent Teacher <span class="text-red-500">*</span></label>
          <select name="absent_teacher_id" class="select {{ (isset($errors) && $errors->has('absent_teacher_id')) ? 'input-error' : '' }}">
            <option value="">Select teacher</option>
            @foreach($teachers as $t)
              <option value="{{ $t->id }}" @selected(old('absent_teacher_id') == $t->id)>{{ $t->first_name }} {{ $t->last_name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Substitute Teacher <span class="text-red-500">*</span></label>
          <select name="substitute_teacher_id" class="select {{ (isset($errors) && $errors->has('substitute_teacher_id')) ? 'input-error' : '' }}">
            <option value="">Select substitute</option>
            @foreach($teachers as $t)
              <option value="{{ $t->id }}" @selected(old('substitute_teacher_id') == $t->id)>{{ $t->first_name }} {{ $t->last_name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select">
            <option value="">Select class</option>
            @foreach($classes as $cls)
              <option value="{{ $cls->id }}" @selected(old('class_id') == $cls->id)>{{ $cls->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label">Period No. <span class="text-red-500">*</span></label>
            <input type="number" name="period_number" value="{{ old('period_number') }}" min="1" max="12" class="input">
          </div>
          <div>
            <label class="label">Subject</label>
            <select name="subject_id" class="select">
              <option value="">Optional</option>
              @foreach($subjects as $s)
                <option value="{{ $s->id }}" @selected(old('subject_id') == $s->id)>{{ $s->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div>
          <label class="label">Remarks</label>
          <input type="text" name="remarks" value="{{ old('remarks') }}" class="input" placeholder="Optional notes">
        </div>
        <button type="submit" class="btn btn-primary w-full justify-center">Record Substitution</button>
      </form>
    </div>

    {{-- List --}}
    <div class="lg:col-span-3">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-800">
          Substitutions on {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
        </h3>
        <form method="GET">
          <input type="date" name="date" value="{{ $date }}" class="input w-40 h-8 text-sm" onchange="this.form.submit()">
        </form>
      </div>
      @forelse($substitutions as $sub)
        <div class="card mb-3">
          <div class="flex items-start justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="badge-red text-xs">Period {{ $sub->period_number }}</span>
                @if($sub->subject) <span class="badge-slate text-xs">{{ $sub->subject->name }}</span> @endif
                <span class="badge-blue text-xs">{{ $sub->class->name }}</span>
              </div>
              <p class="text-sm">
                <span class="text-slate-500">Absent:</span>
                <span class="font-medium text-slate-800">{{ $sub->absentTeacher->first_name }} {{ $sub->absentTeacher->last_name }}</span>
              </p>
              <p class="text-sm">
                <span class="text-slate-500">Substitute:</span>
                <span class="font-semibold text-green-700">{{ $sub->substituteTeacher->first_name }} {{ $sub->substituteTeacher->last_name }}</span>
              </p>
              @if($sub->remarks) <p class="text-xs text-slate-400 mt-1">{{ $sub->remarks }}</p> @endif
            </div>
            <form method="POST" action="{{ route('academics.substitutions.delete', $sub->id) }}" onsubmit="return confirm('Remove?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn-icon text-red-400 hover:text-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </form>
          </div>
        </div>
      @empty
        <div class="card text-center py-8 text-slate-400">No substitutions recorded for this date.</div>
      @endforelse
    </div>

  </div>
</div>
@endsection
