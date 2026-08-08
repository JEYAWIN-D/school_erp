<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; font-size: 10px; color: #333; }
.header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 8px; margin-bottom: 10px; }
.school-name { font-size: 14px; font-weight: bold; color: #1e3a5f; }
.challan-title { font-size: 12px; margin-top: 4px; font-weight: bold; }
table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 10px; }
th { background: #1e3a5f; color: white; padding: 4px 6px; text-align: left; }
td { padding: 3px 6px; border: 1px solid #d1d5db; }
tr:nth-child(even) td { background: #f8fafc; }
.total-row td { background: #e2e8f0; font-weight: bold; }
.summary { display: flex; gap: 20px; margin: 10px 0; }
.summary-box { border: 1px solid #d1d5db; padding: 6px 10px; flex: 1; text-align: center; }
.summary-label { font-size: 9px; color: #6b7280; }
.summary-value { font-size: 14px; font-weight: bold; color: #1e3a5f; }
.footer-note { margin-top: 15px; font-size: 9px; color: #6b7280; border-top: 1px solid #e2e8f0; padding-top: 5px; }
.sign-section { display: flex; justify-content: space-between; margin-top: 30px; }
.sign-box { text-align: center; }
.sign-line { border-top: 1px solid #333; margin-top: 25px; padding-top: 3px; font-size: 9px; width: 120px; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'DASA EduERP'); ?></div>
  <div style="font-size:10px;color:#666"><?php echo e($school?->address ?? ''); ?></div>
  <?php if($school?->pf_establishment_code ?? null): ?><div style="font-size:10px">PF Code: <?php echo e($school->pf_establishment_code); ?></div><?php endif; ?>
  <div class="challan-title">
    <?php if($type === 'pf'): ?> EMPLOYEE PROVIDENT FUND (EPF) CHALLAN
    <?php elseif($type === 'esi'): ?> EMPLOYEES STATE INSURANCE (ESI) CHALLAN
    <?php else: ?> PROFESSIONAL TAX (PT) CHALLAN
    <?php endif; ?>
    — <?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y')); ?>

  </div>
</div>

<div class="summary">
  <?php if($type === 'pf'): ?>
  <div class="summary-box">
    <div class="summary-value">₹<?php echo e(number_format($totals['pf_employee'], 2)); ?></div>
    <div class="summary-label">Employee PF (12%)</div>
  </div>
  <div class="summary-box">
    <div class="summary-value">₹<?php echo e(number_format($totals['pf_employer'], 2)); ?></div>
    <div class="summary-label">Employer PF (12%)</div>
  </div>
  <div class="summary-box">
    <div class="summary-value">₹<?php echo e(number_format($totals['pf_total'], 2)); ?></div>
    <div class="summary-label">Total PF Payable</div>
  </div>
  <?php elseif($type === 'esi'): ?>
  <div class="summary-box">
    <div class="summary-value">₹<?php echo e(number_format($totals['esi_employee'], 2)); ?></div>
    <div class="summary-label">Employee ESI (0.75%)</div>
  </div>
  <div class="summary-box">
    <div class="summary-value">₹<?php echo e(number_format($totals['esi_employer'], 2)); ?></div>
    <div class="summary-label">Employer ESI (3.25%)</div>
  </div>
  <div class="summary-box">
    <div class="summary-value">₹<?php echo e(number_format($totals['esi_total'], 2)); ?></div>
    <div class="summary-label">Total ESI Payable</div>
  </div>
  <?php else: ?>
  <div class="summary-box">
    <div class="summary-value">₹<?php echo e(number_format($totals['pt_total'], 2)); ?></div>
    <div class="summary-label">Total PT Payable</div>
  </div>
  <div class="summary-box">
    <div class="summary-value"><?php echo e($challanData->count()); ?></div>
    <div class="summary-label">Employees</div>
  </div>
  <?php endif; ?>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Employee Name</th>
      <th>Emp ID</th>
      <?php if($type === 'pf'): ?>
      <th>UAN</th>
      <th>Basic (₹)</th>
      <th>Emp PF (₹)</th>
      <th>Emp'r PF (₹)</th>
      <th>Total (₹)</th>
      <?php elseif($type === 'esi'): ?>
      <th>ESI No.</th>
      <th>Gross (₹)</th>
      <th>Emp ESI (₹)</th>
      <th>Emp'r ESI (₹)</th>
      <th>Total (₹)</th>
      <?php else: ?>
      <th>Designation</th>
      <th>Gross (₹)</th>
      <th>PT (₹)</th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody>
    <?php $__currentLoopData = $challanData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
      <td><?php echo e($i + 1); ?></td>
      <td><?php echo e($row['employee']?->full_name); ?></td>
      <td><?php echo e($row['employee']?->employee_number); ?></td>
      <?php if($type === 'pf'): ?>
      <td><?php echo e($row['employee']?->uan_number ?? '—'); ?></td>
      <td><?php echo e(number_format($row['record']->basic_salary ?? 0, 2)); ?></td>
      <td><?php echo e(number_format($row['pf_employee'], 2)); ?></td>
      <td><?php echo e(number_format($row['pf_employer'], 2)); ?></td>
      <td><strong><?php echo e(number_format($row['pf_total'], 2)); ?></strong></td>
      <?php elseif($type === 'esi'): ?>
      <td><?php echo e($row['employee']?->esi_number ?? '—'); ?></td>
      <td><?php echo e(number_format($row['record']->gross_salary ?? 0, 2)); ?></td>
      <td><?php echo e(number_format($row['esi_employee'], 2)); ?></td>
      <td><?php echo e(number_format($row['esi_employer'], 2)); ?></td>
      <td><strong><?php echo e(number_format($row['esi_total'], 2)); ?></strong></td>
      <?php else: ?>
      <td><?php echo e($row['employee']?->designation); ?></td>
      <td><?php echo e(number_format($row['record']->gross_salary ?? 0, 2)); ?></td>
      <td><strong><?php echo e(number_format($row['pt'], 2)); ?></strong></td>
      <?php endif; ?>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <tr class="total-row">
      <td colspan="<?php echo e($type === 'pf' ? 5 : ($type === 'esi' ? 4 : 3)); ?>" style="text-align:right">TOTAL</td>
      <?php if($type === 'pf'): ?>
      <td><?php echo e(number_format($totals['pf_employee'], 2)); ?></td>
      <td><?php echo e(number_format($totals['pf_employer'], 2)); ?></td>
      <td><?php echo e(number_format($totals['pf_total'], 2)); ?></td>
      <?php elseif($type === 'esi'): ?>
      <td><?php echo e(number_format($totals['esi_employee'], 2)); ?></td>
      <td><?php echo e(number_format($totals['esi_employer'], 2)); ?></td>
      <td><?php echo e(number_format($totals['esi_total'], 2)); ?></td>
      <?php else: ?>
      <td><?php echo e(number_format($totals['pt_total'], 2)); ?></td>
      <?php endif; ?>
    </tr>
  </tbody>
</table>

<div class="footer-note">
  Generated on: <?php echo e(now()->format('d M Y H:i')); ?> |
  Month: <?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y')); ?> |
  This is a computer-generated statement.
</div>

<div class="sign-section">
  <div class="sign-box"><div class="sign-line">Accounts Officer</div></div>
  <div class="sign-box"><div class="sign-line">HR Manager</div></div>
  <div class="sign-box"><div class="sign-line">Principal / Authorised Signatory</div></div>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\statutory-challan.blade.php ENDPATH**/ ?>