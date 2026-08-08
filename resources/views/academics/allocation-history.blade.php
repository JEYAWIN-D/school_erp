@extends('layouts.app')
@section('title','Subject Allocation History')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Subject Allocation History</h1>
      <p class="page-subtitle">View teacher-subject assignments across all academic years</p>
    </div>
    <a href="{{ route('academics.allocation') }}" class="btn btn-secondary btn-sm">← Current Allocations</a>
  </div>

  {{-- Filters --}}
  <form method="GET" class="card space-y-0 py-3 px-4">
    <div class="flex gap-3 flex-wrap items-end">
      <div>
        <label class="label text-xs">Academic Year</label>
        <select name="academic_year_id" class="select w-40">
          <option value="">All Years</option>
          @foreach($academicYears as $y)
          <option value="{{ $y->id }}" @selected(request('academic_year_id')==$y->id)>{{ $y->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label text-xs">Teacher</label>
        <select name="teacher_id" class="select w-48">
          <option value="">All Teachers</option>
          @foreach($teachers as $t)
          <option value="{{ $t->id }}" @selected(request('teacher_id')==$t->id)>{{ $t->full_name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label text-xs">Subject</label>
        <select name="subject_id" class="select w-40">
          <option value="">All Subjects</option>
          @foreach($subjects as $s)
          <option value="{{ $s->id }}" @selected(request('subject_id')==$s->id)>{{ $s->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label text-xs">Class</label>
        <select name="class_id" class="select w-36">
          <option value="">All Classes</option>
          @foreach($classes as $c)
          <option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      @if(request()->hasAny(['academic_year_id','teacher_id','subject_id','class_id']))
        <a href="{{ route('academics.allocation.history') }}" class="btn btn-secondary btn-sm text-slate-400">Clear</a>
      @endif
    </div>
  </form>

  <div class="card overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Academic Year','Teacher','Subject','Class','Section','Assigned'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($history as $a)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3">
            <span class="badge-{{ $a->academicYear?->is_current ? 'green' : 'slate' }} text-xs">
              {{ $a->academicYear?->name ?? 'Unspecified' }}
            </span>
          </td>
          <td class="px-4 py-3 font-medium text-slate-800">{{ $a->employee?->full_name ?? '—' }}</td>
          <td class="px-4 py-3 text-slate-700">{{ $a->subject?->name ?? '—' }}
            @if($a->subject?->type)<span class="text-xs text-slate-400 ml-1">({{ $a->subject->type }})</span>@endif
          </td>
          <td class="px-4 py-3 text-slate-600">{{ $a->class?->name ?? '—' }}</td>
          <td class="px-4 py-3 text-slate-500">{{ $a->section?->name ?? <span class="text-slate-300">All</span> }}</td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $a->created_at?->format('d M Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No allocation records found for the selected filters.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($history->hasPages())<div class="px-4 pb-3">{{ $history->links() }}</div>@endif
  </div>
</div>
@endsection
