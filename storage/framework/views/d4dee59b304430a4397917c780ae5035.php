<?php $__env->startSection('title', 'Annual Collection Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Annual Collection Report</h1>
      <p class="page-subtitle">Year-on-year fee collection summary</p>
    </div>
    <a href="<?php echo e(route('fees.index')); ?>" class="btn btn-secondary">Back</a>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-<?php echo e(min(count($data), 4)); ?> gap-4">
    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card text-center py-5">
      <p class="text-2xl font-bold text-indigo-600">₹<?php echo e(number_format($amount / 100000, 2)); ?>L</p>
      <p class="text-sm text-slate-500 mt-1"><?php echo e($year); ?></p>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <div class="card">
    <h2 class="font-semibold text-slate-800 mb-4">Year-wise Collection</h2>
    <?php $maxVal = max($data ?: [1]); ?>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Year</th>
            <th class="th text-right">Total Collection</th>
            <th class="th">Growth</th>
          </tr>
        </thead>
        <tbody>
          <?php $prevAmount = null; ?>
          <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $growth = $prevAmount > 0 ? round(($amount - $prevAmount) / $prevAmount * 100, 1) : null;
            $prevAmount = $amount;
          ?>
          <tr class="tr">
            <td class="td font-medium"><?php echo e($year); ?></td>
            <td class="td text-right font-semibold">₹<?php echo e(number_format($amount, 2)); ?></td>
            <td class="td">
              <?php if($growth !== null): ?>
                <span class="text-sm font-medium <?php echo e($growth >= 0 ? 'text-green-600' : 'text-red-500'); ?>">
                  <?php echo e($growth >= 0 ? '+' : ''); ?><?php echo e($growth); ?>%
                </span>
              <?php else: ?>
                <span class="text-slate-400 text-sm">—</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\annual-collection.blade.php ENDPATH**/ ?>