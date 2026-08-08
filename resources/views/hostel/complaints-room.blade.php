@extends('layouts.app')
@section('title','Room Complaint History')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Complaint History — Room {{ $room->room_number }}</h1>
      <p class="page-subtitle">{{ $room->hostel?->name }} &mdash; {{ ucfirst($room->room_type ?? 'standard') }} room</p>
    </div>
    <a href="{{ route('hostel.complaints') }}" class="btn btn-secondary btn-sm">← All Complaints</a>
  </div>

  <div class="grid grid-cols-3 gap-4">
    @php
      $total    = $complaints->total();
      $open     = $complaints->getCollection()->where('status','open')->count();
      $resolved = $complaints->getCollection()->where('status','resolved')->count();
    @endphp
    <div class="card text-center py-4"><p class="text-2xl font-bold text-slate-700">{{ $total }}</p><p class="text-xs text-slate-400 mt-1">Total Complaints</p></div>
    <div class="card text-center py-4"><p class="text-2xl font-bold text-red-600">{{ $open }}</p><p class="text-xs text-slate-400 mt-1">Open (this page)</p></div>
    <div class="card text-center py-4"><p class="text-2xl font-bold text-green-600">{{ $resolved }}</p><p class="text-xs text-slate-400 mt-1">Resolved (this page)</p></div>
  </div>

  <div class="card overflow-hidden">
    @if($complaints->isEmpty())
      <p class="text-center py-10 text-slate-400">No complaints recorded for this room.</p>
    @else
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Date</th>
          <th class="th">Student</th>
          <th class="th">Category</th>
          <th class="th">Description</th>
          <th class="th">Priority</th>
          <th class="th">Status</th>
          <th class="th">Assigned To</th>
          <th class="th">Resolved On</th>
        </tr></thead>
        <tbody>
          @foreach($complaints as $c)
          <tr class="tr">
            <td class="td text-xs">{{ $c->created_at->format('d M Y') }}</td>
            <td class="td font-medium">{{ $c->student?->full_name ?? '—' }}</td>
            <td class="td"><span class="badge-slate capitalize text-xs">{{ str_replace('_',' ',$c->category ?? $c->complaint_type) }}</span></td>
            <td class="td text-xs text-slate-500 max-w-xs">
              <p class="truncate">{{ $c->description }}</p>
              @if($c->resolution_notes)<p class="text-green-600 italic truncate mt-0.5">{{ $c->resolution_notes }}</p>@endif
            </td>
            <td class="td text-xs font-semibold {{ ($c->priority==='high')?'text-red-600':(($c->priority==='medium')?'text-amber-600':'text-slate-400') }} capitalize">
              {{ $c->priority ?? 'medium' }}
            </td>
            <td class="td"><span class="badge-{{ $c->status==='resolved'?'green':($c->status==='in_progress'?'amber':'red') }} capitalize text-xs">{{ str_replace('_',' ',$c->status) }}</span></td>
            <td class="td text-xs">
              @if($c->assignedTo)
                {{ $c->assignedTo->first_name }} {{ $c->assignedTo->last_name }}
              @elseif($c->vendor_name)
                <span class="text-amber-600">{{ $c->vendor_name }}</span>
              @else
                —
              @endif
            </td>
            <td class="td text-xs text-slate-400">{{ $c->resolved_at?->format('d M Y') ?? '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if($complaints->hasPages())<div class="p-4">{{ $complaints->links() }}</div>@endif
    @endif
  </div>
</div>
@endsection
