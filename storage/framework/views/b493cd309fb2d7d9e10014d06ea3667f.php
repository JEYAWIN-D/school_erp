<?php $__env->startSection('title', 'Fee Collection Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Fee Collection Report</h1>
      <p class="text-sm text-slate-500 mt-0.5"><?php echo e($year?->name ?? 'All Years'); ?></p>
    </div>
    <div class="flex gap-2">
      <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline">← Back</a>
      <a href="<?php echo e(route('reports.fee.excel', request()->query())); ?>" class="btn btn-secondary btn-sm">Export Excel</a>
    </div>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">From</label>
        <input type="date" name="from" value="<?php echo e($from->toDateString()); ?>" class="input">
      </div>
      <div>
        <label class="label">To</label>
        <input type="date" name="to" value="<?php echo e($to->toDateString()); ?>" class="input">
      </div>
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($c->id); ?>" <?php if(request('class_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button class="btn btn-primary">Filter</button>
    </form>
  </div>

  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4">Class-wise Collection — <?php echo e($from->format('d M Y')); ?> to <?php echo e($to->format('d M Y')); ?></h2>
    <?php if($classwise->count()): ?>
    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr>
            <th>Class</th>
            <th class="text-right">Students Paid</th>
            <th class="text-right">Demand (₹)</th>
            <th class="text-right">Collected (₹)</th>
            <th class="text-right">Outstanding (₹)</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $classwise; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="font-medium"><?php echo e($row->class_name); ?></td>
            <td class="text-right"><?php echo e($row->payers); ?></td>
            <td class="text-right"><?php echo e($row->demand > 0 ? number_format($row->demand, 2) : '—'); ?></td>
            <td class="text-right font-semibold text-emerald-700"><?php echo e(number_format($row->collected, 2)); ?></td>
            <td class="text-right <?php echo e(($row->outstanding ?? 0) > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400'); ?>">
              <?php echo e(($row->outstanding ?? 0) > 0 ? number_format($row->outstanding, 2) : '—'); ?>

            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <tr class="font-bold bg-slate-50">
            <td>Total</td>
            <td class="text-right"><?php echo e($classwise->sum('payers')); ?></td>
            <td class="text-right"><?php echo e(number_format($classwise->sum('demand'), 2)); ?></td>
            <td class="text-right text-emerald-700"><?php echo e(number_format($classwise->sum('collected'), 2)); ?></td>
            <td class="text-right text-rose-600"><?php echo e(number_format($classwise->sum('outstanding'), 2)); ?></td>
          </tr>
        </tbody>
      </table>
    </div>
    <?php else: ?>
      <p class="text-slate-400 text-center py-8">No fee payments in this date range.</p>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\reports\fee.blade.php ENDPATH**/ ?>