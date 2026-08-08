@extends('layouts.app')
@section('title', 'Add Vehicle')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="{{ route('transport.vehicles') }}" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="page-title">Add New Vehicle</h1>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('transport.vehicles.store') }}" class="space-y-5">
      @csrf
      @if($errors->any())
      <div class="alert-danger"><ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Vehicle Number <span class="text-red-500">*</span></label>
          <input type="text" name="vehicle_number" class="input" value="{{ old('vehicle_number') }}" placeholder="e.g. MH-12-AB-1234" required>
        </div>
        <div>
          <label class="label">Registration Number</label>
          <input type="text" name="vehicle_number" class="input" value="{{ old('vehicle_number') }}">
        </div>
        <div>
          <label class="label">Make / Brand</label>
          <input type="text" name="make" class="input" value="{{ old('make') }}" placeholder="e.g. Tata, Ashok Leyland">
        </div>
        <div>
          <label class="label">Model</label>
          <input type="text" name="model" class="input" value="{{ old('model') }}">
        </div>
        <div>
          <label class="label">Seating Capacity <span class="text-red-500">*</span></label>
          <input type="number" name="capacity" class="input" value="{{ old('capacity', 40) }}" min="1" required>
        </div>
        <div>
          <label class="label">Vehicle Type</label>
          <select name="vehicle_type" class="select">
            <option value="">Select Type</option>
            @foreach(['bus' => 'Bus', 'van' => 'Van', 'minibus' => 'Mini Bus', 'auto' => 'Auto'] as $val => $label)
            <option value="{{ $val }}" {{ old('vehicle_type') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Assign Route</label>
          <select name="route_id" class="select">
            <option value="">No Route</option>
            @foreach($routes as $route)
            <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>{{ $route->route_name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Driver Name</label>
          <input type="text" name="driver_name" class="input" value="{{ old('driver_name') }}">
        </div>
        <div>
          <label class="label">Driver Mobile</label>
          <input type="text" name="driver_mobile" class="input" value="{{ old('driver_mobile') }}">
        </div>
      </div>

      <h3 class="font-semibold text-slate-700 pt-2 border-t">Document Expiry Dates</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div><label class="label">Fitness Expiry</label><input type="date" name="fitness_expiry" class="input" value="{{ old('fitness_expiry') }}"></div>
        <div><label class="label">Insurance Expiry</label><input type="date" name="insurance_expiry" class="input" value="{{ old('insurance_expiry') }}"></div>
        <div><label class="label">Permit Expiry</label><input type="date" name="permit_expiry" class="input" value="{{ old('permit_expiry') }}"></div>
        <div><label class="label">PUC Expiry</label><input type="date" name="puc_expiry" class="input" value="{{ old('puc_expiry') }}"></div>
        <div><label class="label">Road Tax Expiry</label><input type="date" name="tax_expiry" class="input" value="{{ old('tax_expiry') }}"></div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Add Vehicle</button>
        <a href="{{ route('transport.vehicles') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
