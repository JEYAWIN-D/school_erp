@extends('layouts.app')
@section('title', 'Alumni Management')
@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Alumni Management</h1>
      <p class="page-subtitle">{{ $stats['totalAlumni'] }} alumni &bull; Batches up to {{ $stats['latestBatch'] ?? '—' }}</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('alumni.events') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Events
      </a>
      <a href="{{ route('alumni.directory') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        Directory
      </a>
      <a href="{{ route('alumni.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Alumni
      </a>
    </div>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  {{-- KPI Cards --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-indigo-500 to-blue-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <p class="stat-number">{{ number_format($stats['totalAlumni']) }}</p>
      <p class="text-sm text-slate-500">Total Alumni</p>
    </div>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      </div>
      <p class="stat-number">{{ number_format($stats['verifiedCount']) }}</p>
      <p class="text-sm text-slate-500">Verified</p>
    </div>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-500 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>
      <p class="stat-number">{{ $stats['latestBatch'] ?? '—' }}</p>
      <p class="text-sm text-slate-500">Latest Batch</p>
    </div>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-teal-500 to-cyan-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <p class="stat-number">{{ $stats['citiesCount'] }}</p>
      <p class="text-sm text-slate-500">Cities</p>
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div><label class="label">Search</label><input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Name or city…"></div>
    <div>
      <label class="label">Passing Year</label>
      <select name="passing_year" class="select">
        <option value="">All Years</option>
        @foreach($years as $y) <option value="{{ $y }}" @selected(request('passing_year')==$y)>{{ $y }}</option> @endforeach
      </select>
    </div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="{{ route('alumni.index') }}" class="btn-sm btn-secondary">Reset</a>
    <span class="text-sm text-slate-500 ml-auto self-center">{{ $alumni->total() }} alumni</span>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead><tr>
        <th class="th">Name</th>
        <th class="th">Year</th>
        <th class="th">Last Class</th>
        <th class="th">Occupation</th>
        <th class="th">Employer</th>
        <th class="th">City</th>
        <th class="th">Contact</th>
        <th class="th">Verified</th>
        <th class="th">Actions</th>
      </tr></thead>
      <tbody>
        @forelse($alumni as $a)
        <tr class="tr">
          <td class="td">
            <div class="flex items-center gap-2">
              @if($a->profile_photo)
              <img src="{{ Storage::url($a->profile_photo) }}" class="w-8 h-8 rounded-full object-cover">
              @else
              <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 font-bold text-xs flex items-center justify-center">{{ strtoupper(substr($a->first_name,0,1)) }}</div>
              @endif
              <p class="font-medium">{{ $a->full_name }}</p>
            </div>
          </td>
          <td class="td font-semibold text-indigo-600">{{ $a->passing_year }}</td>
          <td class="td text-xs">{{ $a->last_class ?? '—' }}</td>
          <td class="td text-xs">{{ $a->current_occupation ?? '—' }}</td>
          <td class="td text-xs">{{ $a->current_employer ?? '—' }}</td>
          <td class="td text-xs">{{ $a->current_city ?? '—' }}</td>
          <td class="td text-xs">{{ $a->phone ?? $a->email ?? '—' }}</td>
          <td class="td">@if($a->is_verified) <span class="badge-green">✓</span> @else <span class="badge-slate">—</span> @endif</td>
          <td class="td flex gap-1">
            <a href="{{ route('alumni.show', $a->id) }}" class="btn-xs btn-secondary">View</a>
            <a href="{{ route('alumni.edit', $a->id) }}" class="btn-xs btn-secondary">Edit</a>
          </td>
        </tr>
        @empty
        <tr><td class="td text-center text-slate-400" colspan="9">No alumni records found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div>{{ $alumni->withQueryString()->links() }}</div>
</div>
@endsection
