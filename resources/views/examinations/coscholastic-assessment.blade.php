@extends('layouts.app')
@section('title', '360° Co-Scholastic Assessment')
@section('content')
<div class="space-y-6">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Co-Scholastic Assessment</h1>
      <p class="page-subtitle">NEP 2020 — Arts, Sports, Values, Health & Work Education</p>
    </div>
    <a href="{{ route('examinations.competencies') }}" class="btn btn-secondary btn-sm">Competency Master</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <form method="GET" class="card-flat py-3 flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Student ID</label>
      <input type="text" name="student_id" value="{{ request('student_id') }}" placeholder="Student ID…" class="input w-44">
    </div>
    <div>
      <label class="label">Term</label>
      <select name="term" class="select w-32">
        @foreach(['Term 1', 'Term 2', 'Annual'] as $t)
          <option value="{{ $t }}" @selected(request('term', 'Term 1') === $t)>{{ $t }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </form>

  @if($student)
    <form method="POST" action="{{ route('examinations.coscholastic.save-nep') }}">
      @csrf
      <input type="hidden" name="student_id" value="{{ $student->id }}">
      <input type="hidden" name="academic_year_id" value="{{ $academicYear?->id }}">
      <input type="hidden" name="term" value="{{ request('term', 'Term 1') }}">

      <div class="space-y-4">
        @foreach($areas as $areaKey => $subAreas)
          <div class="card">
            <h3 class="font-semibold text-slate-700 mb-3 capitalize">{{ str_replace('_', ' ', $areaKey) }}</h3>
            <div class="space-y-2">
              @foreach($subAreas as $subArea)
                @php
                  $existing = $assessments->get($areaKey)?->firstWhere('sub_area', $subArea);
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center py-1 border-b border-slate-50">
                  <label class="label mb-0">{{ $subArea }}</label>
                  <select name="areas[{{ $areaKey }}][{{ $subArea }}][grade]" class="select">
                    <option value="">— Not Assessed —</option>
                    @foreach(['A', 'B', 'C', 'D'] as $g)
                      <option value="{{ $g }}" @selected($existing?->grade === $g)>Grade {{ $g }}</option>
                    @endforeach
                  </select>
                  <input type="text" name="areas[{{ $areaKey }}][{{ $subArea }}][remarks]"
                         class="input" placeholder="Remarks…" value="{{ $existing?->remarks }}">
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>

      <div class="flex justify-end mt-4">
        <button type="submit" class="btn btn-primary">Save Co-Scholastic Assessment</button>
      </div>
    </form>
  @else
    <div class="card text-center py-10 text-slate-400">Enter a Student ID to begin assessment.</div>
  @endif

</div>
@endsection
