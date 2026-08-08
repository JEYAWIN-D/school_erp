@extends('layouts.app')
@section('title', 'Purchase Orders')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Purchase Orders</h1>
    <div class="flex gap-2">
      <a href="{{ route('inventory.grn.create') }}" class="btn-sm btn-secondary">Receive Stock (GRN)</a>
      <a href="{{ route('inventory.purchase-orders.create') }}" class="btn-primary btn-sm">+ Create PO</a>
    </div>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  <form method="GET" class="flex gap-3">
    <select name="status" class="select">
      <option value="">All Status</option>
      @foreach(['draft','sent','partial','received','cancelled'] as $st)
        <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
      @endforeach
    </select>
    <button class="btn-primary btn-sm">Filter</button>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">PO No.</th>
          <th class="th">Vendor</th>
          <th class="th">Order Date</th>
          <th class="th">Expected Delivery</th>
          <th class="th text-right">Amount</th>
          <th class="th">Status</th>
          <th class="th">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $po)
        <tr class="tr" x-data="{ open: false }">
          <td class="td font-mono font-semibold text-indigo-600">{{ $po->po_number }}</td>
          <td class="td font-medium">{{ $po->vendor?->name ?? '—' }}</td>
          <td class="td">{{ \Carbon\Carbon::parse($po->order_date)->format('d M Y') }}</td>
          <td class="td">{{ $po->expected_delivery ? \Carbon\Carbon::parse($po->expected_delivery)->format('d M Y') : '—' }}</td>
          <td class="td text-right font-semibold">₹{{ number_format($po->total_amount, 2) }}</td>
          <td class="td">
            @php $c=['draft'=>'badge-slate','sent'=>'badge-blue','partial'=>'badge-amber','received'=>'badge-green','cancelled'=>'badge-red']; @endphp
            <span class="{{ $c[$po->status] ?? 'badge-slate' }}">{{ ucfirst($po->status) }}</span>
          </td>
          <td class="td">
            <button @click="open=!open" class="btn-xs btn-secondary">Items</button>
            <a href="{{ route('inventory.purchase-orders.print', $po->id) }}" target="_blank" class="btn-xs btn-secondary ml-1">Print</a>
            @if(in_array($po->status,['draft','sent','partial']))
            <a href="{{ route('inventory.grn.create', ['po_id'=>$po->id]) }}" class="btn-xs btn-primary ml-1">Receive</a>
            @endif
          </td>
        </tr>
        <tr x-show="open" x-cloak class="bg-slate-50">
          <td colspan="7" class="px-6 py-3">
            <table class="w-full text-xs">
              <thead><tr class="text-slate-500"><th class="text-left py-1">Item</th><th class="text-right">Qty</th><th class="text-right">Unit Price</th><th class="text-right">Total</th><th class="text-right">Received</th></tr></thead>
              <tbody>
                @foreach($po->items as $pi)
                <tr>
                  <td class="py-1">{{ $pi->item?->name }}</td>
                  <td class="text-right">{{ $pi->quantity }}</td>
                  <td class="text-right">₹{{ number_format($pi->unit_price,2) }}</td>
                  <td class="text-right font-semibold">₹{{ number_format($pi->total_price,2) }}</td>
                  <td class="text-right {{ $pi->received_qty >= $pi->quantity ? 'text-emerald-600' : 'text-amber-600' }}">{{ $pi->received_qty }}/{{ $pi->quantity }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </td>
        </tr>
        @empty
        <tr><td class="td text-center text-slate-400" colspan="7">No purchase orders found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div>{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection
