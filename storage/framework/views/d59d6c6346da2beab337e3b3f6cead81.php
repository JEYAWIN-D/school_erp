<?php $__env->startSection('title', 'Create Online Exam'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">

  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('online-exams.index')); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <h1 class="page-title">Create Online Exam</h1>
  </div>

  <?php if($errors->any()): ?>
    <div class="alert-error">
      <ul class="list-disc pl-5 space-y-1 text-sm">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" action="<?php echo e(route('online-exams.store')); ?>" class="card space-y-4">
    <?php echo csrf_field(); ?>

    <div>
      <label class="label">Exam Title <span class="text-red-500">*</span></label>
      <input type="text" name="title" class="input" value="<?php echo e(old('title')); ?>" required placeholder="e.g. Unit Test 1 — Mathematics">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="label">Class <span class="text-red-500">*</span></label>
        <select name="class_id" class="select" required>
          <option value="">Select Class</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($cls->id); ?>" <?php if(old('class_id') == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Subject</label>
        <select name="subject_id" class="select">
          <option value="">All Subjects</option>
          <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($sub->id); ?>" <?php if(old('subject_id') == $sub->id): echo 'selected'; endif; ?>><?php echo e($sub->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="label">Start Time <span class="text-red-500">*</span></label>
        <input type="datetime-local" name="start_time" class="input" value="<?php echo e(old('start_time')); ?>" required>
      </div>
      <div>
        <label class="label">End Time <span class="text-red-500">*</span></label>
        <input type="datetime-local" name="end_time" class="input" value="<?php echo e(old('end_time')); ?>" required>
      </div>
      <div>
        <label class="label">Duration (minutes) <span class="text-red-500">*</span></label>
        <input type="number" name="duration_minutes" class="input" value="<?php echo e(old('duration_minutes', 60)); ?>" min="5" max="300" required>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="label">Pass Marks</label>
        <input type="number" name="pass_marks" class="input" value="<?php echo e(old('pass_marks', 0)); ?>" step="0.5" min="0">
      </div>
      <div>
        <label class="label">Negative Marks per Wrong Answer</label>
        <input type="number" name="negative_marks_per_wrong" class="input" value="<?php echo e(old('negative_marks_per_wrong', 0)); ?>" step="0.25" min="0">
      </div>
    </div>

    <div class="flex flex-wrap gap-6">
      <label class="flex items-center gap-2 text-sm text-slate-700">
        <input type="checkbox" name="randomise_questions" value="1" <?php if(old('randomise_questions', true)): echo 'checked'; endif; ?> class="rounded">
        Randomise question order per student
      </label>
      <label class="flex items-center gap-2 text-sm text-slate-700">
        <input type="checkbox" name="negative_marking" value="1" <?php if(old('negative_marking')): echo 'checked'; endif; ?> class="rounded">
        Enable negative marking
      </label>
    </div>

    <div>
      <label class="label">Instructions for Students</label>
      <textarea name="instructions" class="input" rows="3" placeholder="Optional instructions shown before the exam starts…"><?php echo e(old('instructions')); ?></textarea>
    </div>

    <div class="flex gap-2 justify-end">
      <a href="<?php echo e(route('online-exams.index')); ?>" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Create Exam & Add Questions →</button>
    </div>
  </form>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\online-exams\create.blade.php ENDPATH**/ ?>