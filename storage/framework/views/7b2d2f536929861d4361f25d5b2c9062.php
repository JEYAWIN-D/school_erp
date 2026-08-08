<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 9px; color: #1e293b; margin: 0; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 8px; margin-bottom: 12px; }
  .school-name { font-size: 14px; font-weight: bold; color: #1e40af; }
  .report-title { font-size: 11px; font-weight: bold; margin-top: 4px; color: #334155; }
  .meta { font-size: 9px; color: #64748b; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #1e40af; color: #fff; padding: 4px 5px; text-align: center; font-size: 8px; }
  th.left { text-align: left; }
  td { padding: 3px 5px; border-bottom: 1px solid #e2e8f0; font-size: 8px; text-align: right; }
  td.left { text-align: left; }
  tr:nth-child(even) td { background: #f8fafc; }
  .footer { margin-top: 14px; border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 8px; color: #94a3b8; text-align: right; }
  .total-row td { font-weight: bold; background: #dbeafe; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'School'); ?></div>
  <div class="report-title">Monthly Salary Register — <?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y')); ?></div>
  <div class="meta">Generated: <?php echo e(now()->format('d M Y H:i')); ?></div>
</div>

<table>
  <thead>
    <tr>
      <th class="left">#</th>
      <th class="left">Employee</th>
      <th class="left">Dept</th>
      <th>Gross (₹)</th>
      <th>Deductions (₹)</th>
      <th>Net Pay (₹)</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
      <td class="left"><?php echo e($loop->iteration); ?></td>
      <td class="left"><strong><?php echo e($rec->employee?->name); ?></strong><br><span style="color:#64748b;font-size:7px;"><?php echo e($rec->employee?->designation); ?></span></td>
      <td class="left"><?php echo e($rec->employee?->department?->name ?? '—'); ?></td>
      <td><?php echo e(number_format($rec->gross_salary, 2)); ?></td>
      <td><?php echo e(number_format($rec->deductions, 2)); ?></td>
      <td><strong><?php echo e(number_format($rec->net_salary, 2)); ?></strong></td>
      <td style="text-align:center; color:<?php echo e($rec->status === 'paid' ? '#16a34a' : '#d97706'); ?>;"><?php echo e(ucfirst($rec->status)); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if($records->isEmpty()): ?>
    <tr><td colspan="7" style="text-align:center; padding:14px; color:#94a3b8;">No payroll records found.</td></tr>
    <?php endif; ?>
  </tbody>
  <?php if($records->isNotEmpty()): ?>
  <tfoot>
    <tr class="total-row">
      <td class="left" colspan="3">TOTAL</td>
      <td><?php echo e(number_format($records->sum('gross_salary'), 2)); ?></td>
      <td><?php echo e(number_format($records->sum('deductions'), 2)); ?></td>
      <td><?php echo e(number_format($records->sum('net_salary'), 2)); ?></td>
      <td></td>
    </tr>
  </tfoot>
  <?php endif; ?>
</table>

<div class="footer">
  <?php echo e($school?->school_name ?? ''); ?> | Confidential — Not for distribution | Printed: <?php echo e(now()->format('d M Y H:i')); ?>

</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\salary-register.blade.php ENDPATH**/ ?>