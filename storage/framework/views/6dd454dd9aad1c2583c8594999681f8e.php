<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9px; color: #1e293b; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 8px; margin-bottom: 10px; }
  .school-name { font-size: 12px; font-weight: bold; color: #1e40af; }
  .meta { font-size: 8px; color: #94a3b8; margin-top: 2px; }
  .summary-row { display: table; width: 100%; margin-bottom: 10px; }
  .sum-cell { display: table-cell; text-align: center; border: 1px solid #e2e8f0; padding: 5px; width: 25%; }
  .sum-val { font-size: 14px; font-weight: bold; color: #1e40af; }
  .sum-lbl { font-size: 7px; color: #64748b; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
  th { background: #1e40af; color: white; font-size: 8px; padding: 3px 5px; text-align: left; }
  td { border-bottom: 1px solid #f1f5f9; padding: 3px 5px; font-size: 8px; }
  tr:nth-child(even) td { background: #f8fafc; }
  footer { border-top: 1px solid #e2e8f0; margin-top: 10px; padding-top: 5px; text-align: center; font-size: 7px; color: #94a3b8; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'School'); ?></div>
  <div class="meta">Hostel Current Occupancy Report &nbsp;|&nbsp; Generated: <?php echo e(now()->format('d M Y, h:i A')); ?></div>
</div>

<div class="summary-row">
  <div class="sum-cell"><div class="sum-val"><?php echo e($allotments->count()); ?></div><div class="sum-lbl">Total Residents</div></div>
  <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div class="sum-cell">
    <div class="sum-val"><?php echo e($allotments->filter(fn($a) => $a->room?->hostel_id == $h->id)->count()); ?></div>
    <div class="sum-lbl"><?php echo e($h->name); ?></div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<table>
  <thead><tr>
    <th>#</th><th>Student</th><th>Admission No</th><th>Hostel</th><th>Room</th><th>Type</th><th>From</th><th>Monthly Fee</th>
  </tr></thead>
  <tbody>
    <?php $__currentLoopData = $allotments->sortBy(fn($a) => $a->room?->room_number); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
      <td><?php echo e($i+1); ?></td>
      <td><?php echo e($a->student?->full_name ?? '—'); ?></td>
      <td><?php echo e($a->student?->admission_number ?? '—'); ?></td>
      <td><?php echo e($a->room?->hostel?->name ?? '—'); ?></td>
      <td><?php echo e($a->room?->room_number ?? '—'); ?></td>
      <td><?php echo e($a->room?->room_type ?? '—'); ?></td>
      <td><?php echo e($a->allotment_date?->format('d M Y') ?? '—'); ?></td>
      <td style="text-align:right;">₹<?php echo e(number_format($a->monthly_fee ?? 0, 0)); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </tbody>
</table>

<footer><?php echo e($school?->school_name ?? ''); ?> — Hostel Occupancy Report — <?php echo e(now()->format('d M Y')); ?></footer>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\hostel-occupancy-report.blade.php ENDPATH**/ ?>