<?php $__env->startSection('title', 'Increment History'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Increment History Report</h1>
    <a href="<?php echo e(route('hr.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  
  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">From</label>
        <input type="date" name="from" value="<?php echo e($fyStart->toDateString()); ?>" class="input text-sm">
      </div>
      <div>
        <label class="label text-xs">To</label>
        <input type="date" name="to" value="<?php echo e($fyEnd->toDateString()); ?>" class="input text-sm">
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
      <a href="<?php echo e(route('hr.increment-history.pdf', request()->only('from','to'))); ?>"
         target="_blank" class="btn btn-secondary btn-sm">PDF</a>
      <a href="<?php echo e(route('hr.increment-history.excel', request()->only('from','to'))); ?>"
         class="btn btn-secondary btn-sm">Excel</a>
    </form>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div class="card text-center">
      <p class="text-2xl font-bold text-indigo-700"><?php echo e($increments->count()); ?></p>
      <p class="text-xs text-slate-500 mt-1">Employees Incremented</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-green-600">₹<?php echo e(number_format($totalIncrement, 2)); ?></p>
      <p class="text-xs text-slate-500 mt-1">Total Increment Amount</p>
    </div>
  </div>

  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Employee</th>
          <th class="th">Department</th>
          <th class="th">Appraisal Year</th>
          <th class="th">Increment (₹)</th>
          <th class="th">Increment %</th>
          <th class="th">Effective From</th>
          <th class="th">Rating</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $increments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td text-slate-400"><?php echo e($loop->iteration); ?></td>
          <td class="td font-medium"><?php echo e($inc->employee->name); ?></td>
          <td class="td text-slate-500"><?php echo e($inc->employee->department?->name ?? '—'); ?></td>
          <td class="td"><?php echo e($inc->appraisal_year); ?></td>
          <td class="td text-right font-semibold text-green-700">₹<?php echo e(number_format($inc->increment_amount, 2)); ?></td>
          <td class="td text-center"><?php echo e($inc->increment_percent ? number_format($inc->increment_percent, 1).'%' : '—'); ?></td>
          <td class="td"><?php echo e($inc->increment_effective_date?->format('d M Y') ?? '—'); ?></td>
          <td class="td">
            <?php if($inc->rating_label): ?>
              <span class="badge-green text-xs"><?php echo e($inc->rating_label); ?></span>
            <?php else: ?>
              <span class="text-slate-400">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="td text-center text-slate-400 py-8">No increment records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\increment-history.blade.php ENDPATH**/ ?>