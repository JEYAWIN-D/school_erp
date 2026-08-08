<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
* { box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 11px; color: #111; margin: 0; padding: 25px 35px; background: #fff; }
.header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e3a8a; padding-bottom: 12px; }
.school-name { font-size: 20px; font-weight: bold; color: #1e3a8a; }
.doc-title   { font-size: 14px; font-weight: bold; margin-top: 4px; }
.year-name   { font-size: 12px; color: #64748b; }
.month-title { background: #1e3a8a; color: white; font-size: 12px; font-weight: bold; padding: 6px 12px; margin-top: 16px; border-radius: 4px 4px 0 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
th { background: #f0f4ff; padding: 5px 8px; text-align: left; font-size: 10px; color: #334155; border: 1px solid #e2e8f0; }
td { padding: 5px 8px; border: 1px solid #e2e8f0; font-size: 10px; vertical-align: top; }
.holiday-type { display: inline-block; padding: 1px 6px; border-radius: 999px; font-size: 8px; font-weight: bold; }
.type-national  { background:#dbeafe;color:#1e40af; }
.type-religious { background:#fef3c7;color:#92400e; }
.type-school    { background:#d1fae5;color:#065f46; }
.type-other     { background:#f1f5f9;color:#475569; }
.footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
</style>
</head>
<body>

<div class="header">
  <div class="school-name"><?php echo e($school->school_name ?? config('app.name')); ?></div>
  <div class="doc-title">ACADEMIC CALENDAR</div>
  <div class="year-name"><?php echo e($currentYear?->name ?? date('Y').'-'.(date('Y')+1)); ?></div>
</div>

<?php
$byMonth = $holidays->groupBy(fn($h) => $h->date->format('Y-m'));
$months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
?>

<?php $__currentLoopData = $byMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthKey => $monthHolidays): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php [$y, $m] = explode('-', $monthKey); ?>
  <div class="month-title"><?php echo e($months[(int)$m - 1]); ?> <?php echo e($y); ?></div>
  <table>
    <thead>
      <tr>
        <th style="width:100px;">Date</th>
        <th style="width:60px;">Day</th>
        <th>Holiday / Event</th>
        <th style="width:100px;">Type</th>
      </tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $monthHolidays->sortBy('holiday_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td><?php echo e($h->date->format('d M Y')); ?></td>
          <td><?php echo e($h->date->format('l')); ?></td>
          <td><?php echo e($h->name); ?></td>
          <td>
            <span class="holiday-type type-<?php echo e($h->type ?? 'other'); ?>">
              <?php echo e(ucfirst($h->type ?? 'holiday')); ?>

            </span>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if($holidays->isEmpty()): ?>
  <p style="text-align:center;color:#94a3b8;margin-top:40px;">No holidays defined for this academic year.</p>
<?php endif; ?>

<div class="footer">Total holidays: <?php echo e($holidays->count()); ?> | Generated: <?php echo e(now()->format('d M Y')); ?></div>

</body>
</html>

<?php /**PATH E:\school_erp - Copy\resources\views\pdf\academic-calendar.blade.php ENDPATH**/ ?>