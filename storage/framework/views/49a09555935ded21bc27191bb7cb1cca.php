<?php $__env->startSection('title', 'Mess Attendance Summary'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Mess Attendance Summary</h1>
      <p class="page-subtitle">Monthly meal-wise attendance overview</p>
    </div>
    <a href="<?php echo e(route('hostel.mess-attendance')); ?>" class="btn btn-secondary btn-sm">← Daily Attendance</a>
  </div>

  <div class="card-flat py-4">
    <form method="GET" class="flex gap-3 items-end flex-wrap">
      <div>
        <label class="label">Month</label>
        <input type="month" name="month" value="<?php echo e($month); ?>" class="input" onchange="this.form.submit()">
      </div>
      <?php if($hostels->count() > 1): ?>
      <div>
        <label class="label">Hostel</label>
        <select name="hostel_id" class="select" onchange="this.form.submit()">
          <option value="">All</option>
          <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($h->id); ?>" <?php if(request('hostel_id')==$h->id): echo 'selected'; endif; ?>><?php echo e($h->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <?php endif; ?>
    </form>
  </div>

  <?php if(empty($summaryData)): ?>
    <div class="card text-center py-10 text-slate-400">No mess attendance data for this month.</div>
  <?php else: ?>
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr>
        <th class="th">Student</th>
        <th class="th">Room</th>
        <th class="th text-center">Total Records</th>
        <th class="th text-center">Present</th>
        <th class="th text-center">Absent</th>
        <th class="th text-center">Attendance %</th>
      </tr></thead>
      <tbody>
        <?php $__currentLoopData = $summaryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $pct = $row['total'] > 0 ? round($row['present']/$row['total']*100,1) : null; ?>
        <tr class="tr">
          <td class="td font-medium"><?php echo e($row['student']?->full_name ?? '—'); ?></td>
          <td class="td text-slate-500 text-sm"><?php echo e($row['allotment']->room?->room_number ?? '—'); ?></td>
          <td class="td text-center"><?php echo e($row['total']); ?></td>
          <td class="td text-center text-green-700 font-semibold"><?php echo e($row['present']); ?></td>
          <td class="td text-center <?php echo e($row['absent'] > 0 ? 'text-red-600 font-semibold' : 'text-slate-400'); ?>"><?php echo e($row['absent'] ?: '—'); ?></td>
          <td class="td text-center">
            <?php if($pct !== null): ?>
              <span class="<?php echo e($pct >= 80 ? 'badge-green' : ($pct >= 60 ? 'badge-yellow' : 'badge-red')); ?>"><?php echo e($pct); ?>%</span>
            <?php else: ?>
              <span class="text-slate-400">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\mess-attendance-summary.blade.php ENDPATH**/ ?>