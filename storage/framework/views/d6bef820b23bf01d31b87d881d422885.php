<?php $__env->startSection('title', 'Individual Student Custom Fee'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Individual Student Custom Fee</h1>
  <p class="text-slate-500 text-sm">Override the standard fee amount for a specific student and fee head.</p>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <div class="card space-y-4">
      <h2 class="font-semibold text-slate-800">Set Custom Fee</h2>
      <form method="GET" class="space-y-3">
        <div>
          <label class="label">Student ID</label>
          <input type="number" name="student_id" value="<?php echo e(request('student_id')); ?>" class="input w-full" placeholder="Enter student ID to look up">
        </div>
        <button type="submit" class="btn btn-secondary btn-sm">Load Student</button>
      </form>

      <?php if($student): ?>
      <div class="bg-slate-50 rounded-lg p-3">
        <div class="font-medium text-slate-800"><?php echo e($student->full_name); ?></div>
        <div class="text-xs text-slate-500"><?php echo e($student->admission_number); ?> | <?php echo e($student->currentEnrollment?->class?->name); ?></div>
      </div>

      <form method="POST" action="<?php echo e(route('fees.student-custom-fee.save')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="student_id" value="<?php echo e($student->id); ?>">
        <div>
          <label class="label">Fee Head *</label>
          <select name="fee_head_id" required class="select w-full">
            <option value="">Select Fee Head</option>
            <?php $__currentLoopData = $feeHeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($h->id); ?>"><?php echo e($h->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Custom Amount (₹) *</label>
          <input type="number" name="amount" min="0" step="0.01" required class="input w-full" placeholder="0.00">
        </div>
        <div>
          <label class="label">Reason</label>
          <input type="text" name="reason" class="input w-full" placeholder="Reason for custom fee...">
        </div>
        <button type="submit" class="btn btn-primary btn-sm w-full">Save Custom Fee</button>
      </form>
      <?php endif; ?>
    </div>

    
    <div class="card">
      <h2 class="font-semibold text-slate-800 mb-4">
        <?php if($student): ?> Custom Fees for <?php echo e($student->full_name); ?> <?php else: ?> Custom Fees <?php endif; ?>
        <?php if($currentYear): ?> <span class="text-xs text-slate-400 font-normal ml-1">(<?php echo e($currentYear->name); ?>)</span><?php endif; ?>
      </h2>
      <?php if($customFees->count()): ?>
      <div class="space-y-2">
        <?php $__currentLoopData = $customFees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
          <div>
            <div class="font-medium text-sm text-slate-800"><?php echo e($cf->feeHead?->name); ?></div>
            <?php if($cf->reason): ?><div class="text-xs text-slate-500"><?php echo e($cf->reason); ?></div><?php endif; ?>
          </div>
          <div class="text-right">
            <div class="font-bold text-slate-800">₹<?php echo e(number_format($cf->custom_amount, 2)); ?></div>
            <div class="text-xs text-slate-400">Set <?php echo e($cf->updated_at?->format('d M')); ?></div>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php elseif($student): ?>
      <p class="text-slate-400 text-sm">No custom fees set for this student yet.</p>
      <?php else: ?>
      <p class="text-slate-400 text-sm">Search for a student above to view their custom fees.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\student-custom-fee.blade.php ENDPATH**/ ?>