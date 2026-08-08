@extends('layouts.app')
@section('title', 'Student Directory')
@section('content')
<div class="space-y-6" x-data="{ view: '{{ $view }}' }">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Student Directory</h1>
      <p class="page-subtitle">{{ $currentYear?->name }} &mdash; {{ $students->total() }} active students</p>
    </div>
    {{-- Grid / List toggle --}}
    <div class="flex items-center gap-2">
      <div class="flex items-center bg-slate-100 rounded-lg p-1">
        <button @click="view='grid'; $nextTick(() => updateUrl('grid'))"
          :class="view==='grid' ? 'bg-white shadow text-slate-800' : 'text-slate-400 hover:text-slate-600'"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
          Grid
        </button>
        <button @click="view='list'; $nextTick(() => updateUrl('list'))"
          :class="view==='list' ? 'bg-white shadow text-slate-800' : 'text-slate-400 hover:text-slate-600'"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          List
        </button>
      </div>
    </div>
  </div>

  {{-- Filters --}}
  <form method="GET" class="card-flat py-4" id="filterForm">
    <input type="hidden" name="view" :value="view">
    <div class="flex flex-wrap gap-3 items-end">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or admission#…" class="input w-56">
      <select name="class_id" class="select w-40">
        <option value="">All Classes</option>
        @foreach($classes as $cls)
          <option value="{{ $cls->id }}" @selected(request('class_id') == $cls->id)>{{ $cls->name }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      @if(request()->hasAny(['search','class_id']))
        <a href="{{ route('students.directory') }}" class="btn btn-ghost btn-sm">Clear</a>
      @endif
    </div>
  </form>

  {{-- Grid view --}}
  <div x-show="view==='grid'" x-transition>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
      @forelse($students as $student)
        <a href="{{ route('students.show', $student->id) }}" class="card hover:shadow-md transition group text-center py-5 px-3">
          <div class="w-16 h-16 rounded-full mx-auto mb-3 bg-gradient-to-br from-blue-100 to-indigo-200 flex items-center justify-center">
            @if($student->photo)
              <img src="{{ Storage::url($student->photo) }}" class="w-16 h-16 rounded-full object-cover" alt="{{ $student->full_name }}">
            @else
              <span class="text-blue-700 text-xl font-bold">{{ strtoupper(substr($student->first_name,0,1).substr($student->last_name,0,1)) }}</span>
            @endif
          </div>
          <p class="font-semibold text-slate-800 text-sm leading-tight group-hover:text-blue-600 transition">{{ $student->full_name }}</p>
          <p class="text-xs text-slate-400 mt-0.5">{{ $student->currentEnrollment?->class?->name }}</p>
          <p class="text-xs text-slate-400">{{ $student->currentEnrollment?->section?->name }}</p>
          @if($student->currentEnrollment?->roll_number)
            <p class="text-xs text-indigo-400 mt-1 font-mono">Roll: {{ $student->currentEnrollment->roll_number }}</p>
          @endif
        </a>
      @empty
        <div class="col-span-6 text-center py-12 text-slate-400">No students found.</div>
      @endforelse
    </div>
  </div>

  {{-- List view --}}
  <div x-show="view==='list'" x-transition>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Student</th>
            <th class="th">Adm. #</th>
            <th class="th">Class</th>
            <th class="th">Roll</th>
            <th class="th">Father</th>
            <th class="th">Mobile</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $student)
            <tr class="tr cursor-pointer" onclick="window.location='{{ route('students.show', $student->id) }}'">
              <td class="td">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-blue-700 text-xs font-bold">{{ strtoupper(substr($student->first_name,0,1).substr($student->last_name,0,1)) }}</span>
                  </div>
                  <div>
                    <p class="font-medium text-slate-800">{{ $student->full_name }}</p>
                    <p class="text-xs text-slate-400">{{ $student->dob?->format('d M Y') }}</p>
                  </div>
                </div>
              </td>
              <td class="td font-mono text-xs text-blue-600">{{ $student->admission_number }}</td>
              <td class="td">{{ $student->currentEnrollment?->class?->name }} {{ $student->currentEnrollment?->section?->name }}</td>
              <td class="td font-mono text-sm">{{ $student->currentEnrollment?->roll_number ?? '—' }}</td>
              <td class="td text-sm">{{ $student->father_name }}</td>
              <td class="td font-mono text-sm">{{ $student->father_mobile ?? $student->mobile ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="td text-center py-12 text-slate-400">No students found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($students->hasPages())
    <div class="flex justify-between items-center text-sm text-slate-500">
      <span>Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }}</span>
      {{ $students->links() }}
    </div>
  @endif

</div>
@push('scripts')
<script>
function updateUrl(view) {
  const url = new URL(window.location.href);
  url.searchParams.set('view', view);
  window.history.replaceState({}, '', url);
}
</script>
@endpush
@endsection
