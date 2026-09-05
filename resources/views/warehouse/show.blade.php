@extends('layouts.app')

@section('title', $item->name . ' — Warehouse Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

  {{-- Top Navigation Header --}}
  <div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('warehouse.index', ['tab' => $item->inventory_type === 'general_operations' ? 'general' : 'academic']) }}"
         class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-200">
        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">{{ $item->name }}</h1>
          <span class="badge-blue text-xs font-mono font-bold">{{ $item->item_code }}</span>
        </div>
        <p class="text-xs text-slate-500 font-medium mt-0.5">
          Category: {{ $item->category?->name ?? 'General' }} &bull; Type: {{ ucwords(str_replace('_', ' ', $item->inventory_type)) }}
        </p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <span class="px-3 py-1 rounded-full text-xs font-extrabold
        {{ $item->isOutOfStock() ? 'bg-rose-100 text-rose-700 border border-rose-200' : ($item->isLowStock() ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200') }}">
        {{ $item->isOutOfStock() ? 'Out of Stock' : ($item->isLowStock() ? 'Low Stock Warning' : 'In Stock') }}
      </span>
    </div>
  </div>

  {{-- Item Properties & Stock Summary Grid --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
    {{-- Card 1: Main Item Attributes --}}
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-2xs space-y-3 md:col-span-2">
      <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-2.5">Item Master Specifications</h2>

      <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
        <div>
          <span class="text-slate-400 font-medium block">Current Available Stock</span>
          <span class="text-2xl font-black font-mono {{ $item->isOutOfStock() ? 'text-rose-600' : 'text-slate-900' }}">{{ number_format($item->current_stock) }} {{ $item->unit }}</span>
        </div>

        <div>
          <span class="text-slate-400 font-medium block">Minimum Stock Level</span>
          <span class="text-lg font-bold font-mono text-slate-700">{{ $item->reorder_level }} {{ $item->unit }}</span>
        </div>

        <div>
          <span class="text-slate-400 font-medium block">Unit Standard</span>
          <span class="text-lg font-bold text-slate-800">{{ $item->unit }}</span>
        </div>

        <div>
          <span class="text-slate-400 font-medium block">Purchase Unit Cost</span>
          <span class="text-base font-bold font-mono text-slate-800">₹{{ number_format($item->effective_purchase_cost, 2) }}</span>
        </div>

        <div>
          <span class="text-slate-400 font-medium block">Student Additional Charge</span>
          <span class="text-base font-extrabold font-mono text-blue-700">₹{{ number_format($item->effective_student_price, 2) }}</span>
        </div>

        <div>
          <span class="text-slate-400 font-medium block">Storage Location</span>
          <span class="text-xs font-semibold text-slate-700">{{ $item->location ?: 'Main Warehouse Store' }}</span>
        </div>

        <div>
          <span class="text-slate-400 font-medium block">Vendor / Supplier</span>
          <span class="text-xs font-semibold text-slate-800">{{ $item->supplier_name ?: 'Standard Supplier' }}</span>
        </div>
      </div>

      @if($item->description)
      <div class="pt-2 border-t border-slate-100 text-xs">
        <span class="text-slate-400 font-medium block">Description / Remarks</span>
        <p class="text-slate-700 font-medium mt-0.5">{{ $item->description }}</p>
      </div>
      @endif
    </div>

    {{-- Card 2: Stock Movement Lifetime Totals --}}
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-2xs space-y-3">
      <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-2.5">Stock Movement Summary</h2>

      <div class="space-y-2.5 text-xs font-medium">
        <div class="flex justify-between items-center text-slate-600">
          <span>Total Received (Stock In)</span>
          <span class="font-mono font-bold text-emerald-600">+{{ number_format($summary['stockInCount']) }}</span>
        </div>
        <div class="flex justify-between items-center text-slate-600">
          <span>Total Issued (Stock Out)</span>
          <span class="font-mono font-bold text-amber-600">-{{ number_format($summary['stockOutCount']) }}</span>
        </div>
        <div class="flex justify-between items-center text-slate-500 pl-3">
          <span>&bull; Student Admission Issues</span>
          <span class="font-mono font-bold text-slate-800">{{ number_format($summary['admissionIssues']) }}</span>
        </div>
        <div class="flex justify-between items-center text-slate-500 pl-3">
          <span>&bull; Staff / Manual Issues</span>
          <span class="font-mono font-bold text-slate-800">{{ number_format($summary['manualIssues']) }}</span>
        </div>
        <div class="flex justify-between items-center text-slate-600 pt-2 border-t border-slate-100">
          <span>Total Returns / Reversals</span>
          <span class="font-mono font-bold text-blue-600">+{{ number_format($summary['returnsCount']) }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Transaction History Audit Log Table --}}
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h2 class="text-base font-extrabold text-slate-900">Perpetual Stock Transaction Audit History</h2>
        <p class="text-xs text-slate-400 font-medium">Permanent record of all stock movements for {{ $item->name }}</p>
      </div>
      <span class="text-xs text-slate-500 font-mono font-bold">{{ $item->transactions->count() }} Transactions</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-xs text-left">
        <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-3 px-4">Txn Code / Date</th>
            <th class="py-3 px-4">Movement Type</th>
            <th class="py-3 px-4 text-right">Previous</th>
            <th class="py-3 px-4 text-right">Qty Changed</th>
            <th class="py-3 px-4 text-right">New Stock</th>
            <th class="py-3 px-4 text-right">Unit Cost</th>
            <th class="py-3 px-4 text-right">Total Cost</th>
            <th class="py-3 px-4">Supplier / Reference / Notes</th>
            <th class="py-3 px-4">Performed By</th>
            <th class="py-3 px-4 text-center">Invoice Document</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium">
          @forelse($item->transactions as $txn)
          <tr class="hover:bg-slate-50/70 transition">
            <td class="py-3 px-4">
              <span class="font-mono font-bold text-slate-900 block">{{ $txn->transaction_code }}</span>
              <span class="text-[10px] text-slate-400 font-mono">{{ $txn->created_at->format('d M Y, h:i A') }}</span>
            </td>

            <td class="py-3 px-4">
              @if($txn->transaction_type === 'stock_in')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Stock In</span>
              @elseif($txn->transaction_type === 'admission_issue')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800">Admission Issue</span>
              @elseif($txn->transaction_type === 'manual_stock_out')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">Manual Stock Out</span>
              @elseif(str_contains($txn->transaction_type, 'adjustment'))
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-800">Adjustment</span>
              @else
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-800">{{ ucwords(str_replace('_', ' ', $txn->transaction_type)) }}</span>
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
                <a href="{{ route('warehouse.download-invoice', $txn->id) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  <span>Invoice</span>
                </a>
              @else
                <span class="text-slate-400 text-[10px]">—</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="py-8 text-center text-slate-400 font-medium">No stock transactions recorded yet for this item.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
