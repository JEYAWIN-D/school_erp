<?php $__env->startSection('title','Update Vehicle Documents'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-2xl">
  <h1 class="page-title">Update Vehicle: <?php echo e($vehicle->vehicle_number); ?></h1>
  <form method="POST" action="<?php echo e(route('transport.vehicle.update',$vehicle->id)); ?>" class="card space-y-4">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">Make</label><input type="text" name="make" class="input" value="<?php echo e(old('make',$vehicle->make)); ?>"></div>
      <div><label class="label">Model</label><input type="text" name="model" class="input" value="<?php echo e(old('model',$vehicle->model)); ?>"></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">Vehicle Number</label><input type="text" name="vehicle_number" class="input" value="<?php echo e(old('vehicle_number',$vehicle->vehicle_number)); ?>" readonly></div>
      <div><label class="label">Vehicle Type</label>
        <select name="vehicle_type" class="select">
          <?php $__currentLoopData = ['bus'=>'Bus','mini_bus'=>'Mini Bus','van'=>'Van','car'=>'Car','auto'=>'Auto']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($k); ?>" <?php if(($vehicle->vehicle_type??'')===$k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </div>
    <h3 class="font-semibold text-slate-700 pt-2 border-t border-slate-100">Document Expiry Dates</h3>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">Fitness Expiry</label><input type="date" name="fitness_expiry" class="input" value="<?php echo e(old('fitness_expiry',$vehicle->fitness_expiry)); ?>"></div>
      <div><label class="label">Insurance Expiry</label><input type="date" name="insurance_expiry" class="input" value="<?php echo e(old('insurance_expiry',$vehicle->insurance_expiry)); ?>"></div>
      <div><label class="label">Permit Expiry</label><input type="date" name="permit_expiry" class="input" value="<?php echo e(old('permit_expiry',$vehicle->permit_expiry)); ?>"></div>
      <div><label class="label">PUC Expiry</label><input type="date" name="puc_expiry" class="input" value="<?php echo e(old('puc_expiry',$vehicle->puc_expiry)); ?>"></div>
      <div><label class="label">Tax Expiry</label><input type="date" name="tax_expiry" class="input" value="<?php echo e(old('tax_expiry',$vehicle->tax_expiry)); ?>"></div>
    </div>
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="<?php echo e(route('transport.documents')); ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\vehicle-edit.blade.php ENDPATH**/ ?>