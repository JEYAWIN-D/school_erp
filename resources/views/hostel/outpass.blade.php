@extends('layouts.app')
@section('title', 'Outpass Management')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Outpass Management</h1>
    <button x-data @click="$dispatch('open-modal','new-outpass')" class="btn btn-primary btn-sm">New Outpass</button>
  </div>
  <div class="card text-center py-5 col-span-3">
    <p class="text-slate-500">{{ $allotments->count() }} active hostel residents</p>
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        @foreach(['Student','Room','Out Date','Return Date','Purpose','Status','Action'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($allotments as $op)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800">{{ $op->student?->full_name }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $op->allotment?->room?->room_number ?? '—' }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $op->from_datetime ? \Carbon\Carbon::parse($op->from_datetime)->format('d M Y H:i') : '—' }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $op->to_datetime ? \Carbon\Carbon::parse($op->to_datetime)->format('d M Y H:i') : '—' }}</td>
          <td class="px-4 py-3 text-slate-500 max-w-xs truncate">{{ $op->reason }}</td>
          <td class="px-4 py-3"><span class="badge-{{ $op->status === 'approved' ? 'green' : ($op->status === 'rejected' ? 'red' : 'amber') }}">{{ ucfirst($op->status) }}</span></td>
          <td class="px-4 py-3 text-slate-500 text-xs">—</td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No outpass records found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
