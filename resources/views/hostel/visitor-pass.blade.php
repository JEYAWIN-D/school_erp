@extends('layouts.app')
@section('title', 'Visitor Pass')
@section('content')
<div class="max-w-lg mx-auto space-y-4">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Visitor Pass</h1>
    <div class="flex gap-2">
      <button onclick="window.print()" class="btn-sm btn-secondary">Print Pass</button>
      <a href="{{ route('hostel.visitors') }}" class="btn-sm btn-secondary">← Back</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  {{-- Pass card --}}
  <div id="pass-card" class="card border-2 border-indigo-300 print:border-black">
    <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
      <div>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Hostel Visitor Pass</p>
        <h2 class="text-lg font-bold text-slate-800">{{ $school?->school_name ?? config('app.name') }}</h2>
      </div>
      @if($visitor->isPassValid())
        <span class="badge-green text-sm px-3 py-1">VALID</span>
      @else
        <span class="badge-red text-sm px-3 py-1">EXPIRED</span>
      @endif
    </div>

    <div class="grid grid-cols-3 gap-4">
      <div class="col-span-2 space-y-2 text-sm">
        <div class="grid grid-cols-2 gap-x-4 gap-y-1">
          <div><span class="text-slate-500 text-xs">Visitor Name</span><p class="font-semibold">{{ $visitor->visitor_name }}</p></div>
          <div><span class="text-slate-500 text-xs">Relation</span><p class="font-semibold">{{ $visitor->relation ?? '—' }}</p></div>
          <div><span class="text-slate-500 text-xs">Phone</span><p class="font-semibold">{{ $visitor->visitor_phone ?? $visitor->visitor_mobile ?? '—' }}</p></div>
          <div><span class="text-slate-500 text-xs">ID / {{ $visitor->id_type ?? 'Document' }}</span><p class="font-semibold">{{ $visitor->id_number ?? '—' }}</p></div>
          <div><span class="text-slate-500 text-xs">Visiting</span><p class="font-semibold">{{ $visitor->student?->full_name }}</p></div>
          <div><span class="text-slate-500 text-xs">Hostel</span><p class="font-semibold">{{ $visitor->hostel?->name ?? '—' }}</p></div>
          <div><span class="text-slate-500 text-xs">Entry Time</span><p class="font-semibold">{{ $visitor->entry_time?->format('d M Y, h:i A') }}</p></div>
          <div><span class="text-slate-500 text-xs">Valid Until</span>
            <p class="font-semibold {{ $visitor->isPassValid() ? 'text-green-700' : 'text-red-600' }}">
              {{ $visitor->pass_valid_until?->format('h:i A') }}
            </p>
          </div>
          @if($visitor->purpose)
          <div class="col-span-2"><span class="text-slate-500 text-xs">Purpose</span><p class="font-semibold">{{ $visitor->purpose }}</p></div>
          @endif
        </div>
      </div>
      <div class="flex flex-col items-center gap-2">
        @if($visitor->visitor_photo)
          <img src="{{ Storage::url($visitor->visitor_photo) }}" alt="Visitor Photo"
            class="w-20 h-24 object-cover rounded border border-slate-200">
        @endif
        {{-- QR Code using qrserver.com API --}}
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode('HOSTEL-PASS:'.$visitor->pass_token) }}"
          alt="QR Pass" class="w-20 h-20">
        <p class="text-xs text-slate-400 text-center font-mono break-all">{{ substr($visitor->pass_token, 0, 12) }}…</p>
      </div>
    </div>

    <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-xs text-slate-400">
      <span>Pass #{{ $visitor->id }}</span>
      <span>{{ $visitor->pass_valid_until?->format('d M Y') }}</span>
      <span>Generated {{ now()->format('h:i A') }}</span>
    </div>
  </div>

  {{-- Checkout button --}}
  @if(!$visitor->exit_time)
  <form method="POST" action="{{ route('hostel.visitors.checkout', $visitor->id) }}">
    @csrf
    <button type="submit" class="btn-primary w-full" onclick="return confirm('Mark visitor as checked out?')">Check Out Visitor</button>
  </form>
  @else
  <div class="alert-success">Visitor checked out at {{ $visitor->exit_time?->format('h:i A') }}</div>
  @endif
</div>

<style>
@media print {
  nav, .btn-sm, form, .alert-success { display: none !important; }
  #pass-card { border: 2px solid #000 !important; }
}
</style>
@endsection
