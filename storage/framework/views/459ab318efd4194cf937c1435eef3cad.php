
<?php $__env->startSection('title', 'Date-wise School Strength'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Date-wise School Strength</h1>
      <p class="page-subtitle"><?php echo e($currentYear?->name); ?> — Daily attendance summary</p>
    </div>
  </div>

  <form method="GET" class="card-flat py-4">
    <div class="flex items-center gap-3">
      <div>
        <label class="label">Month</label>
        <input type="month" name="month" value="<?php echo e($month); ?>" class="input">
      </div>
      <button type="submit" class="btn btn-primary btn-sm mt-5">View</button>
    </div>
  </form>

  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">Date</th>
          <th class="th">Day</th>
          <th class="th text-center">Present</th>
          <th class="th text-center">Absent</th>
          <th class="th text-center">Total Marked</th>
          <th class="th text-center">Attendance %</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $pct = $row->total > 0 ? round($row->present / $row->total * 100, 1) : 0;
          ?>
          <tr class="tr">
            <td class="td font-medium"><?php echo e(\Carbon\Carbon::parse($row->date)->format('d M Y')); ?></td>
            <td class="td text-slate-500"><?php echo e(\Carbon\Carbon::parse($row->date)->format('D')); ?></td>
            <td class="td text-center font-semibold text-green-600"><?php echo e($row->present); ?></td>
            <td class="td text-center text-red-500"><?php echo e($row->absent); ?></td>
            <td class="td text-center text-slate-500"><?php echo e($row->total); ?></td>
            <td class="td text-center">
              <span class="font-semibold <?php echo e($pct >= 90 ? 'text-green-600' : ($pct >= 75 ? 'text-amber-600' : 'text-red-600')); ?>"><?php echo e($pct); ?>%</span>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="6" class="td text-center py-10 text-slate-400">No attendance data for this month.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\date-strength.blade.php ENDPATH**/ ?>