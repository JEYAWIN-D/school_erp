<?php $__env->startSection('title', 'Overdue Books'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Overdue Books</h1>
    <form method="POST" action="<?php echo e(route('library.send-overdue-reminders')); ?>">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn-sm btn-primary"
        onclick="return confirm('Send overdue email reminders to all members with overdue books?')">
        Send Email Reminders
      </button>
    </form>
  </div>
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr><th class="th">Book</th><th class="th">Student</th><th class="th">Issue Date</th><th class="th">Due Date</th><th class="th">Days Overdue</th><th class="th">Fine</th><th class="th text-right">Action</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $overdueIssues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td font-medium"><?php echo e($issue->book?->title); ?></td>
            <td class="td"><?php echo e($issue->student?->full_name ?? '—'); ?></td>
            <td class="td"><?php echo e(\Carbon\Carbon::parse($issue->issue_date)->format('d M Y')); ?></td>
            <td class="td text-red-600 font-semibold"><?php echo e(\Carbon\Carbon::parse($issue->due_date)->format('d M Y')); ?></td>
            <td class="td"><span class="badge-red"><?php echo e(now()->diffInDays($issue->due_date)); ?> days</span></td>
            <td class="td font-semibold text-red-600">₹<?php echo e(now()->diffInDays($issue->due_date) * 2); ?></td>
            <td class="td text-right">
              <form method="POST" action="<?php echo e(route('library.return')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="issue_id" value="<?php echo e($issue->id); ?>">
                <button type="submit" class="btn btn-secondary btn-sm">Return</button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="7" class="td text-center py-10 text-slate-400">No overdue books.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if($overdueIssues->hasPages()): ?><div class="text-sm mt-3"><?php echo e($overdueIssues->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\overdue.blade.php ENDPATH**/ ?>