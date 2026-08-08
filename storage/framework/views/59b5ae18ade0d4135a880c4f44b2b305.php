
<?php $__env->startSection('title', 'Create Notice'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">

  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('academics.notices')); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <h1 class="page-title">Create Notice / Circular</h1>
  </div>

  <form method="POST" action="<?php echo e(route('academics.notices.store')); ?>" class="card space-y-5">
    <?php echo csrf_field(); ?>

    <div>
      <label class="label">Title <span class="text-red-500">*</span></label>
      <input type="text" name="title" value="<?php echo e(old('title')); ?>" class="input <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Notice title">
      <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="field-error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="label">Notice Type <span class="text-red-500">*</span></label>
        <select name="notice_type" class="select">
          <?php $__currentLoopData = ['general'=>'General','circular'=>'Circular','academic'=>'Academic','exam'=>'Exam','fee'=>'Fee','event'=>'Event']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($v); ?>" <?php if(old('notice_type') === $v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div x-data="{ audience: '<?php echo e(old('target_audience','all')); ?>' }">
        <label class="label">Target Audience <span class="text-red-500">*</span></label>
        <select name="target_audience" class="select" x-model="audience">
          <?php $__currentLoopData = ['all'=>'All','students'=>'Students','staff'=>'Staff','parents'=>'Parents','class_specific'=>'Specific Class']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($v); ?>"><?php echo e($l); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <div x-show="audience === 'class_specific'" x-transition class="mt-2">
          <select name="target_class_id" class="select">
            <option value="">Select class</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($cls->id); ?>" <?php if(old('target_class_id') == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div>
        <label class="label">Publish Date <span class="text-red-500">*</span></label>
        <input type="date" name="publish_date" value="<?php echo e(old('publish_date', today()->toDateString())); ?>" class="input">
      </div>
      <div>
        <label class="label">Expiry Date</label>
        <input type="date" name="expiry_date" value="<?php echo e(old('expiry_date')); ?>" class="input">
      </div>
    </div>

    <div>
      <label class="label">Content <span class="text-red-500">*</span></label>
      <textarea name="content" rows="6" class="input <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Notice content…"><?php echo e(old('content')); ?></textarea>
      <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="field-error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="flex items-center gap-2">
      <input type="checkbox" name="is_published" value="1" id="pub_check" class="rounded" <?php if(old('is_published')): echo 'checked'; endif; ?>>
      <label for="pub_check" class="text-sm text-slate-700">Publish immediately</label>
    </div>

    <div class="flex gap-3 pt-2">
      <button type="submit" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Create Notice
      </button>
      <a href="<?php echo e(route('academics.notices')); ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </form>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\notice-form.blade.php ENDPATH**/ ?>