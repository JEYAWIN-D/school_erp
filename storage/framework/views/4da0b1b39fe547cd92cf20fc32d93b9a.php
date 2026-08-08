<?php $__env->startSection('title', 'Add Transport Route'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('transport.routes')); ?>" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="page-title">Add New Route</h1>
  </div>

  <div class="card">
    <form method="POST" action="<?php echo e(route('transport.routes.store')); ?>" class="space-y-5">
      <?php echo csrf_field(); ?>
      <?php if($errors->any()): ?>
      <div class="alert-danger"><ul class="list-disc list-inside text-sm"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
      <?php endif; ?>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Route Name <span class="text-red-500">*</span></label>
          <input type="text" name="route_name" class="input" value="<?php echo e(old('route_name')); ?>" placeholder="e.g. Route A - North Zone" required>
        </div>
        <div>
          <label class="label">Route Code</label>
          <input type="text" name="route_code" class="input" value="<?php echo e(old('route_code')); ?>" placeholder="e.g. RT-A1">
        </div>
        <div>
          <label class="label">Assign Vehicle</label>
          <select name="vehicle_id" class="select">
            <option value="">No Vehicle</option>
            <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($vehicle->id); ?>" <?php echo e(old('vehicle_id') == $vehicle->id ? 'selected' : ''); ?>>
              <?php echo e($vehicle->vehicle_number); ?> — <?php echo e($vehicle->driver_name ?? 'No Driver'); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Distance (km)</label>
          <input type="number" name="distance_km" class="input" value="<?php echo e(old('distance_km')); ?>" step="0.1" min="0">
        </div>
        <div>
          <label class="label">Start Point</label>
          <input type="text" name="start_point" class="input" value="<?php echo e(old('start_point')); ?>" placeholder="e.g. School Gate">
        </div>
        <div>
          <label class="label">End Point</label>
          <input type="text" name="end_point" class="input" value="<?php echo e(old('end_point')); ?>" placeholder="e.g. Zone End">
        </div>
        <div>
          <label class="label">Morning Pickup Time</label>
          <input type="time" name="start_time" class="input" value="<?php echo e(old('start_time')); ?>">
        </div>
        <div>
          <label class="label">Afternoon Drop Time</label>
          <input type="time" name="end_time" class="input" value="<?php echo e(old('end_time')); ?>">
        </div>
        <div>
          <label class="label">Monthly Fare (₹)</label>
          <input type="number" name="fare" class="input" value="<?php echo e(old('fare')); ?>" step="0.01" min="0">
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Add Route</button>
        <a href="<?php echo e(route('transport.routes')); ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\route-create.blade.php ENDPATH**/ ?>