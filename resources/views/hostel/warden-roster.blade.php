@extends('layouts.admin')
@section('title', 'Warden Duty Roster')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="page-title">Warden Duty Roster</h1>
        <form method="GET" class="flex gap-2">
            <input type="week" name="week" value="{{ $week }}" class="input" onchange="this.form.submit()">
        </form>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

    @foreach($hostels as $hostel)
    <div class="card">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-lg bg-slate-700 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75"/></svg>
            </div>
            <div>
                <p class="font-semibold text-slate-800">{{ $hostel->name }}</p>
                <p class="text-xs text-slate-400 capitalize">{{ $hostel->type }}</p>
            </div>
        </div>

        @if($hostel->wardens?->count())
        <div class="grid grid-cols-7 gap-2">
            @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
            <div class="border border-slate-200 rounded-lg p-2 text-center">
                <p class="text-xs font-semibold text-slate-500 mb-2">{{ $day }}</p>
                <select name="roster[{{ $hostel->id }}][{{ $day }}]" class="select text-xs w-full">
                    <option value="">—</option>
                    @foreach($hostel->wardens as $warden)
                    <option value="{{ $warden->id }}">{{ $warden->first_name }}</option>
                    @endforeach
                </select>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-slate-400">No wardens assigned to this hostel. <a href="{{ route('hostel.wardens') }}" class="text-blue-600 hover:underline">Assign wardens</a> first.</p>
        @endif
    </div>
    @endforeach

    @if($hostels->count())
    <form method="POST" action="{{ route('hostel.warden-roster.save') }}">
        @csrf
        <input type="hidden" name="week" value="{{ $week }}">
        <button type="submit" class="btn btn-primary">Save Roster</button>
    </form>
    @endif
</div>
@endsection
