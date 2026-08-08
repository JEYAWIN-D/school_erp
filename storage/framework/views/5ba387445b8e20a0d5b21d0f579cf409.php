<?php $__env->startSection('title', 'Inventory Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Inventory Management</h1>
      <p class="page-subtitle"><?php echo e($stats['totalItems']); ?> items across <?php echo e($stats['totalCategories']); ?> categories</p>
    </div>
    <a href="<?php echo e(route('inventory.items.create')); ?>" class="btn btn-primary self-start sm:self-auto">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      Add Item
    </a>
  </div>

  
  <?php if($lowStockCount > 0): ?>
  <div class="rounded-xl border border-red-200 bg-red-50 p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-semibold text-red-800">
      <strong><?php echo e($lowStockCount); ?></strong> item(s) below reorder level —
      <a href="<?php echo e(route('inventory.index', ['low_stock'=>1])); ?>" class="underline">View low stock →</a>
    </p>
  </div>
  <?php endif; ?>

  
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-blue-500 to-indigo-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
      </div>
      <p class="stat-number"><?php echo e($stats['totalItems']); ?></p>
      <p class="text-sm text-slate-500">Items</p>
    </div>
    <a href="<?php echo e(route('inventory.index', ['low_stock'=>1])); ?>" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-red-500 to-rose-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      </div>
      <p class="stat-number <?php echo e($stats['lowStockCount'] > 0 ? 'text-red-600' : ''); ?>"><?php echo e($stats['lowStockCount']); ?></p>
      <p class="text-sm text-slate-500">Low Stock</p>
    </a>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <p class="stat-number text-sm leading-tight">₹<?php echo e(number_format($stats['totalStockValue'])); ?></p>
      <p class="text-sm text-slate-500">Stock Value</p>
    </div>
    <a href="<?php echo e(route('inventory.requisitions')); ?>" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-500 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      </div>
      <p class="stat-number"><?php echo e($stats['pendingRequisitions']); ?></p>
      <p class="text-sm text-slate-500">Pending PRs</p>
    </a>
    <a href="<?php echo e(route('inventory.purchase-orders')); ?>" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-violet-500 to-purple-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      </div>
      <p class="stat-number"><?php echo e($stats['pendingPOs']); ?></p>
      <p class="text-sm text-slate-500">Active POs</p>
    </a>
  </div>

  
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <?php $__currentLoopData = [
      ['Categories',      'inventory.categories',      'from-blue-500 to-indigo-600',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>'],
      ['Vendors',         'inventory.vendors',         'from-teal-500 to-cyan-600',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
      ['Requisitions',    'inventory.requisitions',    'from-amber-500 to-orange-500', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
      ['Purchase Orders', 'inventory.purchase-orders', 'from-violet-500 to-purple-600','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
      ['GRN',             'inventory.grn',             'from-green-500 to-emerald-600','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>'],
      ['Issuances',       'inventory.issuances',       'from-pink-500 to-rose-500',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>'],
      ['Stock Report',    'inventory.stock-report',    'from-slate-500 to-gray-600',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
      ['Add Item',        'inventory.items.create',    'from-indigo-500 to-blue-600',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$route,$color,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route($route)); ?>" class="card-flat flex items-center gap-3 py-3 px-4 hover:shadow-card-md transition">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br <?php echo e($color); ?> flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $icon; ?></svg>
      </div>
      <span class="font-semibold text-sm text-slate-700"><?php echo e($label); ?></span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <h2 class="font-semibold text-slate-700">Item Master</h2>

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
    <div>
      <label class="label">Search</label>
      <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input" placeholder="Name or code…">
    </div>
    <label class="flex items-center gap-2 text-sm text-slate-600 self-end pb-2">
      <input type="checkbox" name="low_stock" value="1" <?php if(request('low_stock')): echo 'checked'; endif; ?> class="rounded"> Low stock only
    </label>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="<?php echo e(route('inventory.index')); ?>" class="btn-sm btn-secondary">Reset</a>
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
          <th class="th">Location</th>
          <th class="th">Status</th>
          <th class="th">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td font-mono text-xs text-slate-500"><?php echo e($item->item_code); ?></td>
          <td class="td font-medium"><?php echo e($item->name); ?></td>
          <td class="td text-xs"><?php echo e($item->category?->name ?? '—'); ?></td>
          <td class="td text-xs text-slate-500"><?php echo e($item->unit); ?></td>
          <td class="td text-right">₹<?php echo e(number_format($item->unit_price, 2)); ?></td>
          <td class="td text-center">
            <span class="font-semibold <?php echo e($item->isLowStock() ? 'text-rose-600' : 'text-emerald-600'); ?>">
              <?php echo e($item->current_stock); ?>

            </span>
          </td>
          <td class="td text-center text-slate-400"><?php echo e($item->reorder_level); ?></td>
          <td class="td text-xs text-slate-500"><?php echo e($item->location ?? '—'); ?></td>
          <td class="td">
            <?php if($item->is_active): ?> <span class="badge-green">Active</span>
            <?php else: ?> <span class="badge-red">Inactive</span> <?php endif; ?>
          </td>
          <td class="td">
            <a href="<?php echo e(route('inventory.items.edit', $item->id)); ?>" class="btn-xs btn-secondary">Edit</a>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-slate-400 text-center" colspan="10">No items found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div><?php echo e($items->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\index.blade.php ENDPATH**/ ?>