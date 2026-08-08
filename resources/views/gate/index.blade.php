@extends('layouts.app')
@section('title', 'Gate & Visitor Management')
@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Gate &amp; Visitor Management</h1>
      <p class="page-subtitle">{{ $todayTotal }} visitor(s) today &bull; {{ $insideCount }} currently inside</p>
    </div>
    <a href="{{ route('gate.create') }}" class="btn btn-primary self-start sm:self-auto">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      Log Visitor
    </a>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif
  @if($errors->any()) <div class="alert-danger">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div> @endif

  {{-- Overdue outpass alert --}}
  @if($pendingOutpasses > 0)
  <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-semibold text-amber-800">
      {{ $pendingOutpasses }} student outpass(es) overdue — students have not returned.
      <a href="{{ route('gate.outpass') }}" class="underline ml-1">View →</a>
    </p>
  </div>
  @endif

  {{-- KPI Cards --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <a href="{{ route('gate.index', ['inside_only'=>1]) }}" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-indigo-500 to-blue-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      </div>
      <p class="stat-number text-indigo-600">{{ $insideCount }}</p>
      <p class="text-sm text-slate-500">Currently Inside</p>
    </a>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <p class="stat-number">{{ $todayTotal }}</p>
      <p class="text-sm text-slate-500">Today's Visitors</p>
    </div>
    <a href="{{ route('gate.outpass') }}" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-500 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      </div>
      <p class="stat-number {{ $pendingOutpasses > 0 ? 'text-amber-600' : '' }}">{{ $pendingOutpasses }}</p>
      <p class="text-sm text-slate-500">Overdue Outpasses</p>
    </a>
    <a href="{{ route('gate.blacklist') }}" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-red-500 to-rose-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
      </div>
      <p class="stat-number">{{ $blacklistCount }}</p>
      <p class="text-sm text-slate-500">Blacklisted</p>
    </a>
  </div>

  {{-- Quick Links --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    @foreach([
      ['Log Visitor',  'gate.create',    'from-green-500 to-emerald-600',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'],
      ['Outpasses',    'gate.outpass',   'from-amber-500 to-orange-500',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
      ['Blacklist',    'gate.blacklist', 'from-red-500 to-rose-600',       '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>'],
      ['Gate Report',  'gate.report',    'from-slate-500 to-gray-600',     '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
    ] as [$label,$route,$color,$icon])
    <a href="{{ route($route) }}" class="card-flat flex items-center gap-3 py-3.5 px-4 hover:shadow-card-md transition">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $color }} flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
      </div>
      <span class="font-semibold text-sm text-slate-700">{{ $label }}</span>
    </a>
    @endforeach
  </div>

  <h2 class="font-semibold text-slate-700">Visitor Log</h2>

  {{-- Filters --}}
  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">Date</label>
      <input type="date" name="date" value="{{ request('date', today()->toDateString()) }}" class="input">
    </div>
    <div>
      <label class="label">Search</label>
      <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Name or phone…">
    </div>
    <label class="flex items-center gap-2 text-sm self-end pb-2">
      <input type="checkbox" name="inside_only" value="1" @checked(request('inside_only')) class="rounded"> Currently inside only
    </label>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">Visitor</th>
          <th class="th">Purpose</th>
          <th class="th">Whom to Meet</th>
          <th class="th">Vehicle</th>
          <th class="th">In Time</th>
          <th class="th">Out Time</th>
          <th class="th">Status</th>
          <th class="th">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($visitors as $v)
        <tr class="tr">
          <td class="td">
            <div class="flex items-center gap-2">
              @if($v->visitor_photo)
              <img src="{{ Storage::url($v->visitor_photo) }}" class="w-8 h-8 rounded-full object-cover">
              @else
              <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-500">{{ strtoupper(substr($v->visitor_name,0,1)) }}</div>
              @endif
              <div>
                <p class="font-medium">{{ $v->visitor_name }}</p>
                <p class="text-xs text-slate-400">{{ $v->visitor_phone }}</p>
              </div>
            </div>
          </td>
          <td class="td text-xs">{{ $v->purpose }}</td>
          <td class="td text-xs">{{ $v->whom_to_meet ?? '—' }}<br><span class="text-slate-400">{{ $v->department ?? '' }}</span></td>
          <td class="td text-xs">{{ $v->vehicle_number ?? '—' }}</td>
          <td class="td text-xs">{{ $v->in_time->format('h:i A') }}</td>
          <td class="td text-xs">{{ $v->out_time ? $v->out_time->format('h:i A') : '—' }}</td>
          <td class="td">
            @if($v->isInside()) <span class="badge-green">Inside</span>
            @else <span class="badge-slate">Out</span> @endif
          </td>
          <td class="td flex gap-1">
            <a href="{{ route('gate.pass', $v->id) }}" class="btn-xs btn-secondary">Pass</a>
            @if($v->isInside())
            <form method="POST" action="{{ route('gate.checkout', $v->id) }}"
                  onsubmit="return confirm('Checkout {{ addslashes($v->visitor_name) }}?')">
              @csrf @method('PATCH')
              <button type="submit" class="btn-xs btn-secondary text-emerald-600">Checkout</button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td class="td text-slate-400 text-center" colspan="8">No visitors today.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div>{{ $visitors->withQueryString()->links() }}</div>
</div>
@endsection
