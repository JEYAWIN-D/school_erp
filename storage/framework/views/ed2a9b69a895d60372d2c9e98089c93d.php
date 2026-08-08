<?php $__env->startSection('title', 'Outpass Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Outpass Management</h1>
    <button x-data @click="$dispatch('open-modal','new-outpass')" class="btn btn-primary btn-sm">New Outpass</button>
  </div>
  <div class="card text-center py-5 col-span-3">
    <p class="text-slate-500"><?php echo e($allotments->count()); ?> active hostel residents</p>
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <?php $__currentLoopData = ['Student','Room','Out Date','Return Date','Purpose','Status','Action']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $allotments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($op->student?->full_name); ?></td>
          <td class="px-4 py-3 text-slate-600"><?php echo e($op->allotment?->room?->room_number ?? '—'); ?></td>
          <td class="px-4 py-3 text-slate-600"><?php echo e($op->from_datetime ? \Carbon\Carbon::parse($op->from_datetime)->format('d M Y H:i') : '—'); ?></td>
          <td class="px-4 py-3 text-slate-600"><?php echo e($op->to_datetime ? \Carbon\Carbon::parse($op->to_datetime)->format('d M Y H:i') : '—'); ?></td>
          <td class="px-4 py-3 text-slate-500 max-w-xs truncate"><?php echo e($op->reason); ?></td>
          <td class="px-4 py-3"><span class="badge-<?php echo e($op->status === 'approved' ? 'green' : ($op->status === 'rejected' ? 'red' : 'amber')); ?>"><?php echo e(ucfirst($op->status)); ?></span></td>
          <td class="px-4 py-3 text-slate-500 text-xs">—</td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No outpass records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\outpass.blade.php ENDPATH**/ ?>