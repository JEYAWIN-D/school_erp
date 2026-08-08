<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9px; color: #1e293b; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 8px; margin-bottom: 10px; }
  .school-name { font-size: 12px; font-weight: bold; color: #1e40af; }
  .report-title { font-size: 9px; color: #475569; margin-top: 2px; }
  .meta { font-size: 8px; color: #94a3b8; margin-top: 2px; }
  .route-block { margin-bottom: 14px; page-break-inside: avoid; }
  .route-header { background: #1e40af; color: white; padding: 4px 8px; font-size: 9px; font-weight: bold; border-radius: 3px 3px 0 0; }
  .route-meta { font-size: 8px; color: #64748b; padding: 2px 8px 4px; background: #f8fafc; border: 1px solid #e2e8f0; border-top: 0; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #e2e8f0; font-size: 8px; padding: 3px 5px; text-align: left; color: #475569; }
  td { border-bottom: 1px solid #f1f5f9; padding: 3px 5px; font-size: 8px; }
  .stop-name { font-weight: bold; color: #334155; }
  footer { border-top: 1px solid #e2e8f0; margin-top: 12px; padding-top: 5px; text-align: center; font-size: 7px; color: #94a3b8; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'School'); ?></div>
  <div class="report-title">Transport Route-wise Student Roster</div>
  <div class="meta">Generated: <?php echo e(now()->format('d M Y')); ?></div>
</div>

<?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="route-block">
  <div class="route-header">Route: <?php echo e($route->route_name); ?> <?php if($route->route_number): ?>(<?php echo e($route->route_number); ?>)<?php endif; ?></div>
  <div class="route-meta">
    Vehicle: <?php echo e($route->vehicle?->vehicle_number ?? '—'); ?> &nbsp;|&nbsp;
    Students: <?php echo e($route->allotments->count()); ?>

  </div>
  <table>
    <thead><tr><th>#</th><th>Stop</th><th>Pickup Time</th><th>Student</th><th>Class</th></tr></thead>
    <tbody>
      <?php $sr=1; ?>
      <?php $__currentLoopData = $route->stops->sortBy('sequence'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $studentsAtStop = $route->allotments->filter(fn($a) => $a->stop_id == $stop->id); ?>
        <?php if($studentsAtStop->isEmpty()): ?>
          <tr>
            <td><?php echo e($sr++); ?></td>
            <td class="stop-name"><?php echo e($stop->stop_name); ?></td>
            <td><?php echo e($stop->pickup_time ?? '—'); ?></td>
            <td colspan="2" style="color:#94a3b8;">—</td>
          </tr>
        <?php else: ?>
          <?php $__currentLoopData = $studentsAtStop; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allotment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e($sr++); ?></td>
            <td class="stop-name"><?php echo e($stop->stop_name); ?></td>
            <td><?php echo e($stop->pickup_time ?? '—'); ?></td>
            <td><?php echo e($allotment->enrollment?->student?->full_name ?? '—'); ?></td>
            <td><?php echo e($allotment->enrollment?->class?->name ?? '—'); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<footer><?php echo e($school?->school_name ?? ''); ?> — Transport Roster — <?php echo e(now()->format('d M Y')); ?></footer>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\transport-roster.blade.php ENDPATH**/ ?>