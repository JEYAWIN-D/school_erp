@extends('layouts.app')
@section('title','Academic Year Management')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Academic Year & Calendar</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form method="POST" action="{{ route('academics.year.save') }}" class="card space-y-4">
      @csrf
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Create Academic Year</h3>
      <div><label class="label">Name (e.g. 2025-26) <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="input" required value="{{ old('name') }}" placeholder="2025-26">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Start Date <span class="text-red-500">*</span></label><input type="date" name="start_date" class="input" required value="{{ old('start_date') }}"></div>
        <div><label class="label">End Date <span class="text-red-500">*</span></label><input type="date" name="end_date" class="input" required value="{{ old('end_date') }}"></div>
      </div>
      <div class="flex items-center gap-2">
        <input type="checkbox" name="is_current" id="is_current" value="1">
        <label for="is_current" class="text-sm text-slate-600">Set as current academic year</label>
      </div>
      <button type="submit" class="btn btn-primary">Create Academic Year</button>
    </form>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">All Academic Years</h3>
      @forelse($academicYears as $y)
      <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
        <div>
          <p class="font-semibold text-slate-800">{{ $y->name }}</p>
          <p class="text-xs text-slate-400">{{ $y->start_date?->format('d M Y') }} — {{ $y->end_date?->format('d M Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
          @if($y->is_current)<span class="badge-green text-xs">Current</span>@endif
          @if(!$y->is_current)
          <form method="POST" action="{{ route('academics.year.set-current',$y->id) }}">@csrf
            <button type="submit" class="text-indigo-600 hover:underline text-xs">Set Current</button>
          </form>
          @endif
        </div>
      </div>
      @empty
      <p class="text-slate-400 text-sm text-center py-6">No academic years.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
