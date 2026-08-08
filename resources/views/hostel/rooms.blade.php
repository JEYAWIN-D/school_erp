@extends('layouts.app')
@section('title', 'Hostel Rooms')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Hostel Rooms</h1>
    <button x-data @click="$dispatch('open-modal','add-room')" class="btn btn-primary btn-sm">+ Add Room</button>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert-danger">{{ session('error') }}</div>@endif

  <form method="GET" class="card-flat py-4"><div class="flex gap-3">
    <select name="hostel_id" class="select w-40">
      <option value="">All Hostels</option>
      @foreach($hostels as $h)<option value="{{ $h->id }}" @selected(request('hostel_id')==$h->id)>{{ $h->name }}</option>@endforeach
    </select>
    <select name="status" class="select w-36">
      <option value="">All Status</option>
      <option value="available" @selected(request('status')==='available')>Available</option>
      <option value="full" @selected(request('status')==='full')>Full</option>
      <option value="maintenance" @selected(request('status')==='maintenance')>Maintenance</option>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
  </div></form>

  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($rooms as $room)
      <div class="card py-4 text-center relative">
        <p class="text-xl font-bold text-slate-800">{{ $room->room_number }}</p>
        <p class="text-xs text-slate-400">{{ $room->hostel?->name }}</p>
        <p class="text-sm mt-2 text-slate-600">{{ $room->occupied ?? 0 }}/{{ $room->capacity }} occupied</p>
        <span class="{{ $room->status === 'available' ? 'badge-green' : ($room->status === 'full' ? 'badge-red' : 'badge-amber') }} mt-2">{{ ucfirst($room->status) }}</span>
        <div class="flex flex-wrap justify-center gap-1 mt-2">
          @if($room->is_ac)<span class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">AC</span>
          @else<span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">Fan</span>@endif
          @if($room->is_attached_bathroom)<span class="text-xs bg-teal-100 text-teal-700 px-1.5 py-0.5 rounded">Attached Bath</span>
          @else<span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">Common Bath</span>@endif
          @if($room->has_locker ?? false)<span class="text-xs bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded">Locker</span>@endif
        </div>
        <div class="mt-3 flex justify-center gap-2">
          <a href="{{ route('hostel.beds', $room->id) }}" class="btn btn-xs btn-secondary">Beds</a>
          <button x-data @click="$dispatch('open-modal','edit-room-{{ $room->id }}')" class="btn btn-xs btn-secondary">Edit</button>
          <form method="POST" action="{{ route('hostel.rooms.destroy', $room->id) }}" onsubmit="return confirm('Delete room {{ $room->room_number }}?')">
            @csrf @method('DELETE')
            <button class="btn btn-xs btn-danger">Del</button>
          </form>
        </div>
      </div>

      {{-- Edit modal per room --}}
      <div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='edit-room-{{ $room->id }}')" x-show="show" style="display:none"
           class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md" @click.stop>
          <h3 class="font-semibold text-slate-700 mb-4">Edit Room {{ $room->room_number }}</h3>
          <form method="POST" action="{{ route('hostel.rooms.update', $room->id) }}" class="space-y-3">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-3">
              <div><label class="label">Room No</label><input type="text" name="room_number" value="{{ $room->room_number }}" class="input" required></div>
              <div><label class="label">Capacity</label><input type="number" name="capacity" value="{{ $room->capacity }}" class="input" min="1" required></div>
              <div><label class="label">Floor</label><input type="text" name="floor" value="{{ $room->floor }}" class="input"></div>
              <div><label class="label">Status</label>
                <select name="status" class="select">
                  <option value="available" @selected($room->status==='available')>Available</option>
                  <option value="full" @selected($room->status==='full')>Full</option>
                  <option value="maintenance" @selected($room->status==='maintenance')>Maintenance</option>
                </select>
              </div>
              <div><label class="label">Monthly Fee</label><input type="number" name="monthly_fee" value="{{ $room->monthly_fee }}" class="input" step="0.01"></div>
            </div>
            <div class="flex gap-4 text-sm">
              <label class="flex items-center gap-2"><input type="checkbox" name="is_ac" value="1" @checked($room->is_ac) class="rounded"> AC</label>
              <label class="flex items-center gap-2"><input type="checkbox" name="is_attached_bathroom" value="1" @checked($room->is_attached_bathroom) class="rounded"> Attached Bath</label>
            </div>
            <div class="flex gap-2">
              <button type="submit" class="btn btn-primary btn-sm flex-1">Save</button>
              <button type="button" @click="show=false" class="btn btn-secondary btn-sm flex-1">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    @empty
      <div class="col-span-4 text-center py-10 text-slate-400">No rooms found.</div>
    @endforelse
  </div>
  @if($rooms->hasPages())<div class="text-sm mt-3">{{ $rooms->links() }}</div>@endif
</div>

{{-- Add Room Modal --}}
<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='add-room')" x-show="show" style="display:none"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md" @click.stop>
    <h3 class="font-semibold text-slate-700 mb-4">Add New Room</h3>
    <form method="POST" action="{{ route('hostel.rooms.store') }}" class="space-y-3">
      @csrf
      <div class="grid grid-cols-2 gap-3">
        <div class="col-span-2">
          <label class="label">Hostel <span class="text-red-500">*</span></label>
          <select name="hostel_id" class="select" required>
            <option value="">Select Hostel</option>
            @foreach($hostels as $h)<option value="{{ $h->id }}">{{ $h->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="label">Room No <span class="text-red-500">*</span></label><input type="text" name="room_number" class="input" required placeholder="e.g. A101"></div>
        <div><label class="label">Capacity <span class="text-red-500">*</span></label><input type="number" name="capacity" class="input" min="1" max="20" value="2" required></div>
        <div><label class="label">Floor</label><input type="text" name="floor" class="input" placeholder="e.g. G, 1, 2"></div>
        <div><label class="label">Monthly Fee (₹)</label><input type="number" name="monthly_fee" class="input" step="0.01" value="0"></div>
      </div>
      <div class="flex gap-4 text-sm">
        <label class="flex items-center gap-2"><input type="checkbox" name="is_ac" value="1" class="rounded"> AC Room</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_attached_bathroom" value="1" class="rounded"> Attached Bath</label>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm flex-1">Add Room</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm flex-1">Cancel</button>
      </div>
    </form>
  </div>
</div>
@endsection
