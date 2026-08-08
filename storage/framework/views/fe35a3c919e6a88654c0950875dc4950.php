<?php $__env->startSection('title', 'Edit Enquiry — ' . $enquiry->enquiry_number); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">

  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('admissions.show', $enquiry->id)); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">Edit Enquiry</h1>
      <p class="page-subtitle font-mono"><?php echo e($enquiry->enquiry_number); ?></p>
    </div>
  </div>

  <form method="POST" action="<?php echo e(route('admissions.update', $enquiry->id)); ?>" class="space-y-6">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Student Information</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label class="label">Student Name <span class="text-red-500">*</span></label>
          <input type="text" name="student_name" value="<?php echo e(old('student_name', $enquiry->student_name)); ?>" class="input <?php $__errorArgs = ['student_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
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
          <input type="date" name="dob" value="<?php echo e(old('dob', $enquiry->dob?->toDateString())); ?>" class="input">
        </div>
        <div>
          <label class="label">Gender</label>
          <select name="gender" class="select">
            <option value="">Select gender</option>
            <option value="male"   <?php if(old('gender', $enquiry->gender) === 'male'): echo 'selected'; endif; ?>>Male</option>
            <option value="female" <?php if(old('gender', $enquiry->gender) === 'female'): echo 'selected'; endif; ?>>Female</option>
            <option value="other"  <?php if(old('gender', $enquiry->gender) === 'other'): echo 'selected'; endif; ?>>Other</option>
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
              <option value="<?php echo e($cls->id); ?>" <?php if(old('class_id', $enquiry->class_id) == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Source</label>
          <select name="source" class="select">
            <option value="">Select source</option>
            <?php $__currentLoopData = ['walk-in' => 'Walk-in', 'phone-call' => 'Phone Call', 'website' => 'Website', 'referral' => 'Referral', 'social-media' => 'Social Media', 'newspaper' => 'Newspaper', 'other' => 'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($val); ?>" <?php if(old('source', $enquiry->source) === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Assigned Counsellor</label>
          <select name="assigned_to" class="select">
            <option value="">— Unassigned —</option>
            <?php $__currentLoopData = $users ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($u->id); ?>" <?php if(old('assigned_to', $enquiry->assigned_to) == $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
    </div>

    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Parent / Guardian</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Parent Name <span class="text-red-500">*</span></label>
          <input type="text" name="parent_name" value="<?php echo e(old('parent_name', $enquiry->parent_name)); ?>" class="input <?php $__errorArgs = ['parent_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        </div>
        <div>
          <label class="label">Mobile <span class="text-red-500">*</span></label>
          <input type="tel" name="parent_mobile" value="<?php echo e(old('parent_mobile', $enquiry->parent_mobile)); ?>" class="input">
        </div>
        <div>
          <label class="label">Email</label>
          <input type="email" name="parent_email" value="<?php echo e(old('parent_email', $enquiry->parent_email)); ?>" class="input">
        </div>
        <div>
          <label class="label">Follow-up Date</label>
          <input type="date" name="follow_up_date" value="<?php echo e(old('follow_up_date', $enquiry->follow_up_date?->toDateString())); ?>" class="input">
        </div>
        <div class="sm:col-span-2">
          <label class="label">Address</label>
          <input type="text" name="address" value="<?php echo e(old('address', $enquiry->address)); ?>" class="input">
        </div>
      </div>
    </div>

    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Previous School</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">School Name</label>
          <input type="text" name="previous_school" value="<?php echo e(old('previous_school', $enquiry->previous_school)); ?>" class="input">
        </div>
        <div>
          <label class="label">Class</label>
          <input type="text" name="previous_class" value="<?php echo e(old('previous_class', $enquiry->previous_class)); ?>" class="input">
        </div>
        <div>
          <label class="label">Percentage</label>
          <input type="number" name="previous_percentage" value="<?php echo e(old('previous_percentage', $enquiry->previous_percentage)); ?>" class="input" min="0" max="100" step="0.01">
        </div>
      </div>
    </div>

    <div class="card">
      <label class="label">Notes</label>
      <textarea name="notes" rows="3" class="input resize-none"><?php echo e(old('notes', $enquiry->notes)); ?></textarea>
    </div>

    <div class="flex items-center gap-3 justify-end">
      <a href="<?php echo e(route('admissions.show', $enquiry->id)); ?>" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>

  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\admissions\edit.blade.php ENDPATH**/ ?>