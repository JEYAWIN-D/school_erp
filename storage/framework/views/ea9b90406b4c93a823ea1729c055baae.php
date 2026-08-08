<?php $__env->startSection('title','Hostel Fee Configuration'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Hostel Fee Configuration</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form method="POST" action="<?php echo e(route('hostel.fee.store')); ?>" class="card space-y-4">
      <?php echo csrf_field(); ?>
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Add Fee Structure</h3>
      <div><label class="label">Hostel <span class="text-red-500">*</span></label>
        <select name="hostel_id" class="select" required>
          <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($h->id); ?>"><?php echo e($h->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div><label class="label">Room Type <span class="text-red-500">*</span></label>
        <select name="room_type" class="select" required>
          <?php $__currentLoopData = ['single'=>'Single Occupancy','double'=>'Double Occupancy','triple'=>'Triple Occupancy','dormitory'=>'Dormitory']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($k); ?>"><?php echo e($v); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Monthly Fee (₹) <span class="text-red-500">*</span></label>
          <input type="number" name="monthly_fee" class="input" required step="0.01" min="0">
        </div>
        <div><label class="label">Admission Fee (₹)</label>
          <input type="number" name="admission_fee" class="input" step="0.01" min="0">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Mess Fee (₹/month)</label>
          <input type="number" name="mess_fee" class="input" step="0.01" min="0">
        </div>
        <div><label class="label">Security Deposit (₹)</label>
          <input type="number" name="security_deposit" class="input" step="0.01" min="0">
        </div>
      </div>
      <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-lg p-3">
        <input type="checkbox" name="mess_included_default" value="1" id="mess_included_default" checked class="w-4 h-4 text-amber-600 rounded mt-0.5">
        <div>
          <label for="mess_included_default" class="text-sm font-medium text-slate-700">Include Mess Fee by Default</label>
          <p class="text-xs text-slate-500 mt-0.5">When checked, new allotments for this fee structure will include mess fee automatically. Can be overridden per student at allotment time.</p>
        </div>
      </div>
      <div><label class="label">Academic Year</label>
        <select name="academic_year_id" class="select">
          <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($y->id); ?>" <?php if($y->is_current): echo 'selected'; endif; ?>><?php echo e($y->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Save Fee Structure</button>
    </form>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Current Fee Structures</h3>
      <?php $__empty_1 = true; $__currentLoopData = $feeStructures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="py-3 border-b border-slate-100 last:border-0">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-semibold text-slate-800 text-sm"><?php echo e($fs->hostel?->name); ?> — <?php echo e(ucfirst($fs->room_type)); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($fs->academicYear?->name); ?></p>
          </div>
          <div class="text-right">
            <p class="font-bold text-slate-800">₹<?php echo e(number_format($fs->monthly_fee,0)); ?>/mo</p>
            <?php if($fs->mess_fee): ?>
            <p class="text-xs text-slate-400">
              +₹<?php echo e(number_format($fs->mess_fee,0)); ?> mess
              <?php if(isset($fs->mess_included_default) && !$fs->mess_included_default): ?>
                <span class="text-amber-500">(optional)</span>
              <?php endif; ?>
            </p>
          <?php endif; ?>
          </div>
        </div>
        <div class="flex gap-4 mt-1 text-xs text-slate-400">
          <?php if($fs->admission_fee): ?><span>Admission: ₹<?php echo e(number_format($fs->admission_fee,0)); ?></span><?php endif; ?>
          <?php if($fs->security_deposit): ?><span>Deposit: ₹<?php echo e(number_format($fs->security_deposit,0)); ?></span><?php endif; ?>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="text-slate-400 text-sm text-center py-6">No fee structures.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\fee-config.blade.php ENDPATH**/ ?>