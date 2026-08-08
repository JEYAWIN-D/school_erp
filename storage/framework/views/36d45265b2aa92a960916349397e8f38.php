<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 0; }
  .header { text-align: center; border-bottom: 2px solid #dc2626; padding-bottom: 10px; margin-bottom: 16px; }
  .school-name { font-size: 16px; font-weight: bold; color: #1e40af; }
  .report-title { font-size: 13px; font-weight: bold; margin-top: 4px; color: #dc2626; }
  .meta { font-size: 10px; color: #64748b; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th { background: #dc2626; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
  td { padding: 5px 8px; border-bottom: 1px solid #fee2e2; font-size: 10px; }
  tr:nth-child(even) td { background: #fef2f2; }
  .supp-yes { color: #d97706; font-weight: bold; }
  .supp-no  { color: #dc2626; font-weight: bold; }
  .footer { margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 9px; color: #94a3b8; text-align: right; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'School'); ?></div>
  <div class="report-title">Failed Students List — <?php echo e($exam->name); ?></div>
  <div class="meta">Supplementary Threshold: <?php echo e($threshold); ?> subject(s) &nbsp;|&nbsp; Generated: <?php echo e(now()->format('d M Y H:i')); ?></div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Adm No</th>
      <th>Student Name</th>
      <th>Failed Subjects</th>
      <th style="text-align:center">Fail Count</th>
      <th style="text-align:center">Supp Eligible?</th>
    </tr>
  </thead>
  <tbody>
    <?php $i = 1; ?>
    <?php $__currentLoopData = $failed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
      <td><?php echo e($i++); ?></td>
      <td><?php echo e($row['student']?->admission_number); ?></td>
      <td><strong><?php echo e($row['student']?->full_name); ?></strong></td>
      <td><?php echo e($row['subjects']->implode(', ')); ?></td>
      <td style="text-align:center; font-weight:bold; color:#dc2626;"><?php echo e($row['subjects']->count()); ?></td>
      <td style="text-align:center" class="<?php echo e($row['supp_eligible'] ? 'supp-yes' : 'supp-no'); ?>">
        <?php echo e($row['supp_eligible'] ? 'Yes' : 'No'); ?>

      </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if($failed->isEmpty()): ?>
    <tr><td colspan="6" style="text-align:center; padding: 20px; color:#64748b;">No failed students found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<div class="footer">
  Total Failed: <?php echo e($failed->count()); ?> &nbsp;|&nbsp;
  Supp Eligible: <?php echo e($failed->where('supp_eligible', true)->count()); ?> &nbsp;|&nbsp;
  <?php echo e($school?->school_name ?? ''); ?> | Printed: <?php echo e(now()->format('d M Y H:i')); ?>

</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\failed-students.blade.php ENDPATH**/ ?>