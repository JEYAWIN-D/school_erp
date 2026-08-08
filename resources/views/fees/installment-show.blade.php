@extends('layouts.app')
@section('title','Installment Plan')
@section('content')
<div class="space-y-6 max-w-2xl">
  <div class="flex items-center justify-between">
    <h1 class="page-title">{{ $plan->name }}</h1>
    <a href="{{ route('fees.installments.edit',$plan->id) }}" class="btn btn-secondary btn-sm">Edit</a>
  </div>
  <div class="card space-y-4">
    <div class="grid grid-cols-2 gap-4 pb-3 border-b border-slate-100">
      <div><p class="text-xs text-slate-400">Class</p><p class="font-semibold text-slate-800">{{ $plan->class?->name ?? 'All Classes' }}</p></div>
      <div><p class="text-xs text-slate-400">Late Fee Rule</p><p class="font-semibold text-slate-800">{{ $plan->lateFeeRule?->name ?? 'None' }}</p></div>
    </div>
    <h3 class="font-semibold text-slate-700">Installments</h3>
    @forelse($plan->installments->sortBy('installment_number') as $inst)
    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
      <div>
        <p class="font-semibold text-slate-800 text-sm">#{{ $inst->installment_number }} — {{ $inst->name ?? 'Installment '.$inst->installment_number }}</p>
        <p class="text-xs text-slate-400">Due: {{ $inst->due_date?->format('d M Y') ?? 'Not set' }}</p>
      </div>
      <p class="font-bold text-indigo-700">₹{{ number_format($inst->amount,0) }}</p>
    </div>
    @empty
    <p class="text-slate-400 text-sm text-center py-4">No installments added yet. <a href="{{ route('fees.installments.edit',$plan->id) }}" class="text-indigo-600 hover:underline">Add installments</a></p>
    @endforelse
    @if($plan->installments->count())
    <div class="flex justify-between font-bold text-slate-800 pt-2 border-t">
      <span>Total</span>
      <span>₹{{ number_format($plan->installments->sum('amount'),0) }}</span>
    </div>
    @endif
  </div>
  <a href="{{ route('fees.installments') }}" class="btn btn-secondary btn-sm">Back to Plans</a>
</div>
@endsection
