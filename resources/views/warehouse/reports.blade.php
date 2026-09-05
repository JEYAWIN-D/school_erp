@extends('layouts.app')

@section('title', 'Warehouse Inventory Reports & Exports — ' . config('app.name'))

@section('content')
<div class="space-y-6">

  {{-- Top Navigation Header --}}
  <div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('warehouse.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-200">
        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Inventory Reports & Analytics</h1>
        <p class="text-xs text-slate-500 font-medium">Export inventory valuation, stock movement history, and low stock warnings</p>
      </div>
    </div>
  </div>

  {{-- Report Type Filter Card --}}
  <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-2xs">
    <form method="GET" action="{{ route('warehouse.reports') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Select Report Type</label>
        <select name="type" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
          <option value="current_stock" {{ $reportType === 'current_stock' ? 'selected' : '' }}>Current Master Inventory Valuation</option>
          <option value="stock_in" {{ $reportType === 'stock_in' ? 'selected' : '' }}>Stock In (Received Stock Log)</option>
          <option value="stock_out" {{ $reportType === 'stock_out' ? 'selected' : '' }}>Stock Out (Manual Issuances Log)</option>
          <option value="admission_issues" {{ $reportType === 'admission_issues' ? 'selected' : '' }}>Admission Issued Kits Report</option>
          <option value="low_stock" {{ $reportType === 'low_stock' ? 'selected' : '' }}>Low Stock & Out of Stock Alert Report</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">From Date</label>
        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">To Date</label>
        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono">
      </div>

      <div class="flex items-center gap-2 self-end">
        <button type="submit" class="btn btn-primary text-xs font-bold w-full">Generate Report</button>
        <a href="{{ route('warehouse.reports', array_merge(request()->all(), ['export' => 'csv'])) }}"
           class="btn bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 whitespace-nowrap px-4">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <span>Export CSV</span>
        </a>
      </div>
    </form>
  </div>

  {{-- Report Content Table --}}
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h2 class="text-base font-extrabold text-slate-900">
        {{ ucwords(str_replace('_', ' ', $reportType)) }} Summary
      </h2>
      <span class="text-xs text-slate-500 font-mono">Records: {{ count($reportData) }}</span>
    </div>

    <div class="overflow-x-auto">
      @if($reportType === 'current_stock' || $reportType === 'low_stock')
        <table class="w-full text-xs text-left">
          <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[10px]">
            <tr>
              <th class="py-3 px-4">Item Code</th>
              <th class="py-3 px-4">Item Name</th>
              <th class="py-3 px-4">Type</th>
              <th class="py-3 px-4">Category</th>
              <th class="py-3 px-4 text-center">Unit</th>
              <th class="py-3 px-4 text-right">Current Stock</th>
              <th class="py-3 px-4 text-right">Min Stock</th>
              <th class="py-3 px-4 text-right">Unit Cost</th>
              <th class="py-3 px-4 text-right">Total Inventory Value</th>
              <th class="py-3 px-4 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse($reportData as $item)
            <tr class="hover:bg-slate-50/70 transition">
              <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $item->item_code }}</td>
              <td class="py-3 px-4 font-bold text-slate-900">{{ $item->name }}</td>
              <td class="py-3 px-4 text-slate-600 capitalize">{{ str_replace('_', ' ', $item->inventory_type) }}</td>
              <td class="py-3 px-4 text-slate-600">{{ $item->category?->name ?? 'N/A' }}</td>
              <td class="py-3 px-4 text-center font-semibold text-slate-600">{{ $item->unit }}</td>
              <td class="py-3 px-4 text-right font-mono font-black text-slate-900">{{ number_format($item->current_stock) }}</td>
              <td class="py-3 px-4 text-right font-mono text-slate-500">{{ $item->reorder_level }}</td>
              <td class="py-3 px-4 text-right font-mono">₹{{ number_format($item->effective_purchase_cost, 2) }}</td>
              <td class="py-3 px-4 text-right font-mono font-bold text-indigo-700">₹{{ number_format($item->current_stock * $item->effective_purchase_cost, 2) }}</td>
              <td class="py-3 px-4 text-center">
                @if($item->isOutOfStock())
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700">Out of Stock</span>
                @elseif($item->isLowStock())
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-700">Low Stock</span>
                @else
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700">In Stock</span>
                @endif
              </td>
            </tr>
            @empty
            <tr><td colspan="10" class="py-8 text-center text-slate-400">No records found for this report.</td></tr>
            @endforelse
          </tbody>
        </table>
      @elseif($reportType === 'admission_issues')
        <table class="w-full text-xs text-left">
          <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[10px]">
            <tr>
              <th class="py-3 px-4">Date</th>
              <th class="py-3 px-4">Student Admission No</th>
              <th class="py-3 px-4">Student Name</th>
              <th class="py-3 px-4">Item Name</th>
              <th class="py-3 px-4 text-center">Default Qty</th>
              <th class="py-3 px-4 text-center">Additional Qty</th>
              <th class="py-3 px-4 text-center">Total Qty</th>
              <th class="py-3 px-4 text-right">Unit Charge</th>
              <th class="py-3 px-4 text-right">Extra Charge Total</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse($reportData as $row)
            <tr class="hover:bg-slate-50/70 transition">
              <td class="py-3 px-4 font-mono text-slate-500">{{ $row->created_at->format('d M Y') }}</td>
              <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $row->student?->admission_no ?? 'N/A' }}</td>
              <td class="py-3 px-4 font-bold text-slate-900">{{ $row->student?->first_name }} {{ $row->student?->last_name }}</td>
              <td class="py-3 px-4 font-semibold text-slate-800">{{ $row->item?->name }}</td>
              <td class="py-3 px-4 text-center font-mono">{{ $row->default_quantity }}</td>
              <td class="py-3 px-4 text-center font-mono font-bold text-blue-600">+{{ $row->additional_quantity }}</td>
              <td class="py-3 px-4 text-center font-mono font-black text-slate-900">{{ $row->total_quantity }}</td>
              <td class="py-3 px-4 text-right font-mono">₹{{ number_format($row->unit_charge, 2) }}</td>
              <td class="py-3 px-4 text-right font-mono font-bold text-emerald-700">₹{{ number_format($row->additional_charge, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="9" class="py-8 text-center text-slate-400">No admission issues recorded in selected date range.</td></tr>
            @endforelse
          </tbody>
        </table>
      @else
        <table class="w-full text-xs text-left">
          <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[10px]">
            <tr>
              <th class="py-3 px-4">Txn Code</th>
              <th class="py-3 px-4">Date</th>
              <th class="py-3 px-4">Item Name</th>
              <th class="py-3 px-4 text-right">Quantity</th>
              <th class="py-3 px-4 text-right">Unit Cost</th>
              <th class="py-3 px-4 text-right">Total Cost</th>
              <th class="py-3 px-4">Supplier / Details</th>
              <th class="py-3 px-4">Performed By</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse($reportData as $row)
            <tr class="hover:bg-slate-50/70 transition">
              <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $row->transaction_code }}</td>
              <td class="py-3 px-4 font-mono text-slate-500">{{ $row->created_at->format('d M Y') }}</td>
              <td class="py-3 px-4 font-bold text-slate-900">{{ $row->item?->name }}</td>
              <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">{{ number_format($row->quantity) }}</td>
              <td class="py-3 px-4 text-right font-mono">₹{{ number_format($row->unit_cost, 2) }}</td>
              <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">₹{{ number_format($row->total_cost, 2) }}</td>
              <td class="py-3 px-4 text-slate-600">{{ $row->supplier_name ?: ($row->notes ?: 'N/A') }}</td>
              <td class="py-3 px-4 text-slate-700">{{ $row->performedBy?->name ?? 'System' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="py-8 text-center text-slate-400">No stock transactions found for selected filter.</td></tr>
            @endforelse
          </tbody>
        </table>
      @endif
    </div>
  </div>

</div>
@endsection
