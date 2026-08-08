<?php $__env->startSection('title', 'New GRN'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-3xl space-y-6" x-data="grnForm()">
  <div class="flex items-center justify-between">
    <h1 class="page-title">New Goods Receipt Note</h1>
    <a href="<?php echo e(route('inventory.grn')); ?>" class="btn-sm btn-secondary">← Back</a>
  </div>

  <form method="POST" action="<?php echo e(route('inventory.grn.store')); ?>" class="card space-y-5">
    <?php echo csrf_field(); ?>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label">GRN Number</label>
        <input type="text" name="grn_number" value="<?php echo e($grnNumber); ?>" class="input" readonly>
      </div>
      <div>
        <label class="label">Purchase Order <span class="text-red-500">*</span></label>
        <select name="po_id" class="select" required x-model="selectedPo" @change="loadPoItems">
          <option value="">Select PO</option>
          <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($po->id); ?>" data-items="<?php echo e(json_encode($po->items)); ?>">
            <?php echo e($po->po_number); ?> — <?php echo e($po->vendor?->name); ?>

          </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Received Date <span class="text-red-500">*</span></label>
        <input type="date" name="received_date" value="<?php echo e(today()->toDateString()); ?>" class="input" required>
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
      <a href="<?php echo e(route('inventory.grn')); ?>" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function grnForm() {
  return {
    selectedPo: '<?php echo e(request("po_id") ?? ""); ?>',
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\grn-form.blade.php ENDPATH**/ ?>