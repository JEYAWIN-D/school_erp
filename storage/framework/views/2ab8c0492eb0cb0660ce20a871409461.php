<?php $__env->startSection('title', 'Purchase Requisitions'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Purchase Requisitions</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('inventory.index')); ?>" class="btn-sm btn-secondary">← Inventory</a>
      <a href="<?php echo e(route('inventory.requisitions.create')); ?>" class="btn-primary btn-sm">+ New Requisition</a>
    </div>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  <form method="GET" class="flex gap-3">
    <select name="status" class="select max-w-xs">
      <option value="">All Status</option>
      <option value="pending" <?php if(request('status')=='pending'): echo 'selected'; endif; ?>>Pending</option>
      <option value="approved" <?php if(request('status')=='approved'): echo 'selected'; endif; ?>>Approved</option>
      <option value="rejected" <?php if(request('status')=='rejected'): echo 'selected'; endif; ?>>Rejected</option>
      <option value="ordered" <?php if(request('status')=='ordered'): echo 'selected'; endif; ?>>Ordered</option>
    </select>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">PR No.</th>
          <th class="th">Required By</th>
          <th class="th">Purpose</th>
          <th class="th">Requested By</th>
          <th class="th">Status</th>
          <th class="th">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $requisitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr" x-data="{ open: false }">
          <td class="td font-mono font-semibold text-indigo-600"><?php echo e($pr->pr_number); ?></td>
          <td class="td"><?php echo e(\Carbon\Carbon::parse($pr->required_by)->format('d M Y')); ?></td>
          <td class="td text-xs text-slate-500"><?php echo e(Str::limit($pr->purpose, 50) ?? '—'); ?></td>
          <td class="td text-xs"><?php echo e($pr->requestedBy?->name ?? '—'); ?></td>
          <td class="td">
            <?php $colors=['pending'=>'badge-amber','approved'=>'badge-green','rejected'=>'badge-red','ordered'=>'badge-blue']; ?>
            <span class="<?php echo e($colors[$pr->status] ?? 'badge-slate'); ?>"><?php echo e(ucfirst($pr->status)); ?></span>
          </td>
          <td class="td flex gap-1">
            <button @click="open=!open" class="btn-xs btn-secondary">Items</button>
            <a href="<?php echo e(route('inventory.requisitions.print', $pr->id)); ?>" target="_blank" class="btn-xs btn-secondary">Print</a>
            <?php if($pr->status === 'pending'): ?>
            <form method="POST" action="<?php echo e(route('inventory.requisitions.approve', $pr->id)); ?>" class="inline"
                  onsubmit="return confirm('Approve requisition PR-<?php echo e($pr->id); ?>?')">
              <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
              <input type="hidden" name="action" value="approve">
              <button type="submit" class="btn-xs btn-primary">Approve</button>
            </form>
            <form method="POST" action="<?php echo e(route('inventory.requisitions.approve', $pr->id)); ?>" class="inline"
                  onsubmit="return confirm('Reject requisition PR-<?php echo e($pr->id); ?>? This cannot be undone.')">
              <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
              <input type="hidden" name="action" value="reject">
              <button type="submit" class="btn-xs btn-secondary text-rose-600">Reject</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <tr x-show="open" x-cloak class="bg-slate-50">
          <td colspan="6" class="px-6 py-3">
            <table class="w-full text-xs">
              <thead><tr class="text-slate-500"><th class="text-left py-1">Item</th><th class="text-right py-1">Qty</th><th class="text-right py-1">Est. Price</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $pr->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td class="py-1"><?php echo e($pi->item?->name ?? 'N/A'); ?></td>
                  <td class="text-right py-1"><?php echo e($pi->quantity); ?> <?php echo e($pi->item?->unit); ?></td>
                  <td class="text-right py-1"><?php echo e($pi->estimated_price ? '₹'.number_format($pi->estimated_price,2) : '—'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-center text-slate-400" colspan="6">No requisitions found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div><?php echo e($requisitions->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\requisitions.blade.php ENDPATH**/ ?>