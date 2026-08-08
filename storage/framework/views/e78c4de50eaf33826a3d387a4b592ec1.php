<?php $__env->startSection('title', 'Assign Driver'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('transport.drivers')); ?>" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="page-title">Assign Driver to Vehicle</h1>
  </div>

  <div class="card">
    <form method="POST" action="<?php echo e(route('transport.drivers.store')); ?>" class="space-y-5">
      <?php echo csrf_field(); ?>
      <?php if($errors->any()): ?>
      <div class="alert-danger"><ul class="list-disc list-inside text-sm"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
      <?php endif; ?>

      <div>
        <label class="label">Vehicle <span class="text-red-500">*</span></label>
        <select name="vehicle_id" class="select" required>
          <option value="">Select Vehicle</option>
          <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($vehicle->id); ?>" <?php echo e(old('vehicle_id') == $vehicle->id ? 'selected' : ''); ?>>
            <?php echo e($vehicle->vehicle_number); ?> (<?php echo e($vehicle->make ?? ''); ?> <?php echo e($vehicle->model ?? ''); ?>)
          </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Driver Name <span class="text-red-500">*</span></label>
        <input type="text" name="driver_name" class="input" value="<?php echo e(old('driver_name')); ?>" required>
      </div>
      <div>
        <label class="label">Driver Mobile <span class="text-red-500">*</span></label>
        <input type="text" name="driver_mobile" class="input" value="<?php echo e(old('driver_mobile')); ?>" required>
      </div>
      <div>
        <label class="label">License Number</label>
        <input type="text" name="license_number" class="input" value="<?php echo e(old('license_number')); ?>">
      </div>
      <div>
        <label class="label">License Expiry</label>
        <input type="date" name="license_expiry" class="input" value="<?php echo e(old('license_expiry')); ?>">
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Assign Driver</button>
        <a href="<?php echo e(route('transport.drivers')); ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\driver-create.blade.php ENDPATH**/ ?>