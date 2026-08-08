<?php $__env->startSection('title', 'Cashier-wise Collection Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Cashier-wise Collection Report</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">From Date</label>
        <input type="date" name="from_date" value="<?php echo e($from); ?>" class="input">
      </div>
      <div>
        <label class="label">To Date</label>
        <input type="date" name="to_date" value="<?php echo e($to); ?>" class="input">
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  <?php if($rows->count()): ?>
  <div class="space-y-4">
    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userId => $cashierRows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
      $cashierName = $users[$userId] ?? 'Unknown';
      $cashierTotal = $cashierRows->sum('total');
    ?>
    <div class="card">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-slate-800"><?php echo e($cashierName); ?></h2>
        <span class="font-bold text-slate-700">Total: ₹<?php echo e(number_format($cashierTotal, 2)); ?></span>
      </div>
      <div class="table-wrap">
        <table class="min-w-full text-sm">
          <thead><tr>
            <th class="th">Payment Mode</th>
            <th class="th text-right">Transactions</th>
            <th class="th text-right">Amount Collected</th>
          </tr></thead>
          <tbody>
            <?php $__currentLoopData = $cashierRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="tr">
              <td class="td capitalize"><?php echo e($r->payment_mode); ?></td>
              <td class="td text-right"><?php echo e($r->txn_count); ?></td>
              <td class="td text-right font-medium">₹<?php echo e(number_format($r->total, 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="bg-slate-50 font-semibold">
              <td class="td">Total</td>
              <td class="td text-right"><?php echo e($cashierRows->sum('txn_count')); ?></td>
              <td class="td text-right text-green-700">₹<?php echo e(number_format($cashierTotal, 2)); ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <?php $grandTotal = collect($rows->flatten())->sum('total'); ?>
    <div class="card bg-slate-50">
      <div class="flex items-center justify-between">
        <span class="font-semibold text-slate-700">Grand Total (All Cashiers)</span>
        <span class="text-xl font-bold text-green-700">₹<?php echo e(number_format($grandTotal, 2)); ?></span>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="card text-center py-10 text-slate-400">
    No collection data found for the selected date range.
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\cashier-report.blade.php ENDPATH**/ ?>