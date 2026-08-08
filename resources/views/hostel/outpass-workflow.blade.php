@extends('layouts.app')
@section('title','Outpass Workflow')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Outpass Workflow</h1>
    <button x-data @click="$dispatch('open-modal','new-outpass')" class="btn btn-primary btn-sm">+ New Outpass</button>
  </div>
  <div class="flex gap-3 border-b border-slate-200 pb-0">
    @foreach(['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','returned'=>'Returned'] as $s=>$l)
    <a href="?status={{ $s }}" class="px-4 py-2 text-sm font-medium {{ request('status',$s==='pending'?'pending':'x') === $s ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500 hover:text-slate-700' }}">
      {{ $l }}
      @if($counts[$s]??0)<span class="ml-1 bg-{{ $s==='pending'?'amber':'slate' }}-100 text-{{ $s==='pending'?'amber':'slate' }}-600 text-xs px-1.5 rounded-full">{{ $counts[$s] }}</span>@endif
    </a>
    @endforeach
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Student','Room','From','To','Reason','Contact','Status','Approved By','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($outpasses as $op)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800 text-sm">{{ $op->student?->full_name }}</td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $op->allotment?->room?->room_number }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $op->from_datetime?->format('d M h:i A') }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $op->to_datetime?->format('d M h:i A') }}</td>
          <td class="px-4 py-3 text-slate-400 text-xs max-w-xs truncate">{{ $op->reason }}</td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $op->parent_contact }}</td>
          <td class="px-4 py-3"><span class="badge-{{ $op->status==='approved'?'green':($op->status==='rejected'?'red':($op->status==='returned'?'indigo':'amber')) }} capitalize text-xs">{{ $op->status }}</span></td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $op->approvedBy?->name ?? '—' }}</td>
          <td class="px-4 py-3">
            @if($op->status === 'pending')
            <div class="flex gap-1">
              <form method="POST" action="{{ route('hostel.outpass.approve',$op->id) }}" class="inline">@csrf
                <button type="submit" class="text-green-600 hover:underline text-xs">Approve</button>
              </form>
              <form method="POST" action="{{ route('hostel.outpass.reject',$op->id) }}" class="inline">@csrf
                <button type="submit" class="text-red-400 hover:underline text-xs">Reject</button>
              </form>
            </div>
            @elseif($op->status === 'approved' && !$op->actual_return_time)
            <div class="flex gap-1">
              <form method="POST" action="{{ route('hostel.outpass.return',$op->id) }}" class="inline">@csrf
                <button type="submit" class="text-indigo-600 hover:underline text-xs">Mark Returned</button>
              </form>
              <a href="{{ route('hostel.outpass.gate-pass-qr', $op->id) }}" target="_blank" class="text-slate-500 hover:text-slate-700 text-xs">Print Pass</a>
            </div>
            @elseif($op->status === 'returned')
            <a href="{{ route('hostel.outpass.gate-pass-qr', $op->id) }}" target="_blank" class="text-slate-400 hover:text-slate-600 text-xs">Print Pass</a>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No outpasses.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($outpasses->hasPages())<div class="px-4 pb-3">{{ $outpasses->links() }}</div>@endif
  </div>
</div>

<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='new-outpass')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-700 mb-4">New Outpass Request</h3>
    <form method="POST" action="{{ route('hostel.outpass.store') }}" class="space-y-3">
      @csrf
      <div><label class="label">Student <span class="text-red-500">*</span></label>
        <select name="student_id" class="select" required>
          <option value="">Select</option>
          @foreach($students as $s)<option value="{{ $s->id }}">{{ $s->full_name }}</option>@endforeach
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">From <span class="text-red-500">*</span></label><input type="datetime-local" name="from_datetime" class="input" required></div>
        <div><label class="label">To <span class="text-red-500">*</span></label><input type="datetime-local" name="to_datetime" class="input" required></div>
      </div>
      <div><label class="label">Reason</label><textarea name="reason" class="input h-16"></textarea></div>
      <div><label class="label">Parent Contact</label><input type="tel" name="parent_contact" class="input"></div>
      <div><label class="label">Destination</label><input type="text" name="destination" class="input"></div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
@endsection
