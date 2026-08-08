@extends('layouts.app')
@section('title','Update Vehicle Documents')
@section('content')
<div class="space-y-6 max-w-2xl">
  <h1 class="page-title">Update Vehicle: {{ $vehicle->vehicle_number }}</h1>
  <form method="POST" action="{{ route('transport.vehicle.update',$vehicle->id) }}" class="card space-y-4">
    @csrf @method('PUT')
    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">Make</label><input type="text" name="make" class="input" value="{{ old('make',$vehicle->make) }}"></div>
      <div><label class="label">Model</label><input type="text" name="model" class="input" value="{{ old('model',$vehicle->model) }}"></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">Vehicle Number</label><input type="text" name="vehicle_number" class="input" value="{{ old('vehicle_number',$vehicle->vehicle_number) }}" readonly></div>
      <div><label class="label">Vehicle Type</label>
        <select name="vehicle_type" class="select">
          @foreach(['bus'=>'Bus','mini_bus'=>'Mini Bus','van'=>'Van','car'=>'Car','auto'=>'Auto'] as $k=>$v)
          <option value="{{ $k }}" @selected(($vehicle->vehicle_type??'')===$k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <h3 class="font-semibold text-slate-700 pt-2 border-t border-slate-100">Document Expiry Dates</h3>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">Fitness Expiry</label><input type="date" name="fitness_expiry" class="input" value="{{ old('fitness_expiry',$vehicle->fitness_expiry) }}"></div>
      <div><label class="label">Insurance Expiry</label><input type="date" name="insurance_expiry" class="input" value="{{ old('insurance_expiry',$vehicle->insurance_expiry) }}"></div>
      <div><label class="label">Permit Expiry</label><input type="date" name="permit_expiry" class="input" value="{{ old('permit_expiry',$vehicle->permit_expiry) }}"></div>
      <div><label class="label">PUC Expiry</label><input type="date" name="puc_expiry" class="input" value="{{ old('puc_expiry',$vehicle->puc_expiry) }}"></div>
      <div><label class="label">Tax Expiry</label><input type="date" name="tax_expiry" class="input" value="{{ old('tax_expiry',$vehicle->tax_expiry) }}"></div>
    </div>
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="{{ route('transport.documents') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
