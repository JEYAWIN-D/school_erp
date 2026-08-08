@extends('layouts.app')
@section('title','Teacher Subject Allocation')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Teacher-Subject Allocation</h1>
    <a href="{{ route('academics.allocation.history') }}" class="btn btn-secondary btn-sm">View History →</a>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Assign Form --}}
    <form method="POST" action="{{ route('academics.allocation.save') }}" class="card space-y-4">
      @csrf
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Assign Teacher to Subject</h3>
      <div>
        <label class="label">Teacher <span class="text-red-500">*</span></label>
        <select name="employee_id" class="select" required>
          <option value="">Select teacher</option>
          @foreach($teachers as $t)<option value="{{ $t->id }}">{{ $t->full_name }}</option>@endforeach
        </select>
      </div>
      <div>
        <label class="label">Subject <span class="text-red-500">*</span></label>
        <select name="subject_id" class="select" required>
          <option value="">Select subject</option>
          @foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
        </select>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="label">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select" required>
            <option value="">Select class</option>
            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label class="label">Section</label>
          <select name="section_id" class="select">
            <option value="">All sections</option>
            @foreach($sections as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
          </select>
        </div>
      </div>
      <p class="text-xs text-slate-400">Leave section as "All sections" to assign one teacher for all sections. Select a specific section to allow different teachers per section of the same subject.</p>
      <button type="submit" class="btn btn-primary">Assign</button>
    </form>

    {{-- Current Allocations --}}
    <div class="card space-y-3">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Current Allocations</h3>
      {{-- Filters --}}
      <form method="GET" class="flex gap-2 flex-wrap">
        <select name="academic_year_id" class="select text-xs py-1 w-36" onchange="this.form.submit()">
          <option value="">All Years</option>
          @foreach($academicYears as $y)
          <option value="{{ $y->id }}" @selected($filterYearId==$y->id)>{{ $y->name }}</option>
          @endforeach
        </select>
        <select name="teacher_id" class="select text-xs py-1 w-44" onchange="this.form.submit()">
          <option value="">All Teachers</option>
          @foreach($teachers as $t)
          <option value="{{ $t->id }}" @selected(request('teacher_id')==$t->id)>{{ $t->full_name }}</option>
          @endforeach
        </select>
        <select name="class_id" class="select text-xs py-1 w-32" onchange="this.form.submit()">
          <option value="">All Classes</option>
          @foreach($classes as $c)
          <option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </form>

      <div class="overflow-y-auto max-h-96 divide-y divide-slate-100">
        @forelse($allocations as $a)
        <div class="flex items-start justify-between py-2">
          <div>
            <p class="text-sm font-medium text-slate-800">{{ $a->employee?->full_name }}</p>
            <p class="text-xs text-slate-400">
              {{ $a->subject?->name }} · {{ $a->class?->name }}
              {{ $a->section ? '/ '.$a->section->name : '(All sections)' }}
              @if($a->academicYear)<span class="ml-1 text-indigo-500">{{ $a->academicYear->name }}</span>@endif
            </p>
          </div>
          <form method="POST" action="{{ route('academics.allocation.delete', $a->id) }}">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-400 hover:text-red-600 text-xs ml-3 shrink-0">Remove</button>
          </form>
        </div>
        @empty
        <p class="text-slate-400 text-sm text-center py-6">No allocations for the selected filters.</p>
        @endforelse
      </div>
      @if($allocations->hasPages())<div class="text-xs mt-2">{{ $allocations->links() }}</div>@endif
    </div>
  </div>
</div>
@endsection
