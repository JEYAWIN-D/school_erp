@extends('layouts.app')
@section('title', 'Alumni Directory')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Alumni Directory</h1>
    <a href="{{ route('alumni.index') }}" class="btn-sm btn-secondary">← Admin View</a>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div><label class="label">Search</label><input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Name…"></div>
    <div>
      <label class="label">Batch Year</label>
      <select name="passing_year" class="select">
        <option value="">All Batches</option>
        @foreach($years as $y) <option value="{{ $y }}" @selected(request('passing_year')==$y)>Class of {{ $y }}</option> @endforeach
      </select>
    </div>
    <button type="submit" class="btn-primary btn-sm">Search</button>
  </form>

  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
    @forelse($alumni as $a)
    <a href="{{ route('alumni.show', $a->id) }}" class="card text-center hover:shadow-md transition-shadow">
      @if($a->profile_photo)
      <img src="{{ Storage::url($a->profile_photo) }}" class="w-16 h-16 rounded-full object-cover mx-auto mb-2">
      @else
      <div class="w-16 h-16 rounded-full bg-indigo-100 text-indigo-600 font-bold text-xl flex items-center justify-center mx-auto mb-2">
        {{ strtoupper(substr($a->first_name,0,1)) }}
      </div>
      @endif
      <p class="font-semibold text-slate-700 text-sm leading-tight">{{ $a->full_name }}</p>
      <p class="text-xs text-indigo-500 font-medium mt-0.5">Class of {{ $a->passing_year }}</p>
      @if($a->current_occupation)
      <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $a->current_occupation }}</p>
      @endif
      @if($a->current_city)
      <p class="text-xs text-slate-400">📍 {{ $a->current_city }}</p>
      @endif
    </a>
    @empty
    <div class="col-span-6 card text-center py-12 text-slate-400">No alumni found.</div>
    @endforelse
  </div>
  <div>{{ $alumni->withQueryString()->links() }}</div>
</div>
@endsection
