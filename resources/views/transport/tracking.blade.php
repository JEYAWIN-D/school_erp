@extends('layouts.app')
@section('title', 'Live Tracking')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Live Vehicle Tracking</h1>
  <div class="card text-center py-16">
    <svg class="w-16 h-16 mx-auto text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    <p class="text-slate-500 font-medium">Live tracking requires GPS hardware integration.</p>
    <p class="text-slate-400 text-sm mt-1">{{ $vehicles->count() }} vehicles registered &mdash; {{ $routes->count() }} routes active.</p>
  </div>
</div>
@endsection
