
<?php $__env->startSection('title', 'Class-wise Attendance Summary'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Class-wise Attendance Summary</h1>
      <p class="page-subtitle"><?php echo e($currentYear?->name); ?> — Monthly class attendance breakdown</p>
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
          <th class="th">Class</th>
          <th class="th text-center">Present</th>
          <th class="th text-center">Absent</th>
          <th class="th text-center">Total Records</th>
          <th class="th text-center">Avg Attendance %</th>
          <th class="th">Rating</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td font-semibold text-slate-800"><?php echo e($row->class?->name ?? '—'); ?></td>
            <td class="td text-center font-semibold text-green-600"><?php echo e($row->present); ?></td>
            <td class="td text-center text-red-500"><?php echo e($row->absent); ?></td>
            <td class="td text-center text-slate-500"><?php echo e($row->total); ?></td>
            <td class="td text-center">
              <span class="font-bold text-<?php echo e($row->percentage >= 90 ? 'green' : ($row->percentage >= 75 ? 'amber' : 'red')); ?>-600">
                <?php echo e($row->percentage); ?>%
              </span>
              <div class="w-20 bg-slate-200 rounded-full h-2 mt-1 mx-auto">
                <div class="h-2 rounded-full <?php echo e($row->percentage >= 90 ? 'bg-green-500' : ($row->percentage >= 75 ? 'bg-amber-500' : 'bg-red-500')); ?>" style="width:<?php echo e($row->percentage); ?>%"></div>
              </div>
            </td>
            <td class="td">
              <?php if($row->percentage >= 90): ?>
                <span class="badge-green">Excellent</span>
              <?php elseif($row->percentage >= 75): ?>
                <span class="badge-amber">Good</span>
              <?php else: ?>
                <span class="badge-red">Needs Attention</span>
              <?php endif; ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\class-summary.blade.php ENDPATH**/ ?>