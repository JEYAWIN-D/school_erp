<?php $__env->startSection('title', 'Outstanding Balance Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Outstanding Balance Report</h1>
      <p class="page-subtitle"><?php echo e($currentYear?->name); ?> — Students with pending dues</p>
    </div>
    <a href="<?php echo e(route('fees.index')); ?>" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($class->id); ?>" <?php echo e(request('class_id') == $class->id ? 'selected' : ''); ?>><?php echo e($class->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
  </div>

  <?php if($outstanding->isNotEmpty()): ?>
  <?php $totalOutstanding = $outstanding->sum('balance'); ?>
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-red-600"><?php echo e($outstanding->count()); ?></p>
      <p class="text-sm text-slate-500 mt-1">Students with Dues</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-red-600">₹<?php echo e(number_format($totalOutstanding / 100000, 2)); ?>L</p>
      <p class="text-sm text-slate-500 mt-1">Total Outstanding</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-amber-600">₹<?php echo e(number_format($outstanding->avg('balance'), 0)); ?></p>
      <p class="text-sm text-slate-500 mt-1">Average Due Per Student</p>
    </div>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">Student</th>
            <th class="th">Class</th>
            <th class="th">Total Fee</th>
            <th class="th">Paid</th>
            <th class="th">Outstanding</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $outstanding; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td text-slate-400"><?php echo e($i + 1); ?></td>
            <td class="td">
              <a href="<?php echo e(route('students.show', $row['student']->id)); ?>" class="font-medium text-indigo-600 hover:underline">
                <?php echo e($row['student']->first_name); ?> <?php echo e($row['student']->last_name); ?>

              </a>
              <div class="text-xs text-slate-400"><?php echo e($row['student']->admission_no ?? $row['student']->admission_number); ?></div>
            </td>
            <td class="td text-slate-500"><?php echo e($row['student']->currentEnrollment?->class?->name ?? '—'); ?></td>
            <td class="td">₹<?php echo e(number_format($row['totalFee'], 2)); ?></td>
            <td class="td text-green-600">₹<?php echo e(number_format($row['totalPaid'], 2)); ?></td>
            <td class="td">
              <span class="font-semibold text-red-600">₹<?php echo e(number_format($row['balance'], 2)); ?></span>
              <div class="mt-1 bg-slate-200 rounded-full h-1.5 w-24">
                <div class="h-1.5 rounded-full bg-red-500" style="width:<?php echo e($row['totalFee'] > 0 ? round($row['balance']/$row['totalFee']*100) : 0); ?>%"></div>
              </div>
            </td>
            <td class="td">
              <a href="<?php echo e(route('fees.collect', ['student_id' => $row['student']->id])); ?>" class="btn btn-primary btn-sm">Collect</a>
              <a href="<?php echo e(route('fees.demand-notice', $row['student']->id)); ?>" class="btn btn-secondary btn-sm mt-1">Notice</a>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php elseif(request()->hasAny(['class_id'])): ?>
  <div class="alert-success">No outstanding dues found for the selected filters.</div>
  <?php else: ?>
  <div class="card text-center py-12 text-slate-400">Select a class or click Filter to see outstanding balances.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\outstanding.blade.php ENDPATH**/ ?>