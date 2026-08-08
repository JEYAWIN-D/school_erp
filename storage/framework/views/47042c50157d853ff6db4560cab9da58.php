<?php $__env->startSection('title', 'New Enquiry'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">

  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('admissions.index')); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">New Admission Enquiry</h1>
      <p class="page-subtitle">Academic Year: <?php echo e($academicYear?->name ?? 'N/A'); ?></p>
    </div>
  </div>

  <form method="POST" action="<?php echo e(route('admissions.store')); ?>" class="space-y-6">
    <?php echo csrf_field(); ?>

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Student Information</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label class="label">Student Name <span class="text-red-500">*</span></label>
          <input type="text" name="student_name" value="<?php echo e(old('student_name')); ?>" class="input <?php $__errorArgs = ['student_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Full name">
          <?php $__errorArgs = ['student_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="field-error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
          <label class="label">Date of Birth</label>
          <input type="date" name="dob" value="<?php echo e(old('dob')); ?>" class="input <?php $__errorArgs = ['dob'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
          <?php $__errorArgs = ['dob'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="field-error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
          <label class="label">Gender</label>
          <select name="gender" class="select">
            <option value="">Select gender</option>
            <option value="male"   <?php if(old('gender') === 'male'): echo 'selected'; endif; ?>>Male</option>
            <option value="female" <?php if(old('gender') === 'female'): echo 'selected'; endif; ?>>Female</option>
            <option value="other"  <?php if(old('gender') === 'other'): echo 'selected'; endif; ?>>Other</option>
          </select>
        </div>
        <div>
          <label class="label">Applying for Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select <?php $__errorArgs = ['class_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <option value="">Select class</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($cls->id); ?>" <?php if(old('class_id') == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php $__errorArgs = ['class_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="field-error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
          <label class="label">Enquiry Source</label>
          <select name="source" class="select">
            <option value="">Select source</option>
            <?php $__currentLoopData = ['Walk-in', 'Phone Call', 'Website', 'Referral', 'Social Media', 'Newspaper', 'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e(strtolower(str_replace(' ', '-', $src))); ?>" <?php if(old('source') === strtolower(str_replace(' ', '-', $src))): echo 'selected'; endif; ?>><?php echo e($src); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Assigned Counsellor</label>
          <select name="assigned_to" class="select">
            <option value="">— Unassigned —</option>
            <?php $__currentLoopData = $users ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($u->id); ?>" <?php if(old('assigned_to') == $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
    </div>

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Parent / Guardian Information</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Parent Name <span class="text-red-500">*</span></label>
          <input type="text" name="parent_name" value="<?php echo e(old('parent_name')); ?>" class="input <?php $__errorArgs = ['parent_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Father / Mother / Guardian">
          <?php $__errorArgs = ['parent_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="field-error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
          <label class="label">Mobile Number <span class="text-red-500">*</span></label>
          <input type="tel" name="parent_mobile" value="<?php echo e(old('parent_mobile')); ?>" class="input <?php $__errorArgs = ['parent_mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="10-digit mobile">
          <?php $__errorArgs = ['parent_mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="field-error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
          <label class="label">Email Address</label>
          <input type="email" name="parent_email" value="<?php echo e(old('parent_email')); ?>" class="input" placeholder="optional">
        </div>
        <div>
          <label class="label">Follow-up Date</label>
          <input type="date" name="follow_up_date" value="<?php echo e(old('follow_up_date')); ?>" class="input" min="<?php echo e(now()->toDateString()); ?>">
        </div>
        <div class="sm:col-span-2">
          <label class="label">Address</label>
          <input type="text" name="address" value="<?php echo e(old('address')); ?>" class="input" placeholder="Full address">
        </div>
      </div>
    </div>

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Previous School (Optional)</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="sm:col-span-1">
          <label class="label">Previous School</label>
          <input type="text" name="previous_school" value="<?php echo e(old('previous_school')); ?>" class="input" placeholder="School name">
        </div>
        <div>
          <label class="label">Previous Class</label>
          <input type="text" name="previous_class" value="<?php echo e(old('previous_class')); ?>" class="input" placeholder="e.g. Class 5">
        </div>
        <div>
          <label class="label">Percentage / CGPA</label>
          <input type="number" name="previous_percentage" value="<?php echo e(old('previous_percentage')); ?>" class="input" placeholder="0–100" min="0" max="100" step="0.01">
        </div>
      </div>
    </div>

    
    <div class="card">
      <label class="label">Additional Notes</label>
      <textarea name="notes" rows="3" class="input resize-none" placeholder="Any specific requirements or notes…"><?php echo e(old('notes')); ?></textarea>
    </div>

    <div class="flex items-center gap-3 justify-end">
      <a href="<?php echo e(route('admissions.index')); ?>" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Save Enquiry
      </button>
    </div>

  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\admissions\create.blade.php ENDPATH**/ ?>