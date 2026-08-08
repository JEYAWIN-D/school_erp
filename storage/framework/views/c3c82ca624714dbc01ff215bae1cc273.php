<?php $__env->startSection('title', 'Staff Attendance Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Staff Attendance Dashboard</h1>
    <div class="text-sm text-slate-500"><?php echo e(\Carbon\Carbon::parse($today)->format('l, d M Y')); ?></div>
  </div>

  
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
    <?php
      $cards = [
        ['label'=>'Total Staff',  'value'=>$stats['total'],      'color'=>'slate'],
        ['label'=>'Present',      'value'=>$stats['present'],     'color'=>'green'],
        ['label'=>'Absent',       'value'=>$stats['absent'],      'color'=>'red'],
        ['label'=>'On Leave',     'value'=>$stats['on_leave'],    'color'=>'amber'],
        ['label'=>'Half Day',     'value'=>$stats['half_day'],    'color'=>'blue'],
        ['label'=>'Not Marked',   'value'=>$stats['not_marked'],  'color'=>'purple'],
      ];
    ?>
    <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card text-center">
      <div class="text-2xl font-bold text-slate-800"><?php echo e($c['value']); ?></div>
      <div class="text-xs text-slate-500 mt-1"><?php echo e($c['label']); ?></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <?php
    $attendancePct = $stats['total'] > 0 ? round($stats['present'] / $stats['total'] * 100) : 0;
  ?>
  <div class="card">
    <div class="flex items-center justify-between mb-2">
      <span class="text-sm font-medium text-slate-700">Attendance Rate Today</span>
      <span class="font-bold <?php echo e($attendancePct >= 90 ? 'text-green-600' : ($attendancePct >= 75 ? 'text-amber-600' : 'text-red-600')); ?>"><?php echo e($attendancePct); ?>%</span>
    </div>
    <div class="w-full bg-slate-100 rounded-full h-3">
      <div class="h-3 rounded-full <?php echo e($attendancePct >= 90 ? 'bg-green-500' : ($attendancePct >= 75 ? 'bg-amber-400' : 'bg-red-500')); ?>" style="width:<?php echo e($attendancePct); ?>%"></div>
    </div>
  </div>

  
  <div class="card">
    <h2 class="font-semibold text-slate-800 mb-4">Department-wise Summary</h2>
    <div class="table-wrap">
      <table class="min-w-full text-sm">
        <thead><tr>
          <th class="th">Department</th>
          <th class="th text-right">Total</th>
          <th class="th text-right">Present</th>
          <th class="th text-right">Absent</th>
          <th class="th text-right">Not Marked</th>
          <th class="th">Rate</th>
        </tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $departmentStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php $notMarkedDept = $dept->total_count - $dept->present_count - $dept->absent_count; $deptPct = $dept->total_count > 0 ? round($dept->present_count/$dept->total_count*100) : 0; ?>
          <tr class="tr">
            <td class="td font-medium"><?php echo e($dept->name); ?></td>
            <td class="td text-right"><?php echo e($dept->total_count); ?></td>
            <td class="td text-right text-green-600 font-medium"><?php echo e($dept->present_count); ?></td>
            <td class="td text-right text-red-500"><?php echo e($dept->absent_count); ?></td>
            <td class="td text-right text-slate-400"><?php echo e(max(0,$notMarkedDept)); ?></td>
            <td class="td w-32">
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-100 rounded-full h-2">
                  <div class="h-2 rounded-full bg-green-500" style="width:<?php echo e($deptPct); ?>%"></div>
                </div>
                <span class="text-xs text-slate-500 w-8"><?php echo e($deptPct); ?>%</span>
              </div>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="6" class="td text-center text-slate-400">No departments.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    
    <div class="card">
      <h2 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
        Absent Today <span class="badge-red"><?php echo e($absentToday->count()); ?></span>
      </h2>
      <?php $__empty_1 = true; $__currentLoopData = $absentToday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
        <div>
          <div class="font-medium text-sm text-slate-800"><?php echo e($emp->full_name); ?></div>
          <div class="text-xs text-slate-500"><?php echo e($emp->department?->name); ?></div>
        </div>
        <span class="badge-red text-xs">Absent</span>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="text-slate-400 text-sm">No one is absent today.</p>
      <?php endif; ?>
    </div>

    
    <div class="card">
      <h2 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
        Attendance Not Marked <span class="badge-amber"><?php echo e($notMarked->count()); ?></span>
      </h2>
      <?php $__empty_1 = true; $__currentLoopData = $notMarked->take(15); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
        <div>
          <div class="font-medium text-sm text-slate-800"><?php echo e($emp->full_name); ?></div>
          <div class="text-xs text-slate-500"><?php echo e($emp->department?->name); ?></div>
        </div>
        <span class="badge-slate text-xs">Pending</span>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="text-slate-400 text-sm">All attendance marked!</p>
      <?php endif; ?>
      <?php if($notMarked->count() > 15): ?>
      <p class="text-xs text-slate-400 mt-2">...and <?php echo e($notMarked->count() - 15); ?> more</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="flex gap-3">
    <a href="<?php echo e(route('attendance.staff')); ?>" class="btn btn-primary btn-sm">Mark Staff Attendance</a>
    <a href="<?php echo e(route('attendance.staff.register')); ?>" class="btn btn-secondary btn-sm">Monthly Register</a>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\staff-dashboard.blade.php ENDPATH**/ ?>