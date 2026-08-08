@extends('layouts.app')
@section('title', 'Transport Routes')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Transport Routes</h1>
    <div class="flex gap-2">
      <a href="{{ request()->fullUrlWithQuery(['show_inactive' => $showInactive ? 0 : 1]) }}"
         class="btn btn-secondary btn-sm {{ $showInactive ? 'bg-slate-200' : '' }}">
        {{ $showInactive ? 'Hide Inactive' : 'Show Inactive' }}
      </a>
      <a href="{{ route('transport.stops') }}" class="btn btn-secondary">Manage Stops</a>
      <a href="{{ route('transport.routes.create') }}" class="btn btn-primary">Add Route</a>
    </div>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  <div class="table-wrap">
    <table class="w-full">
      <thead><tr>
        <th class="th">Route</th><th class="th">From</th><th class="th">To</th>
        <th class="th">Departure</th><th class="th">Fee/Month</th>
        <th class="th">Status</th><th class="th">Actions</th>
      </tr></thead>
      <tbody>
        @forelse($routes as $route)
          <tr class="tr {{ !$route->is_active ? 'opacity-60' : '' }}">
            <td class="td font-medium text-slate-800">{{ $route->route_name }}
              @if($route->route_number)<span class="text-xs text-slate-400 ml-1">#{{ $route->route_number }}</span>@endif
            </td>
            <td class="td">{{ $route->from_location }}</td>
            <td class="td">{{ $route->to_location }}</td>
            <td class="td">{{ $route->departure_time ? \Carbon\Carbon::parse($route->departure_time)->format('h:i A') : '—' }}</td>
            <td class="td">₹{{ number_format($route->fee, 2) }}</td>
            <td class="td"><span class="{{ $route->is_active ? 'badge-green' : 'badge-slate' }}">{{ $route->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="td">
              <form method="POST" action="{{ route('transport.routes.toggle', $route->id) }}" class="inline">
                @csrf @method('PATCH')
                <button type="submit"
                  class="btn-xs {{ $route->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-green-600 hover:text-green-800' }}"
                  onclick="return confirm('{{ $route->is_active ? 'Deactivate' : 'Activate' }} route?')">
                  {{ $route->is_active ? 'Deactivate' : 'Activate' }}
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="td text-center py-10 text-slate-400">No routes configured.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($routes->hasPages())<div class="text-sm mt-3">{{ $routes->links() }}</div>@endif
</div>
@endsection
