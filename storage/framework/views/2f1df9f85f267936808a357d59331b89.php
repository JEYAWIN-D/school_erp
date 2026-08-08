<?php $__env->startSection('title', 'Notice Read Receipts'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Read Receipts</h1>
      <p class="page-subtitle text-slate-500 text-sm"><?php echo e($notice->title); ?></p>
    </div>
    <a href="<?php echo e(route('academics.notices')); ?>" class="btn-sm btn-secondary">← Back to Notices</a>
  </div>

  
  <div class="grid grid-cols-3 gap-4">
    <div class="card text-center">
      <div class="text-2xl font-bold text-green-700"><?php echo e($reads->total()); ?></div>
      <div class="text-xs text-slate-500 mt-1">Users Read</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-slate-700"><?php echo e($totalUsers); ?></div>
      <div class="text-xs text-slate-500 mt-1">Total Active Users</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-indigo-700">
        <?php echo e($totalUsers > 0 ? round($reads->total() / $totalUsers * 100) : 0); ?>%
      </div>
      <div class="text-xs text-slate-500 mt-1">Read Rate</div>
    </div>
  </div>

  
  <div class="card bg-slate-50 border border-slate-200">
    <div class="flex items-center gap-3 flex-wrap">
      <span class="badge-blue"><?php echo e(ucfirst($notice->notice_type)); ?></span>
      <span class="badge-indigo capitalize"><?php echo e($notice->target_audience); ?></span>
      <span class="text-xs text-slate-500">Published: <?php echo e(\Carbon\Carbon::parse($notice->publish_date)->format('d M Y')); ?></span>
      <span class="text-xs text-slate-400">By: <?php echo e($notice->createdBy?->name); ?></span>
    </div>
  </div>

  
  <div class="card">
    <h2 class="text-sm font-semibold text-slate-700 mb-3">Who Read This Notice</h2>
    <?php if($reads->isEmpty()): ?>
      <p class="text-slate-400 text-sm">No read receipts yet.</p>
    <?php else: ?>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">User</th>
            <th class="th">Role</th>
            <th class="th">Read At</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $reads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $receipt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td text-slate-400 text-xs"><?php echo e($reads->firstItem() + $i); ?></td>
            <td class="td">
              <div class="font-medium"><?php echo e($receipt->user?->name ?? '—'); ?></div>
              <div class="text-xs text-slate-400"><?php echo e($receipt->user?->email); ?></div>
            </td>
            <td class="td">
              <?php $__currentLoopData = $receipt->user?->getRoleNames() ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="badge-indigo text-xs"><?php echo e($role); ?></span>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </td>
            <td class="td text-xs text-slate-500"><?php echo e(\Carbon\Carbon::parse($receipt->read_at)->format('d M Y, h:i A')); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <?php if($reads->hasPages()): ?><div class="mt-4"><?php echo e($reads->links()); ?></div><?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\notice-read-receipts.blade.php ENDPATH**/ ?>