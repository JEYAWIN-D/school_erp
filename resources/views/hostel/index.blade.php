@extends('layouts.app')
@section('title', 'Hostel Management')
@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Hostel Management</h1>
      <p class="page-subtitle">{{ $stats['residents'] }} residents across {{ $hostels->count() }} hostel(s)</p>
    </div>
    <a href="{{ route('hostel.allotment') }}" class="btn btn-primary self-start sm:self-auto">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      Allot Room
    </a>
  </div>

  {{-- Alert: Overdue Outpasses --}}
  @if($overdueOutpasses > 0)
  <div class="rounded-xl border border-red-200 bg-red-50 p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-semibold text-red-800">
      {{ $overdueOutpasses }} outpass(es) overdue — students have not returned as scheduled.
      <a href="{{ route('hostel.overdue-outpasses') }}" class="underline ml-1">Review →</a>
    </p>
  </div>
  @endif

  {{-- Alert: Pending Complaints --}}
  @if($pendingComplaints > 0)
  <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
    <p class="text-sm font-semibold text-amber-800">
      {{ $pendingComplaints }} open complaint(s) awaiting resolution.
      <a href="{{ route('hostel.complaints') }}" class="underline ml-1">Resolve →</a>
    </p>
  </div>
  @endif

  {{-- KPI Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-slate-600 to-slate-800 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75"/></svg>
      </div>
      <p class="stat-number">{{ $stats['totalRooms'] }}</p>
      <p class="text-sm text-slate-500">Total Rooms</p>
    </div>
    <a href="{{ route('hostel.occupancy') }}" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-red-500 to-rose-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
      </div>
      <p class="stat-number">{{ $stats['occupiedRooms'] }}</p>
      <p class="text-sm text-slate-500">Occupied Rooms</p>
    </a>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <p class="stat-number text-green-600">{{ $stats['availableRooms'] }}</p>
      <p class="text-sm text-slate-500">Available Rooms</p>
    </div>
    <a href="{{ route('hostel.room-students') }}" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-blue-500 to-indigo-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <p class="stat-number">{{ $stats['residents'] }}</p>
      <p class="text-sm text-slate-500">Residents</p>
    </a>
  </div>

  {{-- Occupancy Progress --}}
  @if($stats['totalRooms'] > 0)
  <div class="card">
    <div class="flex items-center justify-between mb-2">
      <h3 class="font-semibold text-slate-700">Overall Occupancy</h3>
      <span class="text-lg font-bold {{ $stats['occupancyPct'] >= 90 ? 'text-red-600' : ($stats['occupancyPct'] >= 70 ? 'text-amber-600' : 'text-green-600') }}">{{ $stats['occupancyPct'] }}%</span>
    </div>
    <div class="w-full bg-slate-100 rounded-full h-3">
      <div class="h-3 rounded-full transition-all {{ $stats['occupancyPct'] >= 90 ? 'bg-gradient-to-r from-red-500 to-rose-400' : ($stats['occupancyPct'] >= 70 ? 'bg-gradient-to-r from-amber-500 to-orange-400' : 'bg-gradient-to-r from-green-500 to-emerald-400') }}"
           style="width:{{ min(100,$stats['occupancyPct']) }}%"></div>
    </div>
    <p class="text-xs text-slate-400 mt-1.5">{{ $stats['occupiedRooms'] }} of {{ $stats['totalRooms'] }} rooms occupied &mdash; {{ $stats['availableRooms'] }} available</p>
  </div>
  @endif

  {{-- Quick Links --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
    @foreach([
      ['Rooms',             'hostel.rooms',             'from-slate-600 to-slate-800',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75"/>'],
      ['Allot Room',        'hostel.allotment',         'from-blue-500 to-indigo-600',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'],
      ['Outpass',           'hostel.outpass.workflow',  'from-green-500 to-emerald-600', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>'],
      ['Complaints',        'hostel.complaints',        'from-amber-500 to-orange-500',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>'],
      ['Visitors',          'hostel.visitors',          'from-teal-500 to-cyan-600',     '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
      ['Mess',              'hostel.mess',              'from-pink-500 to-rose-500',     '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>'],
      ['Mess Attendance',   'hostel.mess-attendance',   'from-violet-500 to-purple-600', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'],
      ['Mess Rebates',      'hostel.mess-rebates',      'from-cyan-500 to-sky-600',      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>'],
      ['Wardens',           'hostel.wardens',           'from-lime-500 to-green-600',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
      ['Night Duty',        'hostel.night-duty',        'from-indigo-500 to-blue-600',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>'],
      ['Fee Outstanding',   'hostel.fee-outstanding',   'from-red-500 to-rose-600',      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>'],
      ['Disciplinary',      'hostel.disciplinary',      'from-orange-500 to-amber-600',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
    ] as [$label,$route,$color,$icon])
    <a href="{{ route($route) }}" class="card-flat flex items-center gap-3 py-3.5 px-4 hover:shadow-card-md transition">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $color }} flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
      </div>
      <span class="font-semibold text-sm text-slate-700">{{ $label }}</span>
    </a>
    @endforeach
  </div>

  {{-- Hostel Buildings Grid --}}
  @if($hostels->count() > 0)
  <div>
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold text-slate-700">Hostel Buildings</h3>
      <button type="button" onclick="document.getElementById('addHostelModal').classList.remove('hidden')"
              class="btn btn-secondary btn-sm">+ Add Hostel</button>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($hostels as $h)
      @php
        $occ = $h->rooms_count > 0 ? round($h->occupied_count / $h->rooms_count * 100) : 0;
        $occColor = $occ >= 90 ? '#ef4444' : ($occ >= 70 ? '#f59e0b' : '#22c55e');
      @endphp
      <div class="card">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75"/></svg>
          </div>
          <div>
            <p class="font-semibold text-slate-800">{{ $h->name }}</p>
            <span class="badge-slate capitalize text-xs">{{ $h->type }}</span>
          </div>
        </div>
        <dl class="text-sm space-y-1.5 mb-3">
          <div class="flex justify-between"><dt class="text-slate-400">Warden</dt><dd class="font-medium text-slate-700">{{ $h->warden_name ?? '—' }}</dd></div>
          <div class="flex justify-between"><dt class="text-slate-400">Capacity</dt><dd class="font-medium text-slate-700">{{ $h->total_capacity }}</dd></div>
          <div class="flex justify-between"><dt class="text-slate-400">Rooms</dt><dd class="font-medium text-slate-700">{{ $h->rooms_count }} ({{ $h->occupied_count }} occupied)</dd></div>
        </dl>
        <div class="w-full bg-slate-100 rounded-full h-2 mb-1">
          <div class="h-2 rounded-full transition-all" style="width:{{ min(100,$occ) }}%;background:{{ $occColor }};"></div>
        </div>
        <p class="text-xs text-slate-400 mb-3">{{ $occ }}% occupancy</p>
        <div class="flex gap-2 mt-3">
          <a href="{{ route('hostel.rooms', ['hostel_id' => $h->id]) }}" class="btn btn-ghost btn-sm flex-1 justify-center">View Rooms</a>
          <button type="button" onclick="openEditHostel({{ $h->id }}, '{{ addslashes($h->name) }}', '{{ $h->type }}', {{ $h->total_capacity ?? 0 }}, '{{ addslashes($h->address ?? '') }}', '{{ addslashes($h->contact ?? '') }}')"
                  class="btn btn-secondary btn-sm">Edit</button>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @else
  <div class="card py-10 px-6 text-center">
    <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
      <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75"/></svg>
    </div>
    <h3 class="text-lg font-semibold text-slate-700 mb-1">Set up your Hostel</h3>
    <p class="text-sm text-slate-400 mb-6 max-w-sm mx-auto">No hostel buildings have been configured yet. Follow these steps to get started.</p>
    <ol class="text-left space-y-3 max-w-sm mx-auto mb-6">
      <li class="flex gap-3 items-start">
        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
        <div>
          <p class="text-sm font-medium text-slate-700">Add a Hostel Building</p>
          <p class="text-xs text-slate-400">Create buildings for boys/girls hostels with capacity details.</p>
        </div>
      </li>
      <li class="flex gap-3 items-start">
        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
        <div>
          <p class="text-sm font-medium text-slate-700">Add Rooms</p>
          <p class="text-xs text-slate-400">Go to Rooms and create room entries with capacity per room.</p>
        </div>
      </li>
      <li class="flex gap-3 items-start">
        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
        <div>
          <p class="text-sm font-medium text-slate-700">Allot Rooms to Students</p>
          <p class="text-xs text-slate-400">Use Allot Room to assign students to available rooms.</p>
        </div>
      </li>
      <li class="flex gap-3 items-start">
        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">4</span>
        <div>
          <p class="text-sm font-medium text-slate-700">Assign a Warden</p>
          <p class="text-xs text-slate-400">Add warden details under the Wardens section for oversight.</p>
        </div>
      </li>
    </ol>
    <button type="button" onclick="document.getElementById('addHostelModal').classList.remove('hidden')"
            class="btn btn-primary">+ Add First Hostel Building</button>
  </div>
  @endif

  {{-- Add Hostel Modal --}}
  <div id="addHostelModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
      <div class="flex items-center justify-between p-4 border-b border-slate-100">
        <h3 class="font-semibold text-slate-800">Add Hostel Building</h3>
        <button type="button" onclick="document.getElementById('addHostelModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
      </div>
      <form method="POST" action="{{ route('hostel.building.store') }}" class="p-4 space-y-3">
        @csrf
        <div><label class="label">Hostel Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" class="input" required placeholder="e.g. Boys Hostel Block A"></div>
        <div><label class="label">Type</label>
          <select name="type" class="select">
            <option value="boys">Boys</option>
            <option value="girls">Girls</option>
            <option value="co-ed">Co-ed</option>
            <option value="staff">Staff</option>
          </select>
        </div>
        <div><label class="label">Total Capacity</label>
          <input type="number" name="total_capacity" class="input" min="1" placeholder="e.g. 100"></div>
        <div><label class="label">Address</label>
          <input type="text" name="address" class="input" placeholder="Block/Building address"></div>
        <div><label class="label">Contact</label>
          <input type="text" name="contact" class="input" placeholder="Phone number"></div>
        <div class="flex gap-2 justify-end pt-1">
          <button type="button" onclick="document.getElementById('addHostelModal').classList.add('hidden')" class="btn btn-secondary btn-sm">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Save Hostel</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Edit Hostel Modal --}}
  <div id="editHostelModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
      <div class="flex items-center justify-between p-4 border-b border-slate-100">
        <h3 class="font-semibold text-slate-800">Edit Hostel Building</h3>
        <button type="button" onclick="document.getElementById('editHostelModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
      </div>
      <form method="POST" id="editHostelForm" action="" class="p-4 space-y-3">
        @csrf @method('PUT')
        <div><label class="label">Hostel Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" id="editHostelName" class="input" required></div>
        <div><label class="label">Type</label>
          <select name="type" id="editHostelType" class="select">
            <option value="boys">Boys</option>
            <option value="girls">Girls</option>
            <option value="co-ed">Co-ed</option>
            <option value="staff">Staff</option>
          </select>
        </div>
        <div><label class="label">Total Capacity</label>
          <input type="number" name="total_capacity" id="editHostelCapacity" class="input" min="1"></div>
        <div><label class="label">Address</label>
          <input type="text" name="address" id="editHostelAddress" class="input"></div>
        <div><label class="label">Contact</label>
          <input type="text" name="contact" id="editHostelContact" class="input"></div>
        <div class="flex gap-2 justify-end pt-1">
          <button type="button" onclick="document.getElementById('editHostelModal').classList.add('hidden')" class="btn btn-secondary btn-sm">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Update</button>
        </div>
      </form>
    </div>
  </div>

</div>
<script>
function openEditHostel(id, name, type, capacity, address, contact) {
  document.getElementById('editHostelForm').action = '/hostel/buildings/' + id;
  document.getElementById('editHostelName').value = name;
  document.getElementById('editHostelType').value = type;
  document.getElementById('editHostelCapacity').value = capacity;
  document.getElementById('editHostelAddress').value = address;
  document.getElementById('editHostelContact').value = contact;
  document.getElementById('editHostelModal').classList.remove('hidden');
}
</script>
@endsection
