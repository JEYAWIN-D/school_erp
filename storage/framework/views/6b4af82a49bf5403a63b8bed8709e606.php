<?php $__env->startSection('title','Installment Plan'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-2xl">
  <div class="flex items-center justify-between">
    <h1 class="page-title"><?php echo e($plan->name); ?></h1>
    <a href="<?php echo e(route('fees.installments.edit',$plan->id)); ?>" class="btn btn-secondary btn-sm">Edit</a>
  </div>
  <div class="card space-y-4">
    <div class="grid grid-cols-2 gap-4 pb-3 border-b border-slate-100">
      <div><p class="text-xs text-slate-400">Class</p><p class="font-semibold text-slate-800"><?php echo e($plan->class?->name ?? 'All Classes'); ?></p></div>
      <div><p class="text-xs text-slate-400">Late Fee Rule</p><p class="font-semibold text-slate-800"><?php echo e($plan->lateFeeRule?->name ?? 'None'); ?></p></div>
    </div>
    <h3 class="font-semibold text-slate-700">Installments</h3>
    <?php $__empty_1 = true; $__currentLoopData = $plan->installments->sortBy('installment_number'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
      <div>
        <p class="font-semibold text-slate-800 text-sm">#<?php echo e($inst->installment_number); ?> — <?php echo e($inst->name ?? 'Installment '.$inst->installment_number); ?></p>
        <p class="text-xs text-slate-400">Due: <?php echo e($inst->due_date?->format('d M Y') ?? 'Not set'); ?></p>
      </div>
      <p class="font-bold text-indigo-700">₹<?php echo e(number_format($inst->amount,0)); ?></p>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p class="text-slate-400 text-sm text-center py-4">No installments added yet. <a href="<?php echo e(route('fees.installments.edit',$plan->id)); ?>" class="text-indigo-600 hover:underline">Add installments</a></p>
    <?php endif; ?>
    <?php if($plan->installments->count()): ?>
    <div class="flex justify-between font-bold text-slate-800 pt-2 border-t">
      <span>Total</span>
      <span>₹<?php echo e(number_format($plan->installments->sum('amount'),0)); ?></span>
    </div>
    <?php endif; ?>
  </div>
  <a href="<?php echo e(route('fees.installments')); ?>" class="btn btn-secondary btn-sm">Back to Plans</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\installment-show.blade.php ENDPATH**/ ?>