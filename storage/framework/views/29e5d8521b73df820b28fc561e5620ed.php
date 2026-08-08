<?php $__env->startSection('title','Student Leave Requests'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Student Leave Requests</h1>
  </div>
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex gap-3">
      <form method="GET" class="flex gap-3 flex-1">
        <select name="status" class="select w-32">
          <option value="">All</option>
          <option value="pending" <?php if(request('status')==='pending'): echo 'selected'; endif; ?>>Pending</option>
          <option value="approved" <?php if(request('status')==='approved'): echo 'selected'; endif; ?>>Approved</option>
          <option value="rejected" <?php if(request('status')==='rejected'): echo 'selected'; endif; ?>>Rejected</option>
        </select>
        <select name="class_id" class="select w-36">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      </form>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Student','Class','Type','From','To','Days','Reason','Status','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($l->student?->full_name); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($l->student?->currentEnrollment?->class?->name); ?></td>
          <td class="px-4 py-3 capitalize text-slate-500"><?php echo e(str_replace('_',' ',$l->leave_type)); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($l->from_date->format('d M')); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($l->to_date->format('d M')); ?></td>
          <td class="px-4 py-3 font-semibold text-center"><?php echo e($l->days); ?></td>
          <td class="px-4 py-3 text-slate-500 max-w-xs truncate text-xs"><?php echo e($l->reason); ?></td>
          <td class="px-4 py-3"><span class="badge-<?php echo e($l->status === 'approved' ? 'green' : ($l->status === 'rejected' ? 'red' : 'amber')); ?> capitalize"><?php echo e($l->status); ?></span></td>
          <td class="px-4 py-3">
            <?php if($l->status === 'pending'): ?>
            <form method="POST" action="<?php echo e(route('attendance.leave.approve',$l->id)); ?>" class="inline"><?php echo csrf_field(); ?>
              <button type="submit" class="text-green-600 hover:underline text-xs">Approve</button>
            </form>
            <form method="POST" action="<?php echo e(route('attendance.leave.reject',$l->id)); ?>" class="inline ml-1"><?php echo csrf_field(); ?>
              <button type="submit" class="text-red-400 hover:underline text-xs">Reject</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No leave requests.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($leaves->hasPages()): ?><div class="px-4 pb-3"><?php echo e($leaves->links()); ?></div><?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\student-leave.blade.php ENDPATH**/ ?>