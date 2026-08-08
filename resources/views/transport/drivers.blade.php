@extends('layouts.app')
@section('title', 'Drivers')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Drivers</h1>
    <div class="flex gap-2 flex-wrap">
      <a href="{{ route('transport.attendants') }}" class="btn btn-secondary">Attendants</a>
      <a href="{{ route('transport.incidents') }}" class="btn btn-secondary">Incident Log</a>
      <a href="{{ route('transport.vehicle-utilisation') }}" class="btn btn-secondary">Utilisation</a>
      <a href="{{ route('transport.drivers.create') }}" class="btn btn-primary">Assign Driver</a>
    </div>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  {{-- Alert banners --}}
  @if($expiredCount > 0)
  <div class="alert-danger flex items-center gap-2">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    <span><strong>{{ $expiredCount }} driver(s)</strong> have expired driving licences. Immediate renewal required.</span>
  </div>
  @endif
  @if($dueSoonCount > 0)
  <div class="alert-warning flex items-center gap-2">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span><strong>{{ $dueSoonCount }} driver(s)</strong> have licences expiring within 30 days.</span>
  </div>
  @endif
  @if($unverified > 0)
  <div class="bg-orange-50 border border-orange-200 text-orange-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
    <span><strong>{{ $unverified }} driver(s)</strong> have pending or expired police verification.</span>
  </div>
  @endif

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($vehicles as $v)
      <div class="card {{ $v->license_expired ? 'border-red-300' : ($v->license_due_soon ? 'border-amber-300' : '') }}">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center">
            <span class="text-white text-sm font-bold">{{ strtoupper(substr($v->driver_name ?? 'DR', 0, 2)) }}</span>
          </div>
          <div>
            <p class="font-semibold text-slate-800">{{ $v->driver_name }}</p>
            <p class="text-xs text-slate-400">{{ $v->vehicle_number }}</p>
          </div>
          {{-- Police verification badge --}}
          @php $pvs = $v->police_verification_status ?? 'pending'; @endphp
          <span class="ml-auto text-xs px-2 py-0.5 rounded-full font-medium
            {{ $pvs === 'verified' ? 'bg-green-100 text-green-700' : ($pvs === 'expired' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
            {{ ucfirst($pvs) }}
          </span>
        </div>

        <dl class="text-sm space-y-1">
          <div class="flex justify-between"><dt class="text-slate-400">Mobile</dt><dd class="font-mono">{{ $v->driver_mobile ?? '—' }}</dd></div>
          <div class="flex justify-between">
            <dt class="text-slate-400">License No</dt>
            <dd>{{ $v->driver_license ?? '—' }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-slate-400">License Expiry</dt>
            <dd class="{{ $v->license_expired ? 'text-red-600 font-semibold' : ($v->license_due_soon ? 'text-amber-600 font-semibold' : '') }}">
              @if($v->driver_license_expiry)
                {{ \Carbon\Carbon::parse($v->driver_license_expiry)->format('d M Y') }}
                @if($v->license_expired) <span class="badge-red ml-1">Expired</span>
                @elseif($v->license_due_soon) <span class="badge-amber ml-1">Due Soon</span>
                @endif
              @else —
              @endif
            </dd>
          </div>
          <div class="flex justify-between"><dt class="text-slate-400">Route</dt><dd>{{ $v->route?->route_name ?? '—' }}</dd></div>
        </dl>

        {{-- Update police verification inline --}}
        <div x-data="{ pvOpen: false }" class="mt-3 border-t border-slate-100 pt-3">
          <button type="button" @click="pvOpen=!pvOpen" class="text-xs text-indigo-600 hover:underline">Update Police Verification</button>
          <form x-show="pvOpen" x-transition method="POST" action="{{ route('transport.drivers.police-verification', $v->id) }}" class="mt-2 space-y-2">
            @csrf
            <select name="status" class="select text-xs py-1">
              <option value="pending"  @selected(($v->police_verification_status ?? 'pending') === 'pending')>Pending</option>
              <option value="verified" @selected(($v->police_verification_status ?? '') === 'verified')>Verified</option>
              <option value="expired"  @selected(($v->police_verification_status ?? '') === 'expired')>Expired</option>
            </select>
            <input type="date" name="verification_date" value="{{ $v->police_verification_date }}" class="input text-xs py-1" placeholder="Verification date">
            <input type="text"  name="notes" value="{{ $v->police_verification_notes }}" class="input text-xs py-1" placeholder="Notes">
            <button type="submit" class="btn btn-primary btn-xs">Save</button>
          </form>
        </div>
      </div>
    @empty
      <div class="col-span-3 text-center py-10 text-slate-400">No driver records found.</div>
    @endforelse
  </div>
</div>
@endsection
