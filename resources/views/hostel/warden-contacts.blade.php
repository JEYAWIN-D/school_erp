@extends('layouts.app')
@section('title', 'Warden Emergency Contacts')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Warden Emergency Contacts</h1>
    <a href="{{ route('hostel.index') }}" class="btn-sm btn-secondary">← Hostel</a>
  </div>

  <p class="text-sm text-slate-600">Emergency contact information for hostel wardens. This list is also shared with parents of hostel students.</p>

  @if($hostels->isEmpty())
    <div class="card text-center py-12 text-slate-400">
      <p>No warden contacts configured. Go to <a href="{{ route('hostel.wardens') }}" class="text-indigo-600">Warden Management</a> to assign wardens.</p>
    </div>
  @else
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($hostels as $hostel)
    <div class="card border border-slate-200 hover:shadow-md transition-shadow">
      <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $hostel->name }}</p>
          <p class="font-semibold text-slate-800 mt-0.5">{{ $hostel->warden_name }}</p>
          @if($hostel->warden_mobile)
          <a href="tel:{{ $hostel->warden_mobile }}" class="flex items-center gap-1.5 mt-2 text-sm text-indigo-600 hover:text-indigo-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            {{ $hostel->warden_mobile }}
          </a>
          @endif
          @if($hostel->address)
          <p class="text-xs text-slate-400 mt-1">{{ $hostel->address }}</p>
          @endif
        </div>
      </div>
      <div class="mt-3 pt-3 border-t border-slate-100 text-xs text-slate-400 flex gap-3">
        <span>Capacity: {{ $hostel->total_capacity ?? '—' }}</span>
        <span>Type: {{ ucfirst($hostel->gender ?? '—') }}</span>
      </div>
    </div>
    @endforeach
  </div>
  @endif
</div>
@endsection
