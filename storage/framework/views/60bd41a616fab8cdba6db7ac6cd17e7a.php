<?php $__env->startSection('title','New Exam'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('examinations.index')); ?>" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="page-title">Create Examination</h1>
  </div>
  <form method="POST" action="<?php echo e(route('examinations.store')); ?>" class="card space-y-4">
    <?php echo csrf_field(); ?>
    <div><label class="label">Exam Name <span class="text-red-500">*</span></label><input type="text" name="name" value="<?php echo e(old('name')); ?>" class="input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="e.g. Term 1 Exam"></div>
    <div><label class="label">Type <span class="text-red-500">*</span></label>
      <select name="type" class="select">
        <?php $__currentLoopData = ['unit_test'=>'Unit Test','term'=>'Term Exam','final'=>'Final Exam','pre_board'=>'Pre-Board','practice'=>'Practice Test']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($v); ?>" <?php if(old('type')===$v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">Start Date <span class="text-red-500">*</span></label><input type="date" name="start_date" value="<?php echo e(old('start_date')); ?>" class="input"></div>
      <div><label class="label">End Date <span class="text-red-500">*</span></label><input type="date" name="end_date" value="<?php echo e(old('end_date')); ?>" class="input"></div>
    </div>
    <div><label class="label">Passing % (default 33)</label><input type="number" name="passing_percentage" value="<?php echo e(old('passing_percentage',33)); ?>" class="input" min="0" max="100"></div>
    <div x-data="{ isExternal: <?php echo e(old('is_external') ? 'true' : 'false'); ?> }">
      <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
        <input type="checkbox" name="is_external" value="1" x-model="isExternal" <?php if(old('is_external')): echo 'checked'; endif; ?>
          class="w-4 h-4 text-indigo-600 rounded">
        External Examination (Conducted by external board/organisation)
      </label>
      <div x-show="isExternal" x-transition class="mt-2">
        <label class="label text-xs">Conducting Body</label>
        <input type="text" name="conducting_body" value="<?php echo e(old('conducting_body')); ?>"
          class="input" placeholder="e.g. CBSE, State Board, NEET, JEE">
      </div>
    </div>
    <div>
      <label class="label">Best-of-N Subjects (CBSE type)</label>
      <input type="number" name="best_of_n_subjects" value="<?php echo e(old('best_of_n_subjects')); ?>" class="input" min="1" placeholder="Leave blank to count all subjects">
      <p class="text-xs text-slate-400 mt-1">If set, only the top N subjects' marks are counted for total/percentage.</p>
    </div>
    <div class="border-t border-slate-100 pt-4">
      <h3 class="font-medium text-slate-700 mb-3 text-sm">Cumulative / Term Settings</h3>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="label">Term Label</label>
          <input type="text" name="term_label" value="<?php echo e(old('term_label')); ?>" class="input" placeholder="e.g. Term 1, Half-Yearly">
        </div>
        <div>
          <label class="label">Weightage %</label>
          <input type="number" name="weightage_percent" value="<?php echo e(old('weightage_percent')); ?>" class="input" min="0" max="100" step="0.01" placeholder="e.g. 30">
        </div>
      </div>
      <label class="flex items-center gap-2 mt-3 text-sm">
        <input type="checkbox" name="is_cumulative_component" value="1" <?php if(old('is_cumulative_component')): echo 'checked'; endif; ?>>
        Include in Cumulative Marks calculation
      </label>
    </div>
    <div class="flex justify-end gap-3 pt-2">
      <a href="<?php echo e(route('examinations.index')); ?>" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Create Exam</button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\create.blade.php ENDPATH**/ ?>