@extends('layouts.app')
@section('title', 'Issue Stock')
@section('content')
<div class="max-w-3xl space-y-6" x-data="issuanceForm()">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Issue Stock</h1>
    <a href="{{ route('inventory.issuances') }}" class="btn-sm btn-secondary">← Back</a>
  </div>

  @if(session('error')) <div class="alert-danger">{{ session('error') }}</div> @endif

  <form method="POST" action="{{ route('inventory.issuances.store') }}" class="card space-y-5">
    @csrf
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label">Issue Number</label>
        <input type="text" name="issue_number" value="{{ $issueNumber }}" class="input" readonly>
      </div>
      <div>
        <label class="label">Issue Date <span class="text-red-500">*</span></label>
        <input type="date" name="issue_date" value="{{ today()->toDateString() }}" class="input" required>
      </div>
      <div>
        <label class="label">Issued To <span class="text-red-500">*</span></label>
        <input type="text" name="issued_to" class="input" placeholder="Department / Person name" required>
      </div>
      <div>
        <label class="label">Purpose</label>
        <input type="text" name="purpose" class="input" placeholder="Reason for issuance">
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-slate-700">Items to Issue</h3>
        <button type="button" @click="addRow" class="btn-xs btn-secondary">+ Add Item</button>
      </div>
      <div class="space-y-2">
        <template x-for="(row, idx) in rows" :key="idx">
          <div class="grid grid-cols-12 gap-2 items-end">
            <div class="col-span-5">
              <select :name="`items[${idx}][item_id]`" class="select" required x-model="row.item_id" @change="updateStock(idx)">
                <option value="">Select item</option>
                @foreach($items as $item)
                <option value="{{ $item->id }}" data-stock="{{ $item->current_stock }}" data-unit="{{ $item->unit }}">
                  {{ $item->name }} ({{ $item->current_stock }} {{ $item->unit }} available)
                </option>
                @endforeach
              </select>
              <p class="text-xs text-slate-400 mt-0.5" x-show="row.stock !== null">
                Available: <span x-text="row.stock"></span> <span x-text="row.unit"></span>
              </p>
            </div>
            <div class="col-span-3">
              <input type="number" :name="`items[${idx}][quantity]`" x-model.number="row.quantity" class="input" placeholder="Qty" min="1" :max="row.stock" required>
            </div>
            <div class="col-span-3">
              <input type="text" :name="`items[${idx}][remark]`" class="input" placeholder="Remark">
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
      <button type="submit" class="btn-primary">Issue Stock</button>
      <a href="{{ route('inventory.issuances') }}" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
const stockData = @json($items->pluck('current_stock','id'));
const unitData  = @json($items->pluck('unit','id'));
function issuanceForm() {
  return {
    rows: [{ item_id: '', quantity: 1, stock: null, unit: '' }],
    addRow() { this.rows.push({ item_id: '', quantity: 1, stock: null, unit: '' }); },
    updateStock(idx) {
      const id = this.rows[idx].item_id;
      this.rows[idx].stock = stockData[id] ?? null;
      this.rows[idx].unit  = unitData[id] ?? '';
    }
  };
}
</script>
@endpush
@endsection
