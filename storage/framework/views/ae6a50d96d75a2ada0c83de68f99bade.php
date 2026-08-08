<?php $__env->startSection('title', 'Teacher Attendance Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Teacher Attendance Report</h1>
    <?php if($report->count()): ?>
    <a href="<?php echo e(request()->fullUrlWithQuery(['format' => 'print'])); ?>" target="_blank" class="btn btn-secondary flex items-center gap-1">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
      Print
    </a>
    <?php endif; ?>
  </div>

  
  <form method="GET" class="card grid grid-cols-2 md:grid-cols-4 gap-4">
    <div>
      <label class="label">Month</label>
      <input type="month" name="month" value="<?php echo e($month); ?>" class="input">
    </div>
    <div>
      <label class="label">Department</label>
      <select name="department_id" class="select">
        <option value="">All Departments</option>
        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($dept->id); ?>" <?php if(request('department_id') == $dept->id): echo 'selected'; endif; ?>><?php echo e($dept->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="flex items-end gap-2 col-span-2">
      <button type="submit" name="action" value="1" class="btn btn-primary">Generate Report</button>
      <a href="<?php echo e(route('attendance.teacher-report')); ?>" class="btn btn-secondary">Reset</a>
    </div>
  </form>

  <?php if($report->count()): ?>
  
  <?php
    $avgPct    = $report->avg('percentage');
    $below75   = $report->where('percentage', '<', 75)->count();
    $perfect   = $report->where('percentage', '>=', 95)->count();
  ?>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="card text-center">
      <div class="text-2xl font-bold text-indigo-600"><?php echo e($report->count()); ?></div>
      <div class="text-xs text-slate-500 mt-1">Total Employees</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-emerald-600"><?php echo e(number_format($avgPct, 1)); ?>%</div>
      <div class="text-xs text-slate-500 mt-1">Avg Attendance</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-red-500"><?php echo e($below75); ?></div>
      <div class="text-xs text-slate-500 mt-1">Below 75%</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-green-600"><?php echo e($perfect); ?></div>
      <div class="text-xs text-slate-500 mt-1">Above 95%</div>
    </div>
  </div>

  
  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Employee</th>
          <th class="th">Department</th>
          <th class="th">Designation</th>
          <th class="th text-center">Working Days</th>
          <th class="th text-center">Present</th>
          <th class="th text-center">Half Day</th>
          <th class="th text-center">On Leave</th>
          <th class="th text-center">Absent</th>
          <th class="th text-center">Unmarked</th>
          <th class="th text-center">Attendance %</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $pct = $row['percentage'];
          $color = $pct >= 90 ? 'text-green-600' : ($pct >= 75 ? 'text-yellow-600' : 'text-red-600');
          $bg    = $pct < 75 ? 'bg-red-50' : '';
        ?>
        <tr class="tr <?php echo e($bg); ?>">
          <td class="td text-slate-400"><?php echo e($i + 1); ?></td>
          <td class="td">
            <div class="font-medium"><?php echo e($row['employee']->full_name); ?></div>
            <div class="text-xs text-slate-400"><?php echo e($row['employee']->employee_id); ?></div>
          </td>
          <td class="td text-sm"><?php echo e($row['employee']->department?->name ?? '—'); ?></td>
          <td class="td text-sm"><?php echo e($row['employee']->designation?->name ?? '—'); ?></td>
          <td class="td text-center"><?php echo e($row['working']); ?></td>
          <td class="td text-center text-green-600 font-medium"><?php echo e($row['present']); ?></td>
          <td class="td text-center text-blue-500"><?php echo e($row['half_day']); ?></td>
          <td class="td text-center text-yellow-600"><?php echo e($row['on_leave']); ?></td>
          <td class="td text-center text-red-500 font-medium"><?php echo e($row['absent']); ?></td>
          <td class="td text-center text-slate-400"><?php echo e($row['unmarked']); ?></td>
          <td class="td text-center">
            <span class="font-bold <?php echo e($color); ?>"><?php echo e($pct); ?>%</span>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1">
              <div class="h-1.5 rounded-full <?php echo e($pct >= 90 ? 'bg-green-500' : ($pct >= 75 ? 'bg-yellow-400' : 'bg-red-400')); ?>"
                   style="width: <?php echo e(min($pct, 100)); ?>%"></div>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
      <tfoot>
        <tr class="bg-slate-50 font-semibold">
          <td class="td" colspan="4">Total / Average</td>
          <td class="td text-center">—</td>
          <td class="td text-center"><?php echo e($report->sum('present')); ?></td>
          <td class="td text-center"><?php echo e($report->sum('half_day')); ?></td>
          <td class="td text-center"><?php echo e($report->sum('on_leave')); ?></td>
          <td class="td text-center"><?php echo e($report->sum('absent')); ?></td>
          <td class="td text-center"><?php echo e($report->sum('unmarked')); ?></td>
          <td class="td text-center"><?php echo e(number_format($avgPct, 1)); ?>%</td>
        </tr>
      </tfoot>
    </table>
  </div>

  <?php if($below75 > 0): ?>
  <div class="alert-warning text-sm">
    <strong><?php echo e($below75); ?> employee(s)</strong> have attendance below 75% for <?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y')); ?>.
    Consider issuing leave shortage notices.
  </div>
  <?php endif; ?>

  <?php else: ?>
    <?php if(request()->has('action') || request()->has('month')): ?>
    <div class="card text-center text-slate-500 py-10">No attendance data found for the selected filters.</div>
    <?php else: ?>
    <div class="card text-center text-slate-400 py-10">Select a month and click <strong>Generate Report</strong> to view teacher attendance.</div>
    <?php endif; ?>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\teacher-report.blade.php ENDPATH**/ ?>