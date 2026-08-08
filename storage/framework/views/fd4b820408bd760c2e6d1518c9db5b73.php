<?php $__env->startSection('title', 'Payroll'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Payroll</h1>
    <div class="flex gap-2">
        <a href="<?php echo e(route('hr.payroll.department-summary', ['month' => $month])); ?>" class="btn btn-secondary">Dept Summary</a>
        <a href="<?php echo e(route('hr.payroll.bulk-payslip', ['month' => $month])); ?>" class="btn btn-secondary">Bulk Payslip PDF</a>
    </div>
  </div>
  <div class="card-flat py-4">
    <div class="flex flex-wrap gap-3">
      <form method="GET" class="flex gap-2">
        <input type="month" name="month" value="<?php echo e($month); ?>" class="input w-44">
        <button type="submit" class="btn btn-secondary btn-sm">Load</button>
      </form>
      <form method="POST" action="<?php echo e(route('hr.payroll.process')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="month" value="<?php echo e($month); ?>">
        <button type="submit" class="btn btn-primary btn-sm">Generate Payroll</button>
      </form>
      <?php if($records->where('status', 'draft')->count() > 0): ?>
      <form method="POST" action="<?php echo e(route('hr.payroll.approve')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="month" value="<?php echo e($month); ?>">
        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve and lock all payroll for <?php echo e($month); ?>?')">
          Approve &amp; Lock Payroll
        </button>
      </form>
      <?php endif; ?>
    </div>
  </div>
  <?php if($records->where('is_locked', true)->count() > 0): ?>
  <div class="alert-warning text-sm">
    <?php echo e($records->where('is_locked', true)->count()); ?> record(s) are locked for <?php echo e($month); ?>.
  </div>
  <?php endif; ?>
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr><th class="th">Employee</th><th class="th">Working Days</th><th class="th">Present</th><th class="th">Gross</th><th class="th">Deductions</th><th class="th">Net Salary</th><th class="th">Status</th><th class="th">Actions</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr <?php echo e($r->is_locked ? 'bg-slate-50' : ''); ?>">
            <td class="td font-medium"><?php echo e($r->employee?->first_name); ?> <?php echo e($r->employee?->last_name); ?></td>
            <td class="td"><?php echo e($r->working_days); ?></td>
            <td class="td"><?php echo e($r->present_days); ?></td>
            <td class="td">₹<?php echo e(number_format($r->gross_salary, 2)); ?></td>
            <td class="td">₹<?php echo e(number_format($r->deductions, 2)); ?></td>
            <td class="td font-semibold text-green-700">₹<?php echo e(number_format($r->net_salary, 2)); ?></td>
            <td class="td"><span class="<?php echo e($r->status === 'paid' ? 'badge-green' : ($r->status === 'approved' ? 'badge-blue' : 'badge-slate')); ?>"><?php echo e(ucfirst($r->status)); ?></span></td>
            <td class="td">
              <div class="flex flex-wrap gap-1">
                <a href="<?php echo e(route('hr.payroll.payslip', $r->id)); ?>" class="btn btn-ghost btn-xs" title="Download PDF">PDF</a>
                <a href="<?php echo e(route('hr.payroll.payslip-protected', $r->id)); ?>" class="btn btn-ghost btn-xs" title="Download password-protected ZIP">🔒 ZIP</a>
                <form method="POST" action="<?php echo e(route('hr.payroll.email-payslip', $r->id)); ?>" class="inline"
                  onsubmit="return confirm('Email payslip to <?php echo e($r->employee?->official_email ?? $r->employee?->personal_email ?? 'employee'); ?>?')">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-ghost btn-xs text-indigo-500" title="Email payslip">✉ Email</button>
                </form>
                <?php if($r->is_locked): ?>
                <form method="POST" action="<?php echo e(route('hr.payroll.unlock', $r->id)); ?>" class="inline">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-secondary btn-xs">Unlock</button>
                </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="8" class="td text-center py-10 text-slate-400">No payroll for <?php echo e($month); ?>. Click "Generate Payroll".</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\payroll.blade.php ENDPATH**/ ?>