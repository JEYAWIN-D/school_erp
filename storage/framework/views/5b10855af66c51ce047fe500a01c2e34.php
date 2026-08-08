<?php $__env->startSection('title','Attendance vs Leave Reconciliation'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Staff Attendance vs Leave Reconciliation</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label text-xs">Month</label>
        <input type="month" name="month" class="input" value="<?php echo e($month); ?>">
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <?php $__currentLoopData = ['Employee','Dept','Total Days Marked','Present','Absent','Approved Leave','LOP Days','Status']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $reconciliation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php $emp = $row['employee']; ?>
        <tr class="hover:bg-slate-50 <?php echo e($row['lop_days'] > 0 ? 'bg-red-50/30' : ''); ?>">
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800"><?php echo e($emp->full_name); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($emp->employee_id); ?></p>
          </td>
          <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($emp->department?->name ?? '—'); ?></td>
          <td class="px-4 py-3 text-slate-600"><?php echo e($row['total_days']); ?></td>
          <td class="px-4 py-3 text-green-600 font-semibold"><?php echo e($row['present_days']); ?></td>
          <td class="px-4 py-3 <?php echo e($row['absent_days'] > 0 ? 'text-red-600' : 'text-slate-300'); ?>">
            <?php echo e($row['absent_days']); ?>

          </td>
          <td class="px-4 py-3 text-blue-600"><?php echo e($row['approved_leave']); ?></td>
          <td class="px-4 py-3">
            <?php if($row['lop_days'] > 0): ?>
            <span class="badge-red text-xs"><?php echo e($row['lop_days']); ?> LOP</span>
            <?php else: ?>
            <span class="text-slate-300">0</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3">
            <?php if($row['total_days'] === 0): ?>
            <span class="badge-amber text-xs">Not Marked</span>
            <?php elseif($row['lop_days'] > 0): ?>
            <span class="badge-red text-xs">LOP Deduction</span>
            <?php else: ?>
            <span class="badge-green text-xs">Reconciled</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No data found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\attendance-leave-reconciliation.blade.php ENDPATH**/ ?>