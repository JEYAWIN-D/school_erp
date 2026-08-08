@extends('layouts.app')
@section('title','Day Book')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Fee Day Book</h1>
    <form method="GET" action="{{ route('fees.daybook.pdf') }}" class="flex gap-2">
      @foreach(request()->query() as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
      <button type="submit" class="btn btn-secondary btn-sm">Print Day Book</button>
    </form>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap items-center">
    <div class="flex items-center gap-2">
      <label class="text-sm text-slate-600">Date:</label>
      <input type="date" name="date" value="{{ request('date',today()->toDateString()) }}" class="input w-36">
    </div>
    <div class="flex items-center gap-2">
      <label class="text-sm text-slate-600">Fee Head:</label>
      <select name="fee_head_id" class="select w-40">
        <option value="">All</option>
        @foreach($feeHeads as $fh)<option value="{{ $fh->id }}" @selected(request('fee_head_id')==$fh->id)>{{ $fh->name }}</option>@endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </div></form>

  @if(isset($payments))
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @php
      $cash = $payments->where('payment_mode','cash')->sum('amount_paid');
      $online = $payments->where('payment_mode','online')->sum('amount_paid');
      $cheque = $payments->where('payment_mode','cheque')->sum('amount_paid');
      $dayTotal = $payments->sum('amount_paid');
    @endphp
    @foreach(['Cash'=>$cash,'Online'=>$online,'Cheque'=>$cheque,'Total'=>$dayTotal] as $label=>$val)
    <div class="card text-center py-4">
      <p class="text-xs text-slate-400 uppercase tracking-wide">{{ $label }}</p>
      <p class="text-xl font-bold text-slate-800 mt-1">₹{{ number_format($val,2) }}</p>
    </div>
    @endforeach
  </div>
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
      <span class="font-semibold text-slate-700">Transactions on {{ \Carbon\Carbon::parse(request('date'))->format('d M Y') }}</span>
      <span class="text-sm text-slate-400">{{ $payments->count() }} receipts</span>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Time','Receipt No','Student','Class','Fee Head','Amount','Late Fee','Discount','Mode','Action'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($payments as $p)
        <tr class="{{ $p->is_cancelled ? 'opacity-50 bg-red-50' : 'hover:bg-slate-50' }}">
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $p->created_at->format('h:i A') }}</td>
          <td class="px-4 py-3 font-mono text-xs text-indigo-700">{{ $p->receipt_number }}</td>
          <td class="px-4 py-3 font-medium text-slate-800">{{ $p->enrollment?->student?->full_name }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $p->enrollment?->class?->name }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $p->feeHead?->name }}</td>
          <td class="px-4 py-3 font-semibold">₹{{ number_format($p->amount_paid,2) }}</td>
          <td class="px-4 py-3 text-amber-600 text-xs">{{ $p->late_fee > 0 ? '₹'.number_format($p->late_fee,2) : '—' }}</td>
          <td class="px-4 py-3 text-green-600 text-xs">{{ $p->discount > 0 ? '₹'.number_format($p->discount,2) : '—' }}</td>
          <td class="px-4 py-3"><span class="badge-slate capitalize text-xs">{{ $p->payment_mode }}</span></td>
          <td class="px-4 py-3">
            @if($p->is_cancelled)
            <span class="text-red-400 text-xs">Cancelled</span>
            @else
            <a href="{{ route('fees.receipt',$p->id) }}" target="_blank" class="text-indigo-600 hover:underline text-xs">Receipt</a>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="10" class="px-4 py-8 text-center text-slate-400">No transactions.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
