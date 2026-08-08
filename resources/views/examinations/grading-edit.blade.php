@extends('layouts.app')
@section('title','Edit Grading Scheme')
@section('content')
<div class="space-y-6 max-w-2xl">
  <h1 class="page-title">Edit: {{ $scheme->name }}</h1>
  <div class="card space-y-4">
    <form method="POST" action="{{ route('examinations.grading.update',$scheme->id) }}" class="space-y-4">
      @csrf @method('PUT')
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Scheme Name</label><input type="text" name="name" class="input" value="{{ old('name',$scheme->name) }}" required></div>
        <div><label class="label">Pass Percentage</label><input type="number" name="pass_percentage" class="input" value="{{ old('pass_percentage',$scheme->pass_percentage) }}" min="0" max="100"></div>
      </div>
      <div>
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-semibold text-slate-700">Grade Ranges</h3>
          <button type="button" id="addRange" class="text-indigo-600 text-sm hover:underline">+ Add Row</button>
        </div>
        <div id="rangesContainer" class="space-y-2">
          @foreach($scheme->ranges->sortByDesc('min_marks') as $i=>$r)
          <div class="grid grid-cols-5 gap-2 items-center range-row">
            <div><input type="number" name="ranges[{{ $i }}][min_marks]" class="input text-sm" placeholder="Min %" value="{{ $r->min_marks }}" required></div>
            <div><input type="number" name="ranges[{{ $i }}][max_marks]" class="input text-sm" placeholder="Max %" value="{{ $r->max_marks }}" required></div>
            <div><input type="text" name="ranges[{{ $i }}][grade]" class="input text-sm" placeholder="Grade" value="{{ $r->grade }}" required></div>
            <div><input type="text" name="ranges[{{ $i }}][description]" class="input text-sm" placeholder="Description" value="{{ $r->description }}"></div>
            <div><input type="number" name="ranges[{{ $i }}][gpa_points]" class="input text-sm" placeholder="GPA" step="0.1" value="{{ $r->gpa_points }}"></div>
          </div>
          @endforeach
        </div>
        <div class="grid grid-cols-5 gap-2 text-xs text-slate-400 mt-1 px-1">
          <span>Min %</span><span>Max %</span><span>Grade</span><span>Description</span><span>GPA Points</span>
        </div>
      </div>
      <div class="flex gap-3 pt-2 border-t border-slate-100">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('examinations.grading') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<script>
let idx = {{ $scheme->ranges->count() }};
document.getElementById('addRange').addEventListener('click', function() {
  const container = document.getElementById('rangesContainer');
  const div = document.createElement('div');
  div.className = 'grid grid-cols-5 gap-2 items-center range-row';
  div.innerHTML = `<div><input type="number" name="ranges[${idx}][min_marks]" class="input text-sm" placeholder="Min %" required></div><div><input type="number" name="ranges[${idx}][max_marks]" class="input text-sm" placeholder="Max %" required></div><div><input type="text" name="ranges[${idx}][grade]" class="input text-sm" placeholder="Grade" required></div><div><input type="text" name="ranges[${idx}][description]" class="input text-sm" placeholder="Description"></div><div><input type="number" name="ranges[${idx}][gpa_points]" class="input text-sm" placeholder="GPA" step="0.1"></div>`;
  container.appendChild(div);
  idx++;
});
</script>
@endsection
