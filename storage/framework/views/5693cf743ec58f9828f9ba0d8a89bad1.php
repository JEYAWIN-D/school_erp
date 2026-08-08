<?php $__env->startSection('title', 'Purchase Orders'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Purchase Orders</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('inventory.grn.create')); ?>" class="btn-sm btn-secondary">Receive Stock (GRN)</a>
      <a href="<?php echo e(route('inventory.purchase-orders.create')); ?>" class="btn-primary btn-sm">+ Create PO</a>
    </div>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  <form method="GET" class="flex gap-3">
    <select name="status" class="select">
      <option value="">All Status</option>
      <?php $__currentLoopData = ['draft','sent','partial','received','cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($st); ?>" <?php if(request('status')===$st): echo 'selected'; endif; ?>><?php echo e(ucfirst($st)); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button class="btn-primary btn-sm">Filter</button>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">PO No.</th>
          <th class="th">Vendor</th>
          <th class="th">Order Date</th>
          <th class="th">Expected Delivery</th>
          <th class="th text-right">Amount</th>
          <th class="th">Status</th>
          <th class="th">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr" x-data="{ open: false }">
          <td class="td font-mono font-semibold text-indigo-600"><?php echo e($po->po_number); ?></td>
          <td class="td font-medium"><?php echo e($po->vendor?->name ?? '—'); ?></td>
          <td class="td"><?php echo e(\Carbon\Carbon::parse($po->order_date)->format('d M Y')); ?></td>
          <td class="td"><?php echo e($po->expected_delivery ? \Carbon\Carbon::parse($po->expected_delivery)->format('d M Y') : '—'); ?></td>
          <td class="td text-right font-semibold">₹<?php echo e(number_format($po->total_amount, 2)); ?></td>
          <td class="td">
            <?php $c=['draft'=>'badge-slate','sent'=>'badge-blue','partial'=>'badge-amber','received'=>'badge-green','cancelled'=>'badge-red']; ?>
            <span class="<?php echo e($c[$po->status] ?? 'badge-slate'); ?>"><?php echo e(ucfirst($po->status)); ?></span>
          </td>
          <td class="td">
            <button @click="open=!open" class="btn-xs btn-secondary">Items</button>
            <a href="<?php echo e(route('inventory.purchase-orders.print', $po->id)); ?>" target="_blank" class="btn-xs btn-secondary ml-1">Print</a>
            <?php if(in_array($po->status,['draft','sent','partial'])): ?>
            <a href="<?php echo e(route('inventory.grn.create', ['po_id'=>$po->id])); ?>" class="btn-xs btn-primary ml-1">Receive</a>
            <?php endif; ?>
          </td>
        </tr>
        <tr x-show="open" x-cloak class="bg-slate-50">
          <td colspan="7" class="px-6 py-3">
            <table class="w-full text-xs">
              <thead><tr class="text-slate-500"><th class="text-left py-1">Item</th><th class="text-right">Qty</th><th class="text-right">Unit Price</th><th class="text-right">Total</th><th class="text-right">Received</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $po->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td class="py-1"><?php echo e($pi->item?->name); ?></td>
                  <td class="text-right"><?php echo e($pi->quantity); ?></td>
                  <td class="text-right">₹<?php echo e(number_format($pi->unit_price,2)); ?></td>
                  <td class="text-right font-semibold">₹<?php echo e(number_format($pi->total_price,2)); ?></td>
                  <td class="text-right <?php echo e($pi->received_qty >= $pi->quantity ? 'text-emerald-600' : 'text-amber-600'); ?>"><?php echo e($pi->received_qty); ?>/<?php echo e($pi->quantity); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-center text-slate-400" colspan="7">No purchase orders found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div><?php echo e($orders->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\purchase-orders.blade.php ENDPATH**/ ?>