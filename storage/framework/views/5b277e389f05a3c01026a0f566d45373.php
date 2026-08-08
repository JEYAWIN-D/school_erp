<?php $__env->startSection('title', 'PF / ESI / PT / TDS Annual Statutory Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Statutory Reports — PF / ESI / PT / TDS</h1>
    <?php if(isset($report) && $report->count()): ?>
    <div class="flex gap-2">
      <a href="<?php echo e(request()->fullUrlWithQuery(['format' => 'excel'])); ?>" class="btn btn-secondary btn-sm flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export Excel
      </a>
    </div>
    <?php endif; ?>
  </div>

  <form method="GET" class="card grid grid-cols-2 md:grid-cols-3 gap-4">
    <div>
      <label class="label">Financial Year</label>
      <select name="financial_year" class="select">
        <?php $currentY = date('Y'); ?>
        <?php $__currentLoopData = range($currentY - 2, $currentY + 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($y); ?>-<?php echo e($y+1); ?>" <?php if(($financialYear ?? '') === "$y-" . ($y+1)): echo 'selected'; endif; ?>>FY <?php echo e($y); ?>-<?php echo e($y+1); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Department</label>
      <select name="department_id" class="select">
        <option value="">All Departments</option>
        <?php $__currentLoopData = $departments ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($dept->id); ?>" <?php if(request('department_id') == $dept->id): echo 'selected'; endif; ?>><?php echo e($dept->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="flex items-end gap-2">
      <button type="submit" name="action" value="1" class="btn btn-primary">Generate Report</button>
    </div>
  </form>

  <?php if(isset($report) && $report->count()): ?>
  
  <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    <div class="card text-center">
      <div class="text-xl font-bold text-indigo-600"><?php echo e($report->count()); ?></div>
      <div class="text-xs text-slate-500 mt-1">Employees</div>
    </div>
    <div class="card text-center">
      <div class="text-xl font-bold text-blue-600">₹<?php echo e(number_format($report->sum('pf_employee') + $report->sum('pf_employer'), 0)); ?></div>
      <div class="text-xs text-slate-500 mt-1">Total PF (Annual)</div>
    </div>
    <div class="card text-center">
      <div class="text-xl font-bold text-emerald-600">₹<?php echo e(number_format($report->sum('esi_employee') + $report->sum('esi_employer'), 0)); ?></div>
      <div class="text-xs text-slate-500 mt-1">Total ESI (Annual)</div>
    </div>
    <div class="card text-center">
      <div class="text-xl font-bold text-amber-600">₹<?php echo e(number_format($report->sum('pt_annual'), 0)); ?></div>
      <div class="text-xs text-slate-500 mt-1">Total PT (Annual)</div>
    </div>
    <div class="card text-center">
      <div class="text-xl font-bold text-red-600">₹<?php echo e(number_format($report->sum('tds_annual'), 0)); ?></div>
      <div class="text-xs text-slate-500 mt-1">Total TDS (Annual)</div>
    </div>
  </div>

  <div class="table-wrap overflow-x-auto">
    <table class="w-full text-sm whitespace-nowrap">
      <thead>
        <tr>
          <th class="th" rowspan="2">Employee</th>
          <th class="th" rowspan="2">PAN / PF No / ESI No</th>
          <th class="th text-right" rowspan="2">Annual Gross</th>
          <th class="th text-center" colspan="2">PF (Annual)</th>
          <th class="th text-center" colspan="2">ESI (Annual)</th>
          <th class="th text-right" rowspan="2">PT</th>
          <th class="th text-right" rowspan="2">TDS</th>
          <th class="th text-center" rowspan="2">Form 16</th>
        </tr>
        <tr>
          <th class="th text-right text-xs">Employee</th>
          <th class="th text-right text-xs">Employer</th>
          <th class="th text-right text-xs">Employee</th>
          <th class="th text-right text-xs">Employer</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="tr">
          <td class="td">
            <div class="font-medium"><?php echo e($row['employee']->first_name); ?> <?php echo e($row['employee']->last_name); ?></div>
            <div class="text-xs text-slate-400"><?php echo e($row['employee']->employee_code); ?></div>
          </td>
          <td class="td text-xs">
            <div>PAN: <?php echo e($row['employee']->pan_no ?? '—'); ?></div>
            <div>PF: <?php echo e($row['employee']->pf_account_no ?? '—'); ?></div>
            <div>ESI: <?php echo e($row['employee']->esi_no ?? '—'); ?></div>
          </td>
          <td class="td text-right font-medium">₹<?php echo e(number_format($row['annual_gross'], 0)); ?></td>
          <td class="td text-right text-blue-600">₹<?php echo e(number_format($row['pf_employee'], 0)); ?></td>
          <td class="td text-right text-blue-800">₹<?php echo e(number_format($row['pf_employer'], 0)); ?></td>
          <td class="td text-right text-emerald-600">₹<?php echo e(number_format($row['esi_employee'], 0)); ?></td>
          <td class="td text-right text-emerald-800">₹<?php echo e(number_format($row['esi_employer'], 0)); ?></td>
          <td class="td text-right text-amber-600">₹<?php echo e(number_format($row['pt_annual'], 0)); ?></td>
          <td class="td text-right text-red-600 font-semibold">₹<?php echo e(number_format($row['tds_annual'], 0)); ?></td>
          <td class="td text-center">
            <a href="<?php echo e(route('hr.employees.form-16', ['id' => $row['employee']->id, 'financial_year' => $financialYear])); ?>"
               target="_blank" class="btn btn-secondary btn-xs">PDF</a>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
      <tfoot>
        <tr class="bg-slate-50 font-bold">
          <td class="td" colspan="2">Total</td>
          <td class="td text-right">₹<?php echo e(number_format($report->sum('annual_gross'), 0)); ?></td>
          <td class="td text-right text-blue-600">₹<?php echo e(number_format($report->sum('pf_employee'), 0)); ?></td>
          <td class="td text-right text-blue-800">₹<?php echo e(number_format($report->sum('pf_employer'), 0)); ?></td>
          <td class="td text-right text-emerald-600">₹<?php echo e(number_format($report->sum('esi_employee'), 0)); ?></td>
          <td class="td text-right text-emerald-800">₹<?php echo e(number_format($report->sum('esi_employer'), 0)); ?></td>
          <td class="td text-right text-amber-600">₹<?php echo e(number_format($report->sum('pt_annual'), 0)); ?></td>
          <td class="td text-right text-red-600">₹<?php echo e(number_format($report->sum('tds_annual'), 0)); ?></td>
          <td class="td"></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <p class="text-xs text-slate-400">PF: 12% employee + ~13.2% employer of basic | ESI: 0.75% employee + 3.25% employer of gross (applies ≤ ₹21,000/month) | PT: Tamil Nadu slabs | TDS: As per income tax slabs</p>

  <?php else: ?>
  <div class="card text-center text-slate-400 py-12">
    Select a financial year and click <strong>Generate Report</strong>.
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\statutory-report.blade.php ENDPATH**/ ?>