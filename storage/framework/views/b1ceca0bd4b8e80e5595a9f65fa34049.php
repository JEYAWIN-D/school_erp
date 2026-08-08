<?php $__env->startSection('title', 'Fee Collection Register'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Fee Collection Register</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('dashboard')); ?>" class="btn-sm btn-secondary">← Dashboard</a>
      <button onclick="window.print()" class="btn-sm btn-primary">🖨 Print</button>
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">From Date</label>
      <input type="date" name="from" value="<?php echo e($from); ?>" class="input">
    </div>
    <div>
      <label class="label">To Date</label>
      <input type="date" name="to" value="<?php echo e($to); ?>" class="input">
    </div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="<?php echo e(route('reports.fee-collection-register')); ?>" class="btn-sm btn-secondary">Reset</a>
    <div class="ml-auto text-right">
      <p class="text-xs text-slate-400">Total Collection</p>
      <p class="text-xl font-bold text-emerald-600">₹<?php echo e(number_format($totalAmount, 2)); ?></p>
    </div>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">Receipt No.</th>
          <th class="th">Date</th>
          <th class="th">Student</th>
          <th class="th">Adm. No.</th>
          <th class="th">Class</th>
          <th class="th">Fee Head</th>
          <th class="th">Mode</th>
          <th class="th text-right">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td font-mono font-semibold text-indigo-600"><?php echo e($p->receipt_number); ?></td>
          <td class="td"><?php echo e(\Carbon\Carbon::parse($p->payment_date)->format('d/m/Y')); ?></td>
          <td class="td font-medium"><?php echo e($p->first_name); ?> <?php echo e($p->last_name); ?></td>
          <td class="td font-mono text-xs"><?php echo e($p->admission_number); ?></td>
          <td class="td"><?php echo e($p->class_name ?? '—'); ?></td>
          <td class="td text-xs"><?php echo e($p->fee_head ?? '—'); ?></td>
          <td class="td">
            <span class="badge-slate text-xs capitalize"><?php echo e(str_replace('_',' ',$p->payment_mode ?? '')); ?></span>
          </td>
          <td class="td text-right font-semibold">₹<?php echo e(number_format($p->amount, 2)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-slate-400 text-center" colspan="8">No fee payments found for this period.</td></tr>
        <?php endif; ?>
      </tbody>
      <?php if($payments->isNotEmpty()): ?>
      <tfoot>
        <tr class="bg-slate-50">
          <td colspan="7" class="td text-right font-semibold text-slate-700">Page Total:</td>
          <td class="td text-right font-bold text-emerald-700">₹<?php echo e(number_format($payments->sum('amount'), 2)); ?></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>

  <div class="mt-4"><?php echo e($payments->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\reports\fee-collection-register.blade.php ENDPATH**/ ?>