<?php $__env->startSection('title', 'Borrowing History — ' . $student->first_name); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-4">
      <a href="<?php echo e(route('library.members')); ?>" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
      <div>
        <h1 class="page-title"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></h1>
        <p class="page-subtitle">Borrowing History</p>
      </div>
    </div>
  </div>

  <?php
    $totalBorrowed = $issues->total();
    $currentlyHeld = $issues->where('status', 'issued')->count();
    $overdue       = $issues->where('status', 'issued')->filter(fn($i) => today()->gt($i->due_date))->count();
  ?>
  <div class="grid grid-cols-3 gap-4">
    <div class="card text-center py-4"><p class="text-2xl font-bold text-slate-700"><?php echo e($totalBorrowed); ?></p><p class="text-sm text-slate-500">Total Borrowed</p></div>
    <div class="card text-center py-4"><p class="text-2xl font-bold text-blue-600"><?php echo e($currentlyHeld); ?></p><p class="text-sm text-slate-500">Currently Held</p></div>
    <div class="card text-center py-4"><p class="text-2xl font-bold text-red-500"><?php echo e($overdue); ?></p><p class="text-sm text-slate-500">Overdue</p></div>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Book</th>
            <th class="th">Issue Date</th>
            <th class="th">Due Date</th>
            <th class="th">Return Date</th>
            <th class="th">Fine</th>
            <th class="th">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $issues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td">
              <div class="font-medium"><?php echo e($issue->book?->title); ?></div>
              <div class="text-xs text-slate-400"><?php echo e($issue->book?->accession_number); ?></div>
            </td>
            <td class="td text-sm"><?php echo e(\Carbon\Carbon::parse($issue->issue_date)->format('d M Y')); ?></td>
            <td class="td text-sm"><?php echo e(\Carbon\Carbon::parse($issue->due_date)->format('d M Y')); ?></td>
            <td class="td text-sm"><?php echo e($issue->return_date ? \Carbon\Carbon::parse($issue->return_date)->format('d M Y') : '—'); ?></td>
            <td class="td"><?php echo e($issue->fine_amount > 0 ? '₹' . $issue->fine_amount : '—'); ?></td>
            <td class="td">
              <?php $isOverdue = $issue->status === 'issued' && today()->gt($issue->due_date); ?>
              <span class="badge-<?php echo e($issue->status === 'returned' ? 'green' : ($isOverdue ? 'red' : 'blue')); ?>">
                <?php echo e($isOverdue ? 'Overdue' : ucfirst($issue->status)); ?>

              </span>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="6" class="td text-center py-10 text-slate-400">No borrowing history found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if($issues->hasPages()): ?><div class="mt-4"><?php echo e($issues->links()); ?></div><?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\member-history.blade.php ENDPATH**/ ?>