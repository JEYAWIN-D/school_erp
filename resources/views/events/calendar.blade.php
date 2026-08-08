@extends('layouts.app')
@section('title', 'Event Calendar')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Event Calendar</h1>
    <div class="flex gap-2">
      <a href="{{ route('events.index') }}" class="btn-sm btn-secondary">List View</a>
      <a href="{{ route('events.create') }}" class="btn-primary btn-sm">+ New Event</a>
    </div>
  </div>

  {{-- Month Navigation --}}
  @php
    $prevMonth = \Carbon\Carbon::createFromDate($year,$month,1)->subMonth();
    $nextMonth = \Carbon\Carbon::createFromDate($year,$month,1)->addMonth();
    $monthName = \Carbon\Carbon::createFromDate($year,$month,1)->format('F Y');
    $typeColors = [
      'academic'=>'bg-indigo-100 text-indigo-800','cultural'=>'bg-pink-100 text-pink-800',
      'sports'=>'bg-emerald-100 text-emerald-800','holiday'=>'bg-amber-100 text-amber-800',
      'meeting'=>'bg-blue-100 text-blue-800','other'=>'bg-slate-100 text-slate-600',
    ];
  @endphp

  <div class="card">
    <div class="flex items-center justify-between mb-6">
      <a href="{{ route('events.calendar', ['month'=>$prevMonth->month,'year'=>$prevMonth->year]) }}" class="btn-sm btn-secondary">← {{ $prevMonth->format('M') }}</a>
      <div class="flex items-center gap-3">
        <h2 class="text-xl font-bold text-slate-800">{{ $monthName }}</h2>
        @if($month != now()->month || $year != now()->year)
        <a href="{{ route('events.calendar', ['month'=>now()->month,'year'=>now()->year]) }}" class="text-xs px-2 py-1 bg-indigo-50 text-indigo-700 rounded-full font-semibold hover:bg-indigo-100 transition">Today</a>
        @endif
      </div>
      <a href="{{ route('events.calendar', ['month'=>$nextMonth->month,'year'=>$nextMonth->year]) }}" class="btn-sm btn-secondary">{{ $nextMonth->format('M') }} →</a>
    </div>

    {{-- Day headers --}}
    <div class="grid grid-cols-7 mb-2">
      @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
      <div class="text-center text-xs font-semibold text-slate-500 py-2">{{ $d }}</div>
      @endforeach
    </div>

    {{-- Calendar grid --}}
    <div class="grid grid-cols-7 gap-1">
      {{-- Leading empty cells --}}
      @for($i = 0; $i < $startDow; $i++)
      <div class="min-h-24 rounded-lg bg-slate-50 p-1"></div>
      @endfor

      {{-- Days --}}
      @foreach($days as $day)
      @php
        $isToday = (now()->day === $day && now()->month === $month && now()->year === $year);
        $dayEvents = $events->get($day, collect());
      @endphp
      <div class="min-h-24 rounded-lg border border-slate-100 p-1.5 {{ $isToday ? 'bg-indigo-50 border-indigo-300' : 'bg-white hover:bg-slate-50' }}">
        <div class="text-xs font-semibold mb-1 {{ $isToday ? 'text-indigo-600' : 'text-slate-400' }}">{{ $day }}</div>
        @foreach($dayEvents->take(3) as $ev)
        <a href="{{ route('events.show', $ev->id) }}"
           class="block text-xs px-1 py-0.5 rounded mb-0.5 truncate {{ $typeColors[$ev->event_type] ?? 'bg-slate-100 text-slate-600' }}">
          {{ $ev->name }}
        </a>
        @endforeach
        @if($dayEvents->count() > 3)
        <span class="text-xs text-slate-400">+{{ $dayEvents->count() - 3 }} more</span>
        @endif
      </div>
      @endforeach
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-slate-100">
      @foreach(['academic','cultural','sports','holiday','meeting','other'] as $t)
      <span class="flex items-center gap-1 text-xs">
        <span class="w-3 h-3 rounded-sm {{ $typeColors[$t] }}"></span>
        {{ ucfirst($t) }}
      </span>
      @endforeach
    </div>
  </div>
</div>
@endsection
