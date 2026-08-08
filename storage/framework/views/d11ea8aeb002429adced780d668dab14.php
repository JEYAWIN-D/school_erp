<?php $__env->startSection('title','Fee Cancellation & Reversal'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Fee Cancellation & Reversal</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Search Receipt</h3>
      <form method="GET" class="flex gap-3">
        <input type="text" name="receipt" value="<?php echo e(request('receipt')); ?>" class="input flex-1" placeholder="Receipt number...">
        <button type="submit" class="btn btn-secondary btn-sm">Search</button>
      </form>
      <?php if(isset($payment)): ?>
      <div class="border border-slate-200 rounded-lg p-4 space-y-2">
        <div class="flex items-center justify-between">
          <span class="font-mono text-indigo-700 font-semibold"><?php echo e($payment->receipt_number); ?></span>
          <span class="<?php echo e($payment->is_cancelled ? 'badge-red' : 'badge-green'); ?>"><?php echo e($payment->is_cancelled ? 'Cancelled' : 'Active'); ?></span>
        </div>
        <p class="text-sm text-slate-700"><?php echo e($payment->enrollment?->student?->full_name); ?></p>
        <p class="text-xs text-slate-400"><?php echo e($payment->enrollment?->class?->name); ?> | <?php echo e($payment->feeHead?->name); ?></p>
        <div class="grid grid-cols-2 gap-2 text-sm">
          <div><span class="text-slate-400">Amount:</span> <span class="font-semibold">₹<?php echo e(number_format($payment->amount_paid,2)); ?></span></div>
          <div><span class="text-slate-400">Mode:</span> <span class="capitalize"><?php echo e($payment->payment_mode); ?></span></div>
          <div><span class="text-slate-400">Date:</span> <?php echo e($payment->payment_date); ?></div>
          <div><span class="text-slate-400">By:</span> <?php echo e($payment->collectedBy?->name ?? '—'); ?></div>
        </div>
        <?php if(!$payment->is_cancelled): ?>
        <form method="POST" action="<?php echo e(route('fees.cancel',$payment->id)); ?>" class="pt-2 space-y-3">
          <?php echo csrf_field(); ?>
          <div><label class="label">Reason for Cancellation <span class="text-red-500">*</span></label>
            <textarea name="cancel_reason" class="input h-20" required placeholder="State reason..."></textarea>
          </div>
          <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Cancel receipt <?php echo e($payment->receipt_number); ?>? This cannot be undone.')">
            Cancel Receipt
          </button>
        </form>
        <?php else: ?>
        <p class="text-sm text-red-500 pt-2">Cancelled: <?php echo e($payment->cancel_reason); ?></p>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Recent Cancellations</h3>
      <div class="space-y-2">
        <?php $__empty_1 = true; $__currentLoopData = $recentCancellations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
          <div>
            <p class="font-mono text-xs text-indigo-700"><?php echo e($c->receipt_number); ?></p>
            <p class="text-sm text-slate-700"><?php echo e($c->enrollment?->student?->full_name); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($c->cancel_reason); ?></p>
          </div>
          <div class="text-right">
            <p class="font-semibold text-red-600">₹<?php echo e(number_format($c->amount_paid,2)); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($c->updated_at->format('d M')); ?></p>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-slate-400 text-sm text-center py-6">No recent cancellations.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\cancellation.blade.php ENDPATH**/ ?>