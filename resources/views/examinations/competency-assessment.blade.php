@extends('layouts.app')
@section('title', 'Competency Assessment')
@section('content')
<div class="space-y-6">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Competency Assessment</h1>
      <p class="page-subtitle">NEP 2020 — Achieved / Partially Achieved / Not Achieved</p>
    </div>
    <a href="{{ route('examinations.competencies') }}" class="btn btn-secondary btn-sm">Manage Competencies</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  {{-- Student + Term selector --}}
  <form method="GET" class="card-flat py-4 flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Student (Admission No / Name)</label>
      <input type="text" name="student_search" value="{{ request('student_search', $student?->admission_number) }}"
             placeholder="Search student…" class="input w-56"
             list="student-list" id="student-search-field">
      <input type="hidden" name="student_id" id="student-id-field" value="{{ request('student_id') }}">
    </div>
    <div>
      <label class="label">Term</label>
      <select name="term" class="select w-36">
        @foreach(['Term 1', 'Term 2', 'Annual'] as $t)
          <option value="{{ $t }}" @selected(request('term', 'Term 1') === $t)>{{ $t }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </form>

  @if($student && $competencies->count())
    <form method="POST" action="{{ route('examinations.competency-assessment.save') }}">
      @csrf
      <input type="hidden" name="student_id" value="{{ $student->id }}">
      <input type="hidden" name="academic_year_id" value="{{ $academicYear?->id }}">
      <input type="hidden" name="term" value="{{ request('term', 'Term 1') }}">

      <div class="card">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="font-semibold text-slate-700">{{ $student->full_name }}</h3>
            <p class="text-sm text-slate-400">{{ $student->currentEnrollment?->class?->name }} | {{ request('term', 'Term 1') }}</p>
          </div>
          <button type="submit" class="btn btn-primary btn-sm">Save All</button>
        </div>

        @php $currentSubject = null; @endphp
        <div class="space-y-1">
          @foreach($competencies->groupBy('subject_id') as $subjectId => $comps)
            @php $subject = $comps->first()->subject; @endphp
            <div class="pt-3 pb-1">
              <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">{{ $subject?->name }}</p>
            </div>
            @foreach($comps as $comp)
              @php
                $key = $comp->id . '_' . request('term', 'Term 1');
                $existing = $assessments[$key] ?? null;
              @endphp
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-2 border-b border-slate-50 items-start">
                <div class="sm:col-span-1">
                  @if($comp->code)
                    <span class="text-xs text-blue-500 font-mono">{{ $comp->code }}</span><br>
                  @endif
                  <span class="text-sm text-slate-700">{{ $comp->name }}</span>
                </div>
                <div>
                  <select name="assessments[{{ $comp->id }}][level]" class="select w-full text-sm">
                    @foreach(['not_assessed' => 'Not Assessed', 'achieved' => 'Achieved', 'partially_achieved' => 'Partially Achieved', 'not_achieved' => 'Not Achieved'] as $val => $label)
                      <option value="{{ $val }}" @selected(($existing?->level ?? 'not_assessed') === $val)>{{ $label }}</option>
                    @endforeach
                  </select>
                </div>
                <div>
                  <input type="text" name="assessments[{{ $comp->id }}][remarks]" class="input w-full text-sm"
                         placeholder="Remarks (optional)" value="{{ $existing?->remarks }}">
                </div>
              </div>
            @endforeach
          @endforeach
        </div>

        <div class="flex justify-end mt-4">
          <button type="submit" class="btn btn-primary">Save Assessment</button>
        </div>
      </div>
    </form>
  @elseif($student)
    <div class="card text-center py-10 text-slate-400">
      No competencies defined for {{ $student->currentEnrollment?->class?->name }}.
      <a href="{{ route('examinations.competencies') }}" class="text-blue-600 hover:underline">Add competencies first</a>.
    </div>
  @else
    <div class="card text-center py-10 text-slate-400">
      Search for a student above to begin assessment.
    </div>
  @endif

</div>
@endsection
