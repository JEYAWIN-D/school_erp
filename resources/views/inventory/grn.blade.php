@extends('layouts.app')
@section('title', 'Goods Receipt Notes')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Goods Receipt Notes (GRN)</h1>
    <div class="flex gap-2">
      <a href="{{ route('inventory.purchase-orders') }}" class="btn-sm btn-secondary">← POs</a>
      <a href="{{ route('inventory.grn.create') }}" class="btn-primary btn-sm">+ New GRN</a>
    </div>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">GRN No.</th>
          <th class="th">PO No.</th>
          <th class="th">Vendor</th>
          <th class="th">Received Date</th>
          <th class="th">Invoice</th>
          <th class="th text-right">Invoice Amt</th>
          <th class="th">Received By</th>
        </tr>
      </thead>
      <tbody>
        @forelse($records as $grn)
        <tr class="tr" x-data="{ open: false }">
          <td class="td font-mono font-semibold text-indigo-600 cursor-pointer" @click="open=!open">{{ $grn->grn_number }}</td>
          <td class="td font-mono text-xs">{{ $grn->purchaseOrder?->po_number ?? '—' }}</td>
          <td class="td">{{ $grn->purchaseOrder?->vendor?->name ?? '—' }}</td>
          <td class="td">{{ \Carbon\Carbon::parse($grn->received_date)->format('d M Y') }}</td>
          <td class="td text-xs">{{ $grn->invoice_number ?? '—' }}</td>
          <td class="td text-right">{{ $grn->invoice_amount ? '₹'.number_format($grn->invoice_amount,2) : '—' }}</td>
          <td class="td text-xs">{{ $grn->receivedBy?->name ?? '—' }}</td>
        </tr>
        <tr x-show="open" x-cloak class="bg-slate-50">
          <td colspan="7" class="px-6 py-3">
            <table class="w-full text-xs">
              <thead><tr class="text-slate-500"><th class="text-left py-1">Item</th><th class="text-right">Received</th><th class="text-right">Accepted</th><th class="text-right">Rejected</th></tr></thead>
              <tbody>
                @foreach($grn->items as $gi)
                <tr>
                  <td class="py-1">{{ $gi->item?->name }}</td>
                  <td class="text-right">{{ $gi->received_qty }}</td>
                  <td class="text-right text-emerald-600 font-semibold">{{ $gi->accepted_qty }}</td>
                  <td class="text-right {{ $gi->rejected_qty > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">{{ $gi->rejected_qty }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </td>
        </tr>
        @empty
        <tr><td class="td text-center text-slate-400" colspan="7">No GRN records found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div>{{ $records->withQueryString()->links() }}</div>
</div>
@endsection
