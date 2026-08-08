@extends('layouts.app')
@section('title', 'Fee Collection Register')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Fee Collection Register</h1>
    <div class="flex gap-2">
      <a href="{{ route('dashboard') }}" class="btn-sm btn-secondary">← Dashboard</a>
      <button onclick="window.print()" class="btn-sm btn-primary">🖨 Print</button>
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">From Date</label>
      <input type="date" name="from" value="{{ $from }}" class="input">
    </div>
    <div>
      <label class="label">To Date</label>
      <input type="date" name="to" value="{{ $to }}" class="input">
    </div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="{{ route('reports.fee-collection-register') }}" class="btn-sm btn-secondary">Reset</a>
    <div class="ml-auto text-right">
      <p class="text-xs text-slate-400">Total Collection</p>
      <p class="text-xl font-bold text-emerald-600">₹{{ number_format($totalAmount, 2) }}</p>
    </div>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">Receipt No.</th>
          <th class="th">Date</th>
          <th class="th">Student</th>
          <th class="th">Adm. No.</th>
          <th class="th">Class</th>
          <th class="th">Fee Head</th>
          <th class="th">Mode</th>
          <th class="th text-right">Amount</th>
        </tr>
      </thead>
      <tbody>
        @forelse($payments as $p)
        <tr class="tr">
          <td class="td font-mono font-semibold text-indigo-600">{{ $p->receipt_number }}</td>
          <td class="td">{{ \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') }}</td>
          <td class="td font-medium">{{ $p->first_name }} {{ $p->last_name }}</td>
          <td class="td font-mono text-xs">{{ $p->admission_number }}</td>
          <td class="td">{{ $p->class_name ?? '—' }}</td>
          <td class="td text-xs">{{ $p->fee_head ?? '—' }}</td>
          <td class="td">
            <span class="badge-slate text-xs capitalize">{{ str_replace('_',' ',$p->payment_mode ?? '') }}</span>
          </td>
          <td class="td text-right font-semibold">₹{{ number_format($p->amount, 2) }}</td>
        </tr>
        @empty
        <tr><td class="td text-slate-400 text-center" colspan="8">No fee payments found for this period.</td></tr>
        @endforelse
      </tbody>
      @if($payments->isNotEmpty())
      <tfoot>
        <tr class="bg-slate-50">
          <td colspan="7" class="td text-right font-semibold text-slate-700">Page Total:</td>
          <td class="td text-right font-bold text-emerald-700">₹{{ number_format($payments->sum('amount'), 2) }}</td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>

  <div class="mt-4">{{ $payments->withQueryString()->links() }}</div>
</div>
@endsection
