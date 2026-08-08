@extends('layouts.app')
@section('title', 'Assign Driver')
@section('content')
<div class="max-w-xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="{{ route('transport.drivers') }}" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="page-title">Assign Driver to Vehicle</h1>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('transport.drivers.store') }}" class="space-y-5">
      @csrf
      @if($errors->any())
      <div class="alert-danger"><ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif

      <div>
        <label class="label">Vehicle <span class="text-red-500">*</span></label>
        <select name="vehicle_id" class="select" required>
          <option value="">Select Vehicle</option>
          @foreach($vehicles as $vehicle)
          <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
            {{ $vehicle->vehicle_number }} ({{ $vehicle->make ?? '' }} {{ $vehicle->model ?? '' }})
          </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Driver Name <span class="text-red-500">*</span></label>
        <input type="text" name="driver_name" class="input" value="{{ old('driver_name') }}" required>
      </div>
      <div>
        <label class="label">Driver Mobile <span class="text-red-500">*</span></label>
        <input type="text" name="driver_mobile" class="input" value="{{ old('driver_mobile') }}" required>
      </div>
      <div>
        <label class="label">License Number</label>
        <input type="text" name="license_number" class="input" value="{{ old('license_number') }}">
      </div>
      <div>
        <label class="label">License Expiry</label>
        <input type="date" name="license_expiry" class="input" value="{{ old('license_expiry') }}">
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Assign Driver</button>
        <a href="{{ route('transport.drivers') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
