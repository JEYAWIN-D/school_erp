<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Admission Enquiry</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css','resources/js/app.js']); ?>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-lg">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-bold text-slate-800">Admission Enquiry</h1>
      <p class="text-slate-500 text-sm mt-1">Fill in the form below and we'll get back to you.</p>
    </div>
    <?php if(session('success')): ?>
    <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-green-800 text-sm mb-4"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <div class="card space-y-4">
      <form method="POST" action="<?php echo e(route('enquiry.submit')); ?>" class="space-y-4" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div><label class="label">Student Name <span class="text-red-500">*</span></label>
          <input type="text" name="student_name" class="input <?php $__errorArgs = ['student_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required value="<?php echo e(old('student_name')); ?>">
          <?php $__errorArgs = ['student_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div><label class="label">Applying for Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select" required>
            <option value="">Select class</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(old('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="label">Parent/Guardian Name <span class="text-red-500">*</span></label>
            <input type="text" name="parent_name" class="input" required value="<?php echo e(old('parent_name')); ?>">
          </div>
          <div><label class="label">Mobile Number <span class="text-red-500">*</span></label>
            <input type="tel" name="parent_mobile" class="input" required value="<?php echo e(old('parent_mobile')); ?>" placeholder="10-digit mobile">
          </div>
        </div>
        <div><label class="label">Email Address</label>
          <input type="email" name="parent_email" class="input" value="<?php echo e(old('parent_email')); ?>">
        </div>
        <div><label class="label">Academic Year</label>
          <input type="text" class="input bg-slate-50" value="<?php echo e($academicYear?->name ?? 'Current Year'); ?>" readonly>
        </div>
        <div>
          <label class="label">Supporting Documents <span class="text-slate-400 text-xs font-normal">(optional — PDF, JPG, PNG; max 2 MB each)</span></label>
          <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png"
            class="block w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-lg p-1">
          <p class="text-xs text-slate-400 mt-1">You may upload Birth Certificate, Aadhaar, Previous Marksheet, etc.</p>
          <?php $__errorArgs = ['documents.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <button type="submit" class="btn btn-primary w-full">Submit Enquiry</button>
      </form>
    </div>
    <p class="text-center text-xs text-slate-400 mt-4">
      Already applied? <a href="<?php echo e(route('login')); ?>" class="text-indigo-600 hover:underline">Login to check status</a>
    </p>
  </div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\admissions\public-enquiry.blade.php ENDPATH**/ ?>