@extends('layouts.app')
@section('title', 'New GRN')
@section('content')
<div class="max-w-3xl space-y-6" x-data="grnForm()">
  <div class="flex items-center justify-between">
    <h1 class="page-title">New Goods Receipt Note</h1>
    <a href="{{ route('inventory.grn') }}" class="btn-sm btn-secondary">← Back</a>
  </div>

  <form method="POST" action="{{ route('inventory.grn.store') }}" class="card space-y-5">
    @csrf
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label">GRN Number</label>
        <input type="text" name="grn_number" value="{{ $grnNumber }}" class="input" readonly>
      </div>
      <div>
        <label class="label">Purchase Order <span class="text-red-500">*</span></label>
        <select name="po_id" class="select" required x-model="selectedPo" @change="loadPoItems">
          <option value="">Select PO</option>
          @foreach($orders as $po)
          <option value="{{ $po->id }}" data-items="{{ json_encode($po->items) }}">
            {{ $po->po_number }} — {{ $po->vendor?->name }}
          </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Received Date <span class="text-red-500">*</span></label>
        <input type="date" name="received_date" value="{{ today()->toDateString() }}" class="input" required>
      </div>
      <div>
        <label class="label">Invoice Number</label>
        <input type="text" name="invoice_number" class="input">
      </div>
      <div>
        <label class="label">Invoice Date</label>
        <input type="date" name="invoice_date" class="input">
      </div>
      <div>
        <label class="label">Invoice Amount</label>
        <input type="number" name="invoice_amount" class="input" step="0.01">
      </div>
      <div class="col-span-2">
        <label class="label">Remarks</label>
        <textarea name="remarks" rows="2" class="input"></textarea>
      </div>
    </div>

    <div>
      <h3 class="font-semibold text-slate-700 mb-3">Items Received</h3>
      <p x-show="rows.length===0" class="text-sm text-slate-400 text-center py-4">Select a PO to load items.</p>
      <div class="space-y-2">
        <template x-for="(row, idx) in rows" :key="idx">
          <div class="grid grid-cols-12 gap-2 items-start border border-slate-100 rounded-xl p-3">
            <div class="col-span-4">
              <p class="text-sm font-medium text-slate-700" x-text="row.item_name"></p>
              <p class="text-xs text-slate-400">PO Qty: <span x-text="row.po_qty"></span> &bull; Pending: <span x-text="row.po_qty - row.received_qty_already"></span></p>
              <input type="hidden" :name="`items[${idx}][item_id]`" :value="row.item_id">
              <input type="hidden" :name="`items[${idx}][po_item_id]`" :value="row.po_item_id">
            </div>
            <div class="col-span-2">
              <label class="label text-xs">Received</label>
              <input type="number" :name="`items[${idx}][received_qty]`" x-model.number="row.received_qty" class="input" min="0" required>
            </div>
            <div class="col-span-2">
              <label class="label text-xs">Accepted</label>
              <input type="number" :name="`items[${idx}][accepted_qty]`" x-model.number="row.accepted_qty" class="input" min="0" required>
            </div>
            <div class="col-span-2">
              <label class="label text-xs">Rejected</label>
              <input type="number" :name="`items[${idx}][rejected_qty]`" x-model.number="row.rejected_qty" class="input" min="0" required>
            </div>
            <div class="col-span-2">
              <label class="label text-xs">Reason</label>
              <input type="text" :name="`items[${idx}][rejection_reason]`" class="input" placeholder="If rejected">
            </div>
          </div>
        </template>
      </div>
    </div>

    <div class="flex gap-2">
      <button type="submit" class="btn-primary" :disabled="rows.length === 0">Save GRN &amp; Update Stock</button>
      <a href="{{ route('inventory.grn') }}" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
function grnForm() {
  return {
    selectedPo: '{{ request("po_id") ?? "" }}',
    rows: [],
    loadPoItems() {
      const sel = document.querySelector('select[name="po_id"]');
      const opt = sel.options[sel.selectedIndex];
      const items = opt ? JSON.parse(opt.dataset.items || '[]') : [];
      this.rows = items.map(i => ({
        po_item_id: i.id,
        item_id: i.item_id,
        item_name: i.item ? i.item.name : 'Item #'+i.item_id,
        po_qty: i.quantity,
        received_qty_already: i.received_qty,
        received_qty: i.quantity - i.received_qty,
        accepted_qty: i.quantity - i.received_qty,
        rejected_qty: 0,
      }));
    },
    init() { if (this.selectedPo) this.$nextTick(() => this.loadPoItems()); }
  };
}
</script>
@endpush
@endsection
