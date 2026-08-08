@extends('layouts.app')
@section('title', 'Fee Report')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Fee Report</h1>
  <form method="GET" class="card-flat py-4"><div class="flex gap-3 flex-wrap">
    <select name="class_id" class="select w-36">
      <option value="">All Classes</option>
      @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <select name="fee_head_id" class="select w-44">
      <option value="">All Fee Heads</option>
      @foreach($feeHeads as $fh)<option value="{{ $fh->id }}" @selected(request('fee_head_id')==$fh->id)>{{ $fh->name }}</option>@endforeach
    </select>
    <input type="date" name="from_date" value="{{ request('from_date') }}" class="input w-36">
    <input type="date" name="to_date" value="{{ request('to_date') }}" class="input w-36">
    <button type="submit" class="btn btn-primary btn-sm">Generate</button>
  </div></form>
  <div class="grid grid-cols-2 gap-4">
    <div class="card text-center py-5"><p class="text-2xl font-bold text-green-600">₹{{ number_format($total ?? 0, 2) }}</p><p class="text-sm text-slate-500 mt-1">Total Collected</p></div>
    <div class="card text-center py-5"><p class="text-2xl font-bold text-slate-800">{{ $payments->total() }}</p><p class="text-sm text-slate-500 mt-1">Transactions</p></div>
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        @foreach(['Date','Receipt No.','Student','Class','Fee Head','Amount','Mode'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase tracking-wide font-medium">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($payments as $p)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-2 text-slate-500 text-xs">{{ \Carbon\Carbon::parse($p->payment_date)->format('d M Y') }}</td>
          <td class="px-4 py-2 font-mono text-xs text-slate-600">{{ $p->receipt_number }}</td>
          <td class="px-4 py-2 font-medium text-slate-800">{{ $p->student?->full_name }}</td>
          <td class="px-4 py-2 text-slate-500">{{ $p->student?->currentEnrollment?->class?->name }}</td>
          <td class="px-4 py-2 text-slate-600">{{ $p->feeHead?->name }}</td>
          <td class="px-4 py-2 font-semibold text-green-700">₹{{ number_format($p->amount_paid, 2) }}</td>
          <td class="px-4 py-2"><span class="badge-slate capitalize text-xs">{{ str_replace('_',' ',$p->payment_mode) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No records for selected filter.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($payments->hasPages())<div class="px-4 pb-3 text-sm">{{ $payments->links() }}</div>@endif
  </div>
</div>
@endsection
