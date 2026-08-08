@extends('layouts.app')
@section('title', 'Leave Requests')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Leave Requests</h1>
    <a href="{{ route('hr.leave-balance') }}" class="btn btn-secondary">Leave Balance Tracker</a>
  </div>
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr><th class="th">Employee</th><th class="th">Leave Type</th><th class="th">From</th><th class="th">To</th><th class="th">Days</th><th class="th">Status</th><th class="th">Actions</th></tr></thead>
      <tbody>
        @forelse($leaves as $l)
          <tr class="tr">
            <td class="td font-medium">{{ $l->employee?->first_name }} {{ $l->employee?->last_name }}</td>
            <td class="td">{{ $l->leaveType?->name ?? '—' }}</td>
            <td class="td">{{ \Carbon\Carbon::parse($l->from_date)->format('d M Y') }}</td>
            <td class="td">{{ \Carbon\Carbon::parse($l->to_date)->format('d M Y') }}</td>
            <td class="td">{{ $l->total_days }}</td>
            <td class="td">
              <span class="{{ $l->status === 'approved' ? 'badge-green' : ($l->status === 'rejected' ? 'badge-red' : ($l->status === 'cancelled' ? 'badge-slate' : 'badge-amber')) }}">
                {{ ucfirst($l->status) }}
              </span>
            </td>
            <td class="td">
              <div class="flex gap-1 flex-wrap items-center">
                @if($l->status === 'pending')
                  <form method="POST" action="{{ route('hr.leaves.approve', $l->id) }}">@csrf<button class="btn btn-secondary btn-sm">Approve</button></form>
                  <form method="POST" action="{{ route('hr.leaves.reject', $l->id) }}">@csrf<button class="btn btn-danger btn-sm">Reject</button></form>
                @endif
                @if(in_array($l->status, ['pending','approved']))
                  <form method="POST" action="{{ route('hr.leaves.cancel', $l->id) }}">@csrf<button class="btn btn-secondary btn-sm text-red-600" onclick="return confirm('Cancel this leave?')">Cancel</button></form>
                @endif
                @if($l->attachment)
                  <a href="{{ asset('storage/' . $l->attachment) }}" target="_blank" class="btn btn-secondary btn-sm flex items-center gap-1" title="Medical Certificate">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Doc
                  </a>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="td text-center py-10 text-slate-400">No leave requests.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($leaves->hasPages())<div class="text-sm mt-3">{{ $leaves->links() }}</div>@endif
</div>
@endsection
