<?php $__env->startSection('title','Edit Installment Plan'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl">
  <h1 class="page-title">Edit Plan: <?php echo e($plan->name); ?></h1>
  <form method="POST" action="<?php echo e(route('fees.installments.store')); ?>" class="card space-y-4">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="plan_id" value="<?php echo e($plan->id); ?>">
    <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Add Installment</h3>
    <div class="grid grid-cols-3 gap-3">
      <div><label class="label">#</label><input type="number" name="installment_number" class="input" min="1" value="<?php echo e($plan->installments->count() + 1); ?>" required></div>
      <div><label class="label">Name</label><input type="text" name="name" class="input" placeholder="e.g. Q1 April"></div>
      <div><label class="label">Amount (₹) <span class="text-red-500">*</span></label><input type="number" name="amount" class="input" step="0.01" min="0" required></div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div><label class="label">Due Date</label><input type="date" name="due_date" class="input"></div>
      <div><label class="label">Fee Head</label>
        <select name="fee_head_id" class="select">
          <option value="">General</option>
          <?php $__currentLoopData = $feeHeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($fh->id); ?>"><?php echo e($fh->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Add Installment</button>
  </form>

  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Existing Installments</h3>
    <?php $__empty_1 = true; $__currentLoopData = $plan->installments->sortBy('installment_number'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
      <div>
        <p class="text-sm font-semibold text-slate-800">#<?php echo e($inst->installment_number); ?> <?php echo e($inst->name); ?></p>
        <p class="text-xs text-slate-400">Due: <?php echo e($inst->due_date?->format('d M Y') ?? '—'); ?></p>
      </div>
      <div class="flex items-center gap-3">
        <p class="font-bold text-indigo-700">₹<?php echo e(number_format($inst->amount,0)); ?></p>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p class="text-slate-400 text-sm text-center py-4">No installments yet.</p>
    <?php endif; ?>
    <?php if($plan->installments->count()): ?>
    <div class="flex justify-between font-bold text-slate-800 pt-2 border-t text-sm">
      <span>Total</span><span>₹<?php echo e(number_format($plan->installments->sum('amount'),0)); ?></span>
    </div>
    <?php endif; ?>
  </div>
  <a href="<?php echo e(route('fees.installments')); ?>" class="btn btn-secondary btn-sm">Done</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\installment-edit.blade.php ENDPATH**/ ?>