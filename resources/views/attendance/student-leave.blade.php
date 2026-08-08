@extends('layouts.app')
@section('title','Student Leave Requests')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Student Leave Requests</h1>
  </div>
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex gap-3">
      <form method="GET" class="flex gap-3 flex-1">
        <select name="status" class="select w-32">
          <option value="">All</option>
          <option value="pending" @selected(request('status')==='pending')>Pending</option>
          <option value="approved" @selected(request('status')==='approved')>Approved</option>
          <option value="rejected" @selected(request('status')==='rejected')>Rejected</option>
        </select>
        <select name="class_id" class="select w-36">
          <option value="">All Classes</option>
          @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      </form>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Student','Class','Type','From','To','Days','Reason','Status','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($leaves as $l)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800">{{ $l->student?->full_name }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $l->student?->currentEnrollment?->class?->name }}</td>
          <td class="px-4 py-3 capitalize text-slate-500">{{ str_replace('_',' ',$l->leave_type) }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $l->from_date->format('d M') }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $l->to_date->format('d M') }}</td>
          <td class="px-4 py-3 font-semibold text-center">{{ $l->days }}</td>
          <td class="px-4 py-3 text-slate-500 max-w-xs truncate text-xs">{{ $l->reason }}</td>
          <td class="px-4 py-3"><span class="badge-{{ $l->status === 'approved' ? 'green' : ($l->status === 'rejected' ? 'red' : 'amber') }} capitalize">{{ $l->status }}</span></td>
          <td class="px-4 py-3">
            @if($l->status === 'pending')
            <form method="POST" action="{{ route('attendance.leave.approve',$l->id) }}" class="inline">@csrf
              <button type="submit" class="text-green-600 hover:underline text-xs">Approve</button>
            </form>
            <form method="POST" action="{{ route('attendance.leave.reject',$l->id) }}" class="inline ml-1">@csrf
              <button type="submit" class="text-red-400 hover:underline text-xs">Reject</button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No leave requests.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($leaves->hasPages())<div class="px-4 pb-3">{{ $leaves->links() }}</div>@endif
  </div>
</div>
@endsection
