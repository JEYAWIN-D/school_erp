<?php $__env->startSection('title', 'Stop Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Stop Management</h1>
      <p class="page-subtitle">Manage pickup and drop stops for each route</p>
    </div>
    <a href="<?php echo e(route('transport.index')); ?>" class="btn btn-secondary">Back</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="card">
      <h2 class="font-semibold text-slate-800 mb-4">Add New Stop</h2>
      <form method="POST" action="<?php echo e(route('transport.stops.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <?php if($errors->any()): ?>
        <div class="alert-danger text-sm"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($e); ?><br><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
        <?php endif; ?>
        <div>
          <label class="label">Route <span class="text-red-500">*</span></label>
          <select name="route_id" class="select" required>
            <option value="">Select Route</option>
            <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($route->id); ?>" <?php echo e((old('route_id') ?? request('route_id')) == $route->id ? 'selected' : ''); ?>><?php echo e($route->route_name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Stop Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" class="input" value="<?php echo e(old('name')); ?>" placeholder="e.g. Main Market" required>
        </div>
        <div>
          <label class="label">Stop Order <span class="text-red-500">*</span></label>
          <input type="number" name="stop_order" class="input" value="<?php echo e(old('stop_order', 1)); ?>" min="1" required>
        </div>
        <div>
          <label class="label">Pickup Time</label>
          <input type="time" name="pickup_time" class="input" value="<?php echo e(old('pickup_time')); ?>">
        </div>
        <div>
          <label class="label">Drop Time</label>
          <input type="time" name="drop_time" class="input" value="<?php echo e(old('drop_time')); ?>">
        </div>
        <div>
          <label class="label">Monthly Fare (₹)</label>
          <input type="number" name="fare" class="input" value="<?php echo e(old('fare')); ?>" step="0.01" min="0">
        </div>
        <div>
          <label class="label">Distance from School (km)</label>
          <input type="number" name="distance_km" class="input" value="<?php echo e(old('distance_km')); ?>" step="0.1" min="0" placeholder="e.g. 5.2">
        </div>
        <div>
          <label class="label">Landmark</label>
          <input type="text" name="landmark" class="input" value="<?php echo e(old('landmark')); ?>" placeholder="Near temple, etc.">
        </div>
        <button type="submit" class="btn btn-primary w-full">Add Stop</button>
      </form>
    </div>

    
    <div class="lg:col-span-2 space-y-4">
      <div class="card-flat py-3">
        <form method="GET" class="flex gap-3 flex-wrap">
          <select name="route_id" class="select w-56">
            <option value="">All Routes</option>
            <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($route->id); ?>" <?php echo e(request('route_id') == $route->id ? 'selected' : ''); ?>><?php echo e($route->route_name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        </form>
      </div>

      <div class="card">
        <div class="table-wrap">
          <table class="w-full">
            <thead>
              <tr>
                <th class="th">#</th>
                <th class="th">Stop Name</th>
                <th class="th">Route</th>
                <th class="th">Order</th>
                <th class="th">Pickup</th>
                <th class="th">Drop</th>
                <th class="th">Fare</th>
                <th class="th">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $stops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr class="tr">
                <td class="td text-slate-400"><?php echo e($loop->iteration); ?></td>
                <td class="td font-medium">
                  <?php echo e($stop->name); ?>

                  <?php if($stop->landmark): ?>
                  <div class="text-xs text-slate-400"><?php echo e($stop->landmark); ?></div>
                  <?php endif; ?>
                </td>
                <td class="td text-slate-500 text-sm"><?php echo e($stop->route?->route_name ?? '—'); ?></td>
                <td class="td text-center"><?php echo e($stop->stop_order); ?></td>
                <td class="td text-sm"><?php echo e($stop->pickup_time ? \Carbon\Carbon::parse($stop->pickup_time)->format('h:i A') : '—'); ?></td>
                <td class="td text-sm"><?php echo e($stop->drop_time ? \Carbon\Carbon::parse($stop->drop_time)->format('h:i A') : '—'); ?></td>
                <td class="td"><?php echo e($stop->fare ? '₹' . $stop->fare : '—'); ?></td>
                <td class="td">
                  <a href="<?php echo e(route('transport.stops.edit', $stop->id)); ?>" class="btn-icon" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                  </a>
                  <form method="POST" action="<?php echo e(route('transport.stops.delete', $stop->id)); ?>" class="inline">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn-icon text-red-500" onclick="return confirm('Delete this stop?')">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                  </form>
                </td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="8" class="td text-center py-8 text-slate-400">No stops found. Add your first stop.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <?php if($stops->hasPages()): ?><div class="mt-4"><?php echo e($stops->links()); ?></div><?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\stops.blade.php ENDPATH**/ ?>