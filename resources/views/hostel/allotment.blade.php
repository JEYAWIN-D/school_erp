@extends('layouts.app')
@section('title', 'Room Allotment')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Room Allotment</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form method="POST" action="{{ route('hostel.allotment.save') }}" class="card space-y-4">
      @csrf
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">New Allotment</h3>
      <div><label class="label">Student <span class="text-red-500">*</span></label>
        <select name="student_id" class="select">
          <option value="">Select student</option>
          @foreach($students as $s)<option value="{{ $s->id }}" @selected(old('student_id')==$s->id)>{{ $s->full_name }} ({{ $s->admission_number }})</option>@endforeach
        </select>
      </div>
      <div><label class="label">Room <span class="text-red-500">*</span></label>
        <select name="room_id" class="select">
          <option value="">Select room</option>
          @foreach($rooms as $r)<option value="{{ $r->id }}" @selected(old('room_id')==$r->id)>{{ $r->hostel?->name }} — Room {{ $r->room_number }} ({{ $r->capacity - $r->occupied }} spots left)</option>@endforeach
        </select>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Allotment Date</label><input type="date" name="allotment_date" value="{{ old('allotment_date', today()->toDateString()) }}" class="input"></div>
        <div><label class="label">Monthly Fee</label><input type="number" name="monthly_fee" value="{{ old('monthly_fee', 0) }}" class="input" min="0" step="0.01"></div>
      </div>
      <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 space-y-2" x-data="{ messIncluded: true }">
        <div class="flex items-center gap-2">
          <input type="checkbox" name="mess_included" value="1" id="mess_included" x-model="messIncluded"
                 @checked(old('mess_included', true)) class="w-4 h-4 text-amber-600 rounded">
          <label for="mess_included" class="text-sm font-medium text-slate-700">Include Mess Fee for this Student</label>
        </div>
        <div x-show="!messIncluded" x-transition>
          <label class="label text-xs">Reason for Mess Exclusion</label>
          <input type="text" name="mess_exclusion_reason" class="input text-sm" placeholder="e.g. Day scholar meals, personal diet, medical reason">
        </div>
      </div>
      <div class="flex justify-end"><button type="submit" class="btn btn-primary">Allot Room</button></div>
    </form>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Current Allotments</h3>
      @forelse($allotments as $a)
        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0 text-sm">
          <div>
            <p class="font-medium text-slate-800">{{ $a->student?->full_name }}</p>
            <p class="text-xs text-slate-400">{{ $a->room?->hostel?->name }} — Room {{ $a->room?->room_number }}</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="badge-green text-xs">Active</span>
            <a href="{{ route('hostel.vacate', ['allotment_id' => $a->id]) }}" class="text-xs text-red-500 hover:underline">Vacate</a>
          </div>
        </div>
      @empty
        <p class="text-slate-400 text-sm text-center py-6">No allotments yet.</p>
      @endforelse
      @if($allotments->hasPages())
        <div class="pt-3">{{ $allotments->links() }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
