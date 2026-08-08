<?php $__env->startSection('title', 'Defaulter Aging Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Defaulter Aging Report</h1>
      <p class="page-subtitle">Outstanding fee analysis by days since last payment</p>
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

  <?php
    $bands = [
      '0-30'  => ['label' => '0–30 Days',  'color' => 'amber',  'count' => count($agingBands['0-30'])],
      '31-60' => ['label' => '31–60 Days', 'color' => 'orange', 'count' => count($agingBands['31-60'])],
      '61-90' => ['label' => '61–90 Days', 'color' => 'red',    'count' => count($agingBands['61-90'])],
      '90+'   => ['label' => '90+ Days',   'color' => 'rose',   'count' => count($agingBands['90+'])],
    ];
  ?>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <?php $__currentLoopData = $bands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $band): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card text-center py-5">
      <p class="text-2xl font-bold text-<?php echo e($band['color']); ?>-600"><?php echo e($band['count']); ?></p>
      <p class="text-sm text-slate-500 mt-1"><?php echo e($band['label']); ?></p>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <?php $__currentLoopData = ['0-30' => ['0–30 Days','amber'], '31-60' => ['31–60 Days','orange'], '61-90' => ['61–90 Days','red'], '90+' => ['90+ Days','rose']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$label, $color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php if(count($agingBands[$key]) > 0): ?>
  <div class="card">
    <h2 class="font-semibold text-<?php echo e($color); ?>-700 mb-4"><?php echo e($label); ?> — <?php echo e(count($agingBands[$key])); ?> Students</h2>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Student</th>
            <th class="th">Class</th>
            <th class="th">Total Fee</th>
            <th class="th">Paid</th>
            <th class="th">Balance</th>
            <th class="th">Days Since Payment</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $agingBands[$key]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td font-medium">
              <a href="<?php echo e(route('students.show', $d['student']->id)); ?>" class="text-indigo-600 hover:underline">
                <?php echo e($d['student']->first_name); ?> <?php echo e($d['student']->last_name); ?>

              </a>
            </td>
            <td class="td text-slate-500"><?php echo e($d['student']->currentEnrollment?->class?->name ?? '—'); ?></td>
            <td class="td">₹<?php echo e(number_format($d['totalFee'], 2)); ?></td>
            <td class="td text-green-600">₹<?php echo e(number_format($d['totalPaid'], 2)); ?></td>
            <td class="td font-semibold text-<?php echo e($color); ?>-600">₹<?php echo e(number_format($d['balance'], 2)); ?></td>
            <td class="td"><?php echo e($d['daysSince']); ?> days</td>
            <td class="td">
              <a href="<?php echo e(route('fees.demand-notice', $d['student']->id)); ?>" class="btn btn-secondary btn-sm">Demand Notice</a>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  <?php if($defaulterList->isEmpty()): ?>
  <div class="card text-center py-12 text-slate-400">No defaulters found for the current academic year.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\defaulter-aging.blade.php ENDPATH**/ ?>