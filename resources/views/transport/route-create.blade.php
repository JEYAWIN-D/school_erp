@extends('layouts.app')
@section('title', 'Add Transport Route')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="{{ route('transport.routes') }}" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="page-title">Add New Route</h1>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('transport.routes.store') }}" class="space-y-5">
      @csrf
      @if($errors->any())
      <div class="alert-danger"><ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Route Name <span class="text-red-500">*</span></label>
          <input type="text" name="route_name" class="input" value="{{ old('route_name') }}" placeholder="e.g. Route A - North Zone" required>
        </div>
        <div>
          <label class="label">Route Code</label>
          <input type="text" name="route_code" class="input" value="{{ old('route_code') }}" placeholder="e.g. RT-A1">
        </div>
        <div>
          <label class="label">Assign Vehicle</label>
          <select name="vehicle_id" class="select">
            <option value="">No Vehicle</option>
            @foreach($vehicles as $vehicle)
            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
              {{ $vehicle->vehicle_number }} — {{ $vehicle->driver_name ?? 'No Driver' }}
            </option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Distance (km)</label>
          <input type="number" name="distance_km" class="input" value="{{ old('distance_km') }}" step="0.1" min="0">
        </div>
        <div>
          <label class="label">Start Point</label>
          <input type="text" name="start_point" class="input" value="{{ old('start_point') }}" placeholder="e.g. School Gate">
        </div>
        <div>
          <label class="label">End Point</label>
          <input type="text" name="end_point" class="input" value="{{ old('end_point') }}" placeholder="e.g. Zone End">
        </div>
        <div>
          <label class="label">Morning Pickup Time</label>
          <input type="time" name="start_time" class="input" value="{{ old('start_time') }}">
        </div>
        <div>
          <label class="label">Afternoon Drop Time</label>
          <input type="time" name="end_time" class="input" value="{{ old('end_time') }}">
        </div>
        <div>
          <label class="label">Monthly Fare (₹)</label>
          <input type="number" name="fare" class="input" value="{{ old('fare') }}" step="0.01" min="0">
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Add Route</button>
        <a href="{{ route('transport.routes') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
