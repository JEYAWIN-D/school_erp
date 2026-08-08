@extends('layouts.app')
@section('title','Holiday Management')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Holiday Master</h1>
    <a href="{{ route('academics.calendar.pdf') }}" target="_blank" class="btn btn-secondary btn-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
      Academic Calendar PDF
    </a>
  </div>
  {{-- Copy from previous year --}}
  @if(count($academicYears) > 1)
  <div class="card py-3 bg-blue-50 border border-blue-200">
    <form method="POST" action="{{ route('academics.holidays.copy') }}" class="flex flex-wrap items-center gap-3">
        @csrf
        <span class="text-sm font-medium text-blue-700">Copy holidays from:</span>
        <select name="from_year_id" class="select text-sm w-40" required>
            @foreach($academicYears as $y)
                @if(!$y->is_current)
                <option value="{{ $y->id }}">{{ $y->name }}</option>
                @endif
            @endforeach
        </select>
        <input type="hidden" name="to_year_id" value="{{ $currentYear?->id }}">
        <button type="submit" class="btn btn-sm bg-blue-600 text-white hover:bg-blue-700"
            onclick="return confirm('Copy holidays to {{ $currentYear?->name }}?')">Copy</button>
    </form>
  </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form method="POST" action="{{ route('academics.holidays.add') }}" class="card space-y-4">
      @csrf
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Add Holiday</h3>
      <div><label class="label">Holiday Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="input" required value="{{ old('name') }}">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Date <span class="text-red-500">*</span></label>
          <input type="date" name="date" class="input" required value="{{ old('date') }}">
        </div>
        <div><label class="label">Type</label>
          <select name="type" class="select">
            @foreach(['national'=>'National','state'=>'State','school'=>'School','optional'=>'Optional'] as $k=>$v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div><label class="label">Academic Year <span class="text-red-500">*</span></label>
        <select name="academic_year_id" class="select">
          @foreach($academicYears as $y)
            <option value="{{ $y->id }}" @selected($y->is_current)>{{ $y->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Add Holiday</button>
    </form>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Holidays — {{ $currentYear?->name }}</h3>
      @forelse($holidays as $h)
      <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
        <div>
          <p class="text-sm font-medium text-slate-800">{{ $h->name }}</p>
          <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($h->date)->format('D, d M Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
          <span class="badge-{{ $h->type === 'national' ? 'red' : ($h->type === 'state' ? 'amber' : 'slate') }} capitalize text-xs">{{ $h->type }}</span>
          <form method="POST" action="{{ route('academics.holidays.delete', $h->id) }}">@csrf @method('DELETE')
            <button type="submit" class="text-red-400 hover:text-red-600 text-xs" onclick="return confirm('Delete?')">Delete</button>
          </form>
        </div>
      </div>
      @empty
      <p class="text-slate-400 text-sm text-center py-6">No holidays configured.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
