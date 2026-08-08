@extends('layouts.app')
@section('title', 'Stock Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Stock Report</h1>
    <div class="flex gap-2">
      <a href="{{ route('inventory.index') }}" class="btn-sm btn-secondary">← Inventory</a>
      <button onclick="window.print()" class="btn-sm btn-primary">🖨 Print</button>
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">Category</label>
      <select name="category_id" class="select">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}" @selected(request('category_id')==$cat->id)>{{ $cat->name }}</option>
        @endforeach
      </select>
    </div>
    <label class="flex items-center gap-2 text-sm self-end pb-2">
      <input type="checkbox" name="low_stock" value="1" @checked(request('low_stock')) class="rounded"> Low stock only
    </label>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <div class="ml-auto text-right">
      <p class="text-xs text-slate-400">Total Stock Value</p>
      <p class="text-xl font-bold text-emerald-600">₹{{ number_format($totalValue, 2) }}</p>
    </div>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">Code</th>
          <th class="th">Item Name</th>
          <th class="th">Category</th>
          <th class="th">Unit</th>
          <th class="th text-right">Unit Price</th>
          <th class="th text-center">Stock</th>
          <th class="th text-center">Reorder</th>
          <th class="th text-right">Value</th>
          <th class="th">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $item)
        <tr class="tr {{ $item->isLowStock() ? 'bg-rose-50' : '' }}">
          <td class="td font-mono text-xs text-slate-500">{{ $item->item_code }}</td>
          <td class="td font-medium">
            {{ $item->name }}
            @if($item->isLowStock()) <span class="badge-red text-xs ml-1">Low</span> @endif
          </td>
          <td class="td text-xs">{{ $item->category?->name ?? '—' }}</td>
          <td class="td text-xs">{{ $item->unit }}</td>
          <td class="td text-right">₹{{ number_format($item->unit_price, 2) }}</td>
          <td class="td text-center font-semibold {{ $item->isLowStock() ? 'text-rose-600' : 'text-emerald-600' }}">{{ $item->current_stock }}</td>
          <td class="td text-center text-slate-400">{{ $item->reorder_level }}</td>
          <td class="td text-right font-semibold">₹{{ number_format($item->current_stock * $item->unit_price, 2) }}</td>
          <td class="td">
            @if($item->current_stock <= 0) <span class="badge-red">Out of Stock</span>
            @elseif($item->isLowStock()) <span class="badge-amber">Low Stock</span>
            @else <span class="badge-green">In Stock</span> @endif
          </td>
        </tr>
        @empty
        <tr><td class="td text-center text-slate-400" colspan="9">No items found.</td></tr>
        @endforelse
      </tbody>
      @if($items->isNotEmpty())
      <tfoot>
        <tr class="bg-slate-50 font-semibold">
          <td colspan="7" class="td text-right text-slate-700">Total Stock Value:</td>
          <td class="td text-right text-emerald-700">₹{{ number_format($totalValue, 2) }}</td>
          <td class="td"></td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
