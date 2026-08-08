@extends('layouts.app')
@section('title', 'Room-wise Student List')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Room-wise Student List</h1>
      <p class="page-subtitle">View students allocated to each room</p>
    </div>
    <a href="{{ route('hostel.occupancy') }}" class="btn btn-secondary">Occupancy Report</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Hostel</label>
        <select name="hostel_id" class="select">
          <option value="">All Hostels</option>
          @foreach($hostels as $hostel)
          <option value="{{ $hostel->id }}" {{ request('hostel_id') == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Status</label>
        <select name="status" class="select">
          <option value="">All</option>
          <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
          <option value="full" {{ request('status') == 'full' ? 'selected' : '' }}>Full</option>
          <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
  </div>

  @forelse($rooms as $room)
  <div class="card">
    <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
      <div>
        <h3 class="font-semibold text-slate-800">Room {{ $room->room_number }}</h3>
        <p class="text-xs text-slate-500">{{ $room->hostel?->name }} &bull; Floor: {{ $room->floor ?? 'G' }} &bull; Type: {{ ucfirst($room->room_type) }}</p>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm text-slate-600">{{ $room->occupied }}/{{ $room->capacity }} occupied</span>
        <span class="badge-{{ $room->status === 'full' ? 'red' : ($room->status === 'maintenance' ? 'amber' : 'green') }}">{{ ucfirst($room->status) }}</span>
      </div>
    </div>
    @php $activeAllotments = $room->allotments->where('status', 'active'); @endphp
    @if($activeAllotments->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
      @foreach($activeAllotments as $allotment)
      <div class="flex items-center gap-3 bg-slate-50 rounded-lg px-3 py-2">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center flex-shrink-0">
          <span class="text-white text-xs font-bold">{{ strtoupper(substr($allotment->student?->first_name ?? 'S', 0, 1)) }}</span>
        </div>
        <div class="min-w-0 flex-1">
          <a href="{{ route('students.show', $allotment->student_id) }}" class="text-sm font-medium text-slate-800 hover:text-indigo-600 truncate block">
            {{ $allotment->student?->first_name }} {{ $allotment->student?->last_name }}
          </a>
          <p class="text-xs text-slate-400">Since {{ \Carbon\Carbon::parse($allotment->allotment_date)->format('d M Y') }}</p>
        </div>
        <a href="{{ route('hostel.id-card', $allotment->id) }}" target="_blank" title="Print Hostel ID Card"
           class="flex-shrink-0 text-slate-400 hover:text-indigo-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
      </div>
      @endforeach
    </div>
    @else
    <p class="text-sm text-slate-400 italic">No students currently in this room.</p>
    @endif
  </div>
  @empty
  <div class="card text-center py-12 text-slate-400">No rooms found for the selected filters.</div>
  @endforelse

  @if($rooms->hasPages())<div>{{ $rooms->links() }}</div>@endif
</div>
@endsection
