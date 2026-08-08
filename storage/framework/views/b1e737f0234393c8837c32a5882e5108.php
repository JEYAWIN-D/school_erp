<?php $__env->startSection('title', 'Bus Attendance Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Bus Attendance Report</h1>
    <div class="flex gap-2">
      <?php if($vehicleId): ?>
      <a href="<?php echo e(route('transport.bus-attendance.report.excel', ['vehicle_id' => $vehicleId, 'from' => $from, 'to' => $to])); ?>"
        class="btn-sm btn-secondary">Export Excel</a>
      <?php endif; ?>
      <a href="<?php echo e(route('transport.bus-attendance')); ?>" class="btn-sm btn-secondary">← Register</a>
    </div>
  </div>

  <form method="GET" class="card">
    <div class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Vehicle</label>
        <select name="vehicle_id" class="select w-44">
          <option value="">Select Vehicle</option>
          <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($v->id); ?>" <?php echo e($vehicleId == $v->id ? 'selected' : ''); ?>>
              <?php echo e($v->vehicle_number); ?> <?php echo e($v->name ? '— '.$v->name : ''); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">From</label>
        <input type="date" name="from" class="input w-36" value="<?php echo e($from); ?>">
      </div>
      <div>
        <label class="label">To</label>
        <input type="date" name="to" class="input w-36" value="<?php echo e($to); ?>">
      </div>
      <button type="submit" class="btn-primary">Generate</button>
    </div>
  </form>

  <?php if($vehicleId && $records->isNotEmpty()): ?>
  <div class="card">
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">Student</th>
            <th class="th text-center">Total Days</th>
            <th class="th text-center text-green-700">Present</th>
            <th class="th text-center text-red-600">Absent</th>
            <th class="th text-center">Attendance %</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $pct = $row['total'] > 0 ? round($row['present'] / $row['total'] * 100) : 0; ?>
          <tr class="tr">
            <td class="td">
              <div class="font-medium"><?php echo e($row['student']?->full_name); ?></div>
              <div class="text-xs text-slate-400"><?php echo e($row['student']?->admission_number); ?></div>
            </td>
            <td class="td text-center"><?php echo e($row['total']); ?></td>
            <td class="td text-center text-green-700 font-semibold"><?php echo e($row['present']); ?></td>
            <td class="td text-center text-red-600 font-semibold"><?php echo e($row['absent']); ?></td>
            <td class="td text-center">
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-200 rounded-full h-2">
                  <div class="h-2 rounded-full <?php echo e($pct >= 75 ? 'bg-green-500' : ($pct >= 50 ? 'bg-yellow-500' : 'bg-red-500')); ?>"
                    style="width: <?php echo e($pct); ?>%"></div>
                </div>
                <span class="text-xs font-semibold w-10 text-right"><?php echo e($pct); ?>%</span>
              </div>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php elseif($vehicleId): ?>
    <div class="card"><p class="text-slate-400 text-sm">No attendance records found for the selected vehicle and date range.</p></div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\bus-attendance-report.blade.php ENDPATH**/ ?>