@extends('layouts.app')
@section('title','Book Reservations')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Book Reservation Queue</h1>
    <button x-data @click="$dispatch('open-modal','add-reservation')" class="btn btn-primary btn-sm">+ Reserve</button>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3">
    <select name="status" class="select w-36">
      <option value="">All</option>
      <option value="pending" @selected(request('status')==='pending')>Pending</option>
      <option value="ready" @selected(request('status')==='ready')>Ready</option>
      <option value="cancelled" @selected(request('status')==='cancelled')>Cancelled</option>
    </select>
    <input type="text" name="search" value="{{ request('search') }}" class="input flex-1" placeholder="Search book or student...">
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
  </div></form>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['#','Book','Member','Reserved On','Expiry','Status','Position','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($reservations as $i=>$r)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $reservations->firstItem()+$i }}</td>
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800 text-sm">{{ $r->book?->title }}</p>
            <p class="text-xs text-slate-400">{{ $r->book?->isbn }}</p>
          </td>
          <td class="px-4 py-3 text-slate-700 text-sm">{{ $r->member?->name ?? ($r->student?->full_name ?? '—') }}</td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $r->reserved_at?->format('d M Y') }}</td>
          <td class="px-4 py-3 text-xs {{ $r->expiry_date?->isPast() ? 'text-red-500' : 'text-slate-400' }}">{{ $r->expiry_date?->format('d M Y') }}</td>
          <td class="px-4 py-3"><span class="badge-{{ $r->status==='ready'?'green':($r->status==='cancelled'?'red':'amber') }} capitalize text-xs">{{ $r->status }}</span></td>
          <td class="px-4 py-3 text-center font-semibold text-slate-700">{{ $r->queue_position ?? '—' }}</td>
          <td class="px-4 py-3 flex gap-2">
            @if($r->status === 'pending')
            <form method="POST" action="{{ route('library.reservations.ready',$r->id) }}" class="inline">@csrf
              <button type="submit" class="text-green-600 hover:underline text-xs">Mark Ready</button>
            </form>
            <form method="POST" action="{{ route('library.reservations.cancel',$r->id) }}" class="inline">@csrf
              <button type="submit" class="text-red-400 hover:underline text-xs">Cancel</button>
            </form>
            @endif
            @if($r->status === 'ready')
            <form method="POST" action="{{ route('library.issue.store') }}" class="inline">@csrf
              <input type="hidden" name="reservation_id" value="{{ $r->id }}">
              <input type="hidden" name="book_id" value="{{ $r->book_id }}">
              <input type="hidden" name="student_id" value="{{ $r->student_id }}">
              <input type="hidden" name="member_type" value="student">
              <input type="hidden" name="due_date" value="{{ now()->addDays(14)->toDateString() }}">
              <button type="submit" class="text-indigo-600 hover:underline text-xs">Issue Now</button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No reservations.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($reservations->hasPages())<div class="px-4 pb-3">{{ $reservations->links() }}</div>@endif
  </div>
</div>

<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='add-reservation')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-700 mb-4">Reserve a Book</h3>
    <form method="POST" action="{{ route('library.reservations.store') }}" class="space-y-3">
      @csrf
      <div><label class="label">Book <span class="text-red-500">*</span></label>
        <select name="book_id" class="select" required>
          <option value="">Search & select book</option>
          @foreach($books as $b)<option value="{{ $b->id }}">{{ $b->title }} ({{ $b->available_copies }} available)</option>@endforeach
        </select>
      </div>
      <div><label class="label">Member / Student <span class="text-red-500">*</span></label>
        <select name="member_id" class="select" required>
          <option value="">Select</option>
          @foreach($members as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
        </select>
      </div>
      <div><label class="label">Expiry Date</label>
        <input type="date" name="expiry_date" class="input" value="{{ now()->addDays(7)->toDateString() }}">
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Reserve</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
@endsection
