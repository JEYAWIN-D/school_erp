<?php $__env->startSection('title', 'Leave Balance Tracker'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Leave Balance Tracker</h1>
      <p class="page-subtitle">Remaining leave entitlements for <?php echo e($year); ?></p>
    </div>
    <a href="<?php echo e(route('hr.leaves')); ?>" class="btn btn-secondary">All Leave Requests</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Year</label>
        <select name="year" class="select">
          <?php for($y = now()->year; $y >= now()->year - 3; $y--): ?>
          <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div>
        <label class="label">Department</label>
        <select name="department" class="select">
          <option value="">All Departments</option>
          <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($dept); ?>" <?php echo e(request('department') == $dept ? 'selected' : ''); ?>><?php echo e($dept); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
  </div>

  
  <div class="card" x-data="{ showCF: false }">
    <button @click="showCF = !showCF" class="text-sm font-semibold text-indigo-600 flex items-center gap-1">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
      Carry Forward Leaves
    </button>
    <div x-show="showCF" x-transition class="mt-4 pt-4 border-t">
      <form method="POST" action="<?php echo e(route('hr.leaves.carry-forward')); ?>" class="flex flex-wrap gap-4 items-end">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">From Year</label>
          <input type="number" name="from_year" class="input w-28" value="<?php echo e($year); ?>" required>
        </div>
        <div>
          <label class="label">To Year</label>
          <input type="number" name="to_year" class="input w-28" value="<?php echo e($year + 1); ?>" required>
        </div>
        <button type="submit" class="btn btn-warning" onclick="return confirm('This will create carry-forward leave entries for all active employees. Continue?')">
          Run Carry-Forward
        </button>
      </form>
    </div>
  </div>

  <div class="card overflow-x-auto">
    <table class="w-full min-w-max">
      <thead>
        <tr>
          <th class="th">Employee</th>
          <th class="th">Dept</th>
          <?php $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <th class="th text-center" colspan="2"><?php echo e($lt->name); ?><br><span class="text-xs font-normal opacity-70">(<?php echo e($lt->days_allowed); ?> days)</span></th>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
        <tr>
          <th class="th" colspan="2"></th>
          <?php $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <th class="th text-center text-xs">Used</th>
          <th class="th text-center text-xs">Balance</th>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="tr">
          <td class="td">
            <a href="<?php echo e(route('hr.employees.show', $emp->id)); ?>" class="font-medium text-indigo-600 hover:underline">
              <?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?>

            </a>
            <div class="text-xs text-slate-400"><?php echo e($emp->employee_number); ?></div>
          </td>
          <td class="td text-slate-500 text-sm"><?php echo e($emp->department ?? '—'); ?></td>
          <?php $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $used    = $usedMap[$emp->id][$lt->id]->total_days ?? 0;
            $balance = max(0, $lt->days_allowed - $used);
          ?>
          <td class="td text-center text-sm <?php echo e($used > $lt->days_allowed ? 'text-red-600 font-bold' : ''); ?>"><?php echo e($used); ?></td>
          <td class="td text-center">
            <span class="text-sm font-semibold <?php echo e($balance <= 2 ? 'text-red-500' : ($balance <= 5 ? 'text-amber-500' : 'text-green-600')); ?>"><?php echo e($balance); ?></span>
          </td>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($employees->isEmpty()): ?>
        <tr><td colspan="<?php echo e(2 + $leaveTypes->count() * 2); ?>" class="td text-center py-8 text-slate-400">No employees found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\leave-balance.blade.php ENDPATH**/ ?>