@extends('layouts.app')
@section('title', 'Cashier-wise Collection Report')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Cashier-wise Collection Report</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">From Date</label>
        <input type="date" name="from_date" value="{{ $from }}" class="input">
      </div>
      <div>
        <label class="label">To Date</label>
        <input type="date" name="to_date" value="{{ $to }}" class="input">
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  @if($rows->count())
  <div class="space-y-4">
    @foreach($rows as $userId => $cashierRows)
    @php
      $cashierName = $users[$userId] ?? 'Unknown';
      $cashierTotal = $cashierRows->sum('total');
    @endphp
    <div class="card">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-slate-800">{{ $cashierName }}</h2>
        <span class="font-bold text-slate-700">Total: ₹{{ number_format($cashierTotal, 2) }}</span>
      </div>
      <div class="table-wrap">
        <table class="min-w-full text-sm">
          <thead><tr>
            <th class="th">Payment Mode</th>
            <th class="th text-right">Transactions</th>
            <th class="th text-right">Amount Collected</th>
          </tr></thead>
          <tbody>
            @foreach($cashierRows as $r)
            <tr class="tr">
              <td class="td capitalize">{{ $r->payment_mode }}</td>
              <td class="td text-right">{{ $r->txn_count }}</td>
              <td class="td text-right font-medium">₹{{ number_format($r->total, 2) }}</td>
            </tr>
            @endforeach
            <tr class="bg-slate-50 font-semibold">
              <td class="td">Total</td>
              <td class="td text-right">{{ $cashierRows->sum('txn_count') }}</td>
              <td class="td text-right text-green-700">₹{{ number_format($cashierTotal, 2) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    @endforeach

    {{-- Grand total --}}
    @php $grandTotal = collect($rows->flatten())->sum('total'); @endphp
    <div class="card bg-slate-50">
      <div class="flex items-center justify-between">
        <span class="font-semibold text-slate-700">Grand Total (All Cashiers)</span>
        <span class="text-xl font-bold text-green-700">₹{{ number_format($grandTotal, 2) }}</span>
      </div>
    </div>
  </div>
  @else
  <div class="card text-center py-10 text-slate-400">
    No collection data found for the selected date range.
  </div>
  @endif
</div>
@endsection
