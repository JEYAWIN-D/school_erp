@extends('layouts.app')
@section('title','Route Roster')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Route Roster</h1>
    <div class="flex gap-2">
      <a href="{{ route('transport.roster.pdf') }}" target="_blank" class="btn btn-secondary btn-sm">Export PDF</a>
      <a href="{{ route('transport.roster.excel') }}" class="btn btn-secondary btn-sm">Export Excel</a>
    </div>
  </div>
  @forelse($routes as $route)
  <div class="card">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <div>
        <p class="font-semibold text-slate-800">{{ $route->route_name }}</p>
        <p class="text-xs text-slate-400">{{ $route->vehicle?->vehicle_number }} | {{ $route->allotments->count() }} students</p>
      </div>
      <span class="badge-green">Active</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
      @if($route->stops->count())
      <div>
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Stops</h4>
        <div class="space-y-1">
          @foreach($route->stops->sortBy('stop_order') as $stop)
          <div class="flex items-center gap-2 text-sm text-slate-600">
            <span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-600 text-xs flex items-center justify-center font-bold">{{ $stop->stop_order }}</span>
            {{ $stop->name }}
            @if($stop->arrival_time)<span class="text-slate-400 text-xs">{{ $stop->arrival_time }}</span>@endif
          </div>
          @endforeach
        </div>
      </div>
      @endif
      <div>
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Students ({{ $route->allotments->count() }})</h4>
        <div class="space-y-1 max-h-48 overflow-y-auto">
          @foreach($route->allotments as $a)
          <div class="flex items-center justify-between text-sm text-slate-600 py-1 border-b border-slate-50">
            <span>{{ $a->enrollment?->student?->full_name }}</span>
            <span class="text-xs text-slate-400">{{ $a->enrollment?->class?->name }}</span>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
  @empty
  <div class="card text-center py-12 text-slate-400">No routes configured.</div>
  @endforelse
</div>
@endsection
