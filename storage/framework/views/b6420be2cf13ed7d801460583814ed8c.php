<?php $__env->startSection('title', 'Fee Change History'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Fee Change History</h1>
      <p class="page-subtitle">Audit log of all fee revisions, concessions, and overrides</p>
    </div>
    <a href="<?php echo e(route('fees.revision')); ?>" class="btn btn-secondary btn-sm">Fee Revision</a>
  </div>

  
  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Class</label>
        <select name="class_id" class="select text-sm">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($class->id); ?>" <?php if(request('class_id') == $class->id): echo 'selected'; endif; ?>><?php echo e($class->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Change Type</label>
        <select name="change_type" class="select text-sm">
          <option value="">All Types</option>
          <option value="revision" <?php if(request('change_type') === 'revision'): echo 'selected'; endif; ?>>Revision</option>
          <option value="concession" <?php if(request('change_type') === 'concession'): echo 'selected'; endif; ?>>Concession</option>
          <option value="override" <?php if(request('change_type') === 'override'): echo 'selected'; endif; ?>>Override</option>
          <option value="cancellation" <?php if(request('change_type') === 'cancellation'): echo 'selected'; endif; ?>>Cancellation</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      <a href="<?php echo e(route('fees.change-history')); ?>" class="btn btn-secondary btn-sm">Reset</a>
    </form>
  </div>

  <div class="card">
    <?php if($logs->isEmpty()): ?>
      <div class="text-center py-12 text-slate-400">No fee change records found.</div>
    <?php else: ?>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">Date & Time</th>
            <th class="th">Class</th>
            <th class="th">Fee Head</th>
            <th class="th text-center">Type</th>
            <th class="th text-right">Old Amount</th>
            <th class="th text-right">New Amount</th>
            <th class="th text-right">Change</th>
            <th class="th">Reason</th>
            <th class="th">Changed By</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $diff = ($log->new_amount ?? 0) - ($log->old_amount ?? 0);
            $isIncrease = $diff > 0;
          ?>
          <tr class="tr">
            <td class="td text-xs text-slate-500"><?php echo e($log->created_at->format('d M Y H:i')); ?></td>
            <td class="td font-medium"><?php echo e($log->class?->name ?? '—'); ?></td>
            <td class="td"><?php echo e($log->feeHead?->name ?? '—'); ?></td>
            <td class="td text-center">
              <span class="text-xs px-2 py-0.5 rounded-full font-medium
                <?php echo e($log->change_type === 'revision' ? 'bg-blue-100 text-blue-700' :
                   ($log->change_type === 'concession' ? 'bg-green-100 text-green-700' :
                   ($log->change_type === 'cancellation' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'))); ?>">
                <?php echo e(ucfirst($log->change_type)); ?>

              </span>
            </td>
            <td class="td text-right text-slate-500"><?php echo e($log->old_amount ? '₹'.number_format($log->old_amount, 2) : '—'); ?></td>
            <td class="td text-right font-semibold"><?php echo e($log->new_amount ? '₹'.number_format($log->new_amount, 2) : '—'); ?></td>
            <td class="td text-right font-semibold <?php echo e($isIncrease ? 'text-red-500' : 'text-green-600'); ?>">
              <?php echo e($diff != 0 ? ($isIncrease ? '+' : '').number_format($diff, 2) : '—'); ?>

            </td>
            <td class="td text-xs text-slate-500 max-w-xs truncate"><?php echo e($log->reason ?? '—'); ?></td>
            <td class="td text-xs"><?php echo e($log->changedBy?->name ?? 'System'); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <div class="mt-4"><?php echo e($logs->links()); ?></div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\change-history.blade.php ENDPATH**/ ?>