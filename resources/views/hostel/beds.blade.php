@extends('layouts.app')
@section('title', 'Bed Master — Room ' . $room->room_number)
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-3">
      <a href="{{ route('hostel.rooms') }}" class="btn-icon">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h1 class="page-title">Bed Master</h1>
        <p class="text-sm text-slate-500">
          Room {{ $room->room_number }} &nbsp;·&nbsp;
          {{ $room->hostel?->name }} &nbsp;·&nbsp;
          Capacity: {{ $room->capacity }}
        </p>
      </div>
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert-danger">{{ session('error') }}</div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Bed List --}}
    <div class="lg:col-span-2 card">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-slate-700">Beds in Room {{ $room->room_number }}</h2>
        <span class="text-xs text-slate-400">{{ $room->beds->count() }} of {{ $room->capacity }} beds defined</span>
      </div>

      @if($room->beds->isEmpty())
        <div class="text-center py-10 text-slate-400">
          No beds defined for this room. Add beds below.
        </div>
      @else
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach($room->beds->sortBy('bed_number') as $bed)
        <div x-data="{ editing: false }" class="border rounded-lg p-3 {{ $bed->status === 'available' ? 'border-green-200 bg-green-50' : ($bed->status === 'occupied' ? 'border-blue-200 bg-blue-50' : 'border-amber-200 bg-amber-50') }}">
          <div class="flex items-center justify-between mb-1">
            <span class="font-bold text-slate-800 text-lg">{{ $bed->bed_number }}</span>
            <span class="text-xs px-2 py-0.5 rounded-full font-medium
              {{ $bed->status === 'available' ? 'bg-green-100 text-green-700' : ($bed->status === 'occupied' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
              {{ ucfirst($bed->status) }}
            </span>
          </div>
          @if($bed->notes)
            <p class="text-xs text-slate-500 mb-2">{{ $bed->notes }}</p>
          @endif

          <div class="flex gap-1.5 mt-2" x-show="!editing">
            <button @click="editing = true" class="btn btn-xs btn-secondary">Edit</button>
            @if($bed->status !== 'occupied')
            <form method="POST" action="{{ route('hostel.beds.delete', $bed->id) }}"
                  onsubmit="return confirm('Delete bed {{ $bed->bed_number }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-xs bg-red-100 text-red-700 hover:bg-red-200">Del</button>
            </form>
            @endif
          </div>

          <form method="POST" action="{{ route('hostel.beds.update', $bed->id) }}"
                class="space-y-2 mt-2" x-show="editing" x-transition>
            @csrf @method('PATCH')
            <select name="status" class="select text-xs py-1">
              @foreach(['available' => 'Available', 'occupied' => 'Occupied', 'maintenance' => 'Maintenance'] as $v => $l)
                <option value="{{ $v }}" @selected($bed->status === $v)>{{ $l }}</option>
              @endforeach
            </select>
            <input type="text" name="notes" value="{{ $bed->notes }}" class="input text-xs py-1" placeholder="Notes (optional)">
            <div class="flex gap-1.5">
              <button type="submit" class="btn btn-xs btn-primary">Save</button>
              <button type="button" @click="editing = false" class="btn btn-xs btn-secondary">Cancel</button>
            </div>
          </form>
        </div>
        @endforeach
      </div>
      @endif
    </div>

    {{-- Add Beds --}}
    <div class="space-y-4">
      {{-- Bulk Create --}}
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-3 text-sm">Bulk Create Beds</h3>
        <form method="POST" action="{{ route('hostel.beds.bulk', $room->id) }}" class="space-y-3">
          @csrf
          <div>
            <label class="label text-xs">Number of Beds to Create</label>
            <input type="number" name="count" class="input text-sm" min="1" max="{{ $room->capacity }}"
                   value="{{ $room->capacity }}" placeholder="e.g. 4">
            <p class="text-xs text-slate-400 mt-1">Creates B1, B2, B3… skipping existing bed numbers.</p>
          </div>
          <button type="submit" class="btn btn-primary btn-sm w-full">Auto-Create Beds</button>
        </form>
      </div>

      {{-- Add Single Bed --}}
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-3 text-sm">Add Single Bed</h3>
        <form method="POST" action="{{ route('hostel.beds.store', $room->id) }}" class="space-y-3">
          @csrf
          <div>
            <label class="label text-xs">Bed ID / Number <span class="text-red-500">*</span></label>
            <input type="text" name="bed_number" class="input text-sm" placeholder="e.g. A1, Top Bunk" required>
          </div>
          <div>
            <label class="label text-xs">Notes</label>
            <input type="text" name="notes" class="input text-sm" placeholder="e.g. Window side">
          </div>
          <button type="submit" class="btn btn-secondary btn-sm w-full">Add Bed</button>
        </form>
      </div>

      {{-- Stats --}}
      <div class="card bg-slate-50">
        <h3 class="font-semibold text-slate-700 mb-3 text-sm">Bed Summary</h3>
        @php
          $avail = $room->beds->where('status', 'available')->count();
          $occup = $room->beds->where('status', 'occupied')->count();
          $maint = $room->beds->where('status', 'maintenance')->count();
        @endphp
        <div class="space-y-2 text-sm">
          <div class="flex justify-between"><span class="text-slate-500">Available</span><span class="font-semibold text-green-600">{{ $avail }}</span></div>
          <div class="flex justify-between"><span class="text-slate-500">Occupied</span><span class="font-semibold text-blue-600">{{ $occup }}</span></div>
          <div class="flex justify-between"><span class="text-slate-500">Maintenance</span><span class="font-semibold text-amber-600">{{ $maint }}</span></div>
          <div class="border-t border-slate-200 pt-2 flex justify-between font-semibold"><span>Total</span><span>{{ $room->beds->count() }}</span></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
