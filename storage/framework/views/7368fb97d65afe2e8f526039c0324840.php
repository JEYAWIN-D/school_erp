<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; }
  .slip { page-break-after: always; padding: 28px 36px; }
  .slip:last-child { page-break-after: auto; }
  .header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px; margin-bottom: 14px; }
  .school-name { font-size: 16px; font-weight: bold; color: #1e3a5f; }
  .slip-title { text-align: center; font-size: 12px; font-weight: bold; letter-spacing: 1px; margin-bottom: 14px; }
  .info-grid { display: flex; gap: 12px; margin-bottom: 14px; }
  .info-box { flex: 1; background: #f0f4f8; border: 1px solid #d1dce8; border-radius: 4px; padding: 7px 10px; }
  .info-label { font-size: 8px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
  .info-value { font-size: 11px; font-weight: bold; margin-top: 1px; }
  .cols { display: flex; gap: 16px; }
  .col { flex: 1; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  th { background: #1e3a5f; color: white; padding: 5px 8px; font-size: 9px; text-align: left; }
  td { padding: 5px 8px; border-bottom: 1px solid #e8eef4; font-size: 10px; }
  .amount { text-align: right; }
  .total-row td { font-weight: bold; background: #eff6ff; border-top: 2px solid #1e3a5f; }
  .net-box { background: #1e3a5f; color: white; border-radius: 4px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; margin-top: 10px; }
  .net-label { font-size: 10px; opacity: 0.8; }
  .net-amount { font-size: 20px; font-weight: bold; }
  .sig-row { display: flex; justify-content: space-between; margin-top: 28px; }
  .sig-box { text-align: center; width: 140px; border-top: 1px solid #333; padding-top: 4px; font-size: 8px; color: #555; }
</style>
</head>
<body>
<?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
  $emp = $payroll->employee;
  $basic = $payroll->basic_salary ?? ($emp->basic_salary ?? 0);
  $hra   = $payroll->hra ?? round($basic * 0.4);
  $ta    = $payroll->transport_allowance ?? 1600;
  $other = $payroll->other_allowance ?? 0;
  $gross = $basic + $hra + $ta + $other;
  $pf    = $payroll->pf_employee ?? round($basic * 0.12);
  $esi   = $payroll->esi_employee ?? (($gross <= 21000) ? round($gross * 0.0075) : 0);
  $tds   = $payroll->tds ?? 0;
  $otherDeductions = $payroll->other_deductions ?? 0;
  $totalDeductions = $pf + $esi + $tds + $otherDeductions;
  $net   = $gross - $totalDeductions;
?>
<div class="slip">
  <div class="header">
    <div class="school-name"><?php echo e($school->name ?? $school->school_name ?? 'SCHOOL NAME'); ?></div>
    <div style="font-size:9px;color:#555;margin-top:2px;">Pay Slip — <?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $year . '-' . $month)->format('F Y')); ?></div>
  </div>

  <div class="info-grid">
    <div class="info-box"><div class="info-label">Employee Name</div><div class="info-value"><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></div></div>
    <div class="info-box"><div class="info-label">Emp No</div><div class="info-value"><?php echo e($emp->employee_number); ?></div></div>
    <div class="info-box"><div class="info-label">Designation</div><div class="info-value"><?php echo e($emp->designation ?? '—'); ?></div></div>
    <div class="info-box"><div class="info-label">Department</div><div class="info-value"><?php echo e($emp->department ?? '—'); ?></div></div>
    <div class="info-box"><div class="info-label">Working / Present</div><div class="info-value"><?php echo e($payroll->working_days ?? 26); ?> / <?php echo e($payroll->present_days ?? 26); ?></div></div>
  </div>

  <div class="cols">
    <div class="col">
      <table>
        <thead><tr><th>Earnings</th><th class="amount">Amount (₹)</th></tr></thead>
        <tbody>
          <tr><td>Basic Salary</td><td class="amount"><?php echo e(number_format($basic, 2)); ?></td></tr>
          <tr><td>HRA</td><td class="amount"><?php echo e(number_format($hra, 2)); ?></td></tr>
          <tr><td>Transport Allowance</td><td class="amount"><?php echo e(number_format($ta, 2)); ?></td></tr>
          <?php if($other > 0): ?><tr><td>Other Allowances</td><td class="amount"><?php echo e(number_format($other, 2)); ?></td></tr><?php endif; ?>
          <tr class="total-row"><td>Gross Earnings</td><td class="amount"><?php echo e(number_format($gross, 2)); ?></td></tr>
        </tbody>
      </table>
    </div>
    <div class="col">
      <table>
        <thead><tr><th>Deductions</th><th class="amount">Amount (₹)</th></tr></thead>
        <tbody>
          <tr><td>PF (Employee)</td><td class="amount"><?php echo e(number_format($pf, 2)); ?></td></tr>
          <?php if($esi > 0): ?><tr><td>ESI (Employee)</td><td class="amount"><?php echo e(number_format($esi, 2)); ?></td></tr><?php endif; ?>
          <?php if($tds > 0): ?><tr><td>TDS</td><td class="amount"><?php echo e(number_format($tds, 2)); ?></td></tr><?php endif; ?>
          <?php if($otherDeductions > 0): ?><tr><td>Other Deductions</td><td class="amount"><?php echo e(number_format($otherDeductions, 2)); ?></td></tr><?php endif; ?>
          <tr class="total-row"><td>Total Deductions</td><td class="amount"><?php echo e(number_format($totalDeductions, 2)); ?></td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="net-box">
    <div><div class="net-label">Net Pay</div></div>
    <div class="net-amount">₹<?php echo e(number_format($net, 2)); ?></div>
  </div>

  <div class="sig-row">
    <div class="sig-box">Employee Signature</div>
    <div class="sig-box">HR / Accounts Officer</div>
  </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\bulk-payslips.blade.php ENDPATH**/ ?>