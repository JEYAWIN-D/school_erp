<?php $__env->startSection('title', 'New Course'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-5 max-w-2xl">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Create New Course</h1>
    <a href="<?php echo e(route('lms.index')); ?>" class="btn-secondary btn-sm">← Back</a>
  </div>

  <div class="card">
    <form method="POST" action="<?php echo e(route('lms.courses.store')); ?>" enctype="multipart/form-data" class="space-y-4">
      <?php echo csrf_field(); ?>
      <div>
        <label class="label">Course Title</label>
        <input type="text" name="title" value="<?php echo e(old('title')); ?>" class="input w-full" required>
        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="label">Class</label>
          <select name="class_id" class="select w-full">
            <option value="">— All Classes —</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($cls->id); ?>" <?php echo e(old('class_id') == $cls->id ? 'selected' : ''); ?>><?php echo e($cls->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Subject</label>
          <select name="subject_id" class="select w-full">
            <option value="">— Any Subject —</option>
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($sub->id); ?>" <?php echo e(old('subject_id') == $sub->id ? 'selected' : ''); ?>><?php echo e($sub->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div>
        <label class="label">Description</label>
        <textarea name="description" rows="3" class="input w-full"><?php echo e(old('description')); ?></textarea>
      </div>
      <div>
        <label class="label">Thumbnail (optional)</label>
        <input type="file" name="thumbnail" accept="image/*" class="input w-full">
      </div>
      <div>
        <label class="label">Status</label>
        <select name="status" class="select w-full">
          <option value="draft"     <?php echo e(old('status', 'draft') === 'draft' ? 'selected' : ''); ?>>Draft</option>
          <option value="published" <?php echo e(old('status') === 'published' ? 'selected' : ''); ?>>Published</option>
        </select>
      </div>
      <button type="submit" class="btn-primary w-full">Create Course</button>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\lms\courses\create.blade.php ENDPATH**/ ?>