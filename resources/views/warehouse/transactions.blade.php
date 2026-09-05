@extends('layouts.app')

@section('title', 'Warehouse Stock Transaction Log — ' . config('app.name'))

@section('content')
<div class="space-y-6">

  {{-- Top Navigation Header --}}
  <div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('warehouse.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-200">
        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Perpetual Stock Transaction Audit History</h1>
        <p class="text-xs text-slate-500 font-medium">Complete, immutable audit ledger of all inventory stock movements</p>
      </div>
    </div>

    <a href="{{ route('warehouse.reports', ['type' => 'stock_in', 'export' => 'csv']) }}" class="btn btn-secondary text-xs font-bold flex items-center gap-1.5">
      <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      <span>Export CSV Log</span>
    </a>
  </div>

  {{-- Filter Card --}}
  <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
    <form method="GET" action="{{ route('warehouse.transactions') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
      <div>
        <label class="block text-[11px] font-bold text-slate-500 mb-1">Search Code/Invoice/Notes</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code, invoice, notes..."
               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-500 mb-1">Filter Item</label>
        <select name="item_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-700">
          <option value="">All Inventory Items</option>
          @foreach($items as $i)
            <option value="{{ $i->id }}" {{ request('item_id') == $i->id ? 'selected' : '' }}>{{ $i->name }} ({{ $i->item_code }})</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-500 mb-1">Transaction Type</label>
        <select name="type" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-700">
          <option value="">All Transaction Types</option>
          <option value="stock_in" {{ request('type') === 'stock_in' ? 'selected' : '' }}>Stock In (Receiving)</option>
          <option value="admission_issue" {{ request('type') === 'admission_issue' ? 'selected' : '' }}>Admission Issue</option>
          <option value="manual_stock_out" {{ request('type') === 'manual_stock_out' ? 'selected' : '' }}>Manual Stock Out</option>
          <option value="adjustment_increase" {{ request('type') === 'adjustment_increase' ? 'selected' : '' }}>Adjustment Increase</option>
          <option value="adjustment_decrease" {{ request('type') === 'adjustment_decrease' ? 'selected' : '' }}>Adjustment Decrease</option>
          <option value="return_reversal" {{ request('type') === 'return_reversal' ? 'selected' : '' }}>Return / Reversal</option>
        </select>
      </div>

      <div class="flex items-center justify-end gap-2 self-end">
        <a href="{{ route('warehouse.transactions') }}" class="btn btn-secondary btn-xs text-xs font-semibold">Clear</a>
        <button type="submit" class="btn btn-primary btn-xs text-xs font-bold">Apply Filters</button>
      </div>
    </form>
  </div>

  {{-- Transaction Log Table --}}
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h2 class="text-base font-extrabold text-slate-900">Transaction History Log</h2>
      <span class="text-xs text-slate-500 font-mono">Showing {{ $transactions->firstItem() ?? 0 }}–{{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }}</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-xs text-left">
        <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-3 px-4">Txn Code / Date</th>
            <th class="py-3 px-4">Item Name / Code</th>
            <th class="py-3 px-4">Type</th>
            <th class="py-3 px-4 text-right">Prev</th>
            <th class="py-3 px-4 text-right">Qty Changed</th>
            <th class="py-3 px-4 text-right">New Stock</th>
            <th class="py-3 px-4 text-right">Unit Cost</th>
            <th class="py-3 px-4 text-right">Total Cost</th>
            <th class="py-3 px-4">Supplier / Reference / Notes</th>
            <th class="py-3 px-4">User</th>
            <th class="py-3 px-4 text-center">Invoice</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium">
          @forelse($transactions as $txn)
          <tr class="hover:bg-slate-50/70 transition">
            <td class="py-3 px-4">
              <span class="font-mono font-bold text-slate-900 block">{{ $txn->transaction_code }}</span>
              <span class="text-[10px] text-slate-400 font-mono">{{ $txn->created_at->format('d M Y, h:i A') }}</span>
            </td>

            <td class="py-3 px-4">
              <a href="{{ route('warehouse.show-item', $txn->item_id) }}" class="font-bold text-slate-900 hover:text-blue-600 block truncate max-w-[180px]">
                {{ $txn->item?->name ?? 'Unknown Item' }}
              </a>
              <span class="text-[10px] text-slate-400 font-mono">{{ $txn->item?->item_code }}</span>
            </td>

            <td class="py-3 px-4">
              @if($txn->transaction_type === 'stock_in')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Stock In</span>
              @elseif($txn->transaction_type === 'admission_issue')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800">Admission Issue</span>
              @elseif($txn->transaction_type === 'manual_stock_out')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">Manual Stock Out</span>
              @else
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-800">{{ ucwords(str_replace('_', ' ', $txn->transaction_type)) }}</span>
              @endif
            </td>

            <td class="py-3 px-4 text-right font-mono text-slate-500">{{ number_format($txn->previous_stock) }}</td>

            <td class="py-3 px-4 text-right font-mono font-extrabold {{ $txn->quantity_changed > 0 ? 'text-emerald-600' : 'text-amber-600' }}">
              {{ $txn->quantity_changed > 0 ? '+' : '' }}{{ number_format($txn->quantity_changed) }}
            </td>

            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">{{ number_format($txn->new_stock) }}</td>

            <td class="py-3 px-4 text-right font-mono text-slate-700">₹{{ number_format($txn->unit_cost, 2) }}</td>

            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">₹{{ number_format($txn->total_cost, 2) }}</td>

            <td class="py-3 px-4 text-slate-700 max-w-xs truncate">
              @if($txn->supplier_name)
                <span class="font-bold block text-slate-900 truncate">Supplier: {{ $txn->supplier_name }}</span>
              @endif
              @if($txn->invoice_number)
                <span class="text-[10px] font-mono text-blue-600 block">Inv: {{ $txn->invoice_number }}</span>
              @endif
              <span class="text-[11px] text-slate-500 block truncate">{{ $txn->notes ?: 'N/A' }}</span>
            </td>

            <td class="py-3 px-4 font-semibold text-slate-700">
              {{ $txn->performedBy?->name ?? 'System' }}
            </td>

            <td class="py-3 px-4 text-center">
              @if($txn->invoice_path)
                <a href="{{ route('warehouse.download-invoice', $txn->id) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  <span>Doc</span>
                </a>
              @else
                <span class="text-slate-400 text-[10px]">—</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="11" class="py-12 text-center text-slate-400 font-medium">No stock transactions found matching filter criteria.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($transactions->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
      {{ $transactions->links() }}
    </div>
    @endif
  </div>

</div>
@endsection
