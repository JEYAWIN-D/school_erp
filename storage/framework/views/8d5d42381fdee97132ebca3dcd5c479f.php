<?php $__env->startSection('title', 'Stock Issuances'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Stock Issuances</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('inventory.index')); ?>" class="btn-sm btn-secondary">← Inventory</a>
      <a href="<?php echo e(route('inventory.issuances.create')); ?>" class="btn-primary btn-sm">+ Issue Stock</a>
    </div>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">Issue No.</th>
          <th class="th">Date</th>
          <th class="th">Issued To</th>
          <th class="th">Purpose</th>
          <th class="th">Issued By</th>
          <th class="th">Items</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $issuances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iss): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr" x-data="{ open: false }">
          <td class="td font-mono font-semibold text-indigo-600 cursor-pointer" @click="open=!open"><?php echo e($iss->issue_number); ?></td>
          <td class="td"><?php echo e(\Carbon\Carbon::parse($iss->issue_date)->format('d M Y')); ?></td>
          <td class="td font-medium"><?php echo e($iss->issued_to); ?></td>
          <td class="td text-xs text-slate-500"><?php echo e(Str::limit($iss->purpose, 50) ?? '—'); ?></td>
          <td class="td text-xs"><?php echo e($iss->issuedBy?->name ?? '—'); ?></td>
          <td class="td text-center"><?php echo e($iss->items->count()); ?></td>
        </tr>
        <tr x-show="open" x-cloak class="bg-slate-50">
          <td colspan="6" class="px-6 py-3">
            <table class="w-full text-xs">
              <thead><tr class="text-slate-500"><th class="text-left py-1">Item</th><th class="text-right">Qty</th><th class="text-left pl-4">Remark</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $iss->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $si): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td class="py-1"><?php echo e($si->item?->name); ?></td>
                  <td class="text-right"><?php echo e($si->quantity); ?> <?php echo e($si->item?->unit); ?></td>
                  <td class="pl-4 text-slate-500"><?php echo e($si->remark ?? '—'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-center text-slate-400" colspan="6">No issuances recorded.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div><?php echo e($issuances->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\issuances.blade.php ENDPATH**/ ?>