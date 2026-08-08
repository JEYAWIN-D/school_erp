<?php $__env->startSection('title', 'Issue Outpass'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-xl space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Issue Student Outpass</h1>
    <a href="<?php echo e(route('gate.outpass')); ?>" class="btn-sm btn-secondary">← Outpass</a>
  </div>

  <form method="POST" action="<?php echo e(route('gate.outpass.store')); ?>" class="card space-y-4">
    <?php echo csrf_field(); ?>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label">Pass Number</label>
        <input type="text" name="pass_number" value="<?php echo e($passNumber); ?>" class="input" readonly>
      </div>
      <div>
        <label class="label">Student <span class="text-red-500">*</span></label>
        <select name="student_id" class="select" required>
          <option value="">Select student</option>
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($s->id); ?>"><?php echo e($s->first_name); ?> <?php echo e($s->last_name); ?> (<?php echo e($s->admission_number); ?>)</option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Out Time <span class="text-red-500">*</span></label>
        <input type="datetime-local" name="out_time" value="<?php echo e(now()->format('Y-m-d\TH:i')); ?>" class="input" required>
      </div>
      <div>
        <label class="label">Expected Return</label>
        <input type="datetime-local" name="expected_return" class="input">
      </div>
      <div class="col-span-2">
        <label class="label">Reason <span class="text-red-500">*</span></label>
        <input type="text" name="reason" class="input" required>
      </div>
      <div class="col-span-2">
        <label class="label">Authorized By <span class="text-red-500">*</span></label>
        <input type="text" name="authorized_by" class="input" placeholder="Class teacher / HOD name" required>
      </div>
    </div>

    <?php if($errors->any()): ?> <div class="alert-danger text-sm"><?php echo e($errors->first()); ?></div> <?php endif; ?>

    <div class="flex gap-2">
      <button type="submit" class="btn-primary">Issue Outpass</button>
      <a href="<?php echo e(route('gate.outpass')); ?>" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\gate\outpass-form.blade.php ENDPATH**/ ?>