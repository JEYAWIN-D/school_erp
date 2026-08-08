<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
* { box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 10px; color: #111; margin: 0; padding: 20px 30px; background: #fff; }
.header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #1e3a8a; padding-bottom: 12px; }
.school-name { font-size: 18px; font-weight: bold; color: #1e3a8a; }
.doc-title   { font-size: 13px; font-weight: bold; margin-top: 4px; }
table { width: 100%; border-collapse: collapse; }
th { background: #1e3a8a; color: white; padding: 6px 8px; text-align: center; font-size: 9px; letter-spacing: 0.5px; }
td { padding: 5px 6px; border: 1px solid #e2e8f0; font-size: 9px; vertical-align: top; }
tr:nth-child(even) td { background: #f8fafc; }
.period-cell { background: #f0f4ff; font-weight: bold; text-align: center; color: #1e3a8a; }
.subject { font-weight: bold; color: #1e40af; }
.teacher  { font-size: 8px; color: #64748b; }
.footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
</style>
</head>
<body>

<div class="header">
  <div class="school-name"><?php echo e($school->school_name ?? config('app.name')); ?></div>
  <div class="doc-title">CLASS TIMETABLE — <?php echo e($class->name); ?></div>
</div>

<?php
$days = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday'];
$maxPeriods = 0;
foreach($timetable as $entries) { $maxPeriods = max($maxPeriods, $entries->count()); }
?>

<table>
  <thead>
    <tr>
      <th style="width:80px;">Day</th>
      <?php for($p = 1; $p <= max($maxPeriods, 8); $p++): ?>
        <th>P<?php echo e($p); ?></th>
      <?php endfor; ?>
    </tr>
  </thead>
  <tbody>
    <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayNum => $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php $dayEntries = $timetable[$dayNum] ?? collect(); ?>
      <tr>
        <td class="period-cell"><?php echo e($dayName); ?></td>
        <?php for($p = 1; $p <= max($maxPeriods, 8); $p++): ?>
          <?php $entry = $dayEntries->firstWhere('period_number', $p); ?>
          <td>
            <?php if($entry): ?>
              <div class="subject"><?php echo e($entry->subject?->name ?? '—'); ?></div>
              <div class="teacher"><?php echo e($entry->teacher?->first_name ? ($entry->teacher->first_name . ' ' . substr($entry->teacher->last_name,0,1) . '.') : ''); ?></div>
              <?php if($entry->start_time): ?> <div style="font-size:7px;color:#94a3b8;"><?php echo e($entry->start_time); ?>–<?php echo e($entry->end_time); ?></div> <?php endif; ?>
            <?php endif; ?>
          </td>
        <?php endfor; ?>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </tbody>
</table>

<div class="footer">Generated: <?php echo e(now()->format('d M Y h:i A')); ?></div>

</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\timetable.blade.php ENDPATH**/ ?>