<?php $__env->startSection('title', 'Create Purchase Order'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-4xl space-y-6" x-data="poForm()">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Create Purchase Order</h1>
    <a href="<?php echo e(route('inventory.purchase-orders')); ?>" class="btn-sm btn-secondary">← Back</a>
  </div>

  <form method="POST" action="<?php echo e(route('inventory.purchase-orders.store')); ?>" class="card space-y-5">
    <?php echo csrf_field(); ?>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label">PO Number</label>
        <input type="text" name="po_number" value="<?php echo e($poNumber); ?>" class="input" readonly>
      </div>
      <div>
        <label class="label">Vendor <span class="text-red-500">*</span></label>
        <select name="vendor_id" class="select" required>
          <option value="">Select vendor</option>
          <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($v->id); ?>"><?php echo e($v->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Order Date <span class="text-red-500">*</span></label>
        <input type="date" name="order_date" value="<?php echo e(today()->toDateString()); ?>" class="input" required>
      </div>
      <div>
        <label class="label">Expected Delivery</label>
        <input type="date" name="expected_delivery" class="input">
      </div>
      <div>
        <label class="label">Link Requisition</label>
        <select name="requisition_id" class="select">
          <option value="">None</option>
          <?php $__currentLoopData = $requisitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($pr->id); ?>"><?php echo e($pr->pr_number); ?> (<?php echo e(\Carbon\Carbon::parse($pr->required_by)->format('d M')); ?>)</option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-span-2">
        <label class="label">Terms &amp; Conditions</label>
        <textarea name="terms" rows="2" class="input"></textarea>
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-slate-700">Order Items</h3>
        <button type="button" @click="addRow" class="btn-xs btn-secondary">+ Add Item</button>
      </div>
      <div class="space-y-2">
        <template x-for="(row, idx) in rows" :key="idx">
          <div class="grid grid-cols-12 gap-2 items-end">
            <div class="col-span-5">
              <select :name="`items[${idx}][item_id]`" class="select" required x-model="row.item_id" @change="updatePrice(idx)">
                <option value="">Select item</option>
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($item->id); ?>" data-price="<?php echo e($item->unit_price); ?>"><?php echo e($item->name); ?> (<?php echo e($item->unit); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-span-2">
              <input type="number" :name="`items[${idx}][quantity]`" x-model.number="row.quantity" class="input" placeholder="Qty" min="1" required @input="calcTotal">
            </div>
            <div class="col-span-3">
              <input type="number" :name="`items[${idx}][unit_price]`" x-model.number="row.unit_price" class="input" placeholder="Unit Price" step="0.01" required @input="calcTotal">
            </div>
            <div class="col-span-1 text-xs text-right text-slate-600 font-semibold" x-text="'₹'+(row.quantity*row.unit_price).toFixed(2)"></div>
            <div class="col-span-1">
              <button type="button" @click="rows.splice(idx,1);calcTotal()" class="btn-xs btn-secondary text-rose-600 w-full">✕</button>
            </div>
          </div>
        </template>
      </div>
      <div class="text-right mt-3 font-semibold text-slate-700">
        Total: ₹<span x-text="total.toFixed(2)">0.00</span>
      </div>
    </div>

    <div class="flex gap-2">
      <button type="submit" class="btn-primary">Create Purchase Order</button>
      <a href="<?php echo e(route('inventory.purchase-orders')); ?>" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const itemPrices = <?php echo json_encode($items->pluck('unit_price', 'id'), 512) ?>;
function poForm() {
  return {
    rows: [{ item_id: '', quantity: 1, unit_price: 0 }],
    total: 0,
    addRow() { this.rows.push({ item_id: '', quantity: 1, unit_price: 0 }); },
    updatePrice(idx) {
      const id = this.rows[idx].item_id;
      this.rows[idx].unit_price = itemPrices[id] || 0;
      this.calcTotal();
    },
    calcTotal() {
      this.total = this.rows.reduce((s,r) => s + (r.quantity * r.unit_price), 0);
    }
  };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\po-form.blade.php ENDPATH**/ ?>