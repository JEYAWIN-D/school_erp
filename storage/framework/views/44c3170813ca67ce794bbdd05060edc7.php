<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 0; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 16px; }
  .school-name { font-size: 15px; font-weight: bold; color: #1e40af; }
  .report-title { font-size: 12px; font-weight: bold; margin-top: 4px; color: #334155; }
  .meta { font-size: 9px; color: #64748b; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th { background: #1e40af; color: #fff; padding: 5px 6px; text-align: center; font-size: 9px; }
  th.left { text-align: left; }
  td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; font-size: 9px; text-align: center; }
  td.left { text-align: left; }
  tr:nth-child(even) td { background: #f8fafc; }
  .above { color: #16a34a; font-weight: bold; }
  .below { color: #dc2626; font-weight: bold; }
  .school-row td { background: #dbeafe; font-weight: bold; color: #1e40af; }
  .footer { margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 8px; color: #94a3b8; text-align: right; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'School'); ?></div>
  <div class="report-title">Comparative Analysis — <?php echo e($exam->name); ?></div>
  <div class="meta">School Overall Average: <?php echo e($schoolAvg ?? '—'); ?>% &nbsp;|&nbsp; Generated: <?php echo e(now()->format('d M Y H:i')); ?></div>
</div>

<table>
  <thead>
    <tr>
      <th class="left">Class</th>
      <th>Overall Avg</th>
      <th>vs School Avg</th>
      <?php $__currentLoopData = array_keys($subjectSchoolAvg); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th><?php echo e($subj); ?></th>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tr>
  </thead>
  <tbody>
    <?php $__currentLoopData = $classData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $diff = round($row['overall_avg'] - ($schoolAvg ?? 0), 1); ?>
    <tr>
      <td class="left"><strong><?php echo e($row['class']->name); ?></strong></td>
      <td><strong><?php echo e($row['overall_avg']); ?>%</strong></td>
      <td class="<?php echo e($diff >= 0 ? 'above' : 'below'); ?>"><?php echo e($diff >= 0 ? '+' : ''); ?><?php echo e($diff); ?>%</td>
      <?php $__currentLoopData = array_keys($subjectSchoolAvg); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        $cAvg = $row['subject_avgs'][$subj] ?? null;
        $sAvg = $subjectSchoolAvg[$subj];
      ?>
      <td class="<?php echo e($cAvg !== null ? ($cAvg >= $sAvg ? 'above' : 'below') : ''); ?>">
        <?php echo e($cAvg ?? '—'); ?>

      </td>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if(!empty($subjectSchoolAvg)): ?>
    <tr class="school-row">
      <td class="left">School Average</td>
      <td><?php echo e($schoolAvg ?? '—'); ?>%</td>
      <td>—</td>
      <?php $__currentLoopData = $subjectSchoolAvg; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $avg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <td><?php echo e($avg); ?></td>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tr>
    <?php endif; ?>
  </tbody>
</table>

<div class="footer">
  Green = above school average | Red = below school average &nbsp;|&nbsp;
  <?php echo e($school?->school_name ?? ''); ?> | Printed: <?php echo e(now()->format('d M Y H:i')); ?>

</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\comparative-analysis.blade.php ENDPATH**/ ?>