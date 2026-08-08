<?php $__env->startSection('title', 'Add Vehicle'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('transport.vehicles')); ?>" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="page-title">Add New Vehicle</h1>
  </div>

  <div class="card">
    <form method="POST" action="<?php echo e(route('transport.vehicles.store')); ?>" class="space-y-5">
      <?php echo csrf_field(); ?>
      <?php if($errors->any()): ?>
      <div class="alert-danger"><ul class="list-disc list-inside text-sm"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
      <?php endif; ?>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Vehicle Number <span class="text-red-500">*</span></label>
          <input type="text" name="vehicle_number" class="input" value="<?php echo e(old('vehicle_number')); ?>" placeholder="e.g. MH-12-AB-1234" required>
        </div>
        <div>
          <label class="label">Registration Number</label>
          <input type="text" name="vehicle_number" class="input" value="<?php echo e(old('vehicle_number')); ?>">
        </div>
        <div>
          <label class="label">Make / Brand</label>
          <input type="text" name="make" class="input" value="<?php echo e(old('make')); ?>" placeholder="e.g. Tata, Ashok Leyland">
        </div>
        <div>
          <label class="label">Model</label>
          <input type="text" name="model" class="input" value="<?php echo e(old('model')); ?>">
        </div>
        <div>
          <label class="label">Seating Capacity <span class="text-red-500">*</span></label>
          <input type="number" name="capacity" class="input" value="<?php echo e(old('capacity', 40)); ?>" min="1" required>
        </div>
        <div>
          <label class="label">Vehicle Type</label>
          <select name="vehicle_type" class="select">
            <option value="">Select Type</option>
            <?php $__currentLoopData = ['bus' => 'Bus', 'van' => 'Van', 'minibus' => 'Mini Bus', 'auto' => 'Auto']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($val); ?>" <?php echo e(old('vehicle_type') == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Assign Route</label>
          <select name="route_id" class="select">
            <option value="">No Route</option>
            <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($route->id); ?>" <?php echo e(old('route_id') == $route->id ? 'selected' : ''); ?>><?php echo e($route->route_name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Driver Name</label>
          <input type="text" name="driver_name" class="input" value="<?php echo e(old('driver_name')); ?>">
        </div>
        <div>
          <label class="label">Driver Mobile</label>
          <input type="text" name="driver_mobile" class="input" value="<?php echo e(old('driver_mobile')); ?>">
        </div>
      </div>

      <h3 class="font-semibold text-slate-700 pt-2 border-t">Document Expiry Dates</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div><label class="label">Fitness Expiry</label><input type="date" name="fitness_expiry" class="input" value="<?php echo e(old('fitness_expiry')); ?>"></div>
        <div><label class="label">Insurance Expiry</label><input type="date" name="insurance_expiry" class="input" value="<?php echo e(old('insurance_expiry')); ?>"></div>
        <div><label class="label">Permit Expiry</label><input type="date" name="permit_expiry" class="input" value="<?php echo e(old('permit_expiry')); ?>"></div>
        <div><label class="label">PUC Expiry</label><input type="date" name="puc_expiry" class="input" value="<?php echo e(old('puc_expiry')); ?>"></div>
        <div><label class="label">Road Tax Expiry</label><input type="date" name="tax_expiry" class="input" value="<?php echo e(old('tax_expiry')); ?>"></div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Add Vehicle</button>
        <a href="<?php echo e(route('transport.vehicles')); ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\vehicle-create.blade.php ENDPATH**/ ?>