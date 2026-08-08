@extends('layouts.app')
@section('title','Seat Management')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Seat Management</h1>
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Class</th>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Category</th>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Total Seats</th>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Filled</th>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Available</th>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Fill %</th>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Action</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($seats as $seat)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800">{{ $seat->class?->name }}</td>
          <td class="px-4 py-3 capitalize text-slate-500">{{ $seat->category }}</td>
          <td class="px-4 py-3">{{ $seat->total_seats }}</td>
          <td class="px-4 py-3 text-red-600">{{ $seat->filled_seats }}</td>
          <td class="px-4 py-3 text-green-600 font-semibold">{{ $seat->available_seats }}</td>
          <td class="px-4 py-3">
            @php $pct = $seat->total_seats > 0 ? round($seat->filled_seats / $seat->total_seats * 100) : 0; @endphp
            <div class="w-24 bg-slate-200 rounded-full h-1.5"><div class="h-1.5 rounded-full {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-green-500') }}" style="width:{{ $pct }}%"></div></div>
            <span class="text-xs text-slate-400 ml-1">{{ $pct }}%</span>
          </td>
          <td class="px-4 py-3">
            <button x-data @click="$dispatch('open-modal','edit-seat-{{ $seat->id }}')" class="text-indigo-600 hover:underline text-xs">Edit</button>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No seat capacities configured.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{-- Add Seats Form --}}
  <div class="card max-w-lg">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Configure Seats</h3>
    <form method="POST" action="{{ route('admissions.seats.save') }}" class="space-y-4">
      @csrf
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select">
            <option value="">Select</option>
            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="label">Category</label>
          <select name="category" class="select">
            @foreach(['general','sc','st','obc','ews','minority'] as $cat)
            <option value="{{ $cat }}">{{ strtoupper($cat) }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div><label class="label">Total Seats</label>
        <input type="number" name="total_seats" class="input" min="1" value="30">
      </div>
      <button type="submit" class="btn btn-primary">Save</button>
    </form>
  </div>
</div>
@endsection
