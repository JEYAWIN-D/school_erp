<?php $__env->startSection('title', 'Category-wise Fee Variation'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Category-wise Fee Variation</h1>
  <p class="text-slate-500 text-sm">Define different fee amounts per student category (General / SC / ST / OBC / EWS / Minority) for any fee head.</p>

  
  <div class="card" x-data="{open:false}">
    <button @click="open=!open" class="btn btn-primary btn-sm mb-4">+ Add Category Variation</button>
    <div x-show="open" x-transition>
      <form method="POST" action="<?php echo e(route('fees.category-variations.save')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="label">Fee Structure *</label>
            <select name="fee_structure_id" required class="select w-full">
              <option value="">Select Structure</option>
              <?php $__currentLoopData = $structures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s->id); ?>"><?php echo e($s->class?->name); ?> — <?php echo e($s->feeHead?->name ?? 'N/A'); ?> (<?php echo e($s->academic_year_id); ?>)</option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div>
            <label class="label">Fee Head *</label>
            <select name="fee_head_id" required class="select w-full">
              <option value="">Select Head</option>
              <?php $__currentLoopData = $feeHeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($h->id); ?>"><?php echo e($h->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
        </div>
        <div>
          <label class="label mb-2 block">Amount per Category</label>
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <?php $__currentLoopData = ['General','SC','ST','OBC','EWS','Minority']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
              <label class="label text-xs"><?php echo e($cat); ?></label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">₹</span>
                <input type="number" name="variations[<?php echo e($cat); ?>]" min="0" step="0.01"
                  class="input pl-6 text-sm" placeholder="0.00">
              </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        </div>
        <div>
          <label class="label">Remarks</label>
          <input type="text" name="remarks" class="input w-full" placeholder="Optional note">
        </div>
        <div class="flex gap-3">
          <button type="submit" class="btn btn-primary btn-sm">Save Variations</button>
          <button type="button" @click="open=false" class="btn btn-secondary btn-sm">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  
  <?php $__currentLoopData = $variations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $structureId => $structureVariations): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php $first = $structureVariations->first(); ?>
  <div class="card">
    <h2 class="font-semibold text-slate-800 mb-3">
      <?php echo e($first->feeStructure?->class?->name); ?> — <?php echo e($first->feeHead?->name); ?>

    </h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
      <?php $__currentLoopData = $structureVariations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="bg-slate-50 rounded-lg p-3 text-center">
        <div class="text-xs text-slate-500 mb-1"><?php echo e($v->student_category); ?></div>
        <div class="font-bold text-slate-800">₹<?php echo e(number_format($v->amount, 2)); ?></div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  <?php if($variations->isEmpty()): ?>
  <div class="card text-center py-10 text-slate-400">
    No category-wise fee variations configured yet.
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\category-fee-variations.blade.php ENDPATH**/ ?>