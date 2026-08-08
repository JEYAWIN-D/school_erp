
<?php $__env->startSection('title', 'Substitution Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Substitution Register</h1>
      <p class="page-subtitle">Track teacher substitutions and replacements</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    
    <div class="lg:col-span-2">
      <form method="POST" action="<?php echo e(route('academics.substitutions.store')); ?>" class="card space-y-4">
        <?php echo csrf_field(); ?>
        <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Record Substitution</h3>

        <div>
          <label class="label">Date <span class="text-red-500">*</span></label>
          <input type="date" name="date" value="<?php echo e(old('date', $date)); ?>" class="input">
        </div>
        <div>
          <label class="label">Absent Teacher <span class="text-red-500">*</span></label>
          <select name="absent_teacher_id" class="select <?php $__errorArgs = ['absent_teacher_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <option value="">Select teacher</option>
            <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($t->id); ?>" <?php if(old('absent_teacher_id') == $t->id): echo 'selected'; endif; ?>><?php echo e($t->first_name); ?> <?php echo e($t->last_name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Substitute Teacher <span class="text-red-500">*</span></label>
          <select name="substitute_teacher_id" class="select <?php $__errorArgs = ['substitute_teacher_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <option value="">Select substitute</option>
            <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($t->id); ?>" <?php if(old('substitute_teacher_id') == $t->id): echo 'selected'; endif; ?>><?php echo e($t->first_name); ?> <?php echo e($t->last_name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select">
            <option value="">Select class</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($cls->id); ?>" <?php if(old('class_id') == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label">Period No. <span class="text-red-500">*</span></label>
            <input type="number" name="period_number" value="<?php echo e(old('period_number')); ?>" min="1" max="12" class="input">
          </div>
          <div>
            <label class="label">Subject</label>
            <select name="subject_id" class="select">
              <option value="">Optional</option>
              <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s->id); ?>" <?php if(old('subject_id') == $s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
        </div>
        <div>
          <label class="label">Remarks</label>
          <input type="text" name="remarks" value="<?php echo e(old('remarks')); ?>" class="input" placeholder="Optional notes">
        </div>
        <button type="submit" class="btn btn-primary w-full justify-center">Record Substitution</button>
      </form>
    </div>

    
    <div class="lg:col-span-3">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-800">
          Substitutions on <?php echo e(\Carbon\Carbon::parse($date)->format('d M Y')); ?>

        </h3>
        <form method="GET">
          <input type="date" name="date" value="<?php echo e($date); ?>" class="input w-40 h-8 text-sm" onchange="this.form.submit()">
        </form>
      </div>
      <?php $__empty_1 = true; $__currentLoopData = $substitutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card mb-3">
          <div class="flex items-start justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="badge-red text-xs">Period <?php echo e($sub->period_number); ?></span>
                <?php if($sub->subject): ?> <span class="badge-slate text-xs"><?php echo e($sub->subject->name); ?></span> <?php endif; ?>
                <span class="badge-blue text-xs"><?php echo e($sub->class->name); ?></span>
              </div>
              <p class="text-sm">
                <span class="text-slate-500">Absent:</span>
                <span class="font-medium text-slate-800"><?php echo e($sub->absentTeacher->first_name); ?> <?php echo e($sub->absentTeacher->last_name); ?></span>
              </p>
              <p class="text-sm">
                <span class="text-slate-500">Substitute:</span>
                <span class="font-semibold text-green-700"><?php echo e($sub->substituteTeacher->first_name); ?> <?php echo e($sub->substituteTeacher->last_name); ?></span>
              </p>
              <?php if($sub->remarks): ?> <p class="text-xs text-slate-400 mt-1"><?php echo e($sub->remarks); ?></p> <?php endif; ?>
            </div>
            <form method="POST" action="<?php echo e(route('academics.substitutions.delete', $sub->id)); ?>" onsubmit="return confirm('Remove?')">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button type="submit" class="btn-icon text-red-400 hover:text-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="card text-center py-8 text-slate-400">No substitutions recorded for this date.</div>
      <?php endif; ?>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\substitutions.blade.php ENDPATH**/ ?>