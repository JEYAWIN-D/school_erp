<?php $__env->startSection('title', isset($item) ? 'Edit Item' : 'Add Item'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title"><?php echo e(isset($item) ? 'Edit Item' : 'Add Inventory Item'); ?></h1>
    <a href="<?php echo e(route('inventory.index')); ?>" class="btn-sm btn-secondary">← Back</a>
  </div>

  <form method="POST" action="<?php echo e(isset($item) ? route('inventory.items.update', $item->id) : route('inventory.items.store')); ?>" class="card space-y-4">
    <?php echo csrf_field(); ?>
    <?php if(isset($item)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label">Item Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="<?php echo e(old('name', $item->name ?? '')); ?>" class="input" required>
      </div>
      <div>
        <label class="label">Item Code <span class="text-red-500">*</span></label>
        <input type="text" name="item_code" value="<?php echo e(old('item_code', $item->item_code ?? '')); ?>" class="input" required>
      </div>
      <div>
        <label class="label">Category <span class="text-red-500">*</span></label>
        <select name="category_id" class="select" required>
          <option value="">Select category</option>
          <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($cat->id); ?>" <?php if(old('category_id', $item->category_id ?? '')==$cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Unit <span class="text-red-500">*</span></label>
        <input type="text" name="unit" value="<?php echo e(old('unit', $item->unit ?? '')); ?>" class="input" placeholder="pcs / kg / litre…" required>
      </div>
      <div>
        <label class="label">Unit Price (₹)</label>
        <input type="number" name="unit_price" value="<?php echo e(old('unit_price', $item->unit_price ?? 0)); ?>" class="input" step="0.01" min="0">
      </div>
      <div>
        <label class="label">Reorder Level</label>
        <input type="number" name="reorder_level" value="<?php echo e(old('reorder_level', $item->reorder_level ?? 0)); ?>" class="input" min="0">
      </div>
      <?php if(!isset($item)): ?>
      <div>
        <label class="label">Opening Stock</label>
        <input type="number" name="current_stock" value="<?php echo e(old('current_stock', 0)); ?>" class="input" min="0">
      </div>
      <?php endif; ?>
      <div>
        <label class="label">Storage Location</label>
        <input type="text" name="location" value="<?php echo e(old('location', $item->location ?? '')); ?>" class="input" placeholder="Room / Shelf">
      </div>
    </div>

    <div>
      <label class="label">Description</label>
      <textarea name="description" rows="2" class="input"><?php echo e(old('description', $item->description ?? '')); ?></textarea>
    </div>

    <?php if(isset($item)): ?>
    <label class="flex items-center gap-2 text-sm">
      <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $item->is_active ?? true)): echo 'checked'; endif; ?> class="rounded">
      Active
    </label>
    <?php endif; ?>

    <?php $__errorArgs = ['*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="alert-danger text-sm"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

    <div class="flex gap-2 pt-2">
      <button type="submit" class="btn-primary"><?php echo e(isset($item) ? 'Update Item' : 'Add Item'); ?></button>
      <a href="<?php echo e(route('inventory.index')); ?>" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\item-form.blade.php ENDPATH**/ ?>