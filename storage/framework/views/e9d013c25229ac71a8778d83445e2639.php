<?php $__env->startSection('title','Fine Waiver'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Library Fine Collection & Waiver</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Search Overdue Issue</h3>
      <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input flex-1" placeholder="Student name or admission no...">
        <button type="submit" class="btn btn-secondary btn-sm">Search</button>
      </form>
      <?php if(isset($issues) && $issues->count()): ?>
      <div class="space-y-3">
        <?php $__currentLoopData = $issues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="border border-slate-200 rounded-lg p-3">
          <div class="flex items-center justify-between">
            <div>
              <p class="font-semibold text-slate-800 text-sm"><?php echo e($issue->book?->title); ?></p>
              <p class="text-xs text-slate-500"><?php echo e($issue->member?->name); ?> | Overdue: <?php echo e($issue->days_overdue); ?>d</p>
            </div>
            <span class="text-red-600 font-bold">₹<?php echo e(number_format($issue->fine_amount,2)); ?></span>
          </div>
          <?php if($issue->fine_amount > 0 && !$issue->fine_paid && !$issue->fine_waived): ?>
          <div class="mt-2 flex gap-2 flex-wrap items-center">
            <form method="POST" action="<?php echo e(route('library.fine.collect-via-fee', $issue->id)); ?>" class="inline flex gap-1 items-center"
              onsubmit="return confirm('Collect ₹<?php echo e($issue->fine_amount); ?> and record in Fee module?')">
              <?php echo csrf_field(); ?>
              <select name="payment_mode" class="select text-xs h-7 w-24">
                <option value="cash">Cash</option>
                <option value="upi">UPI</option>
                <option value="cheque">Cheque</option>
              </select>
              <button type="submit" class="btn btn-primary btn-xs">
                ₹<?php echo e(number_format($issue->fine_amount,0)); ?> → Fee Module
              </button>
            </form>
          </div>
          <form method="POST" action="<?php echo e(route('library.fine.waive',$issue->id)); ?>" class="mt-2 space-y-2">
            <?php echo csrf_field(); ?>
            <div><label class="label text-xs">Reason for Waiver</label>
              <input type="text" name="waiver_reason" class="input text-sm" required placeholder="e.g. Medical emergency">
            </div>
            <div><label class="label text-xs">Waive Amount (max ₹<?php echo e(number_format($issue->fine_amount,2)); ?>)</label>
              <input type="number" name="waiver_amount" class="input text-sm" max="<?php echo e($issue->fine_amount); ?>" step="0.01" value="<?php echo e($issue->fine_amount); ?>">
            </div>
            <button type="submit" class="btn btn-warning btn-sm text-xs">Apply Waiver Only</button>
          </form>
          <?php elseif($issue->fine_paid): ?>
          <p class="text-xs text-green-600 mt-2">✓ Fine collected via Fee module.</p>
          <?php elseif($issue->fine_waived): ?>
          <p class="text-xs text-green-600 mt-2">Fine waived. Reason: <?php echo e($issue->waiver_reason); ?></p>
          <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php endif; ?>
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Recent Waivers</h3>
      <?php $__empty_1 = true; $__currentLoopData = $recentWaivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
        <div>
          <p class="text-sm font-medium text-slate-800"><?php echo e($w->member?->name); ?></p>
          <p class="text-xs text-slate-400"><?php echo e($w->book?->title); ?> | <?php echo e($w->waiver_reason); ?></p>
        </div>
        <div class="text-right">
          <p class="font-semibold text-green-600">₹<?php echo e(number_format($w->waiver_amount,2)); ?></p>
          <p class="text-xs text-slate-400">By <?php echo e($w->waivedBy?->name); ?></p>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="text-slate-400 text-sm text-center py-6">No waivers.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\fine-waiver.blade.php ENDPATH**/ ?>