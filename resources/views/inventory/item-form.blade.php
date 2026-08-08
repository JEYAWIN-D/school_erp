@extends('layouts.app')
@section('title', isset($item) ? 'Edit Item' : 'Add Item')
@section('content')
<div class="max-w-2xl space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">{{ isset($item) ? 'Edit Item' : 'Add Inventory Item' }}</h1>
    <a href="{{ route('inventory.index') }}" class="btn-sm btn-secondary">← Back</a>
  </div>

  <form method="POST" action="{{ isset($item) ? route('inventory.items.update', $item->id) : route('inventory.items.store') }}" class="card space-y-4">
    @csrf
    @if(isset($item)) @method('PUT') @endif

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label">Item Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $item->name ?? '') }}" class="input" required>
      </div>
      <div>
        <label class="label">Item Code <span class="text-red-500">*</span></label>
        <input type="text" name="item_code" value="{{ old('item_code', $item->item_code ?? '') }}" class="input" required>
      </div>
      <div>
        <label class="label">Category <span class="text-red-500">*</span></label>
        <select name="category_id" class="select" required>
          <option value="">Select category</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id', $item->category_id ?? '')==$cat->id)>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Unit <span class="text-red-500">*</span></label>
        <input type="text" name="unit" value="{{ old('unit', $item->unit ?? '') }}" class="input" placeholder="pcs / kg / litre…" required>
      </div>
      <div>
        <label class="label">Unit Price (₹)</label>
        <input type="number" name="unit_price" value="{{ old('unit_price', $item->unit_price ?? 0) }}" class="input" step="0.01" min="0">
      </div>
      <div>
        <label class="label">Reorder Level</label>
        <input type="number" name="reorder_level" value="{{ old('reorder_level', $item->reorder_level ?? 0) }}" class="input" min="0">
      </div>
      @if(!isset($item))
      <div>
        <label class="label">Opening Stock</label>
        <input type="number" name="current_stock" value="{{ old('current_stock', 0) }}" class="input" min="0">
      </div>
      @endif
      <div>
        <label class="label">Storage Location</label>
        <input type="text" name="location" value="{{ old('location', $item->location ?? '') }}" class="input" placeholder="Room / Shelf">
      </div>
    </div>

    <div>
      <label class="label">Description</label>
      <textarea name="description" rows="2" class="input">{{ old('description', $item->description ?? '') }}</textarea>
    </div>

    @if(isset($item))
    <label class="flex items-center gap-2 text-sm">
      <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true)) class="rounded">
      Active
    </label>
    @endif

    @error('*') <div class="alert-danger text-sm">{{ $message }}</div> @enderror

    <div class="flex gap-2 pt-2">
      <button type="submit" class="btn-primary">{{ isset($item) ? 'Update Item' : 'Add Item' }}</button>
      <a href="{{ route('inventory.index') }}" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
