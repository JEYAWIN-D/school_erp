@extends('layouts.app')

@section('title', 'Warehouse Management — ' . config('app.name'))

@section('content')
<div class="space-y-6" x-data="{
  activeTab: '{{ $activeTab }}',
  stockInModal: false,
  stockOutModal: false,
  adjustModal: false,
  addItemModal: false,
  newInvoiceName: '',

  selectedItem: null,
  stockInUnitCost: 0,
  stockInStudentPrice: 0,
  selectItem(item) {
    this.selectedItem = item;
    this.stockInUnitCost = item ? (item.purchase_cost ?? item.effective_purchase_cost ?? 0) : 0;
    this.stockInStudentPrice = item ? (item.student_price ?? item.effective_student_price ?? 0) : 0;
  },

  formatMoney(num) {
    return '₹' + Number(num || 0).toLocaleString('en-IN');
  }
}">

  {{-- ── Top Navigation / Title Header ────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Warehouse</h1>
        <span class="badge-blue text-xs font-bold">Inventory System</span>
      </div>
      <p class="text-xs text-slate-500 font-medium mt-0.5">Manage physical inventory, stock movements, supplier invoices, and student admission kits.</p>
    </div>

    <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
      <a href="{{ route('warehouse.admission-kit-config') }}" class="btn bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200 shadow-2xs flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <span>Admission Kit Config</span>
      </a>

      <a href="{{ route('warehouse.transactions') }}" class="btn btn-secondary text-xs font-bold flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        <span>Transaction History</span>
      </a>

      <a href="{{ route('warehouse.reports') }}" class="btn btn-secondary text-xs font-bold flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Reports</span>
      </a>

      <button @click="addItemModal = true" class="btn btn-primary text-xs font-bold flex items-center gap-1.5 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Add Item</span>
      </button>
    </div>
  </div>

  {{-- ── Primary Navigation Tabs ──────────────────────────────── --}}
  <div class="border-b border-slate-200 flex items-center gap-2 overflow-x-auto">
    <a href="{{ route('warehouse.index', ['tab' => 'academic']) }}"
       class="py-3 px-5 border-b-2 text-sm font-extrabold flex items-center gap-2 transition select-none cursor-pointer"
       :class="activeTab === 'academic' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
      <span>Academic Inventory</span>
      <span class="px-2 py-0.5 rounded-full text-xs font-mono font-bold" :class="activeTab === 'academic' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-600'">
        Default
      </span>
    </a>

    <a href="{{ route('warehouse.index', ['tab' => 'general']) }}"
       class="py-3 px-5 border-b-2 text-sm font-extrabold flex items-center gap-2 transition select-none cursor-pointer"
       :class="activeTab === 'general' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
      <span>General Operations Inventory</span>
    </a>
  </div>

  {{-- ── Dynamic Summary Cards Banner ──────────────────────────── --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Items</span>
      <p class="text-xl font-black text-slate-900 font-mono mt-1">{{ number_format($stats['totalItems']) }}</p>
      <span class="text-[10px] text-slate-500 font-medium">Active item master</span>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <span class="text-[11px] font-extrabold text-blue-600 uppercase tracking-wider">Total Stock Qty</span>
      <p class="text-xl font-black text-blue-700 font-mono mt-1">{{ number_format($stats['totalStockQty']) }}</p>
      <span class="text-[10px] text-slate-500 font-medium">Units available</span>
    </div>

    <a href="{{ route('warehouse.index', ['tab' => $activeTab, 'stock_status' => 'low']) }}"
       class="bg-white p-4 rounded-2xl border transition shadow-2xs block group cursor-pointer {{ request('stock_status') === 'low' ? 'ring-2 ring-amber-500 border-amber-300 bg-amber-50/20' : 'border-amber-200/90 hover:border-amber-400' }}"
       title="Click to view Low Stock items">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-extrabold text-amber-700 uppercase tracking-wider">Low Stock</span>
        @if(request('stock_status') === 'low')
          <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500 text-white">Active Filter</span>
        @endif
      </div>
      <p class="text-xl font-black text-amber-600 font-mono mt-1 group-hover:scale-105 transition-transform">{{ number_format($stats['lowStockCount']) }}</p>
      <span class="text-[10px] text-amber-700 font-medium">&le; Min stock level (Click to filter)</span>
    </a>

    <a href="{{ route('warehouse.index', ['tab' => $activeTab, 'stock_status' => 'out']) }}"
       class="bg-white p-4 rounded-2xl border transition shadow-2xs block group cursor-pointer {{ request('stock_status') === 'out' ? 'ring-2 ring-rose-500 border-rose-300 bg-rose-50/20' : 'border-rose-200/90 hover:border-rose-400' }}"
       title="Click to view Out of Stock items">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-extrabold text-rose-600 uppercase tracking-wider">Out of Stock</span>
        @if(request('stock_status') === 'out')
          <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-600 text-white">Active Filter</span>
        @endif
      </div>
      <p class="text-xl font-black text-rose-600 font-mono mt-1 group-hover:scale-105 transition-transform">{{ number_format($stats['outOfStockCount']) }}</p>
      <span class="text-[10px] text-rose-700 font-medium">Zero stock items (Click to filter)</span>
    </a>

    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <span class="text-[11px] font-extrabold text-indigo-600 uppercase tracking-wider">Academic Value</span>
      <p class="text-lg font-black text-indigo-700 font-mono mt-1">₹{{ number_format($stats['academicValue']) }}</p>
      <span class="text-[10px] text-slate-500 font-medium">At purchase cost</span>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <span class="text-[11px] font-extrabold text-emerald-600 uppercase tracking-wider">General Ops Value</span>
      <p class="text-lg font-black text-emerald-700 font-mono mt-1">₹{{ number_format($stats['generalOpsValue']) }}</p>
      <span class="text-[10px] text-slate-500 font-medium">At purchase cost</span>
    </div>
  </div>

  {{-- ── Search & Filter Controls Card ────────────────────────── --}}
  <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
    <form method="GET" action="{{ route('warehouse.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
      <input type="hidden" name="tab" value="{{ $activeTab }}">

      <div class="sm:col-span-2">
        <label class="block text-[11px] font-bold text-slate-500 mb-1">Search Items</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name, SKU/item code, description..."
               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-500 mb-1">Category</label>
        <select name="category_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-700">
          <option value="">All Categories</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-500 mb-1">Stock Status</label>
        <select name="stock_status" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-700">
          <option value="">All Stock Status</option>
          <option value="in" {{ request('stock_status') === 'in' ? 'selected' : '' }}>In Stock (&gt;0)</option>
          <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Low Stock</option>
          <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Out of Stock (0)</option>
        </select>
      </div>

      <div class="sm:col-span-4 flex items-center justify-end gap-2 pt-1">
        <a href="{{ route('warehouse.index', ['tab' => $activeTab]) }}" class="btn btn-secondary btn-xs text-xs font-semibold">Clear Filters</a>
        <button type="submit" class="btn btn-primary btn-xs text-xs font-bold">Apply Filters</button>
      </div>
    </form>
  </div>

  {{-- ── Main Inventory Data Table ──────────────────────────────── --}}
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h2 class="text-base font-extrabold text-slate-900">
        {{ $activeTab === 'general' ? 'General Operations Inventory Items' : 'Academic Inventory Items' }}
      </h2>
      <span class="text-xs text-slate-500 font-medium font-mono">Showing {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} of {{ $items->total() }}</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-xs text-left">
        <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-3.5 px-4">Item Code / Name</th>
            <th class="py-3.5 px-4">Category</th>
            <th class="py-3.5 px-4 text-center">Unit</th>
            <th class="py-3.5 px-4 text-right">Current Stock</th>
            <th class="py-3.5 px-4 text-right">Min Level</th>
            <th class="py-3.5 px-4 text-right">Purchase Cost</th>
            <th class="py-3.5 px-4 text-right">Student Charge</th>
            <th class="py-3.5 px-4 text-center whitespace-nowrap">Status</th>
            <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[280px]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium">
          @forelse($items as $item)
          <tr class="hover:bg-slate-50/70 transition">
            <td class="py-3.5 px-4">
              <a href="{{ route('warehouse.show-item', $item->id) }}" class="font-extrabold text-slate-900 hover:text-blue-600 text-sm block">
                {{ $item->name }}
              </a>
              <span class="text-[11px] text-slate-400 font-mono">SKU: {{ $item->item_code }}</span>
            </td>

            <td class="py-3.5 px-4 whitespace-nowrap">
              <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold text-[11px] inline-block whitespace-nowrap">
                {{ $item->category?->name ?? 'General' }}
              </span>
            </td>

            <td class="py-3.5 px-4 text-center text-slate-600 font-semibold whitespace-nowrap">
              {{ $item->unit }}
            </td>

            <td class="py-3.5 px-4 text-right font-mono whitespace-nowrap">
              <span class="text-base font-black {{ $item->isOutOfStock() ? 'text-rose-600' : ($item->isLowStock() ? 'text-amber-600' : 'text-slate-900') }}">
                {{ number_format($item->current_stock) }}
              </span>
            </td>

            <td class="py-3.5 px-4 text-right font-mono text-slate-500 whitespace-nowrap">
              {{ $item->reorder_level }}
            </td>

            <td class="py-3.5 px-4 text-right font-mono font-semibold text-slate-700 whitespace-nowrap">
              ₹{{ number_format($item->effective_purchase_cost, 2) }}
            </td>

            <td class="py-3.5 px-4 text-right font-mono font-bold text-blue-700 whitespace-nowrap">
              ₹{{ number_format($item->effective_student_price, 2) }}
            </td>

            <td class="py-3.5 px-4 text-center whitespace-nowrap align-middle">
              @if($item->isOutOfStock())
                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700 border border-rose-200 whitespace-nowrap leading-none">Out of Stock</span>
              @elseif($item->isLowStock())
                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-700 border border-amber-200 whitespace-nowrap leading-none">Low Stock</span>
              @else
                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200 whitespace-nowrap leading-none">In Stock</span>
              @endif
            </td>

            <td class="py-3.5 px-4 text-center whitespace-nowrap align-middle">
              <div class="flex items-center justify-center gap-1.5 whitespace-nowrap">
                {{-- View Item --}}
                <a href="{{ route('warehouse.show-item', $item->id) }}" class="btn btn-secondary btn-2xs text-[11px] font-bold whitespace-nowrap inline-flex items-center justify-center h-7 px-2.5" title="View Item Details & History">
                  View
                </a>

                {{-- Stock In Button --}}
                <button @click="selectItem({{ json_encode($item) }}); stockInModal = true" class="h-7 px-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-bold transition cursor-pointer whitespace-nowrap inline-flex items-center justify-center gap-1" title="Receive Stock (Stock In)">
                  + Stock In
                </button>

                {{-- Stock Out Button --}}
                <button @click="selectItem({{ json_encode($item) }}); stockOutModal = true" class="h-7 px-2.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-[11px] font-bold transition cursor-pointer whitespace-nowrap inline-flex items-center justify-center gap-1" title="Issue Stock (Stock Out)">
                  - Issue
                </button>

                {{-- Adjust Button --}}
                <button @click="selectItem({{ json_encode($item) }}); adjustModal = true" class="h-7 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition cursor-pointer whitespace-nowrap inline-flex items-center justify-center" title="Adjust Physical Stock">
                  Adjust
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="py-12 text-center text-slate-400">
              <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
              <p class="text-sm font-bold text-slate-600">No inventory items found matching filter criteria.</p>
              <p class="text-xs text-slate-400 mt-1">Try clearing filters or add new inventory items above.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($items->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
      {{ $items->links() }}
    </div>
    @endif
  </div>

  {{-- ── 1. MODAL: Stock In / Update Stock ──────────────────────── --}}
  <div x-show="stockInModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak style="display:none">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-5 border border-slate-100" @click.outside="stockInModal = false">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-base font-extrabold text-slate-900">Stock In / Receive Stock</h3>
          <p class="text-xs text-slate-500 font-medium" x-text="'Item: ' + (selectedItem?.name || '') + ' (' + (selectedItem?.item_code || '') + ')' + (selectedItem?.supplier_name ? ' • Vendor: ' + selectedItem.supplier_name : '')"></p>
        </div>
        <button @click="stockInModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
      </div>

      <form method="POST" action="{{ route('warehouse.stock-in') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <input type="hidden" name="item_id" :value="selectedItem?.id">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Quantity Received <span class="text-rose-500">*</span></label>
            <input type="number" name="quantity_received" required min="1" placeholder="e.g. 500"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Unit Cost (₹) <span class="text-rose-500">*</span></label>
            <input type="number" step="0.01" min="0" name="unit_cost" required x-model="stockInUnitCost"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Student Charge (₹)</label>
            <input type="number" step="0.01" min="0" name="student_price" x-model="stockInStudentPrice"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Invoice Number</label>
            <input type="text" name="invoice_number" placeholder="INV-2026-001"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Invoice Date</label>
            <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Upload Invoice (PDF/Image)</label>
          <input type="file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png"
                 class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Notes / Remarks</label>
          <textarea name="notes" rows="2" placeholder="Delivery notes, PO reference, batch details..."
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium"></textarea>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button type="button" @click="stockInModal = false" class="btn btn-secondary text-xs font-bold">Cancel</button>
          <button type="submit" class="btn btn-primary text-xs font-bold">Add Stock</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ── 2. MODAL: Stock Out / Manual Issue ────────────────────── --}}
  <div x-show="stockOutModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak style="display:none">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 border border-slate-100" @click.outside="stockOutModal = false">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-base font-extrabold text-slate-900">Issue Stock / Stock Out</h3>
          <p class="text-xs text-slate-500 font-medium" x-text="'Item: ' + (selectedItem?.name || '')"></p>
        </div>
        <button @click="stockOutModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
      </div>

      <form method="POST" action="{{ route('warehouse.stock-out') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="item_id" :value="selectedItem?.id">

        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs font-medium text-amber-800">
          Available Current Stock: <span class="font-extrabold font-mono text-sm" x-text="(selectedItem?.current_stock || 0) + ' ' + (selectedItem?.unit || '')"></span>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Quantity to Issue <span class="text-rose-500">*</span></label>
          <input type="number" name="quantity_issue" required min="1" :max="selectedItem?.current_stock || 1" placeholder="e.g. 10"
                 class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Issued To (Person / Department)</label>
          <input type="text" name="issued_to" placeholder="e.g. Class 5 Teacher / Cleaning Staff"
                 class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Purpose / Reason</label>
          <input type="text" name="purpose" placeholder="e.g. Daily classroom cleaning / Lab usage"
                 class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Additional Notes</label>
          <textarea name="notes" rows="2" placeholder="Additional notes..."
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium"></textarea>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button type="button" @click="stockOutModal = false" class="btn btn-secondary text-xs font-bold">Cancel</button>
          <button type="submit" class="btn bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold">Issue Stock</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ── 3. MODAL: Manual Stock Count Adjustment ────────────────── --}}
  <div x-show="adjustModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak style="display:none">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 border border-slate-100" @click.outside="adjustModal = false">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-base font-extrabold text-slate-900">Stock Count Adjustment</h3>
          <p class="text-xs text-slate-500 font-medium" x-text="'Item: ' + (selectedItem?.name || '')"></p>
        </div>
        <button @click="adjustModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
      </div>

      <form method="POST" action="{{ route('warehouse.adjustment') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="item_id" :value="selectedItem?.id">

        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs font-medium text-slate-700">
          Current System Stock: <span class="font-extrabold font-mono text-sm" x-text="(selectedItem?.current_stock || 0) + ' ' + (selectedItem?.unit || '')"></span>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Actual Physical Count <span class="text-rose-500">*</span></label>
          <input type="number" name="actual_qty" required min="0" :value="selectedItem?.current_stock"
                 class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Adjustment Reason <span class="text-rose-500">*</span></label>
          <select name="reason" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium">
            <option value="Physical stock verification count">Physical stock verification count</option>
            <option value="Damaged / Expired stock removal">Damaged / Expired stock removal</option>
            <option value="Lost / Unaccounted stock audit">Lost / Unaccounted stock audit</option>
            <option value="Inventory opening audit reconciliation">Inventory opening audit reconciliation</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Notes</label>
          <textarea name="notes" rows="2" placeholder="Explain discrepancy rationale..."
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium"></textarea>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button type="button" @click="adjustModal = false" class="btn btn-secondary text-xs font-bold">Cancel</button>
          <button type="submit" class="btn btn-primary text-xs font-bold">Save Adjustment</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ── 4. MODAL: Add New Inventory Item ──────────────────────── --}}
  <div x-show="addItemModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak style="display:none">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-5 border border-slate-100" @click.outside="addItemModal = false">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-base font-extrabold text-slate-900">Add New Inventory Item</h3>
          <p class="text-xs text-slate-500 font-medium">Create item master in Warehouse inventory</p>
        </div>
        <button @click="addItemModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
      </div>

      <form method="POST" action="{{ route('warehouse.items.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Inventory Type <span class="text-rose-500">*</span></label>
            <select name="inventory_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium">
              <option value="academic" {{ $activeTab === 'academic' ? 'selected' : '' }}>Academic Inventory</option>
              <option value="general_operations" {{ $activeTab === 'general' ? 'selected' : '' }}>General Operations Inventory</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
            <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium">
              @foreach($categories as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Item Name <span class="text-rose-500">*</span></label>
            <input type="text" name="name" required placeholder="e.g. Class 5 Mathematics Book"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Item Code / SKU <span class="text-rose-500">*</span></label>
            <input type="text" name="item_code" required value="SKU-{{ date('Ymd') }}-{{ rand(10,99) }}"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold">
          </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Unit <span class="text-rose-500">*</span></label>
            <select name="unit" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium">
              <option value="Piece">Piece</option>
              <option value="Set">Set</option>
              <option value="Pair">Pair</option>
              <option value="Box">Box</option>
              <option value="Pack">Pack</option>
              <option value="Can">Can</option>
              <option value="Kg">Kg</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Purchase Cost (₹) <span class="text-rose-500">*</span></label>
            <input type="number" step="0.01" name="purchase_cost" required value="0"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Student Charge (₹)</label>
            <input type="number" step="0.01" name="student_price" value="0"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Opening Stock <span class="text-rose-500">*</span></label>
            <input type="number" name="current_stock" required value="0" min="0"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Min Stock Level <span class="text-rose-500">*</span></label>
            <input type="number" name="reorder_level" required value="10" min="0"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Supplier / Vendor Name</label>
            <input type="text" name="supplier_name" placeholder="e.g. Navneet Publications / ABC Uniforms"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Location / Rack</label>
            <input type="text" name="location" placeholder="e.g. Rack A1"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Invoice / Purchase Bill (PDF/Image)</label>
          <input type="file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png"
                 x-ref="newInvoiceInput"
                 @change="newInvoiceName = $refs.newInvoiceInput.files[0]?.name || ''"
                 class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700">
          <p x-show="newInvoiceName" x-text="'Selected file: ' + newInvoiceName" class="text-[11px] text-blue-600 font-medium mt-1 font-mono"></p>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
          <textarea name="description" rows="2" placeholder="Item details, brand, spec..."
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium"></textarea>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button type="button" @click="addItemModal = false" class="btn btn-secondary text-xs font-bold">Cancel</button>
          <button type="submit" class="btn btn-primary text-xs font-bold">Save New Item</button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
