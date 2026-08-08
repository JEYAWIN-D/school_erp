<?php $__env->startSection('title', 'Inventory Categories'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ editId: null, editName: '', editCode: '', editDesc: '' }">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Inventory Categories</h1>
    <a href="<?php echo e(route('inventory.index')); ?>" class="btn-sm btn-secondary">← Items</a>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Add Category</h3>
      <form method="POST" action="<?php echo e(route('inventory.categories.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" class="input" required>
        </div>
        <div>
          <label class="label">Code</label>
          <input type="text" name="code" class="input" placeholder="e.g. STAT">
        </div>
        <div>
          <label class="label">Description</label>
          <textarea name="description" rows="2" class="input"></textarea>
        </div>
        <button type="submit" class="btn-primary w-full">Add Category</button>
      </form>
    </div>

    
    <div class="lg:col-span-2 table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">Name</th>
            <th class="th">Code</th>
            <th class="th text-center">Items</th>
            <th class="th">Status</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td font-medium"><?php echo e($cat->name); ?></td>
            <td class="td font-mono text-xs"><?php echo e($cat->code ?? '—'); ?></td>
            <td class="td text-center"><?php echo e($cat->items_count); ?></td>
            <td class="td">
              <?php if($cat->is_active): ?> <span class="badge-green">Active</span>
              <?php else: ?> <span class="badge-slate">Inactive</span> <?php endif; ?>
            </td>
            <td class="td">
              <button @click="editId=<?php echo e($cat->id); ?>;editName='<?php echo e(addslashes($cat->name)); ?>';editCode='<?php echo e(addslashes($cat->code ?? '')); ?>';editDesc='<?php echo e(addslashes($cat->description ?? '')); ?>'"
                class="btn-xs btn-secondary">Edit</button>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td class="td text-center text-slate-400" colspan="5">No categories yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  
  <div x-show="editId" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm space-y-4" @click.stop>
      <h3 class="font-semibold text-slate-700">Edit Category</h3>
      <form method="POST" :action="'/inventory/categories/' + editId" class="space-y-3">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div>
          <label class="label">Name</label>
          <input type="text" name="name" x-model="editName" class="input" required>
        </div>
        <div>
          <label class="label">Code</label>
          <input type="text" name="code" x-model="editCode" class="input">
        </div>
        <div>
          <label class="label">Description</label>
          <textarea name="description" rows="2" class="input" x-model="editDesc"></textarea>
        </div>
        <div class="flex gap-2">
          <button type="submit" class="btn-primary flex-1">Update</button>
          <button type="button" @click="editId=null" class="btn-secondary flex-1">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\categories.blade.php ENDPATH**/ ?>