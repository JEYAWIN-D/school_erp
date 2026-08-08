<?php $__env->startSection('title', 'Gate Visitor Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Gate Visitor Report</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('gate.index')); ?>" class="btn-sm btn-secondary">← Gate</a>
      <a href="<?php echo e(route('gate.report', array_merge(request()->query(), ['export'=>'csv']))); ?>" class="btn-sm btn-secondary">Export CSV</a>
      <button onclick="window.print()" class="btn-sm btn-primary">🖨 Print</button>
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div><label class="label">From</label><input type="date" name="from" value="<?php echo e($from); ?>" class="input"></div>
    <div><label class="label">To</label><input type="date" name="to" value="<?php echo e($to); ?>" class="input"></div>
    <button type="submit" class="btn-primary btn-sm">Generate</button>
    <div class="ml-auto flex gap-4 text-center">
      <div><p class="text-2xl font-bold text-indigo-600"><?php echo e($totalIn); ?></p><p class="text-xs text-slate-400">Total Visitors</p></div>
      <div><p class="text-2xl font-bold text-emerald-600"><?php echo e($totalOut); ?></p><p class="text-xs text-slate-400">Checked Out</p></div>
      <div><p class="text-2xl font-bold text-rose-600"><?php echo e($stillInside); ?></p><p class="text-xs text-slate-400">Still Inside</p></div>
    </div>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead><tr>
        <th class="th">#</th>
        <th class="th">Visitor Name</th>
        <th class="th">Phone</th>
        <th class="th">Purpose</th>
        <th class="th">Whom to Meet</th>
        <th class="th">Vehicle</th>
        <th class="th">In Time</th>
        <th class="th">Out Time</th>
        <th class="th">Duration</th>
      </tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $visitors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td text-slate-400"><?php echo e($i+1); ?></td>
          <td class="td font-medium"><?php echo e($v->visitor_name); ?></td>
          <td class="td text-xs"><?php echo e($v->visitor_phone ?? '—'); ?></td>
          <td class="td text-xs"><?php echo e($v->purpose); ?></td>
          <td class="td text-xs"><?php echo e($v->whom_to_meet ?? '—'); ?></td>
          <td class="td text-xs"><?php echo e($v->vehicle_number ?? '—'); ?></td>
          <td class="td text-xs"><?php echo e($v->in_time->format('d/m h:i A')); ?></td>
          <td class="td text-xs"><?php echo e($v->out_time ? $v->out_time->format('h:i A') : '—'); ?></td>
          <td class="td text-xs">
            <?php if($v->out_time): ?>
              <?php echo e($v->in_time->diffForHumans($v->out_time, true)); ?>

            <?php else: ?> <span class="text-rose-500">Still inside</span> <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-center text-slate-400" colspan="9">No visitors in this period.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\gate\report.blade.php ENDPATH**/ ?>