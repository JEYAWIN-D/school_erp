<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; font-size: 9px; margin: 0; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 2px 4px; text-align: center; }
    th { background: #f1f5f9; font-weight: bold; font-size: 8px; text-transform: uppercase; }
    .name-col { text-align: left; white-space: nowrap; min-width: 120px; }
    .present { color: #16a34a; font-weight: bold; }
    .absent { color: #dc2626; font-weight: bold; }
    .header { margin-bottom: 8px; }
    h2 { font-size: 14px; margin: 0 0 4px 0; }
    p { font-size: 9px; margin: 0; }
  </style>
</head>
<body>
  <div class="header">
    <h2>Attendance Register</h2>
    <p><?php echo e(request('month')); ?></p>
  </div>
  <table>
    <thead>
      <tr>
        <th class="name-col">Student</th>
        <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th><?php echo e($d->format('d')); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <th>P</th><th>A</th><th>%</th>
      </tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php $rec = $attendanceMap[$s->student_id] ?? []; ?>
      <tr>
        <td class="name-col"><?php echo e($s->student?->full_name); ?></td>
        <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $status = $rec[$d->toDateString()] ?? null; ?>
        <td>
          <?php if($status==='present'): ?><span class="present">P</span>
          <?php elseif($status==='absent'): ?><span class="absent">A</span>
          <?php elseif($status==='late'): ?><span style="color:#d97706">L</span>
          <?php else: ?><span style="color:#e2e8f0">-</span><?php endif; ?>
        </td>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php
          $p = collect($rec)->filter(fn($v)=>in_array($v,['present','late']))->count();
          $a = collect($rec)->filter(fn($v)=>$v==='absent')->count();
          $t = $p+$a;
          $pct = $t>0 ? round($p/$t*100) : 0;
        ?>
        <td class="present"><?php echo e($p); ?></td>
        <td class="absent"><?php echo e($a); ?></td>
        <td style="<?php echo e($pct<75 ? 'color:#dc2626;font-weight:bold' : ''); ?>"><?php echo e($pct); ?>%</td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\attendance-register.blade.php ENDPATH**/ ?>