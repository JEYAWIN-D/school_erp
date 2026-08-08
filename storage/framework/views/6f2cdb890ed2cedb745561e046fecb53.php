<?php $__env->startSection('title','Day Book'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Fee Day Book</h1>
    <form method="GET" action="<?php echo e(route('fees.daybook.pdf')); ?>" class="flex gap-2">
      <?php $__currentLoopData = request()->query(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <button type="submit" class="btn btn-secondary btn-sm">Print Day Book</button>
    </form>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap items-center">
    <div class="flex items-center gap-2">
      <label class="text-sm text-slate-600">Date:</label>
      <input type="date" name="date" value="<?php echo e(request('date',today()->toDateString())); ?>" class="input w-36">
    </div>
    <div class="flex items-center gap-2">
      <label class="text-sm text-slate-600">Fee Head:</label>
      <select name="fee_head_id" class="select w-40">
        <option value="">All</option>
        <?php $__currentLoopData = $feeHeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($fh->id); ?>" <?php if(request('fee_head_id')==$fh->id): echo 'selected'; endif; ?>><?php echo e($fh->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </div></form>

  <?php if(isset($payments)): ?>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php
      $cash = $payments->where('payment_mode','cash')->sum('amount_paid');
      $online = $payments->where('payment_mode','online')->sum('amount_paid');
      $cheque = $payments->where('payment_mode','cheque')->sum('amount_paid');
      $dayTotal = $payments->sum('amount_paid');
    ?>
    <?php $__currentLoopData = ['Cash'=>$cash,'Online'=>$online,'Cheque'=>$cheque,'Total'=>$dayTotal]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card text-center py-4">
      <p class="text-xs text-slate-400 uppercase tracking-wide"><?php echo e($label); ?></p>
      <p class="text-xl font-bold text-slate-800 mt-1">₹<?php echo e(number_format($val,2)); ?></p>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
      <span class="font-semibold text-slate-700">Transactions on <?php echo e(\Carbon\Carbon::parse(request('date'))->format('d M Y')); ?></span>
      <span class="text-sm text-slate-400"><?php echo e($payments->count()); ?> receipts</span>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Time','Receipt No','Student','Class','Fee Head','Amount','Late Fee','Discount','Mode','Action']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="<?php echo e($p->is_cancelled ? 'opacity-50 bg-red-50' : 'hover:bg-slate-50'); ?>">
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($p->created_at->format('h:i A')); ?></td>
          <td class="px-4 py-3 font-mono text-xs text-indigo-700"><?php echo e($p->receipt_number); ?></td>
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($p->enrollment?->student?->full_name); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($p->enrollment?->class?->name); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($p->feeHead?->name); ?></td>
          <td class="px-4 py-3 font-semibold">₹<?php echo e(number_format($p->amount_paid,2)); ?></td>
          <td class="px-4 py-3 text-amber-600 text-xs"><?php echo e($p->late_fee > 0 ? '₹'.number_format($p->late_fee,2) : '—'); ?></td>
          <td class="px-4 py-3 text-green-600 text-xs"><?php echo e($p->discount > 0 ? '₹'.number_format($p->discount,2) : '—'); ?></td>
          <td class="px-4 py-3"><span class="badge-slate capitalize text-xs"><?php echo e($p->payment_mode); ?></span></td>
          <td class="px-4 py-3">
            <?php if($p->is_cancelled): ?>
            <span class="text-red-400 text-xs">Cancelled</span>
            <?php else: ?>
            <a href="<?php echo e(route('fees.receipt',$p->id)); ?>" target="_blank" class="text-indigo-600 hover:underline text-xs">Receipt</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="10" class="px-4 py-8 text-center text-slate-400">No transactions.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\daybook.blade.php ENDPATH**/ ?>