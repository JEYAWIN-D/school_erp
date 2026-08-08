<?php $__env->startSection('title', 'Fine Collection Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Fine Collection Report</h1>
    <a href="<?php echo e(route('library.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  
  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">From</label>
        <input type="date" name="from" value="<?php echo e($from->toDateString()); ?>" class="input text-sm">
      </div>
      <div>
        <label class="label text-xs">To</label>
        <input type="date" name="to" value="<?php echo e($to->toDateString()); ?>" class="input text-sm">
      </div>
      <div>
        <label class="label text-xs">Payment Status</label>
        <select name="paid" class="select text-sm">
          <option value="">All</option>
          <option value="yes" <?php if(request('paid') === 'yes'): echo 'selected'; endif; ?>>Paid</option>
          <option value="no"  <?php if(request('paid') === 'no'): echo 'selected'; endif; ?>>Pending</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    </form>
  </div>

  
  <div class="grid grid-cols-3 gap-4">
    <div class="card text-center">
      <p class="text-2xl font-bold text-slate-800">₹<?php echo e(number_format($totalFine, 2)); ?></p>
      <p class="text-xs text-slate-500 mt-1">Total Fines</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-green-600">₹<?php echo e(number_format($totalPaid, 2)); ?></p>
      <p class="text-xs text-slate-500 mt-1">Collected</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-red-600">₹<?php echo e(number_format($totalPending, 2)); ?></p>
      <p class="text-xs text-slate-500 mt-1">Pending</p>
    </div>
  </div>

  
  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Member</th>
          <th class="th">Book</th>
          <th class="th">Due Date</th>
          <th class="th">Return Date</th>
          <th class="th">Fine (₹)</th>
          <th class="th">Status</th>
          <th class="th">Receipt</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $issues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr <?php echo e($issue->fine_paid ? '' : 'bg-red-50'); ?>">
          <td class="td text-slate-400"><?php echo e($loop->iteration); ?></td>
          <td class="td">
            <?php if($issue->student): ?>
              <?php echo e($issue->student->first_name); ?> <?php echo e($issue->student->last_name); ?><br>
              <span class="text-xs text-slate-400"><?php echo e($issue->student->admission_number); ?></span>
            <?php else: ?>
              Staff
            <?php endif; ?>
          </td>
          <td class="td"><?php echo e($issue->book->title); ?></td>
          <td class="td"><?php echo e($issue->due_date->format('d M Y')); ?></td>
          <td class="td"><?php echo e($issue->return_date?->format('d M Y') ?? '—'); ?></td>
          <td class="td font-semibold"><?php echo e(number_format($issue->fine_amount, 2)); ?></td>
          <td class="td">
            <?php if($issue->fine_paid): ?>
              <span class="badge-green">Paid</span>
            <?php else: ?>
              <span class="badge-red">Pending</span>
            <?php endif; ?>
          </td>
          <td class="td">
            <a href="<?php echo e(route('library.issue.receipt', $issue->id)); ?>" target="_blank" class="btn-xs btn-secondary">Receipt</a>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="td text-center text-slate-400 py-8">No fines in this period.</td></tr>
        <?php endif; ?>
      </tbody>
      <?php if($issues->count()): ?>
      <tfoot>
        <tr class="bg-slate-50">
          <td colspan="5" class="td font-semibold text-right">Total:</td>
          <td class="td font-bold">₹<?php echo e(number_format($totalFine, 2)); ?></td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\fine-report.blade.php ENDPATH**/ ?>