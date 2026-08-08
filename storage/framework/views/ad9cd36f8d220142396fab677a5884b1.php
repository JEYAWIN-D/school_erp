<?php $__env->startSection('title', 'Goods Receipt Notes'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Goods Receipt Notes (GRN)</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('inventory.purchase-orders')); ?>" class="btn-sm btn-secondary">← POs</a>
      <a href="<?php echo e(route('inventory.grn.create')); ?>" class="btn-primary btn-sm">+ New GRN</a>
    </div>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">GRN No.</th>
          <th class="th">PO No.</th>
          <th class="th">Vendor</th>
          <th class="th">Received Date</th>
          <th class="th">Invoice</th>
          <th class="th text-right">Invoice Amt</th>
          <th class="th">Received By</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr" x-data="{ open: false }">
          <td class="td font-mono font-semibold text-indigo-600 cursor-pointer" @click="open=!open"><?php echo e($grn->grn_number); ?></td>
          <td class="td font-mono text-xs"><?php echo e($grn->purchaseOrder?->po_number ?? '—'); ?></td>
          <td class="td"><?php echo e($grn->purchaseOrder?->vendor?->name ?? '—'); ?></td>
          <td class="td"><?php echo e(\Carbon\Carbon::parse($grn->received_date)->format('d M Y')); ?></td>
          <td class="td text-xs"><?php echo e($grn->invoice_number ?? '—'); ?></td>
          <td class="td text-right"><?php echo e($grn->invoice_amount ? '₹'.number_format($grn->invoice_amount,2) : '—'); ?></td>
          <td class="td text-xs"><?php echo e($grn->receivedBy?->name ?? '—'); ?></td>
        </tr>
        <tr x-show="open" x-cloak class="bg-slate-50">
          <td colspan="7" class="px-6 py-3">
            <table class="w-full text-xs">
              <thead><tr class="text-slate-500"><th class="text-left py-1">Item</th><th class="text-right">Received</th><th class="text-right">Accepted</th><th class="text-right">Rejected</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $grn->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td class="py-1"><?php echo e($gi->item?->name); ?></td>
                  <td class="text-right"><?php echo e($gi->received_qty); ?></td>
                  <td class="text-right text-emerald-600 font-semibold"><?php echo e($gi->accepted_qty); ?></td>
                  <td class="text-right <?php echo e($gi->rejected_qty > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400'); ?>"><?php echo e($gi->rejected_qty); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-center text-slate-400" colspan="7">No GRN records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div><?php echo e($records->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\grn.blade.php ENDPATH**/ ?>