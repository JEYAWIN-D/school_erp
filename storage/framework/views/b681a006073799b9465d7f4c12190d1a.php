<?php $__env->startSection('title', 'Overdue Books Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Overdue Books Report</h1>
      <p class="page-subtitle">Books not returned by their due date</p>
    </div>
    <a href="<?php echo e(route('library.currently-issued')); ?>" class="btn btn-secondary">All Issued</a>
  </div>

  <?php if($overdueIssues->count() > 0): ?>
  <div class="alert-warning">
    <strong><?php echo e($overdueIssues->total()); ?> overdue books</strong> found. Please follow up with borrowers.
  </div>
  <?php endif; ?>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Book</th>
            <th class="th">Student / Borrower</th>
            <th class="th">Issue Date</th>
            <th class="th">Due Date</th>
            <th class="th">Days Overdue</th>
            <th class="th">Fine (₹2/day)</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $overdueIssues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $daysOverdue = today()->diffInDays($issue->due_date);
            $fine = $daysOverdue * 2;
          ?>
          <tr class="tr">
            <td class="td">
              <div class="font-medium text-slate-800"><?php echo e($issue->book?->title); ?></div>
              <div class="text-xs text-slate-400"><?php echo e($issue->book?->accession_number); ?></div>
            </td>
            <td class="td font-medium"><?php echo e($issue->student?->first_name); ?> <?php echo e($issue->student?->last_name); ?></td>
            <td class="td text-sm"><?php echo e(\Carbon\Carbon::parse($issue->issue_date)->format('d M Y')); ?></td>
            <td class="td text-sm text-red-600 font-semibold"><?php echo e(\Carbon\Carbon::parse($issue->due_date)->format('d M Y')); ?></td>
            <td class="td">
              <span class="font-bold text-red-600"><?php echo e($daysOverdue); ?></span>
            </td>
            <td class="td font-semibold text-amber-600">₹<?php echo e(number_format($fine, 2)); ?></td>
            <td class="td">
              <form method="POST" action="<?php echo e(route('library.return')); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="issue_id" value="<?php echo e($issue->id); ?>">
                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Record return for this book?')">Return</button>
              </form>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="7" class="td text-center py-10 text-green-600 font-semibold">No overdue books — great!</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if($overdueIssues->hasPages()): ?><div class="mt-4"><?php echo e($overdueIssues->links()); ?></div><?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\overdue-report.blade.php ENDPATH**/ ?>