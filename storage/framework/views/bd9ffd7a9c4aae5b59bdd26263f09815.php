<?php $__env->startSection('title', 'Issue Stock'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-3xl space-y-6" x-data="issuanceForm()">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Issue Stock</h1>
    <a href="<?php echo e(route('inventory.issuances')); ?>" class="btn-sm btn-secondary">← Back</a>
  </div>

  <?php if(session('error')): ?> <div class="alert-danger"><?php echo e(session('error')); ?></div> <?php endif; ?>

  <form method="POST" action="<?php echo e(route('inventory.issuances.store')); ?>" class="card space-y-5">
    <?php echo csrf_field(); ?>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label">Issue Number</label>
        <input type="text" name="issue_number" value="<?php echo e($issueNumber); ?>" class="input" readonly>
      </div>
      <div>
        <label class="label">Issue Date <span class="text-red-500">*</span></label>
        <input type="date" name="issue_date" value="<?php echo e(today()->toDateString()); ?>" class="input" required>
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
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($item->id); ?>" data-stock="<?php echo e($item->current_stock); ?>" data-unit="<?php echo e($item->unit); ?>">
                  <?php echo e($item->name); ?> (<?php echo e($item->current_stock); ?> <?php echo e($item->unit); ?> available)
                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
      <a href="<?php echo e(route('inventory.issuances')); ?>" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const stockData = <?php echo json_encode($items->pluck('current_stock', 'id'), 512) ?>;
const unitData  = <?php echo json_encode($items->pluck('unit', 'id'), 512) ?>;
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\issuance-form.blade.php ENDPATH**/ ?>