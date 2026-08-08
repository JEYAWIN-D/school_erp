<?php $__env->startSection('title', 'Edit — ' . $employee->first_name); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('hr.employees.show', $employee->id)); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <h1 class="page-title">Edit Employee</h1>
  </div>
  <form method="POST" action="<?php echo e(route('hr.employees.update', $employee->id)); ?>" enctype="multipart/form-data" class="space-y-6">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Personal & Employment</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label class="label">First Name</label><input type="text" name="first_name" value="<?php echo e(old('first_name', $employee->first_name)); ?>" class="input"></div>
        <div><label class="label">Last Name</label><input type="text" name="last_name" value="<?php echo e(old('last_name', $employee->last_name)); ?>" class="input"></div>
        <div><label class="label">Mobile</label><input type="tel" name="mobile" value="<?php echo e(old('mobile', $employee->mobile)); ?>" class="input"></div>
        <div><label class="label">Email</label><input type="email" name="email" value="<?php echo e(old('email', $employee->email)); ?>" class="input"></div>
        <div><label class="label">Designation</label><input type="text" name="designation" value="<?php echo e(old('designation', $employee->designation)); ?>" class="input"></div>
        <div><label class="label">Department</label><input type="text" name="department" value="<?php echo e(old('department', $employee->department)); ?>" class="input"></div>
        <div><label class="label">Type</label>
          <select name="employee_type" class="select">
            <?php $__currentLoopData = ['teaching' => 'Teaching', 'non_teaching' => 'Non-Teaching', 'admin' => 'Admin', 'support' => 'Support']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($v); ?>" <?php if(old('employee_type', $employee->employee_type) === $v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="flex items-center gap-2 pt-5">
          <input type="checkbox" name="is_active" value="1" id="is_active" <?php if(old('is_active', $employee->is_active)): echo 'checked'; endif; ?> class="w-4 h-4 text-blue-600 rounded">
          <label for="is_active" class="text-sm text-slate-700">Active Employee</label>
        </div>
      </div>
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Photo</h3>
      <div class="flex items-center gap-6">
        <?php if($employee->photo): ?>
          <img src="<?php echo e(asset('storage/'.$employee->photo)); ?>" class="w-20 h-20 rounded-full object-cover border-2 border-slate-200" alt="">
        <?php else: ?>
          <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl font-bold">
            <?php echo e(strtoupper(substr($employee->first_name, 0, 1))); ?>

          </div>
        <?php endif; ?>
        <div class="flex-1">
          <label class="label text-xs">Upload New Photo</label>
          <input type="file" name="photo" accept="image/*" class="input text-sm">
          <p class="text-xs text-slate-400 mt-1">JPG/PNG, max 2MB. Replaces existing photo.</p>
        </div>
      </div>
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Statutory Details</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">PF Account No.</label>
          <input type="text" name="pf_account_no" value="<?php echo e(old('pf_account_no', $employee->pf_account_no)); ?>" class="input" placeholder="e.g. TN/52345/12345">
        </div>
        <div>
          <label class="label">ESI No.</label>
          <input type="text" name="esi_no" value="<?php echo e(old('esi_no', $employee->esi_no)); ?>" class="input" placeholder="17-digit ESI number">
        </div>
        <div>
          <label class="label">PAN No.</label>
          <input type="text" name="pan_no" value="<?php echo e(old('pan_no', $employee->pan_no)); ?>" class="input" placeholder="ABCDE1234F" maxlength="15" style="text-transform:uppercase">
        </div>
      </div>
    </div>
    <div class="flex justify-end gap-3">
      <a href="<?php echo e(route('hr.employees.show', $employee->id)); ?>" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\employees-edit.blade.php ENDPATH**/ ?>