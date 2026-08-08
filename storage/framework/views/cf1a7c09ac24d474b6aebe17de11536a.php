<?php $__env->startSection('title', 'Warden Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Warden Management</h1>
      <p class="page-subtitle">Assign and manage hostel wardens</p>
    </div>
    <a href="<?php echo e(route('hostel.index')); ?>" class="btn btn-secondary">Back</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <div class="card">
      <h2 class="font-semibold text-slate-800 mb-4">Assign Warden</h2>
      <form method="POST" action="<?php echo e(route('hostel.wardens.assign')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>
        <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
        <div>
          <label class="label">Hostel <span class="text-red-500">*</span></label>
          <select name="hostel_id" class="select" required>
            <option value="">Select Hostel</option>
            <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hostel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($hostel->id); ?>"><?php echo e($hostel->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Warden Name <span class="text-red-500">*</span></label>
          <input type="hidden" name="warden_id" id="warden_id_input">
          <select name="warden_name" class="select" required onchange="document.getElementById('warden_id_input').value=this.options[this.selectedIndex].dataset.id||''; document.getElementById('warden_mobile_input').value=this.options[this.selectedIndex].dataset.mobile||'';">
            <option value="">Select Employee</option>
            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?>" data-id="<?php echo e($emp->id); ?>" data-mobile="<?php echo e($emp->phone ?? ''); ?>">
              <?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?> <?php echo e($emp->designation ? '('.$emp->designation.')' : ''); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Warden Mobile</label>
          <input type="text" name="warden_mobile" id="warden_mobile_input" class="input" placeholder="Phone number">
        </div>
        <button type="submit" class="btn btn-primary">Assign Warden</button>
      </form>
    </div>

    
    <div class="space-y-3">
      <h2 class="font-semibold text-slate-800">Current Wardens</h2>
      <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hostel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-semibold text-slate-800"><?php echo e($hostel->name); ?></h3>
            <p class="text-xs text-slate-400 capitalize"><?php echo e($hostel->type); ?> hostel</p>
          </div>
          <div class="text-right">
            <?php if($hostel->warden_name): ?>
            <?php if($hostel->warden_id): ?>
            <a href="<?php echo e(route('employees.show', $hostel->warden_id)); ?>" class="font-medium text-indigo-600 hover:underline"><?php echo e($hostel->warden_name); ?></a>
            <?php else: ?>
            <p class="font-medium text-slate-700"><?php echo e($hostel->warden_name); ?></p>
            <?php endif; ?>
            <p class="text-xs text-slate-400"><?php echo e($hostel->warden_mobile ?? 'No phone'); ?></p>
            <form method="POST" action="<?php echo e(route('hostel.wardens.remove', $hostel->id)); ?>" class="mt-2">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button type="submit" class="text-xs text-red-500 hover:underline" onclick="return confirm('Remove warden?')">Remove</button>
            </form>
            <?php else: ?>
            <span class="text-slate-400 text-sm italic">No warden assigned</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\wardens.blade.php ENDPATH**/ ?>