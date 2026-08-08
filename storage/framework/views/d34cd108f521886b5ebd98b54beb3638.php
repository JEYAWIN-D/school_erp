<?php $__env->startSection('title', isset($alumni) ? 'Edit Alumni' : 'Add Alumni'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title"><?php echo e(isset($alumni) ? 'Edit Alumni' : 'Add Alumni Record'); ?></h1>
    <a href="<?php echo e(route('alumni.index')); ?>" class="btn-sm btn-secondary">← Back</a>
  </div>

  <form method="POST"
    action="<?php echo e(isset($alumni) ? route('alumni.update', $alumni->id) : route('alumni.store')); ?>"
    enctype="multipart/form-data"
    class="card space-y-4">
    <?php echo csrf_field(); ?> <?php if(isset($alumni)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">First Name <span class="text-red-500">*</span></label><input type="text" name="first_name" value="<?php echo e(old('first_name', $alumni->first_name ?? '')); ?>" class="input" required></div>
      <div><label class="label">Last Name <span class="text-red-500">*</span></label><input type="text" name="last_name" value="<?php echo e(old('last_name', $alumni->last_name ?? '')); ?>" class="input" required></div>
      <div><label class="label">Email</label><input type="email" name="email" value="<?php echo e(old('email', $alumni->email ?? '')); ?>" class="input"></div>
      <div><label class="label">Phone</label><input type="tel" name="phone" value="<?php echo e(old('phone', $alumni->phone ?? '')); ?>" class="input"></div>
      <div>
        <label class="label">Link Student Record</label>
        <select name="student_id" class="select">
          <option value="">None</option>
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($s->id); ?>" <?php if(old('student_id', $alumni->student_id ?? '')==$s->id): echo 'selected'; endif; ?>>
            <?php echo e($s->first_name); ?> <?php echo e($s->last_name); ?> (<?php echo e($s->admission_no); ?>)
          </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div><label class="label">Passing Year <span class="text-red-500">*</span></label><input type="number" name="passing_year" value="<?php echo e(old('passing_year', $alumni->passing_year ?? '')); ?>" class="input" min="1990" max="<?php echo e(now()->year+1); ?>" required></div>
      <div><label class="label">Last Class Studied</label><input type="text" name="last_class" value="<?php echo e(old('last_class', $alumni->last_class ?? '')); ?>" class="input"></div>
      <div><label class="label">Current Occupation</label><input type="text" name="current_occupation" value="<?php echo e(old('current_occupation', $alumni->current_occupation ?? '')); ?>" class="input"></div>
      <div><label class="label">Current Employer</label><input type="text" name="current_employer" value="<?php echo e(old('current_employer', $alumni->current_employer ?? '')); ?>" class="input"></div>
      <div><label class="label">Current City</label><input type="text" name="current_city" value="<?php echo e(old('current_city', $alumni->current_city ?? '')); ?>" class="input"></div>
      <div><label class="label">LinkedIn URL</label><input type="url" name="linkedin_url" value="<?php echo e(old('linkedin_url', $alumni->linkedin_url ?? '')); ?>" class="input"></div>
      <div class="col-span-2"><label class="label">Achievements / Notable Info</label><textarea name="achievements" rows="3" class="input"><?php echo e(old('achievements', $alumni->achievements ?? '')); ?></textarea></div>
      <div class="col-span-2">
        <label class="label">Profile Photo</label>
        <input type="file" name="profile_photo" accept="image/*" class="input">
        <?php if(isset($alumni) && $alumni->profile_photo): ?>
        <img src="<?php echo e(Storage::url($alumni->profile_photo)); ?>" class="h-16 rounded-full mt-2">
        <?php endif; ?>
      </div>
    </div>

    <?php if(isset($alumni)): ?>
    <div class="flex flex-wrap gap-4">
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_verified" value="1" <?php if(old('is_verified', $alumni->is_verified ?? false)): echo 'checked'; endif; ?> class="rounded">
        Mark as Verified Alumni
      </label>
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $alumni->is_active ?? true)): echo 'checked'; endif; ?> class="rounded">
        Active Record
      </label>
    </div>
    <?php endif; ?>

    <?php if($errors->any()): ?> <div class="alert-danger text-sm"><?php echo e($errors->first()); ?></div> <?php endif; ?>

    <div class="flex gap-2">
      <button type="submit" class="btn-primary"><?php echo e(isset($alumni) ? 'Update' : 'Add Alumni'); ?></button>
      <a href="<?php echo e(route('alumni.index')); ?>" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\alumni\form.blade.php ENDPATH**/ ?>