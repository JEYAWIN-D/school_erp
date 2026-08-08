<?php $__env->startSection('title', 'Notice Read Receipts'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-5">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Read Receipts</h1>
    <a href="<?php echo e(route('communication.staff-notices')); ?>" class="btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-2"><?php echo e($notice->title); ?></h3>
    <p class="text-sm text-slate-500 mb-4"><?php echo e($reads->count()); ?> staff have read this notice</p>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr><th class="th">Staff Name</th><th class="th">Read At</th></tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $reads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td font-medium text-slate-700"><?php echo e($r->user?->name ?? '—'); ?></td>
            <td class="td text-slate-500"><?php echo e(\Carbon\Carbon::parse($r->read_at)->format('d M Y H:i')); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="2" class="td text-center text-slate-400 py-6">No read receipts yet</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\communication\notice-receipts.blade.php ENDPATH**/ ?>