<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 10px; color: #1e293b; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 8px; margin-bottom: 12px; }
  .school-name { font-size: 13px; font-weight: bold; color: #1e40af; }
  .report-title { font-size: 10px; color: #475569; margin-top: 2px; }
  .meta { font-size: 8px; color: #94a3b8; margin-top: 2px; }
  .totals { display: table; width: 100%; margin-bottom: 12px; }
  .total-box { display: table-cell; width: 50%; text-align: center; border: 1px solid #e2e8f0; border-radius: 3px; padding: 6px; }
  .total-val { font-size: 16px; font-weight: bold; color: #1e40af; }
  .total-label { font-size: 8px; color: #64748b; margin-top: 1px; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #1e40af; color: white; font-size: 8px; padding: 4px 6px; text-align: left; }
  td { border-bottom: 1px solid #f1f5f9; padding: 4px 6px; font-size: 9px; }
  tr:nth-child(even) td { background: #f8fafc; }
  .total-row td { font-weight: bold; background: #f1f5f9; }
  footer { border-top: 1px solid #e2e8f0; margin-top: 14px; padding-top: 6px; text-align: center; font-size: 7px; color: #94a3b8; }
</style>
</head>
<body>
<?php [$yr,$mo] = explode('-',$month); $monthLabel = \Carbon\Carbon::createFromDate($yr,$mo,1)->format('F Y'); ?>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'School'); ?></div>
  <div class="report-title">Transport — Fuel Expense Monthly Summary</div>
  <div class="meta">Month: <?php echo e($monthLabel); ?> &nbsp;|&nbsp; Generated: <?php echo e(now()->format('d M Y')); ?></div>
</div>

<div class="totals">
  <div class="total-box">
    <div class="total-val"><?php echo e(number_format($grandTotals['litres'],1)); ?> L</div>
    <div class="total-label">Total Fuel Consumed</div>
  </div>
  <div class="total-box" style="padding-left:6px;">
    <div class="total-val">₹<?php echo e(number_format($grandTotals['amount'],0)); ?></div>
    <div class="total-label">Total Fuel Cost</div>
  </div>
</div>

<table>
  <thead><tr>
    <th>Vehicle</th><th>Fill Ups</th><th>Litres</th><th>Amount (₹)</th><th>KM Covered</th><th>Efficiency</th>
  </tr></thead>
  <tbody>
    <?php $__currentLoopData = $fuelByVehicle->sortByDesc('total_amount'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $efficiency = ($row->total_litres > 0 && $row->km_covered) ? round($row->km_covered/$row->total_litres,2) : null; ?>
    <tr>
      <td><?php echo e($row->vehicle?->vehicle_number ?? ('Vehicle #'.$row->vehicle_id)); ?></td>
      <td style="text-align:center;"><?php echo e($row->fill_count); ?></td>
      <td style="text-align:right;"><?php echo e(number_format($row->total_litres,1)); ?></td>
      <td style="text-align:right;"><?php echo e(number_format($row->total_amount,0)); ?></td>
      <td style="text-align:right;"><?php echo e($row->km_covered ? number_format($row->km_covered,0) : '—'); ?></td>
      <td style="text-align:center;"><?php echo e($efficiency ? $efficiency.' km/L' : '—'); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <tr class="total-row">
      <td>Total</td>
      <td style="text-align:center;"><?php echo e($fuelByVehicle->sum('fill_count')); ?></td>
      <td style="text-align:right;"><?php echo e(number_format($grandTotals['litres'],1)); ?></td>
      <td style="text-align:right;"><?php echo e(number_format($grandTotals['amount'],0)); ?></td>
      <td>—</td><td>—</td>
    </tr>
  </tbody>
</table>

<footer><?php echo e($school?->school_name ?? 'School'); ?> — Fuel Expense Summary — <?php echo e($monthLabel); ?></footer>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\transport-fuel-summary.blade.php ENDPATH**/ ?>