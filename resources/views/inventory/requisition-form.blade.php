@extends('layouts.app')
@section('title', 'New Purchase Requisition')
@section('content')
<div class="max-w-3xl space-y-6" x-data="requisitionForm()">
  <div class="flex items-center justify-between">
    <h1 class="page-title">New Purchase Requisition</h1>
    <a href="{{ route('inventory.requisitions') }}" class="btn-sm btn-secondary">← Back</a>
  </div>

  <form method="POST" action="{{ route('inventory.requisitions.store') }}" class="card space-y-5">
    @csrf
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label">PR Number</label>
        <input type="text" name="pr_number" value="{{ $prNumber }}" class="input" readonly>
      </div>
      <div>
        <label class="label">Required By <span class="text-red-500">*</span></label>
        <input type="date" name="required_by" class="input" required>
      </div>
      <div class="col-span-2">
        <label class="label">Purpose</label>
        <textarea name="purpose" rows="2" class="input"></textarea>
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-slate-700">Items</h3>
        <button type="button" @click="addRow" class="btn-xs btn-secondary">+ Add Item</button>
      </div>
      <div class="space-y-2">
        <template x-for="(row, idx) in rows" :key="idx">
          <div class="grid grid-cols-12 gap-2 items-end">
            <div class="col-span-5">
              <select :name="`items[${idx}][item_id]`" class="select" required x-model="row.item_id">
                <option value="">Select item</option>
                @foreach($items as $item)
                <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-span-2">
              <input type="number" :name="`items[${idx}][quantity]`" x-model="row.quantity" class="input" placeholder="Qty" min="1" required>
            </div>
            <div class="col-span-3">
              <input type="number" :name="`items[${idx}][estimated_price]`" x-model="row.estimated_price" class="input" placeholder="Est. price" step="0.01">
            </div>
            <div class="col-span-1">
              <button type="button" @click="rows.splice(idx,1)" class="btn-xs btn-secondary text-rose-600 w-full">✕</button>
            </div>
          </div>
        </template>
      </div>
      <p x-show="rows.length===0" class="text-sm text-slate-400 text-center py-4">Click "+ Add Item" to add items.</p>
    </div>

    <div class="flex gap-2">
      <button type="submit" class="btn-primary">Submit Requisition</button>
      <a href="{{ route('inventory.requisitions') }}" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
function requisitionForm() {
  return {
    rows: [{ item_id: '', quantity: 1, estimated_price: '' }],
    addRow() { this.rows.push({ item_id: '', quantity: 1, estimated_price: '' }); }
  };
}
</script>
@endpush
@endsection
