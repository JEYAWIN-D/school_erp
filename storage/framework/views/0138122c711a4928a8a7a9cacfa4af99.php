<?php $__env->startSection('title', 'Leave Requests'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Leave Requests</h1>
    <a href="<?php echo e(route('hr.leave-balance')); ?>" class="btn btn-secondary">Leave Balance Tracker</a>
  </div>
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr><th class="th">Employee</th><th class="th">Leave Type</th><th class="th">From</th><th class="th">To</th><th class="th">Days</th><th class="th">Status</th><th class="th">Actions</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td font-medium"><?php echo e($l->employee?->first_name); ?> <?php echo e($l->employee?->last_name); ?></td>
            <td class="td"><?php echo e($l->leaveType?->name ?? '—'); ?></td>
            <td class="td"><?php echo e(\Carbon\Carbon::parse($l->from_date)->format('d M Y')); ?></td>
            <td class="td"><?php echo e(\Carbon\Carbon::parse($l->to_date)->format('d M Y')); ?></td>
            <td class="td"><?php echo e($l->total_days); ?></td>
            <td class="td">
              <span class="<?php echo e($l->status === 'approved' ? 'badge-green' : ($l->status === 'rejected' ? 'badge-red' : ($l->status === 'cancelled' ? 'badge-slate' : 'badge-amber'))); ?>">
                <?php echo e(ucfirst($l->status)); ?>

              </span>
            </td>
            <td class="td">
              <div class="flex gap-1 flex-wrap items-center">
                <?php if($l->status === 'pending'): ?>
                  <form method="POST" action="<?php echo e(route('hr.leaves.approve', $l->id)); ?>"><?php echo csrf_field(); ?><button class="btn btn-secondary btn-sm">Approve</button></form>
                  <form method="POST" action="<?php echo e(route('hr.leaves.reject', $l->id)); ?>"><?php echo csrf_field(); ?><button class="btn btn-danger btn-sm">Reject</button></form>
                <?php endif; ?>
                <?php if(in_array($l->status, ['pending','approved'])): ?>
                  <form method="POST" action="<?php echo e(route('hr.leaves.cancel', $l->id)); ?>"><?php echo csrf_field(); ?><button class="btn btn-secondary btn-sm text-red-600" onclick="return confirm('Cancel this leave?')">Cancel</button></form>
                <?php endif; ?>
                <?php if($l->attachment): ?>
                  <a href="<?php echo e(asset('storage/' . $l->attachment)); ?>" target="_blank" class="btn btn-secondary btn-sm flex items-center gap-1" title="Medical Certificate">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Doc
                  </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="6" class="td text-center py-10 text-slate-400">No leave requests.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if($leaves->hasPages()): ?><div class="text-sm mt-3"><?php echo e($leaves->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\leaves.blade.php ENDPATH**/ ?>