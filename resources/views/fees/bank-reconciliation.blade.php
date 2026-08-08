@extends('layouts.app')
@section('title','Bank Reconciliation Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Bank Reconciliation Report</h1>
      <p class="page-subtitle">Track and reconcile cheque / bank transfer payments</p>
    </div>
    <a href="{{ route('fees.cheque-pending') }}" class="btn btn-secondary btn-sm">Pending Cheques</a>
  </div>

  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap items-end">
    <div>
      <label class="label">Month</label>
      <input type="month" name="month" value="{{ $month }}" class="input w-36">
    </div>
    <div>
      <label class="label">Status</label>
      <select name="status" class="select w-36">
        <option value="">All</option>
        <option value="pending" @selected($status==='pending')>Pending</option>
        <option value="cleared" @selected($status==='cleared')>Cleared</option>
        <option value="bounced" @selected($status==='bounced')>Bounced</option>
      </select>
    </div>
    <button type="submit" class="btn btn-secondary btn-sm">Apply</button>
  </div></form>

  {{-- Summary boxes --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-slate-700">{{ $summary['total'] }}</p>
      <p class="text-xs text-slate-400 mt-1">Total Instruments</p>
      <p class="text-sm font-semibold text-indigo-600 mt-0.5">₹{{ number_format($summary['total_amount'], 0) }}</p>
    </div>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-amber-600">{{ $summary['pending'] }}</p>
      <p class="text-xs text-slate-400 mt-1">Pending Clearance</p>
      <p class="text-sm font-semibold text-amber-500 mt-0.5">₹{{ number_format($summary['pending_amt'], 0) }}</p>
    </div>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-green-600">{{ $summary['cleared'] }}</p>
      <p class="text-xs text-slate-400 mt-1">Cleared</p>
      <p class="text-sm font-semibold text-green-500 mt-0.5">₹{{ number_format($summary['cleared_amt'], 0) }}</p>
    </div>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-red-600">{{ $summary['bounced'] }}</p>
      <p class="text-xs text-slate-400 mt-1">Bounced / Rejected</p>
      <p class="text-sm font-semibold text-red-500 mt-0.5">₹{{ number_format($summary['bounced_amt'], 0) }}</p>
    </div>
  </div>

  {{-- Reconciliation table --}}
  <div class="card overflow-x-auto">
    @if($payments->isEmpty())
      <p class="text-center py-12 text-slate-400">No bank instruments found for the selected period.</p>
    @else
    <table class="w-full text-sm">
      <thead><tr>
        <th class="th">Receipt #</th>
        <th class="th">Date</th>
        <th class="th">Student</th>
        <th class="th">Fee Head</th>
        <th class="th">Mode</th>
        <th class="th">Cheque / Ref #</th>
        <th class="th">Bank</th>
        <th class="th">Cheque Date</th>
        <th class="th text-right">Amount</th>
        <th class="th">Status</th>
        <th class="th">Actions</th>
      </tr></thead>
      <tbody>
        @foreach($payments as $p)
        <tr class="tr {{ $p->cheque_status==='bounced' ? 'bg-red-50' : '' }}">
          <td class="td font-mono text-xs">{{ $p->receipt_number }}</td>
          <td class="td text-xs">{{ $p->payment_date?->format('d M Y') }}</td>
          <td class="td">
            <p class="font-medium text-slate-800 text-xs">{{ $p->student?->full_name ?? '—' }}</p>
            <p class="text-slate-400 text-xs">{{ $p->student?->admission_number }}</p>
          </td>
          <td class="td text-xs text-slate-500">{{ $p->feeHead?->name ?? '—' }}</td>
          <td class="td"><span class="badge-slate text-xs uppercase">{{ $p->payment_mode }}</span></td>
          <td class="td font-mono text-xs">{{ $p->cheque_number ?? $p->transaction_id ?? '—' }}</td>
          <td class="td text-xs text-slate-500">{{ $p->cheque_bank ?? '—' }}@if($p->cheque_branch) / {{ $p->cheque_branch }}@endif</td>
          <td class="td text-xs text-slate-500">{{ $p->cheque_date?->format('d M Y') ?? '—' }}</td>
          <td class="td text-right font-semibold">₹{{ number_format($p->total_paid, 0) }}</td>
          <td class="td">
            @php $cs = $p->cheque_status ?? 'n/a'; @endphp
            <span class="badge-{{ $cs==='cleared'?'green':($cs==='bounced'?'red':($cs==='pending'?'amber':'slate')) }} text-xs capitalize">
              {{ $cs }}
            </span>
          </td>
          <td class="td">
            @if(($p->cheque_status ?? '') === 'pending')
            <div class="flex gap-1">
              <form method="POST" action="{{ route('fees.cheque.clear', $p->id) }}">
                @csrf
                <button class="btn btn-secondary btn-xs text-green-600">Clear</button>
              </form>
              <button x-data @click="$dispatch('open-modal','bounce-{{ $p->id }}')" class="btn btn-secondary btn-xs text-red-500">Bounce</button>
            </div>
            {{-- Bounce modal --}}
            <div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='bounce-{{ $p->id }}')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
              <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm">
                <h3 class="font-semibold text-slate-700 mb-3">Mark Cheque Bounced</h3>
                <form method="POST" action="{{ route('fees.cheque.bounce', $p->id) }}" class="space-y-3">
                  @csrf
                  <div><label class="label">Bounce Reason</label><input type="text" name="bounce_reason" class="input" required placeholder="Insufficient funds, etc."></div>
                  <div><label class="label">Bounce Charge (₹)</label><input type="number" name="bounce_charge" class="input" value="250" step="1" min="0"></div>
                  <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Confirm Bounce</button>
                    <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
                  </div>
                </form>
              </div>
            </div>
            @elseif(($p->cheque_status ?? '') === 'cleared')
              <span class="text-green-500 text-xs">Reconciled</span>
            @elseif(($p->cheque_status ?? '') === 'bounced')
              <span class="text-red-400 text-xs">Reversed</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="bg-slate-50">
          <td colspan="8" class="td font-semibold text-right">Total (this view)</td>
          <td class="td text-right font-bold text-indigo-700">₹{{ number_format($payments->where('cheque_status','!=','bounced')->sum('total_paid'), 0) }}</td>
          <td colspan="2" class="td"></td>
        </tr>
      </tfoot>
    </table>
    @endif
  </div>
</div>
@endsection
