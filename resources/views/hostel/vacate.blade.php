@extends('layouts.app')
@section('title', 'Student Hostel Vacate')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Hostel Vacate</h1>
    <a href="{{ route('hostel.allotment') }}" class="btn btn-secondary btn-sm">Back to Allotment</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  {{-- Search --}}
  <div class="card">
    <form method="GET" class="flex gap-3 items-end">
      <div class="flex-1">
        <label class="label text-xs">Search Student</label>
        <input type="text" name="search" value="{{ request('search') }}" class="input text-sm" placeholder="Name or admission number">
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Search</button>
    </form>
  </div>

  @if($allotments->isEmpty())
    <div class="card text-center py-12 text-slate-400">
      No active hostel allotments found.
    </div>
  @else
  <div class="space-y-3">
    @foreach($allotments as $allotment)
    <div class="card" x-data="{ open: false }">
      <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-4">
          @if($allotment->student->photo)
            <img src="{{ asset('storage/'.$allotment->student->photo) }}" class="w-10 h-10 rounded-full object-cover border border-slate-200" alt="">
          @else
            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
          @endif
          <div>
            <p class="font-semibold text-slate-800">{{ $allotment->student->full_name }}</p>
            <p class="text-xs text-slate-500">
              {{ $allotment->student->admission_number }} ·
              {{ $allotment->hostel->name ?? '—' }} ·
              Room {{ $allotment->room->room_number ?? '—' }}
            </p>
            <p class="text-xs text-slate-400 mt-0.5">Allotted: {{ $allotment->allotment_date->format('d M Y') }}</p>
          </div>
        </div>
        <button @click="open = !open" class="btn btn-sm bg-red-100 text-red-700 hover:bg-red-200">
          Vacate Student
        </button>
      </div>

      <div x-show="open" x-transition class="mt-4 pt-4 border-t border-slate-100">
        <form method="POST" action="{{ route('hostel.vacate.process', $allotment->id) }}" class="space-y-3"
              onsubmit="return confirm('Vacate {{ addslashes($allotment->student->full_name) }} from hostel?')">
          @csrf
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="label text-xs">Vacating Date <span class="text-red-500">*</span></label>
              <input type="date" name="vacating_date" class="input text-sm" required value="{{ today()->toDateString() }}">
            </div>
            <div>
              <label class="label text-xs">Reason <span class="text-red-500">*</span></label>
              <select name="vacate_reason" class="select text-sm" required>
                <option value="">Select reason</option>
                <option value="Passed out / Graduated">Passed out / Graduated</option>
                <option value="Withdrawal from school">Withdrawal from school</option>
                <option value="Disciplinary action">Disciplinary action</option>
                <option value="Request by parents">Request by parents</option>
                <option value="Transfer to day scholar">Transfer to day scholar</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-sm bg-red-600 text-white hover:bg-red-700">Confirm Vacate</button>
        </form>
      </div>
    </div>
    @endforeach
  </div>
  @endif
</div>
@endsection
