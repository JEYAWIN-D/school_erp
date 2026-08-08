<?php $__env->startSection('title', 'New Purchase Requisition'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-3xl space-y-6" x-data="requisitionForm()">
  <div class="flex items-center justify-between">
    <h1 class="page-title">New Purchase Requisition</h1>
    <a href="<?php echo e(route('inventory.requisitions')); ?>" class="btn-sm btn-secondary">← Back</a>
  </div>

  <form method="POST" action="<?php echo e(route('inventory.requisitions.store')); ?>" class="card space-y-5">
    <?php echo csrf_field(); ?>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label">PR Number</label>
        <input type="text" name="pr_number" value="<?php echo e($prNumber); ?>" class="input" readonly>
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
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?> (<?php echo e($item->unit); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
      <a href="<?php echo e(route('inventory.requisitions')); ?>" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function requisitionForm() {
  return {
    rows: [{ item_id: '', quantity: 1, estimated_price: '' }],
    addRow() { this.rows.push({ item_id: '', quantity: 1, estimated_price: '' }); }
  };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\requisition-form.blade.php ENDPATH**/ ?>