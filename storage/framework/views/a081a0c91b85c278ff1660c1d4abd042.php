<?php $__env->startSection('title','Tally Export'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Tally Export</h1>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  
  <div class="card">
    <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100 mb-4">Configure Tally Ledger Mapping</h3>
    <p class="text-sm text-slate-500 mb-3">Map each fee head to its corresponding Tally ledger name. Leave blank to use the fee head name as-is.</p>
    <form method="POST" action="<?php echo e(route('fees.tally.ledger-map')); ?>" class="space-y-2">
      <?php echo csrf_field(); ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <?php $__currentLoopData = $feeHeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex items-center gap-3">
          <span class="text-sm text-slate-600 w-40 shrink-0"><?php echo e($fh->name); ?></span>
          <span class="text-slate-300">→</span>
          <input type="text" name="ledger_names[<?php echo e($fh->id); ?>]" value="<?php echo e($fh->tally_ledger_name); ?>"
                 class="input text-sm flex-1" placeholder="<?php echo e($fh->name); ?> (default)">
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div class="pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Save Mapping</button>
      </div>
    </form>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Export Vouchers</h3>
      <form method="GET" action="<?php echo e(route('fees.tally.download')); ?>" class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div><label class="label">From Date <span class="text-red-500">*</span></label>
            <input type="date" name="from_date" class="input" required value="<?php echo e(request('from_date')); ?>">
          </div>
          <div><label class="label">To Date <span class="text-red-500">*</span></label>
            <input type="date" name="to_date" class="input" required value="<?php echo e(request('to_date')); ?>">
          </div>
        </div>
        <div><label class="label">Voucher Type</label>
          <select name="voucher_type" class="select">
            <option value="receipt">Receipt Vouchers</option>
            <option value="payment">Payment Vouchers</option>
            <option value="all">All</option>
          </select>
        </div>
        <div><label class="label">Format</label>
          <select name="format" class="select">
            <option value="xml">XML (Tally Import)</option>
            <option value="csv">CSV</option>
          </select>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-800">
          <strong>Note:</strong> Export the XML file and import it into Tally ERP via Gateway of Tally → Import of Data → Vouchers.
        </div>
        <button type="submit" class="btn btn-primary">Export for Tally</button>
      </form>
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Export History</h3>
      <?php $__empty_1 = true; $__currentLoopData = $exportLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
        <div>
          <p class="text-sm font-medium text-slate-800"><?php echo e($log->from_date); ?> — <?php echo e($log->to_date); ?></p>
          <p class="text-xs text-slate-400"><?php echo e($log->records_count); ?> records | <?php echo e(strtoupper($log->format)); ?> | By <?php echo e($log->user?->name); ?></p>
        </div>
        <div class="text-right">
          <p class="text-xs text-slate-400"><?php echo e($log->created_at->diffForHumans()); ?></p>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="text-slate-400 text-sm text-center py-6">No export history.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\tally-export.blade.php ENDPATH**/ ?>