<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 0; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 14px; }
  .school-name { font-size: 15px; font-weight: bold; color: #1e40af; }
  .report-title { font-size: 12px; font-weight: bold; margin-top: 4px; color: #334155; }
  .meta { font-size: 9px; color: #64748b; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #1e40af; color: #fff; padding: 5px 7px; text-align: left; font-size: 9px; }
  td { padding: 4px 7px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
  tr:nth-child(even) td { background: #f8fafc; }
  .footer { margin-top: 16px; border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 8px; color: #94a3b8; text-align: right; }
  .total-row td { font-weight: bold; background: #eff6ff; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'School'); ?></div>
  <div class="report-title">Leave Encashment Report</div>
  <div class="meta">Period: <?php echo e($fyStart->format('d M Y')); ?> to <?php echo e($fyEnd->format('d M Y')); ?> &nbsp;|&nbsp; Generated: <?php echo e(now()->format('d M Y H:i')); ?></div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Employee</th>
      <th>Department</th>
      <th>Date</th>
      <th style="text-align:center">Days</th>
      <th style="text-align:right">Amount (₹)</th>
      <th>Remarks</th>
    </tr>
  </thead>
  <tbody>
    <?php $__currentLoopData = $encashments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
      <td><?php echo e($loop->iteration); ?></td>
      <td><strong><?php echo e($enc->employee?->name); ?></strong></td>
      <td><?php echo e($enc->employee?->department?->name ?? '—'); ?></td>
      <td><?php echo e($enc->encashment_date?->format('d M Y')); ?></td>
      <td style="text-align:center"><?php echo e($enc->days ?? '—'); ?></td>
      <td style="text-align:right; font-weight:bold; color:#16a34a;">₹<?php echo e(number_format($enc->amount, 2)); ?></td>
      <td><?php echo e($enc->remarks ?? '—'); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if($encashments->isEmpty()): ?>
    <tr><td colspan="7" style="text-align:center; padding:16px; color:#94a3b8;">No encashment records found.</td></tr>
    <?php endif; ?>
  </tbody>
  <?php if($encashments->isNotEmpty()): ?>
  <tfoot>
    <tr class="total-row">
      <td colspan="5" style="text-align:right">Total Paid:</td>
      <td style="text-align:right">₹<?php echo e(number_format($totalPaid, 2)); ?></td>
      <td></td>
    </tr>
  </tfoot>
  <?php endif; ?>
</table>

<div class="footer">
  <?php echo e($school?->school_name ?? ''); ?> | Confidential | Printed: <?php echo e(now()->format('d M Y H:i')); ?>

</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\leave-encashment-report.blade.php ENDPATH**/ ?>