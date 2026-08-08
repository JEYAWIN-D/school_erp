@extends('layouts.app')
@section('title', 'Staff ID Cards')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Staff ID Cards</h1>
    <a href="{{ route('hr.index') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  {{-- Filter + Print --}}
  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Department</label>
        <select name="department_id" class="select text-sm">
          <option value="">All Departments</option>
          @foreach($departments as $dept)
            <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <a href="{{ route('hr.staff-id-cards.pdf', request()->all()) }}" target="_blank"
         class="btn btn-primary btn-sm">Print / Download PDF</a>
    </form>
  </div>

  {{-- Preview Grid --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($employees as $emp)
    <a href="{{ route('hr.employees.id-card', $emp->id) }}" class="group card border border-indigo-100 p-0 overflow-hidden hover:shadow-lg hover:border-indigo-300 transition-all duration-200 block">
      <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-3 py-2 text-xs font-bold flex items-center justify-between">
        <span>STAFF ID CARD</span>
        <span class="text-[10px] font-mono opacity-80">{{ $emp->employee_code }}</span>
      </div>
      <div class="p-3.5 flex items-center gap-3">
        @if($emp->photo)
          <img src="{{ asset('storage/' . $emp->photo) }}" class="w-14 h-14 rounded-xl object-cover border border-slate-200 shrink-0 shadow-sm" alt="">
        @else
          <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-base shrink-0 shadow-sm">
            {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1)) }}
          </div>
        @endif
        <div class="min-w-0 flex-1">
          <p class="font-bold text-sm text-slate-800 truncate group-hover:text-indigo-600 transition-colors">{{ $emp->full_name }}</p>
          <p class="text-xs text-indigo-600 font-semibold">{{ $emp->designation ?? 'Staff Member' }}</p>
          <p class="text-xs text-slate-500 truncate">{{ $emp->department ?? $emp->assigned_block ?? 'General' }}</p>
          <span class="inline-block mt-1.5 text-[10px] font-bold text-indigo-600 hover:underline">Customize & Print Card &rarr;</span>
        </div>
      </div>
    </a>
    @empty
    <div class="col-span-full text-center text-slate-400 py-12">No active employees found.</div>
    @endforelse
  </div>
</div>
@endsection
