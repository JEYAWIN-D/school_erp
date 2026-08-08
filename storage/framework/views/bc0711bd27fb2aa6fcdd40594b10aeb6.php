<?php $__env->startSection('title', 'Leave Encashment Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Leave Encashment Report</h1>
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
      <a href="<?php echo e(route('hr.leave-encashment-report.pdf', request()->only('from','to'))); ?>"
         target="_blank" class="btn btn-secondary btn-sm">PDF</a>
      <a href="<?php echo e(route('hr.leave-encashment-report.excel', request()->only('from','to'))); ?>"
         class="btn btn-secondary btn-sm">Excel</a>
    </form>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div class="card text-center">
      <p class="text-2xl font-bold text-indigo-700"><?php echo e($encashments->count()); ?></p>
      <p class="text-xs text-slate-500 mt-1">Encashments</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-green-600">₹<?php echo e(number_format($totalPaid, 2)); ?></p>
      <p class="text-xs text-slate-500 mt-1">Total Paid</p>
    </div>
  </div>

  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Employee</th>
          <th class="th">Department</th>
          <th class="th">Encashment Date</th>
          <th class="th">Days Encashed</th>
          <th class="th">Per Day (₹)</th>
          <th class="th">Amount (₹)</th>
          <th class="th">Year</th>
          <th class="th">Remarks</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $encashments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td text-slate-400"><?php echo e($loop->iteration); ?></td>
          <td class="td font-medium"><?php echo e($enc->employee->name); ?></td>
          <td class="td text-slate-500"><?php echo e($enc->employee->department?->name ?? '—'); ?></td>
          <td class="td"><?php echo e($enc->encashment_date->format('d M Y')); ?></td>
          <td class="td text-center"><?php echo e($enc->days_encashed ?? '—'); ?></td>
          <td class="td text-right"><?php echo e($enc->basic_per_day ? '₹'.number_format($enc->basic_per_day, 2) : '—'); ?></td>
          <td class="td text-right font-semibold text-green-700">₹<?php echo e(number_format($enc->amount, 2)); ?></td>
          <td class="td text-center"><?php echo e($enc->year); ?></td>
          <td class="td text-slate-500 text-xs"><?php echo e($enc->remarks ?? '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="9" class="td text-center text-slate-400 py-8">No leave encashment records found.</td></tr>
        <?php endif; ?>
      </tbody>
      <?php if($encashments->count()): ?>
      <tfoot>
        <tr class="bg-slate-50 font-semibold">
          <td colspan="6" class="td text-right">Total:</td>
          <td class="td text-right text-green-700">₹<?php echo e(number_format($totalPaid, 2)); ?></td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\leave-encashment-report.blade.php ENDPATH**/ ?>