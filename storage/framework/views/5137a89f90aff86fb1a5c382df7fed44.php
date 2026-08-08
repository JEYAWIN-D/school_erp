<?php $__env->startSection('title','Vehicle Maintenance Cost Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Maintenance Cost per Vehicle</h1>
      <p class="page-subtitle">Annual breakdown of maintenance expenses across fleet</p>
    </div>
    <div class="flex gap-2">
      <a href="<?php echo e(route('transport.maintenance-cost.pdf', ['year' => $year])); ?>" target="_blank" class="btn btn-secondary btn-sm">Export PDF</a>
      <a href="<?php echo e(route('transport.maintenance-cost.excel', ['year' => $year])); ?>" class="btn btn-secondary btn-sm">Export Excel</a>
      <a href="<?php echo e(route('transport.maintenance')); ?>" class="btn btn-secondary btn-sm">← Maintenance Logs</a>
    </div>
  </div>

  <form method="GET" class="card-flat py-3"><div class="flex gap-3 items-end">
    <div>
      <label class="label">Year</label>
      <select name="year" class="select w-32" onchange="this.form.submit()">
        <?php $__currentLoopData = range(now()->year, now()->year - 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($y); ?>" <?php if($y == $year): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
  </div></form>

  
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-slate-700">₹<?php echo e(number_format($grandTotal, 0)); ?></p>
      <p class="text-xs text-slate-400 mt-1">Total Maintenance Cost <?php echo e($year); ?></p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-indigo-600"><?php echo e($perVehicle->count()); ?></p>
      <p class="text-xs text-slate-400 mt-1">Vehicles in Fleet</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-red-600"><?php echo e($perVehicle->sum('breakdown_count')); ?></p>
      <p class="text-xs text-slate-400 mt-1">Breakdown Incidents</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-green-600"><?php echo e($perVehicle->sum('scheduled_count')); ?></p>
      <p class="text-xs text-slate-400 mt-1">Scheduled Services</p>
    </div>
  </div>

  
  <?php $__empty_1 = true; $__currentLoopData = $perVehicle->filter(fn($v) => $v['total'] > 0); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="font-semibold text-slate-800"><?php echo e($row['vehicle']->vehicle_number); ?></h3>
        <p class="text-xs text-slate-400"><?php echo e($row['vehicle']->make ?? ''); ?> <?php echo e($row['vehicle']->model ?? ''); ?> &nbsp;|&nbsp; <?php echo e($row['vehicle']->vehicle_type ?? ''); ?></p>
      </div>
      <div class="text-right">
        <p class="text-xl font-bold text-indigo-600">₹<?php echo e(number_format($row['total'], 0)); ?></p>
        <p class="text-xs text-slate-400">Total for <?php echo e($year); ?></p>
      </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-4 text-sm text-center">
      <div class="bg-slate-50 rounded-lg p-3">
        <p class="text-lg font-bold text-red-600"><?php echo e($row['breakdown_count']); ?></p>
        <p class="text-xs text-slate-400">Breakdowns</p>
      </div>
      <div class="bg-slate-50 rounded-lg p-3">
        <p class="text-lg font-bold text-green-600"><?php echo e($row['scheduled_count']); ?></p>
        <p class="text-xs text-slate-400">Scheduled</p>
      </div>
      <div class="bg-slate-50 rounded-lg p-3">
        <p class="text-lg font-bold text-slate-600"><?php echo e($row['logs']->count()); ?></p>
        <p class="text-xs text-slate-400">Total Jobs</p>
      </div>
    </div>

    
    <?php if($row['by_month']->isNotEmpty()): ?>
    <div class="mb-4">
      <p class="text-xs text-slate-400 mb-2">Monthly cost distribution</p>
      <div class="flex items-end gap-1 h-10">
        <?php $maxMonth = $row['by_month']->max() ?: 1; ?>
        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $val = $row['by_month'][$num] ?? 0; $h = round(($val/$maxMonth)*40); ?>
          <div class="flex-1 flex flex-col items-center gap-0.5" title="<?php echo e($name); ?>: ₹<?php echo e(number_format($val,0)); ?>">
            <div class="w-full rounded-sm bg-indigo-400" style="height: <?php echo e($h > 0 ? max($h, 2) : 0); ?>px"></div>
            <span class="text-slate-400" style="font-size:7px"><?php echo e($name); ?></span>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
    <?php endif; ?>

    
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Date</th>
          <th class="th">Type</th>
          <th class="th">Work Done</th>
          <th class="th">Vendor</th>
          <th class="th text-right">Cost</th>
          <th class="th">Next Service</th>
        </tr></thead>
        <tbody>
          <?php $__currentLoopData = $row['logs']->sortByDesc('maintenance_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td text-xs"><?php echo e(\Carbon\Carbon::parse($log->maintenance_date)->format('d M Y')); ?></td>
            <td class="td">
              <span class="badge-<?php echo e($log->maintenance_type==='breakdown'?'red':'green'); ?> text-xs capitalize">
                <?php echo e(ucfirst($log->maintenance_type)); ?>

              </span>
            </td>
            <td class="td text-xs text-slate-600 max-w-xs truncate"><?php echo e($log->work_done); ?></td>
            <td class="td text-xs text-slate-500"><?php echo e($log->vendor ?? '—'); ?></td>
            <td class="td text-right font-semibold text-sm">₹<?php echo e(number_format($log->cost, 0)); ?></td>
            <td class="td text-xs text-slate-400">
              <?php if($log->next_service_date): ?>
                <?php $next = \Carbon\Carbon::parse($log->next_service_date); ?>
                <span class="<?php echo e($next->isPast() ? 'text-red-500 font-semibold' : 'text-slate-500'); ?>">
                  <?php echo e($next->format('d M Y')); ?>

                  <?php if($next->isPast()): ?> (overdue)<?php endif; ?>
                </span>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
          <tr class="bg-slate-50">
            <td colspan="4" class="td font-semibold">Vehicle Total</td>
            <td class="td text-right font-bold text-indigo-700">₹<?php echo e(number_format($row['total'], 0)); ?></td>
            <td class="td"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  <div class="card text-center py-12 text-slate-400">
    No maintenance records found for <?php echo e($year); ?>.
  </div>
  <?php endif; ?>

  
  <?php if($perVehicle->filter(fn($v) => $v['total'] == 0)->count() > 0): ?>
  <div class="card">
    <h3 class="font-semibold text-slate-600 mb-3 text-sm">No Maintenance Recorded in <?php echo e($year); ?></h3>
    <div class="flex flex-wrap gap-2">
      <?php $__currentLoopData = $perVehicle->filter(fn($v) => $v['total'] == 0); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <span class="badge-slate text-xs"><?php echo e($row['vehicle']->vehicle_number); ?></span>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\maintenance-cost.blade.php ENDPATH**/ ?>