<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 0; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 16px; }
  .school-name { font-size: 16px; font-weight: bold; color: #1e40af; }
  .report-title { font-size: 13px; font-weight: bold; margin-top: 4px; color: #334155; }
  .meta { font-size: 10px; color: #64748b; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th { background: #1e40af; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
  td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
  tr:nth-child(even) td { background: #f8fafc; }
  .pass { color: #16a34a; font-weight: bold; }
  .fail { color: #dc2626; font-weight: bold; }
  .rank { color: #7c3aed; font-weight: bold; }
  .footer { margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 9px; color: #94a3b8; text-align: right; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'School'); ?></div>
  <div class="report-title">Class Result Summary — <?php echo e($exam->name); ?></div>
  <div class="meta">Class: <?php echo e($class->name); ?> &nbsp;|&nbsp; Generated: <?php echo e(now()->format('d M Y H:i')); ?></div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Adm No</th>
      <th>Student Name</th>
      <th style="text-align:right">Marks Obtained</th>
      <th style="text-align:right">Total Marks</th>
      <th style="text-align:right">Percentage</th>
      <th style="text-align:center">Result</th>
    </tr>
  </thead>
  <tbody>
    <?php $rank = 1; ?>
    <?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
      <td class="rank"><?php echo e($rank++); ?></td>
      <td><?php echo e($row['student']?->admission_number); ?></td>
      <td><strong><?php echo e($row['student']?->full_name); ?></strong></td>
      <td style="text-align:right"><?php echo e($row['obtained']); ?></td>
      <td style="text-align:right"><?php echo e($row['totalMarks']); ?></td>
      <td style="text-align:right"><strong><?php echo e($row['percentage']); ?>%</strong></td>
      <td style="text-align:center" class="<?php echo e($row['hasFail'] ? 'fail' : 'pass'); ?>">
        <?php echo e($row['hasFail'] ? 'FAIL' : 'PASS'); ?>

      </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="3" style="font-weight:bold; padding-top:8px;">Summary</td>
      <td style="text-align:right; font-weight:bold;"><?php echo e($summary->sum('obtained')); ?></td>
      <td></td>
      <td style="text-align:right; font-weight:bold;">
        <?php echo e($summary->count() > 0 ? round($summary->avg('percentage'), 1) : 0); ?>% avg
      </td>
      <td style="text-align:center">
        Pass: <?php echo e($summary->where('hasFail', false)->count()); ?> /
        Fail: <?php echo e($summary->where('hasFail', true)->count()); ?>

      </td>
    </tr>
  </tfoot>
</table>

<div class="footer">
  <?php echo e($school?->school_name ?? ''); ?> | Confidential | Printed: <?php echo e(now()->format('d M Y H:i')); ?>

</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\class-result-summary.blade.php ENDPATH**/ ?>