<?php $__env->startSection('title','Student Fee Ledger'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Student Fee Ledger</h1>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="class_id" class="select w-36">
      <option value="">Select Class</option>
      <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="student_id" class="select w-56">
      <option value="">Select Student</option>
      <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('student_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->full_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Load Ledger</button>
  </div></form>

  <?php if(isset($enrollment)): ?>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="card text-center py-4"><p class="text-xs text-slate-400 uppercase">Total Fees</p><p class="text-xl font-bold text-slate-800 mt-1">₹<?php echo e(number_format($totalFees,2)); ?></p></div>
    <div class="card text-center py-4"><p class="text-xs text-slate-400 uppercase">Paid</p><p class="text-xl font-bold text-green-700 mt-1">₹<?php echo e(number_format($totalPaid,2)); ?></p></div>
    <div class="card text-center py-4"><p class="text-xs text-slate-400 uppercase">Concession</p><p class="text-xl font-bold text-indigo-600 mt-1">₹<?php echo e(number_format($totalConcession,2)); ?></p></div>
    <div class="card text-center py-4"><p class="text-xs text-slate-400 uppercase">Balance Due</p><p class="text-xl font-bold <?php echo e($balance > 0 ? 'text-red-600' : 'text-slate-800'); ?> mt-1">₹<?php echo e(number_format($balance,2)); ?></p></div>
  </div>
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
      <div>
        <p class="font-semibold text-slate-800"><?php echo e($enrollment->student?->full_name); ?></p>
        <p class="text-xs text-slate-400"><?php echo e($enrollment->class?->name); ?> <?php echo e($enrollment->section?->name); ?> | <?php echo e($enrollment->roll_number); ?></p>
      </div>
      <a href="<?php echo e(route('fees.ledger.pdf', request()->query())); ?>" class="btn btn-secondary btn-sm">Print Ledger</a>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Date','Receipt No','Fee Head','Charged','Paid','Late Fee','Discount','Balance','Mode','Status']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $ledger; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="<?php echo e($row['type']==='charge'?'bg-amber-50/40':''); ?> hover:bg-slate-50">
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($row['date']); ?></td>
          <td class="px-4 py-3 font-mono text-xs text-indigo-700"><?php echo e($row['receipt'] ?? '—'); ?></td>
          <td class="px-4 py-3 text-slate-700 text-xs"><?php echo e($row['fee_head']); ?></td>
          <td class="px-4 py-3 text-amber-600 font-semibold text-xs"><?php echo e($row['charged'] > 0 ? '₹'.number_format($row['charged'],2) : ''); ?></td>
          <td class="px-4 py-3 text-green-700 font-semibold text-xs"><?php echo e($row['paid'] > 0 ? '₹'.number_format($row['paid'],2) : ''); ?></td>
          <td class="px-4 py-3 text-red-400 text-xs"><?php echo e($row['late_fee'] > 0 ? '₹'.number_format($row['late_fee'],2) : '—'); ?></td>
          <td class="px-4 py-3 text-indigo-500 text-xs"><?php echo e($row['discount'] > 0 ? '₹'.number_format($row['discount'],2) : '—'); ?></td>
          <td class="px-4 py-3 font-semibold text-xs <?php echo e($row['balance'] > 0 ? 'text-red-600' : 'text-slate-700'); ?>">₹<?php echo e(number_format($row['balance'],2)); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs capitalize"><?php echo e($row['mode'] ?? '—'); ?></td>
          <td class="px-4 py-3"><span class="badge-<?php echo e($row['status']==='paid'?'green':($row['status']==='partial'?'amber':'slate')); ?> capitalize text-xs"><?php echo e($row['status'] ?? '—'); ?></span></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="10" class="px-4 py-8 text-center text-slate-400">No transactions found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\ledger.blade.php ENDPATH**/ ?>