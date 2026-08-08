@extends('layouts.app')
@section('title', 'Events')
@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Events &amp; Calendar</h1>
      <p class="page-subtitle">{{ $totalEvents }} total events &bull; {{ $upcomingCount }} upcoming</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('events.calendar') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Calendar
      </a>
      <a href="{{ route('events.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Event
      </a>
    </div>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  {{-- KPI Cards --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-blue-500 to-indigo-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>
      <p class="stat-number">{{ $totalEvents }}</p>
      <p class="text-sm text-slate-500">Total Events</p>
    </div>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      </div>
      <p class="stat-number">{{ $upcomingCount }}</p>
      <p class="text-sm text-slate-500">Upcoming</p>
    </div>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-500 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/></svg>
      </div>
      <p class="stat-number">{{ $todayEvents }}</p>
      <p class="text-sm text-slate-500">Today</p>
    </div>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-violet-500 to-purple-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      </div>
      <p class="stat-number">{{ $thisMonthCount }}</p>
      <p class="text-sm text-slate-500">This Month</p>
    </div>
  </div>

  {{-- Upcoming Events Panel --}}
  @if($upcomingEvents->count() > 0)
  <div class="card">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold text-slate-700">Upcoming Events</h3>
      <a href="{{ route('events.calendar') }}" class="text-xs text-blue-600 hover:underline">Calendar view →</a>
    </div>
    <div class="space-y-2">
      @foreach($upcomingEvents as $ue)
      @php
        $typeColors = ['academic'=>'#2563eb','cultural'=>'#db2777','sports'=>'#059669','holiday'=>'#d97706','meeting'=>'#7c3aed','other'=>'#64748b'];
        $col = $typeColors[$ue->event_type] ?? '#64748b';
        $daysTo = now()->diffInDays($ue->event_date, false);
      @endphp
      <div class="flex items-center gap-3 py-2 border-b border-slate-100 last:border-0">
        <div class="flex-shrink-0 w-12 text-center">
          <p class="text-lg font-bold text-slate-800">{{ $ue->event_date->format('d') }}</p>
          <p class="text-xs text-slate-400">{{ $ue->event_date->format('M') }}</p>
        </div>
        <div class="w-0.5 h-10 rounded-full flex-shrink-0" style="background:{{ $col }};"></div>
        <div class="flex-1 min-w-0">
          <p class="font-medium text-slate-800 truncate text-sm">{{ $ue->name }}</p>
          <p class="text-xs text-slate-400">{{ $ue->venue ?? 'No venue' }} &bull; {{ ucfirst($ue->event_type) }}</p>
        </div>
        <span class="text-xs font-semibold {{ $daysTo === 0 ? 'text-red-600 bg-red-50' : ($daysTo <= 3 ? 'text-amber-600 bg-amber-50' : 'text-slate-500 bg-slate-50') }} px-2 py-0.5 rounded-full flex-shrink-0">
          {{ $daysTo === 0 ? 'Today' : ($daysTo === 1 ? 'Tomorrow' : "in $daysTo days") }}
        </span>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  <div class="flex items-center justify-between flex-wrap gap-3">
    <h2 class="font-semibold text-slate-700">All Events</h2>
    <div class="flex gap-1 flex-wrap">
      @foreach([''=>'All','draft'=>'Draft','published'=>'Published','upcoming'=>'Upcoming','past'=>'Past'] as $val=>$label)
      <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}"
         class="px-3 py-1 text-xs font-semibold rounded-full border transition {{ request('status') === $val ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-200 hover:border-indigo-300' }}">
        {{ $label }}
      </a>
      @endforeach
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <div>
      <label class="label">Type</label>
      <select name="type" class="select">
        <option value="">All Types</option>
        @foreach(['academic','cultural','sports','holiday','meeting','other'] as $t)
          <option value="{{ $t }}" @selected(request('type')===$t)>{{ ucfirst($t) }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="label">Search</label>
      <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Event name…">
    </div>
    <div>
      <label class="label">From</label>
      <input type="date" name="from" value="{{ request('from') }}" class="input">
    </div>
    <div>
      <label class="label">To</label>
      <input type="date" name="to" value="{{ request('to') }}" class="input">
    </div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="{{ route('events.index') }}" class="btn-sm btn-secondary">Reset</a>
  </form>

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($events as $event)
    @php
      $typeColors = [
        'academic'=>'bg-indigo-100 text-indigo-700','cultural'=>'bg-pink-100 text-pink-700',
        'sports'=>'bg-emerald-100 text-emerald-700','holiday'=>'bg-amber-100 text-amber-700',
        'meeting'=>'bg-blue-100 text-blue-700','other'=>'bg-slate-100 text-slate-600',
      ];
    @endphp
    <div class="card hover:shadow-md transition-shadow">
      @if($event->banner_image)
      <img src="{{ Storage::url($event->banner_image) }}" alt="" class="w-full h-32 object-cover rounded-xl mb-3">
      @endif
      <div class="flex items-start justify-between gap-2">
        <h3 class="font-semibold text-slate-800 leading-tight">{{ $event->name }}</h3>
        <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0 {{ $typeColors[$event->event_type] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($event->event_type) }}</span>
      </div>
      <p class="text-sm text-slate-500 mt-1">
        {{ $event->event_date->format('d M Y') }}
        @if($event->start_time) &bull; {{ substr($event->start_time,0,5) }}@endif
      </p>
      @if($event->venue)
      <p class="text-xs text-slate-400 mt-0.5">{{ $event->venue }}</p>
      @endif
      @if($event->description)
      <p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ $event->description }}</p>
      @endif
      <div class="mt-3 flex items-center justify-between">
        <div class="flex gap-1">
          @if($event->is_published) <span class="badge-green text-xs">Published</span> @else <span class="text-xs px-2 py-0.5 rounded-full font-semibold bg-amber-100 text-amber-700 border border-amber-300">Draft</span> @endif
          @if($event->allow_rsvp) <span class="badge-blue text-xs">RSVP</span> @endif
        </div>
        <div class="flex gap-1">
          <a href="{{ route('events.show', $event->id) }}" class="btn-xs btn-secondary">View</a>
          <a href="{{ route('events.edit', $event->id) }}" class="btn-xs btn-secondary">Edit</a>
        </div>
      </div>
    </div>
    @empty
    <div class="col-span-3 card text-center py-12 text-slate-400">
      No events found. <a href="{{ route('events.create') }}" class="text-indigo-600">Create one</a>.
    </div>
    @endforelse
  </div>

  <div>{{ $events->withQueryString()->links() }}</div>
</div>
@endsection
