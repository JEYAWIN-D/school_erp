<?php $__env->startSection('title','Outpass Workflow'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Outpass Workflow</h1>
    <button x-data @click="$dispatch('open-modal','new-outpass')" class="btn btn-primary btn-sm">+ New Outpass</button>
  </div>
  <div class="flex gap-3 border-b border-slate-200 pb-0">
    <?php $__currentLoopData = ['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','returned'=>'Returned']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="?status=<?php echo e($s); ?>" class="px-4 py-2 text-sm font-medium <?php echo e(request('status',$s==='pending'?'pending':'x') === $s ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500 hover:text-slate-700'); ?>">
      <?php echo e($l); ?>

      <?php if($counts[$s]??0): ?><span class="ml-1 bg-<?php echo e($s==='pending'?'amber':'slate'); ?>-100 text-<?php echo e($s==='pending'?'amber':'slate'); ?>-600 text-xs px-1.5 rounded-full"><?php echo e($counts[$s]); ?></span><?php endif; ?>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Student','Room','From','To','Reason','Contact','Status','Approved By','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $outpasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800 text-sm"><?php echo e($op->student?->full_name); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($op->allotment?->room?->room_number); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($op->from_datetime?->format('d M h:i A')); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($op->to_datetime?->format('d M h:i A')); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs max-w-xs truncate"><?php echo e($op->reason); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($op->parent_contact); ?></td>
          <td class="px-4 py-3"><span class="badge-<?php echo e($op->status==='approved'?'green':($op->status==='rejected'?'red':($op->status==='returned'?'indigo':'amber'))); ?> capitalize text-xs"><?php echo e($op->status); ?></span></td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($op->approvedBy?->name ?? '—'); ?></td>
          <td class="px-4 py-3">
            <?php if($op->status === 'pending'): ?>
            <div class="flex gap-1">
              <form method="POST" action="<?php echo e(route('hostel.outpass.approve',$op->id)); ?>" class="inline"><?php echo csrf_field(); ?>
                <button type="submit" class="text-green-600 hover:underline text-xs">Approve</button>
              </form>
              <form method="POST" action="<?php echo e(route('hostel.outpass.reject',$op->id)); ?>" class="inline"><?php echo csrf_field(); ?>
                <button type="submit" class="text-red-400 hover:underline text-xs">Reject</button>
              </form>
            </div>
            <?php elseif($op->status === 'approved' && !$op->actual_return_time): ?>
            <div class="flex gap-1">
              <form method="POST" action="<?php echo e(route('hostel.outpass.return',$op->id)); ?>" class="inline"><?php echo csrf_field(); ?>
                <button type="submit" class="text-indigo-600 hover:underline text-xs">Mark Returned</button>
              </form>
              <a href="<?php echo e(route('hostel.outpass.gate-pass-qr', $op->id)); ?>" target="_blank" class="text-slate-500 hover:text-slate-700 text-xs">Print Pass</a>
            </div>
            <?php elseif($op->status === 'returned'): ?>
            <a href="<?php echo e(route('hostel.outpass.gate-pass-qr', $op->id)); ?>" target="_blank" class="text-slate-400 hover:text-slate-600 text-xs">Print Pass</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No outpasses.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($outpasses->hasPages()): ?><div class="px-4 pb-3"><?php echo e($outpasses->links()); ?></div><?php endif; ?>
  </div>
</div>

<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='new-outpass')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-700 mb-4">New Outpass Request</h3>
    <form method="POST" action="<?php echo e(route('hostel.outpass.store')); ?>" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div><label class="label">Student <span class="text-red-500">*</span></label>
        <select name="student_id" class="select" required>
          <option value="">Select</option>
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->full_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">From <span class="text-red-500">*</span></label><input type="datetime-local" name="from_datetime" class="input" required></div>
        <div><label class="label">To <span class="text-red-500">*</span></label><input type="datetime-local" name="to_datetime" class="input" required></div>
      </div>
      <div><label class="label">Reason</label><textarea name="reason" class="input h-16"></textarea></div>
      <div><label class="label">Parent Contact</label><input type="tel" name="parent_contact" class="input"></div>
      <div><label class="label">Destination</label><input type="text" name="destination" class="input"></div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\outpass-workflow.blade.php ENDPATH**/ ?>