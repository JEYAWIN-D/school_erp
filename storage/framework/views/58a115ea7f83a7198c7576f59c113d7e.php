<?php $__env->startSection('title', 'Bulk Rollover Progress Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Bulk Rollover Progress Report</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label">Academic Year</label>
        <select name="academic_year_id" class="select w-48">
          <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($yr->id); ?>" <?php if(($selectedYear?->id) == $yr->id): echo 'selected'; endif; ?>><?php echo e($yr->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  <?php if($report->count()): ?>
  <?php
    $totalEnrolled  = $report->sum('enrolled');
    $totalPromoted  = $report->sum('promoted');
    $totalDetained  = $report->sum('detained');
    $totalPending   = $report->sum('not_processed');
    $overallDone    = $report->where('complete', true)->count();
  ?>

  <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    <div class="card text-center">
      <div class="text-2xl font-bold text-indigo-600"><?php echo e($totalEnrolled); ?></div>
      <div class="text-xs text-slate-500 mt-1">Total Students</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-green-600"><?php echo e($totalPromoted); ?></div>
      <div class="text-xs text-slate-500 mt-1">Promoted</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-red-500"><?php echo e($totalDetained); ?></div>
      <div class="text-xs text-slate-500 mt-1">Detained</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-amber-500"><?php echo e($totalPending); ?></div>
      <div class="text-xs text-slate-500 mt-1">Not Processed</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold <?php echo e($overallDone === $report->count() ? 'text-green-600' : 'text-amber-500'); ?>">
        <?php echo e($overallDone); ?>/<?php echo e($report->count()); ?>

      </div>
      <div class="text-xs text-slate-500 mt-1">Classes Completed</div>
    </div>
  </div>

  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">Class</th>
          <th class="th text-center">Total Enrolled</th>
          <th class="th text-center">Promoted</th>
          <th class="th text-center">Detained</th>
          <th class="th text-center">Not Processed</th>
          <th class="th text-center">Promotion Rate</th>
          <th class="th text-center">Status</th>
          <th class="th text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="tr">
          <td class="td font-medium"><?php echo e($row['class']->name); ?></td>
          <td class="td text-center"><?php echo e($row['enrolled']); ?></td>
          <td class="td text-center text-green-600 font-medium"><?php echo e($row['promoted']); ?></td>
          <td class="td text-center text-red-500"><?php echo e($row['detained']); ?></td>
          <td class="td text-center <?php echo e($row['not_processed'] > 0 ? 'text-amber-600 font-semibold' : 'text-slate-400'); ?>"><?php echo e($row['not_processed']); ?></td>
          <td class="td text-center">
            <div class="flex items-center gap-2">
              <div class="flex-1 bg-slate-100 rounded-full h-2">
                <div class="h-2 rounded-full <?php echo e($row['promotion_rate'] >= 90 ? 'bg-green-500' : ($row['promotion_rate'] >= 70 ? 'bg-yellow-400' : 'bg-red-400')); ?>"
                     style="width: <?php echo e(min($row['promotion_rate'], 100)); ?>%"></div>
              </div>
              <span class="text-xs font-medium w-10"><?php echo e($row['promotion_rate']); ?>%</span>
            </div>
          </td>
          <td class="td text-center">
            <?php if($row['complete']): ?>
              <span class="badge-green text-xs">Complete</span>
            <?php else: ?>
              <span class="badge-amber text-xs">Pending</span>
            <?php endif; ?>
          </td>
          <td class="td text-center">
            <?php if(!$row['complete']): ?>
            <a href="<?php echo e(route('students.promotions', ['class_id' => $row['class']->id])); ?>"
               class="btn btn-secondary btn-xs">Process →</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>

  <?php if($totalPending === 0): ?>
  <div class="alert-success text-sm">
    <strong>Rollover Complete!</strong> All students in <?php echo e($selectedYear?->name); ?> have been processed.
  </div>
  <?php else: ?>
  <div class="alert-warning text-sm">
    <strong><?php echo e($totalPending); ?> students</strong> in <?php echo e($report->where('complete', false)->count()); ?> class(es) have not been processed yet.
    Please complete the rollover before starting the new academic year.
  </div>
  <?php endif; ?>

  <?php else: ?>
  <div class="card text-center text-slate-400 py-10">Select an academic year to view the rollover progress.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\rollover-report.blade.php ENDPATH**/ ?>