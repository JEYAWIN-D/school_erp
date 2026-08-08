<?php $__env->startSection('title', 'Online Application Form'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">

  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('admissions.index')); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">Online Application Form</h1>
      <p class="page-subtitle"><?php echo e($academicYear?->name); ?> — Share this page link with parents</p>
    </div>
  </div>

  
  <div class="alert-info">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>This form is accessible from: <strong><?php echo e(url('/admissions/application-form')); ?></strong> — share with prospective parents.</span>
  </div>

  <div class="card">
    <div class="border-b border-slate-100 pb-4 mb-6">
      <h2 class="text-lg font-bold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">Admission Application Form</h2>
      <p class="text-sm text-slate-500 mt-1"><?php echo e($academicYear?->name ?? date('Y') . '–' . (date('Y')+1)); ?></p>
    </div>

    <form method="POST" action="<?php echo e(route('admissions.application-form.store')); ?>" class="space-y-6">
      <?php echo csrf_field(); ?>

      
      <div>
        <h3 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
          <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs flex items-center justify-center font-bold">1</span>
          Student Details
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="label">Student Full Name <span class="text-red-500">*</span></label>
            <input type="text" name="student_name" value="<?php echo e(old('student_name')); ?>" class="input <?php $__errorArgs = ['student_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="First Middle Last Name">
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
            <input type="date" name="dob" value="<?php echo e(old('dob')); ?>" class="input">
          </div>
          <div>
            <label class="label">Gender</label>
            <select name="gender" class="select">
              <option value="">Select</option>
              <option value="male"   <?php if(old('gender') === 'male'): echo 'selected'; endif; ?>>Male</option>
              <option value="female" <?php if(old('gender') === 'female'): echo 'selected'; endif; ?>>Female</option>
              <option value="other"  <?php if(old('gender') === 'other'): echo 'selected'; endif; ?>>Other</option>
            </select>
          </div>
          <div>
            <label class="label">Class Applying For <span class="text-red-500">*</span></label>
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
            <label class="label">Previous School</label>
            <input type="text" name="previous_school" value="<?php echo e(old('previous_school')); ?>" class="input" placeholder="Name of last school attended">
          </div>
        </div>
      </div>

      <hr class="border-slate-100">

      
      <div>
        <h3 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
          <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs flex items-center justify-center font-bold">2</span>
          Parent / Guardian Details
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="label">Parent / Guardian Name <span class="text-red-500">*</span></label>
            <input type="text" name="parent_name" value="<?php echo e(old('parent_name')); ?>" class="input <?php $__errorArgs = ['parent_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
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
            <input type="email" name="parent_email" value="<?php echo e(old('parent_email')); ?>" class="input" placeholder="For communication">
          </div>
          <div>
            <label class="label">Residential Address</label>
            <input type="text" name="address" value="<?php echo e(old('address')); ?>" class="input" placeholder="Full address">
          </div>
        </div>
      </div>

      <div class="pt-2">
        <button type="submit" class="btn btn-primary w-full justify-center">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
          Submit Application
        </button>
      </div>
    </form>
  </div>

  
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-800">Recent Applications</h3>
      <a href="<?php echo e(route('admissions.index', ['status' => 'application'])); ?>" class="text-xs text-blue-600 hover:underline">View all</a>
    </div>
    <?php
      $recent = \App\Models\Enquiry::where('status', 'application')->latest()->take(5)->get();
    ?>
    <?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
        <div>
          <p class="text-sm font-medium text-slate-800"><?php echo e($r->student_name); ?></p>
          <p class="text-xs text-slate-400"><?php echo e($r->enquiry_number); ?> · <?php echo e($r->class?->name); ?></p>
        </div>
        <span class="text-xs text-slate-400"><?php echo e($r->created_at->diffForHumans()); ?></span>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="text-sm text-slate-400 text-center py-4">No applications yet</p>
    <?php endif; ?>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\admissions\application-form.blade.php ENDPATH**/ ?>