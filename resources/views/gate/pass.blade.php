@extends('layouts.app')
@section('title', 'Visitor Pass')
@section('content')
<div class="max-w-sm mx-auto space-y-4">
  <div class="flex items-center justify-between no-print">
    <a href="{{ route('gate.index') }}" class="btn-sm btn-secondary">← Gate</a>
    <button onclick="window.print()" class="btn-primary btn-sm">🖨 Print Pass</button>
  </div>

  @if(session('success')) <div class="alert-success no-print">{{ session('success') }}</div> @endif

  <div class="card border-2 border-indigo-200 printable text-center">
    <div class="bg-indigo-600 text-white py-3 px-4 rounded-t-xl -mx-6 -mt-6 mb-4">
      <p class="text-xs font-semibold tracking-widest uppercase opacity-80">DASA EduERP</p>
      <p class="text-lg font-bold">Visitor Pass</p>
    </div>

    @if($visitor->visitor_photo)
    <img src="{{ Storage::url($visitor->visitor_photo) }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-3 border-4 border-white shadow">
    @else
    <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center text-2xl font-bold text-indigo-600 mx-auto mb-3">
      {{ strtoupper(substr($visitor->visitor_name,0,1)) }}
    </div>
    @endif

    <h2 class="text-xl font-bold text-slate-800">{{ $visitor->visitor_name }}</h2>
    @if($visitor->visitor_phone)
    <p class="text-sm text-slate-500">{{ $visitor->visitor_phone }}</p>
    @endif

    <div class="mt-4 text-left space-y-2 text-sm">
      <div class="flex justify-between border-b border-slate-100 pb-1">
        <span class="text-slate-400">Purpose</span>
        <span class="font-medium text-slate-700">{{ $visitor->purpose }}</span>
      </div>
      @if($visitor->whom_to_meet)
      <div class="flex justify-between border-b border-slate-100 pb-1">
        <span class="text-slate-400">Meeting</span>
        <span class="font-medium text-slate-700">{{ $visitor->whom_to_meet }}</span>
      </div>
      @endif
      @if($visitor->department)
      <div class="flex justify-between border-b border-slate-100 pb-1">
        <span class="text-slate-400">Department</span>
        <span class="font-medium text-slate-700">{{ $visitor->department }}</span>
      </div>
      @endif
      <div class="flex justify-between border-b border-slate-100 pb-1">
        <span class="text-slate-400">In Time</span>
        <span class="font-medium text-slate-700">{{ $visitor->in_time->format('d M Y, h:i A') }}</span>
      </div>
      @if($visitor->vehicle_number)
      <div class="flex justify-between">
        <span class="text-slate-400">Vehicle</span>
        <span class="font-medium text-slate-700">{{ $visitor->vehicle_number }}</span>
      </div>
      @endif
    </div>

    {{-- QR --}}
    <div class="mt-4 flex justify-center">
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('gate.verify', $visitor->pass_token)) }}"
           alt="QR" class="w-24 h-24">
    </div>
    <p class="text-xs text-slate-400 mt-1">Scan to verify (returns JSON)</p>

    @if($visitor->isInside())
    <div class="mt-4 no-print">
      <form method="POST" action="{{ route('gate.checkout', $visitor->id) }}">
        @csrf @method('PATCH')
        <button type="submit" class="btn-primary w-full">✓ Checkout Visitor</button>
      </form>
    </div>
    @else
    <div class="mt-4 bg-slate-100 rounded-xl py-2">
      <p class="text-xs text-slate-500">Out: {{ $visitor->out_time?->format('h:i A') }}</p>
    </div>
    @endif
  </div>
</div>
@endsection
