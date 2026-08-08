<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 10px; color: #1e293b; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 8px; margin-bottom: 14px; }
  .school-name { font-size: 14px; font-weight: bold; color: #1e40af; }
  .report-title { font-size: 11px; color: #475569; margin-top: 2px; }
  .meta { font-size: 8px; color: #94a3b8; margin-top: 2px; }
  .section { margin-bottom: 14px; }
  .section-title { font-size: 10px; font-weight: bold; background: #f1f5f9; padding: 4px 6px; border-left: 3px solid #1e40af; margin-bottom: 6px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
  th { background: #1e40af; color: white; font-size: 8px; padding: 4px 6px; text-align: left; }
  td { border-bottom: 1px solid #e2e8f0; padding: 3px 6px; font-size: 9px; }
  tr:nth-child(even) td { background: #f8fafc; }
  .two-col { display: table; width: 100%; }
  .col-half { display: table-cell; width: 50%; vertical-align: top; padding-right: 8px; }
  .col-half:last-child { padding-right: 0; padding-left: 8px; }
  .badge-up { color: #16a34a; font-weight: bold; }
  .badge-down { color: #dc2626; font-weight: bold; }
  .yoy-box { border: 1px solid #e2e8f0; border-radius: 3px; padding: 6px 10px; text-align: center; display: inline-block; width: 45%; margin: 0 2%; }
  .yoy-year { font-size: 9px; color: #64748b; }
  .yoy-val { font-size: 18px; font-weight: bold; color: #1e40af; }
  .yoy-label { font-size: 8px; color: #94a3b8; }
  footer { border-top: 1px solid #e2e8f0; margin-top: 16px; padding-top: 6px; text-align: center; font-size: 7px; color: #94a3b8; }
</style>
</head>
<body>

<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'School'); ?></div>
  <div class="report-title">Admission Analytics Report</div>
  <div class="meta">
    Academic Year: <?php echo e($currentYear?->name ?? 'Current'); ?> &nbsp;|&nbsp; Generated: <?php echo e(now()->format('d M Y, h:i A')); ?>

  </div>
</div>


<?php if($yoyData): ?>
<div class="section">
  <div class="section-title">Year-over-Year Comparison</div>
  <div style="text-align:center; padding:6px 0;">
    <div class="yoy-box">
      <div class="yoy-year"><?php echo e($yoyData['prev']['year']); ?></div>
      <div class="yoy-val"><?php echo e(number_format($yoyData['prev']['enquiries'])); ?></div>
      <div class="yoy-label">Enquiries</div>
      <div style="margin-top:4px; font-size:10px; font-weight:bold; color:#475569;"><?php echo e(number_format($yoyData['prev']['converted'])); ?> converted</div>
    </div>
    <div class="yoy-box">
      <div class="yoy-year"><?php echo e($yoyData['current']['year']); ?></div>
      <div class="yoy-val"><?php echo e(number_format($yoyData['current']['enquiries'])); ?></div>
      <div class="yoy-label">Enquiries</div>
      <div style="margin-top:4px; font-size:10px; font-weight:bold; color:#475569;"><?php echo e(number_format($yoyData['current']['converted'])); ?> converted</div>
    </div>
    <?php
      $diff = $yoyData['current']['enquiries'] - $yoyData['prev']['enquiries'];
      $pct  = $yoyData['prev']['enquiries'] > 0
        ? round($diff / $yoyData['prev']['enquiries'] * 100, 1) : null;
    ?>
    <?php if($pct !== null): ?>
      <div style="display:inline-block; width:8%; text-align:center; vertical-align:top; padding-top:12px;">
        <span class="<?php echo e($diff >= 0 ? 'badge-up' : 'badge-down'); ?>">
          <?php echo e($diff >= 0 ? '▲' : '▼'); ?> <?php echo e(abs($pct)); ?>%
        </span>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>


<div class="two-col">
  <div class="col-half">
    <div class="section">
      <div class="section-title">Source-wise Breakdown</div>
      <table>
        <thead><tr><th>Source</th><th>Count</th><th>%</th></tr></thead>
        <tbody>
          <?php $sourceTotal = $bySource->sum(); ?>
          <?php $__empty_1 = true; $__currentLoopData = $bySource; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e(ucfirst($source ?: 'Unknown')); ?></td>
            <td><?php echo e($count); ?></td>
            <td><?php echo e($sourceTotal > 0 ? round($count/$sourceTotal*100,1).'%' : '—'); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="3" style="text-align:center;color:#94a3b8;">No data</td></tr>
          <?php endif; ?>
          <?php if($bySource->isNotEmpty()): ?>
          <tr style="font-weight:bold; background:#f1f5f9;">
            <td>Total</td><td><?php echo e($sourceTotal); ?></td><td>100%</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-half">
    <div class="section">
      <div class="section-title">Status-wise Breakdown</div>
      <table>
        <thead><tr><th>Status</th><th>Count</th><th>%</th></tr></thead>
        <tbody>
          <?php $statusTotal = $byStatus->sum(); ?>
          <?php $__empty_1 = true; $__currentLoopData = $byStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e(ucfirst(str_replace('_',' ',$status ?: 'Unknown'))); ?></td>
            <td><?php echo e($count); ?></td>
            <td><?php echo e($statusTotal > 0 ? round($count/$statusTotal*100,1).'%' : '—'); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="3" style="text-align:center;color:#94a3b8;">No data</td></tr>
          <?php endif; ?>
          <?php if($byStatus->isNotEmpty()): ?>
          <tr style="font-weight:bold; background:#f1f5f9;">
            <td>Total</td><td><?php echo e($statusTotal); ?></td><td>100%</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>


<div class="section">
  <div class="section-title">Class-wise Admission Progress vs Seat Capacity</div>
  <table>
    <thead><tr><th>Class</th><th>Enquiries</th><th>Total Seats</th><th>Fill %</th></tr></thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $classSeats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td><?php echo e($row['class']); ?></td>
        <td><?php echo e($row['enquiries']); ?></td>
        <td><?php echo e($row['seats'] ?: '—'); ?></td>
        <td>
          <?php if($row['fill_pct'] !== null): ?>
            <span class="<?php echo e($row['fill_pct'] >= 100 ? 'badge-up' : ($row['fill_pct'] >= 70 ? '' : 'badge-down')); ?>">
              <?php echo e($row['fill_pct']); ?>%
            </span>
          <?php else: ?>
            —
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="4" style="text-align:center;color:#94a3b8;">No class data</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>


<div class="section">
  <div class="section-title">Monthly Enquiry Trend (<?php echo e(now()->year); ?>)</div>
  <?php $months = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; ?>
  <table>
    <thead><tr>
      <?php for($i=1;$i<=12;$i++): ?><th style="text-align:center;"><?php echo e($months[$i]); ?></th><?php endfor; ?>
    </tr></thead>
    <tbody><tr>
      <?php for($i=1;$i<=12;$i++): ?>
        <td style="text-align:center;"><?php echo e($monthlyTrend->get($i, 0)); ?></td>
      <?php endfor; ?>
    </tr></tbody>
  </table>
</div>


<?php if($byCounsellor->isNotEmpty()): ?>
<div class="section">
  <div class="section-title">Counsellor-wise Conversion</div>
  <table>
    <thead><tr><th>Counsellor</th><th>Total Enquiries</th><th>Converted</th><th>Conversion Rate</th></tr></thead>
    <tbody>
      <?php $__currentLoopData = $byCounsellor->sortByDesc('total'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr>
        <td><?php echo e($row->assignedTo?->name ?? ('Staff #'.$row->assigned_to)); ?></td>
        <td><?php echo e($row->total); ?></td>
        <td><?php echo e($row->converted); ?></td>
        <td>
          <?php $rate = $row->total > 0 ? round($row->converted/$row->total*100,1) : 0; ?>
          <span class="<?php echo e($rate >= 50 ? 'badge-up' : 'badge-down'); ?>"><?php echo e($rate); ?>%</span>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<footer>
  <?php echo e($school?->school_name ?? 'School'); ?> &mdash; Admission Analytics &mdash; <?php echo e(now()->format('d M Y')); ?>

</footer>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\admission-analytics.blade.php ENDPATH**/ ?>