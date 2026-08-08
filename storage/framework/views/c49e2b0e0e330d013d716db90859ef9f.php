<?php $__env->startSection('title', 'Student Outpass'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Student Outpass</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('gate.index')); ?>" class="btn-sm btn-secondary">← Gate</a>
      <a href="<?php echo e(route('gate.outpass.create')); ?>" class="btn-primary btn-sm">+ Issue Outpass</a>
    </div>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  <form method="GET" class="flex gap-3">
    <select name="status" class="select">
      <option value="">All Status</option>
      <option value="active" <?php if(request('status')==='active'): echo 'selected'; endif; ?>>Active</option>
      <option value="returned" <?php if(request('status')==='returned'): echo 'selected'; endif; ?>>Returned</option>
      <option value="overdue" <?php if(request('status')==='overdue'): echo 'selected'; endif; ?>>Overdue</option>
    </select>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead><tr>
        <th class="th">Pass No.</th>
        <th class="th">Student</th>
        <th class="th">Reason</th>
        <th class="th">Out Time</th>
        <th class="th">Expected Return</th>
        <th class="th">Actual Return</th>
        <th class="th">Authorized By</th>
        <th class="th">Status</th>
        <th class="th">Actions</th>
      </tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $outpasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td font-mono font-semibold text-indigo-600"><?php echo e($op->pass_number); ?></td>
          <td class="td">
            <p class="font-medium"><?php echo e($op->student?->first_name); ?> <?php echo e($op->student?->last_name); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($op->student?->admission_number); ?></p>
          </td>
          <td class="td text-xs"><?php echo e($op->reason); ?></td>
          <td class="td text-xs"><?php echo e($op->out_time?->format('d M, h:i A')); ?></td>
          <td class="td text-xs"><?php echo e($op->expected_return?->format('d M, h:i A') ?? '—'); ?></td>
          <td class="td text-xs"><?php echo e($op->actual_return?->format('d M, h:i A') ?? '—'); ?></td>
          <td class="td text-xs"><?php echo e($op->authorized_by); ?></td>
          <td class="td">
            <?php if($op->status==='active'): ?> <span class="badge-blue">Active</span>
            <?php elseif($op->status==='returned'): ?> <span class="badge-green">Returned</span>
            <?php else: ?> <span class="badge-red">Overdue</span> <?php endif; ?>
          </td>
          <td class="td">
            <?php if($op->status !== 'returned'): ?>
            <form method="POST" action="<?php echo e(route('gate.outpass.return', $op->id)); ?>">
              <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
              <button type="submit" class="btn-xs btn-primary">Mark Returned</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-center text-slate-400" colspan="9">No outpasses found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div><?php echo e($outpasses->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\gate\outpass.blade.php ENDPATH**/ ?>