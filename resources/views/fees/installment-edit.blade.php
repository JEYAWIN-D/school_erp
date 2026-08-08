@extends('layouts.app')
@section('title','Edit Installment Plan')
@section('content')
<div class="space-y-6 max-w-3xl">
  <h1 class="page-title">Edit Plan: {{ $plan->name }}</h1>
  <form method="POST" action="{{ route('fees.installments.store') }}" class="card space-y-4">
    @csrf
    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
    <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Add Installment</h3>
    <div class="grid grid-cols-3 gap-3">
      <div><label class="label">#</label><input type="number" name="installment_number" class="input" min="1" value="{{ $plan->installments->count() + 1 }}" required></div>
      <div><label class="label">Name</label><input type="text" name="name" class="input" placeholder="e.g. Q1 April"></div>
      <div><label class="label">Amount (₹) <span class="text-red-500">*</span></label><input type="number" name="amount" class="input" step="0.01" min="0" required></div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div><label class="label">Due Date</label><input type="date" name="due_date" class="input"></div>
      <div><label class="label">Fee Head</label>
        <select name="fee_head_id" class="select">
          <option value="">General</option>
          @foreach($feeHeads as $fh)<option value="{{ $fh->id }}">{{ $fh->name }}</option>@endforeach
        </select>
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Add Installment</button>
  </form>

  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Existing Installments</h3>
    @forelse($plan->installments->sortBy('installment_number') as $inst)
    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
      <div>
        <p class="text-sm font-semibold text-slate-800">#{{ $inst->installment_number }} {{ $inst->name }}</p>
        <p class="text-xs text-slate-400">Due: {{ $inst->due_date?->format('d M Y') ?? '—' }}</p>
      </div>
      <div class="flex items-center gap-3">
        <p class="font-bold text-indigo-700">₹{{ number_format($inst->amount,0) }}</p>
      </div>
    </div>
    @empty
    <p class="text-slate-400 text-sm text-center py-4">No installments yet.</p>
    @endforelse
    @if($plan->installments->count())
    <div class="flex justify-between font-bold text-slate-800 pt-2 border-t text-sm">
      <span>Total</span><span>₹{{ number_format($plan->installments->sum('amount'),0) }}</span>
    </div>
    @endif
  </div>
  <a href="{{ route('fees.installments') }}" class="btn btn-secondary btn-sm">Done</a>
</div>
@endsection
