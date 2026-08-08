<?php $__env->startSection('title', 'TDS Computation'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">TDS Computation</h1>
    <?php if($tdsReport->count()): ?>
    <a href="<?php echo e(request()->fullUrlWithQuery(['format' => 'print'])); ?>" target="_blank" class="btn btn-secondary btn-sm">Print</a>
    <?php endif; ?>
  </div>

  
  <form method="GET" class="card grid grid-cols-2 md:grid-cols-3 gap-4">
    <div>
      <label class="label">Financial Year</label>
      <select name="financial_year" class="select">
        <?php $currentY = date('Y'); ?>
        <?php $__currentLoopData = range($currentY - 2, $currentY + 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($y); ?>-<?php echo e($y+1); ?>" <?php if($financialYear === "$y-" . ($y+1)): echo 'selected'; endif; ?>>FY <?php echo e($y); ?>-<?php echo e($y+1); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
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
    <div class="flex items-end gap-2">
      <button type="submit" name="action" value="1" class="btn btn-primary">Compute TDS</button>
      <a href="<?php echo e(route('hr.tds-computation')); ?>" class="btn btn-secondary">Reset</a>
    </div>
  </form>

  <?php if($tdsReport->count()): ?>
  
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="card text-center">
      <div class="text-2xl font-bold text-indigo-600"><?php echo e($tdsReport->count()); ?></div>
      <div class="text-xs text-slate-500 mt-1">Employees</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-slate-700">₹<?php echo e(number_format($tdsReport->sum('annual_tax'), 0)); ?></div>
      <div class="text-xs text-slate-500 mt-1">Total Annual TDS</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-green-600">₹<?php echo e(number_format($tdsReport->sum('tds_paid'), 0)); ?></div>
      <div class="text-xs text-slate-500 mt-1">TDS Paid YTD</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-red-500">₹<?php echo e(number_format($tdsReport->sum('balance'), 0)); ?></div>
      <div class="text-xs text-slate-500 mt-1">Outstanding TDS</div>
    </div>
  </div>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">Employee</th>
          <th class="th">Regime</th>
          <th class="th text-right">Annual Gross</th>
          <th class="th text-right">Std. Ded.</th>
          <th class="th text-right">80C</th>
          <th class="th text-right">80D</th>
          <th class="th text-right">HRA Exempt</th>
          <th class="th text-right">Taxable</th>
          <th class="th text-right">Annual Tax</th>
          <th class="th text-right">Monthly TDS</th>
          <th class="th text-right">Paid</th>
          <th class="th text-right">Balance</th>
          <th class="th text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $tdsReport; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="tr <?php echo e($row['balance'] > 0 ? 'bg-amber-50' : ''); ?>">
          <td class="td">
            <div class="font-medium"><?php echo e($row['employee']->first_name); ?> <?php echo e($row['employee']->last_name); ?></div>
            <div class="text-xs text-slate-400"><?php echo e($row['employee']->employee_code); ?> · <?php echo e($row['employee']->pan_no ?? 'No PAN'); ?></div>
          </td>
          <td class="td">
            <span class="<?php echo e($row['regime'] === 'new' ? 'badge-blue' : 'badge-green'); ?> text-xs uppercase"><?php echo e($row['regime']); ?></span>
          </td>
          <td class="td text-right">₹<?php echo e(number_format($row['gross'], 0)); ?></td>
          <td class="td text-right text-slate-500">₹<?php echo e(number_format($row['std_ded'], 0)); ?></td>
          <td class="td text-right text-slate-500">₹<?php echo e(number_format($row['invest_80c'], 0)); ?></td>
          <td class="td text-right text-slate-500">₹<?php echo e(number_format($row['invest_80d'], 0)); ?></td>
          <td class="td text-right text-slate-500">₹<?php echo e(number_format($row['hra_exempt'], 0)); ?></td>
          <td class="td text-right font-medium">₹<?php echo e(number_format($row['taxable'], 0)); ?></td>
          <td class="td text-right font-semibold text-red-600">₹<?php echo e(number_format($row['annual_tax'], 0)); ?></td>
          <td class="td text-right">₹<?php echo e(number_format($row['monthly_tds'], 0)); ?></td>
          <td class="td text-right text-green-600">₹<?php echo e(number_format($row['tds_paid'], 0)); ?></td>
          <td class="td text-right <?php echo e($row['balance'] > 0 ? 'text-red-600 font-bold' : 'text-slate-400'); ?>">
            ₹<?php echo e(number_format($row['balance'], 0)); ?>

          </td>
          <td class="td text-center">
            <a href="<?php echo e(route('hr.employees.form-16', ['id' => $row['employee']->id, 'financial_year' => $financialYear])); ?>"
               target="_blank" class="btn btn-secondary btn-xs">Form 16</a>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>

  <?php else: ?>
  <div class="card text-center text-slate-400 py-12">
    Select a financial year and click <strong>Compute TDS</strong> to view the report.
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\tds-computation.blade.php ENDPATH**/ ?>