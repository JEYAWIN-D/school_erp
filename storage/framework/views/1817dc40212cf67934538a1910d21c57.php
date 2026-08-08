<?php $__env->startSection('title', 'Stock Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Stock Report</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('inventory.index')); ?>" class="btn-sm btn-secondary">← Inventory</a>
      <button onclick="window.print()" class="btn-sm btn-primary">🖨 Print</button>
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">Category</label>
      <select name="category_id" class="select">
        <option value="">All Categories</option>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($cat->id); ?>" <?php if(request('category_id')==$cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <label class="flex items-center gap-2 text-sm self-end pb-2">
      <input type="checkbox" name="low_stock" value="1" <?php if(request('low_stock')): echo 'checked'; endif; ?> class="rounded"> Low stock only
    </label>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <div class="ml-auto text-right">
      <p class="text-xs text-slate-400">Total Stock Value</p>
      <p class="text-xl font-bold text-emerald-600">₹<?php echo e(number_format($totalValue, 2)); ?></p>
    </div>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">Code</th>
          <th class="th">Item Name</th>
          <th class="th">Category</th>
          <th class="th">Unit</th>
          <th class="th text-right">Unit Price</th>
          <th class="th text-center">Stock</th>
          <th class="th text-center">Reorder</th>
          <th class="th text-right">Value</th>
          <th class="th">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr <?php echo e($item->isLowStock() ? 'bg-rose-50' : ''); ?>">
          <td class="td font-mono text-xs text-slate-500"><?php echo e($item->item_code); ?></td>
          <td class="td font-medium">
            <?php echo e($item->name); ?>

            <?php if($item->isLowStock()): ?> <span class="badge-red text-xs ml-1">Low</span> <?php endif; ?>
          </td>
          <td class="td text-xs"><?php echo e($item->category?->name ?? '—'); ?></td>
          <td class="td text-xs"><?php echo e($item->unit); ?></td>
          <td class="td text-right">₹<?php echo e(number_format($item->unit_price, 2)); ?></td>
          <td class="td text-center font-semibold <?php echo e($item->isLowStock() ? 'text-rose-600' : 'text-emerald-600'); ?>"><?php echo e($item->current_stock); ?></td>
          <td class="td text-center text-slate-400"><?php echo e($item->reorder_level); ?></td>
          <td class="td text-right font-semibold">₹<?php echo e(number_format($item->current_stock * $item->unit_price, 2)); ?></td>
          <td class="td">
            <?php if($item->current_stock <= 0): ?> <span class="badge-red">Out of Stock</span>
            <?php elseif($item->isLowStock()): ?> <span class="badge-amber">Low Stock</span>
            <?php else: ?> <span class="badge-green">In Stock</span> <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-center text-slate-400" colspan="9">No items found.</td></tr>
        <?php endif; ?>
      </tbody>
      <?php if($items->isNotEmpty()): ?>
      <tfoot>
        <tr class="bg-slate-50 font-semibold">
          <td colspan="7" class="td text-right text-slate-700">Total Stock Value:</td>
          <td class="td text-right text-emerald-700">₹<?php echo e(number_format($totalValue, 2)); ?></td>
          <td class="td"></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\stock-report.blade.php ENDPATH**/ ?>