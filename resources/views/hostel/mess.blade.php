@extends('layouts.app')
@section('title', 'Mess Management')
@section('content')
@php
  $allMenus = [];
  foreach($hostels as $h) {
    $allMenus[$h->id] = $h->meal_menus ? json_decode($h->meal_menus, true) : [];
  }
@endphp
<div class="space-y-6" x-data="{
  editDay: null,
  editHostel: null,
  allMenus: {{ json_encode($allMenus) }},
  get currentMenu() {
    return (this.editHostel && this.editDay && this.allMenus[this.editHostel])
      ? (this.allMenus[this.editHostel][this.editDay] ?? {})
      : {};
  }
}">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Mess Management</h1>
  </div>

  <div class="grid grid-cols-2 lg:grid-cols-{{ count($hostels) }} gap-4">
    @foreach($hostels as $h)
    <div class="card text-center py-5">
      <p class="font-semibold text-slate-800">{{ $h->name }}</p>
      <p class="text-xs text-slate-400 mt-1">Capacity: {{ $h->total_capacity }}</p>
      <span class="badge-green mt-2">{{ ucfirst($h->type) }}</span>
    </div>
    @endforeach
  </div>

  @foreach($hostels as $h)
  @php $menus = $h->meal_menus ? json_decode($h->meal_menus, true) : []; @endphp
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">Weekly Menu — {{ $h->name }}</h3>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-slate-500 border-b border-slate-100">
            @foreach(['Day','Breakfast','Lunch','Snacks','Dinner',''] as $col)
            <th class="pb-2 pr-4 font-medium">{{ $col }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
          @php $menu = $menus[$day] ?? []; @endphp
          <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="py-2 pr-4 font-medium text-slate-700">{{ $day }}</td>
            <td class="py-2 pr-4 text-slate-500">{{ $menu['breakfast'] ?? 'Not set' }}</td>
            <td class="py-2 pr-4 text-slate-500">{{ $menu['lunch'] ?? 'Not set' }}</td>
            <td class="py-2 pr-4 text-slate-500">{{ $menu['snacks'] ?? 'Not set' }}</td>
            <td class="py-2 pr-4 text-slate-500">{{ $menu['dinner'] ?? 'Not set' }}</td>
            <td class="py-2">
              <button @click="editDay = '{{ $day }}'; editHostel = {{ $h->id }}" class="text-xs text-indigo-600 hover:underline">Edit</button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endforeach

  {{-- Edit Meal Modal --}}
  <div x-show="editDay !== null" x-transition class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="modal-box max-w-md w-full" @click.outside="editDay = null">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-800" x-text="'Edit Menu — ' + editDay"></h3>
        <button @click="editDay = null" class="btn-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
      </div>
      <form method="POST" action="{{ route('hostel.mess.menu') }}" class="space-y-3">
        @csrf
        <input type="hidden" name="hostel_id" :value="editHostel">
        <input type="hidden" name="day" :value="editDay">
        <div><label class="label">Breakfast</label><input type="text" name="breakfast" class="input" :value="currentMenu.breakfast ?? ''" placeholder="e.g. Idli, Sambar, Chutney"></div>
        <div><label class="label">Lunch</label><input type="text" name="lunch" class="input" :value="currentMenu.lunch ?? ''" placeholder="e.g. Rice, Dal, Sabzi, Salad"></div>
        <div><label class="label">Snacks</label><input type="text" name="snacks" class="input" :value="currentMenu.snacks ?? ''" placeholder="e.g. Tea, Biscuits"></div>
        <div><label class="label">Dinner</label><input type="text" name="dinner" class="input" :value="currentMenu.dinner ?? ''" placeholder="e.g. Chapati, Sabzi, Dal"></div>
        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn btn-primary">Save Menu</button>
          <button type="button" @click="editDay = null" class="btn btn-secondary">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
