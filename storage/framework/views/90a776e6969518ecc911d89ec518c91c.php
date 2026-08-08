<?php $__env->startSection('title', 'Edit Stop'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-lg mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('transport.stops')); ?>" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="page-title">Edit Stop: <?php echo e($stop->name); ?></h1>
  </div>

  <div class="card">
    <form method="POST" action="<?php echo e(route('transport.stops.update', $stop->id)); ?>" class="space-y-4">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <div>
        <label class="label">Route</label>
        <select name="route_id" class="select">
          <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($route->id); ?>" <?php echo e($stop->route_id == $route->id ? 'selected' : ''); ?>><?php echo e($route->route_name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Stop Name</label>
        <input type="text" name="name" class="input" value="<?php echo e(old('name', $stop->name)); ?>" required>
      </div>
      <div>
        <label class="label">Stop Order</label>
        <input type="number" name="stop_order" class="input" value="<?php echo e(old('stop_order', $stop->stop_order)); ?>" min="1" required>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Pickup Time</label><input type="time" name="pickup_time" class="input" value="<?php echo e($stop->pickup_time); ?>"></div>
        <div><label class="label">Drop Time</label><input type="time" name="drop_time" class="input" value="<?php echo e($stop->drop_time); ?>"></div>
      </div>
      <div>
        <label class="label">Monthly Fare (₹)</label>
        <input type="number" name="fare" class="input" value="<?php echo e(old('fare', $stop->fare)); ?>" step="0.01" min="0">
      </div>
      <div>
        <label class="label">Landmark</label>
        <input type="text" name="landmark" class="input" value="<?php echo e(old('landmark', $stop->landmark)); ?>">
      </div>
      <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Update Stop</button>
        <a href="<?php echo e(route('transport.stops')); ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\stop-edit.blade.php ENDPATH**/ ?>