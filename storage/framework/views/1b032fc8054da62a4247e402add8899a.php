<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 8px; color: #1e293b; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 6px; margin-bottom: 10px; }
  .school-name { font-size: 11px; font-weight: bold; color: #1e40af; }
  .meta { font-size: 7px; color: #94a3b8; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #1e40af; color: white; font-size: 7px; padding: 3px 4px; text-align: left; }
  td { border-bottom: 1px solid #f1f5f9; padding: 3px 4px; font-size: 7px; }
  tr:nth-child(even) td { background: #f8fafc; }
  .badge-green { color: #15803d; font-weight: bold; }
  .badge-red { color: #dc2626; }
  .badge-amber { color: #d97706; }
  footer { border-top: 1px solid #e2e8f0; margin-top: 10px; padding-top: 5px; text-align: center; font-size: 6px; color: #94a3b8; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'School'); ?></div>
  <div class="meta">Hostel Gate Pass Register — <?php echo e(now()->format('F Y')); ?> &nbsp;|&nbsp; Generated: <?php echo e(now()->format('d M Y')); ?></div>
</div>

<table>
  <thead><tr>
    <th>#</th><th>Student</th><th>Room</th><th>From</th><th>To</th><th>Destination</th><th>Purpose</th><th>Status</th><th>Returned</th>
  </tr></thead>
  <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $outpasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
      <td><?php echo e($i+1); ?></td>
      <td><?php echo e($op->student?->full_name ?? '—'); ?></td>
      <td><?php echo e($op->allotment?->room?->room_number ?? '—'); ?></td>
      <td><?php echo e($op->from_datetime?->format('d M H:i') ?? '—'); ?></td>
      <td><?php echo e($op->to_datetime?->format('d M H:i') ?? '—'); ?></td>
      <td><?php echo e($op->destination ?? '—'); ?></td>
      <td><?php echo e(\Illuminate\Support\Str::limit($op->reason ?? '—', 25)); ?></td>
      <td class="badge-<?php echo e($op->status==='approved'||$op->status==='returned' ? 'green' : ($op->status==='rejected'?'red':'amber')); ?>"><?php echo e(ucfirst($op->status)); ?></td>
      <td><?php echo e($op->actual_return_time?->format('d M H:i') ?? '—'); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr><td colspan="9" style="text-align:center;padding:8px;color:#94a3b8;">No gate passes this month.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<footer><?php echo e($school?->school_name ?? ''); ?> — Gate Pass Register — <?php echo e(now()->format('F Y')); ?></footer>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\hostel-gate-pass-register.blade.php ENDPATH**/ ?>