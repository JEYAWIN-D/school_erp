@extends('layouts.app')
@section('title','Vehicle Maintenance Log')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Vehicle Maintenance Log</h1>
    <a href="{{ route('transport.maintenance-cost') }}" class="btn btn-secondary btn-sm">Cost Report →</a>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Log Maintenance</h3>
      <form method="POST" action="{{ route('transport.maintenance.store') }}" class="space-y-3">
        @csrf
        <div><label class="label">Vehicle <span class="text-red-500">*</span></label>
          <select name="vehicle_id" class="select" required>
            <option value="">Select vehicle</option>
            @foreach($vehicles as $v)<option value="{{ $v->id }}" @selected(old('vehicle_id')==$v->id)>{{ $v->vehicle_number }} – {{ $v->make }}</option>@endforeach
          </select>
        </div>
        <div><label class="label">Maintenance Type <span class="text-red-500">*</span></label>
          <select name="maintenance_type" class="select" required>
            @foreach(['service'=>'Service','repair'=>'Repair','tyre'=>'Tyre Change','oil_change'=>'Oil Change','battery'=>'Battery','other'=>'Other'] as $k=>$v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div><label class="label">Date <span class="text-red-500">*</span></label>
          <input type="date" name="service_date" class="input" required value="{{ old('service_date',today()->toDateString()) }}">
        </div>
        <div><label class="label">Odometer Reading (km)</label>
          <input type="number" name="odometer" class="input" min="0" value="{{ old('odometer') }}">
        </div>
        <div><label class="label">Cost (₹)</label>
          <input type="number" name="cost" class="input" step="0.01" min="0" value="{{ old('cost') }}">
        </div>
        <div><label class="label">Vendor / Garage</label>
          <input type="text" name="vendor" class="input" value="{{ old('vendor') }}">
        </div>
        <div><label class="label">Description</label>
          <textarea name="description" class="input h-16">{{ old('description') }}</textarea>
        </div>
        <div><label class="label">Next Service Date</label>
          <input type="date" name="next_service_date" class="input" value="{{ old('next_service_date') }}">
        </div>
        <button type="submit" class="btn btn-primary">Log Entry</button>
      </form>
    </div>
    <div class="lg:col-span-2 space-y-4">
      <form method="GET" class="card-flat py-3"><div class="flex gap-3">
        <select name="vehicle_id" class="select w-44">
          <option value="">All Vehicles</option>
          @foreach($vehicles as $v)<option value="{{ $v->id }}" @selected(request('vehicle_id')==$v->id)>{{ $v->vehicle_number }}</option>@endforeach
        </select>
        <input type="month" name="month" value="{{ request('month') }}" class="input w-36">
        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      </div></form>
      <div class="card overflow-hidden">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 border-b"><tr>
            @foreach(['Vehicle','Type','Date','Odometer','Cost','Vendor','Next Service'] as $h)
            <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium">{{ $h }}</th>
            @endforeach
          </tr></thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($logs as $log)
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3 font-mono text-xs text-indigo-700">{{ $log->vehicle?->vehicle_number }}</td>
              <td class="px-4 py-3"><span class="badge-slate capitalize text-xs">{{ str_replace('_',' ',$log->maintenance_type) }}</span></td>
              <td class="px-4 py-3 text-slate-500 text-xs">{{ $log->service_date?->format('d M Y') }}</td>
              <td class="px-4 py-3 text-slate-500 text-xs">{{ $log->odometer ? number_format($log->odometer).' km' : '—' }}</td>
              <td class="px-4 py-3 font-semibold text-xs">{{ $log->cost ? '₹'.number_format($log->cost,0) : '—' }}</td>
              <td class="px-4 py-3 text-slate-400 text-xs">{{ $log->vendor ?? '—' }}</td>
              <td class="px-4 py-3 text-xs {{ $log->next_service_date?->isPast() ? 'text-red-500 font-semibold' : 'text-slate-400' }}">{{ $log->next_service_date?->format('d M Y') ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No maintenance logs.</td></tr>
            @endforelse
          </tbody>
        </table>
        @if($logs->hasPages())<div class="px-4 pb-3">{{ $logs->links() }}</div>@endif
      </div>
    </div>
  </div>
</div>
@endsection
