<?php $__env->startSection('title','HR & Payroll'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">HR & Payroll</h1>
      <p class="page-subtitle"><?php echo e(now()->format('F Y')); ?></p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <?php if($pendingLeaves > 0): ?>
      <a href="<?php echo e(route('hr.leaves')); ?>" class="btn btn-secondary btn-sm">
        Leave Requests
        <span class="ml-1 inline-flex items-center justify-center w-5 h-5 bg-amber-500 text-white text-xs rounded-full font-bold"><?php echo e($pendingLeaves); ?></span>
      </a>
      <?php endif; ?>
      <a href="<?php echo e(route('hr.payroll')); ?>" class="btn btn-secondary btn-sm">Run Payroll</a>
      <a href="<?php echo e(route('hr.employees.create')); ?>" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        Add Employee
      </a>
    </div>
  </div>

  
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
    <a href="<?php echo e(route('hr.employees')); ?>" class="card text-center py-4 hover:shadow-md hover:border-indigo-300 transition">
      <p class="text-2xl font-black text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['total']); ?></p>
      <p class="text-xs font-semibold text-slate-500 mt-1">Total Staff</p>
    </a>
    <a href="<?php echo e(route('hr.employees', ['type' => 'teaching'])); ?>" class="card text-center py-4 hover:shadow-md hover:border-indigo-300 transition">
      <p class="text-2xl font-black text-indigo-600" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['teaching']); ?></p>
      <p class="text-xs font-semibold text-indigo-600 mt-1">Teaching Staff</p>
    </a>
    <a href="<?php echo e(route('hr.employees', ['type' => 'non_teaching'])); ?>" class="card text-center py-4 hover:shadow-md hover:border-purple-300 transition">
      <p class="text-2xl font-black text-purple-600" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['non_teaching']); ?></p>
      <p class="text-xs font-semibold text-purple-600 mt-1">Non-Teaching</p>
    </a>
    <a href="<?php echo e(route('hr.employees', ['type' => 'driver'])); ?>" class="card text-center py-4 hover:shadow-md hover:border-amber-300 transition">
      <p class="text-2xl font-black text-amber-600" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['driver']); ?></p>
      <p class="text-xs font-semibold text-amber-600 mt-1">Drivers</p>
    </a>
    <a href="<?php echo e(route('hr.employees', ['type' => 'cleaner'])); ?>" class="card text-center py-4 hover:shadow-md hover:border-teal-300 transition">
      <p class="text-2xl font-black text-teal-600" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['cleaner']); ?></p>
      <p class="text-xs font-semibold text-teal-600 mt-1">Cleaners</p>
    </a>
    <a href="<?php echo e(route('hr.employees', ['type' => 'nanny'])); ?>" class="card text-center py-4 hover:shadow-md hover:border-rose-300 transition">
      <p class="text-2xl font-black text-rose-600" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['nanny']); ?></p>
      <p class="text-xs font-semibold text-rose-600 mt-1">Nannies (Naanis)</p>
    </a>
  </div>

  
  <div class="space-y-3">
    <?php if($pendingLeaves > 0): ?>
    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 flex items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm font-semibold text-amber-800"><?php echo e($pendingLeaves); ?> leave request<?php echo e($pendingLeaves > 1 ? 's' : ''); ?> pending approval</p>
      </div>
      <a href="<?php echo e(route('hr.leaves')); ?>" class="btn btn-sm bg-amber-500 hover:bg-amber-600 text-white flex-shrink-0">Review</a>
    </div>
    <?php endif; ?>

    <?php
      $payrollPct = $payrollTotal > 0 ? round($payrollProcessed / $payrollTotal * 100) : 0;
    ?>
    <?php if($payrollProcessed < $payrollTotal): ?>
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 flex items-center justify-between gap-4">
      <div class="flex items-center gap-3 flex-1">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-blue-800"><?php echo e(now()->format('F Y')); ?> Payroll: <?php echo e($payrollProcessed); ?>/<?php echo e($payrollTotal); ?> processed (<?php echo e($payrollPct); ?>%)</p>
          <div class="mt-1.5 w-full bg-blue-200 rounded-full h-1.5">
            <div class="h-1.5 rounded-full bg-blue-600" style="width: <?php echo e($payrollPct); ?>%"></div>
          </div>
        </div>
      </div>
      <a href="<?php echo e(route('hr.payroll')); ?>" class="btn btn-sm bg-blue-600 hover:bg-blue-700 text-white flex-shrink-0">Process</a>
    </div>
    <?php else: ?>
    <div class="rounded-xl border border-green-200 bg-green-50 p-3 flex items-center gap-3">
      <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <p class="text-sm font-semibold text-green-700"><?php echo e(now()->format('F Y')); ?> payroll fully processed</p>
    </div>
    <?php endif; ?>
  </div>

  
  <?php if($todayPresent + $todayAbsent > 0): ?>
  <div class="card">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold text-slate-700 text-sm">Today's Staff Attendance</h3>
      <a href="<?php echo e(route('attendance.staff')); ?>" class="text-xs text-blue-600 hover:underline">Mark / View →</a>
    </div>
    <div class="flex items-center gap-6">
      <div class="text-center">
        <p class="text-2xl font-bold text-green-600"><?php echo e($todayPresent); ?></p>
        <p class="text-xs text-slate-400">Present</p>
      </div>
      <div class="text-center">
        <p class="text-2xl font-bold text-red-500"><?php echo e($todayAbsent); ?></p>
        <p class="text-xs text-slate-400">Absent</p>
      </div>
      <div class="flex-1">
        <?php $staffPct = ($todayPresent + $todayAbsent) > 0 ? round($todayPresent / ($todayPresent + $todayAbsent) * 100) : 0; ?>
        <div class="flex justify-between text-xs text-slate-500 mb-1">
          <span>Attendance rate</span>
          <span class="font-bold <?php echo e($staffPct >= 90 ? 'text-green-600' : 'text-amber-600'); ?>"><?php echo e($staffPct); ?>%</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2">
          <div class="h-2 rounded-full <?php echo e($staffPct >= 90 ? 'bg-green-500' : 'bg-amber-500'); ?>" style="width: <?php echo e($staffPct); ?>%"></div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
    <?php $__currentLoopData = [
      ['Employees',       'hr.employees',      'from-teal-500 to-cyan-600',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'],
      ['Payroll',         'hr.payroll',        'from-indigo-500 to-blue-600',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
      ['Leave Requests',  'hr.leaves',         'from-amber-500 to-orange-500', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
      ['Departments',     'hr.departments',    'from-green-500 to-emerald-600','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
      ['Salary Register', 'hr.payroll.salary-register', 'from-violet-500 to-purple-600','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
      ['Staff Attendance','attendance.staff',  'from-slate-500 to-gray-600',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$route,$color,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route($route)); ?>" class="card-flat flex items-center gap-4 py-5 px-5 hover:shadow-card-md transition">
      <div class="w-11 h-11 rounded-2xl bg-gradient-to-br <?php echo e($color); ?> flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $icon; ?></svg>
      </div>
      <div>
        <span class="font-semibold text-slate-700"><?php echo e($label); ?></span>
        <?php if($label === 'Leave Requests' && $pendingLeaves > 0): ?>
        <span class="ml-2 inline-flex items-center justify-center w-5 h-5 bg-amber-500 text-white text-xs rounded-full font-bold"><?php echo e($pendingLeaves); ?></span>
        <?php endif; ?>
      </div>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  
  <?php if($recentHires->count()): ?>
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">New Joiners — Last 30 Days</h3>
    <div class="space-y-3">
      <?php $__currentLoopData = $recentHires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-100 to-blue-100 flex items-center justify-center flex-shrink-0">
          <span class="text-indigo-700 text-xs font-bold"><?php echo e(strtoupper(substr($emp->first_name,0,1).substr($emp->last_name,0,1))); ?></span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-medium text-slate-800 text-sm truncate"><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></p>
          <p class="text-xs text-slate-400"><?php echo e($emp->designation ?? ucfirst(str_replace('_',' ',$emp->employee_type ?? ''))); ?></p>
        </div>
        <div class="text-right flex-shrink-0">
          <p class="text-xs font-medium text-slate-600"><?php echo e(\Carbon\Carbon::parse($emp->joining_date)->format('d M Y')); ?></p>
          <a href="<?php echo e(route('hr.employees.show', $emp->id)); ?>" class="text-xs text-blue-600 hover:underline">View →</a>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\index.blade.php ENDPATH**/ ?>