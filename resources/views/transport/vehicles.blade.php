@extends('layouts.app')
@section('title', 'Vehicles')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Vehicles</h1>
    <a href="{{ route('transport.vehicles.create') }}" class="btn btn-primary">Add Vehicle</a>
  </div>

  @php $overdueCount = $vehicles->filter(fn($v) => $v->next_service_overdue)->count(); @endphp
  @if($overdueCount > 0)
  <div class="alert-warning">
    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <strong>{{ $overdueCount }} vehicle{{ $overdueCount > 1 ? 's' : '' }}</strong> have overdue scheduled service. Check the Service Due column below and book maintenance.
  </div>
  @endif

  <div class="table-wrap">
    <table class="w-full">
      <thead><tr>
        <th class="th">Vehicle #</th><th class="th">Type</th><th class="th">Make/Model</th>
        <th class="th">Capacity</th><th class="th">Driver</th><th class="th">Route</th>
        <th class="th">Next Service</th><th class="th">Status</th><th class="th">Actions</th>
      </tr></thead>
      <tbody>
        @forelse($vehicles as $v)
          <tr class="tr">
            <td class="td font-mono font-semibold text-blue-600">{{ $v->vehicle_number }}</td>
            <td class="td">{{ $v->vehicle_type }}</td>
            <td class="td">{{ $v->make }} {{ $v->model }}</td>
            <td class="td">{{ $v->capacity ?: $v->seating_capacity }}</td>
            <td class="td">{{ $v->driver_name ?? '—' }}</td>
            <td class="td">{{ $v->route?->route_name ?? '—' }}</td>
            <td class="td">
              @if($v->next_service_date)
                <span class="{{ $v->next_service_overdue ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                  {{ \Carbon\Carbon::parse($v->next_service_date)->format('d M Y') }}
                  @if($v->next_service_overdue)
                    <br><span class="badge-red text-xs">Overdue</span>
                  @elseif(\Carbon\Carbon::parse($v->next_service_date)->diffInDays(now()) <= 14)
                    <br><span class="badge-amber text-xs">Due Soon</span>
                  @endif
                </span>
              @else
                <span class="text-slate-300">—</span>
              @endif
            </td>
            <td class="td"><span class="{{ $v->is_active ? 'badge-green' : 'badge-red' }}">{{ $v->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="td">
              <a href="{{ route('transport.vehicle.edit', $v->id) }}" class="btn-xs text-indigo-600">Edit</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="td text-center py-10 text-slate-400">No vehicles added.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
