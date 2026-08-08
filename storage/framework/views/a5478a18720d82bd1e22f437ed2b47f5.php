<?php $__env->startSection('title', 'Fee Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Fee Report</h1>
  <form method="GET" class="card-flat py-4"><div class="flex gap-3 flex-wrap">
    <select name="class_id" class="select w-36">
      <option value="">All Classes</option>
      <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="fee_head_id" class="select w-44">
      <option value="">All Fee Heads</option>
      <?php $__currentLoopData = $feeHeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($fh->id); ?>" <?php if(request('fee_head_id')==$fh->id): echo 'selected'; endif; ?>><?php echo e($fh->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <input type="date" name="from_date" value="<?php echo e(request('from_date')); ?>" class="input w-36">
    <input type="date" name="to_date" value="<?php echo e(request('to_date')); ?>" class="input w-36">
    <button type="submit" class="btn btn-primary btn-sm">Generate</button>
  </div></form>
  <div class="grid grid-cols-2 gap-4">
    <div class="card text-center py-5"><p class="text-2xl font-bold text-green-600">₹<?php echo e(number_format($total ?? 0, 2)); ?></p><p class="text-sm text-slate-500 mt-1">Total Collected</p></div>
    <div class="card text-center py-5"><p class="text-2xl font-bold text-slate-800"><?php echo e($payments->total()); ?></p><p class="text-sm text-slate-500 mt-1">Transactions</p></div>
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <?php $__currentLoopData = ['Date','Receipt No.','Student','Class','Fee Head','Amount','Mode']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase tracking-wide font-medium"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-2 text-slate-500 text-xs"><?php echo e(\Carbon\Carbon::parse($p->payment_date)->format('d M Y')); ?></td>
          <td class="px-4 py-2 font-mono text-xs text-slate-600"><?php echo e($p->receipt_number); ?></td>
          <td class="px-4 py-2 font-medium text-slate-800"><?php echo e($p->student?->full_name); ?></td>
          <td class="px-4 py-2 text-slate-500"><?php echo e($p->student?->currentEnrollment?->class?->name); ?></td>
          <td class="px-4 py-2 text-slate-600"><?php echo e($p->feeHead?->name); ?></td>
          <td class="px-4 py-2 font-semibold text-green-700">₹<?php echo e(number_format($p->amount_paid, 2)); ?></td>
          <td class="px-4 py-2"><span class="badge-slate capitalize text-xs"><?php echo e(str_replace('_',' ',$p->payment_mode)); ?></span></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No records for selected filter.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($payments->hasPages()): ?><div class="px-4 pb-3 text-sm"><?php echo e($payments->links()); ?></div><?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\report.blade.php ENDPATH**/ ?>