<?php $__env->startSection('title', 'Admit Student'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6" x-data="admitForm()">

  <nav class="text-sm text-slate-400 flex items-center gap-1.5 mb-1">
    <a href="<?php echo e(route('students.index')); ?>" class="hover:text-slate-600">Students</a>
    <span>/</span>
    <span class="text-slate-600">Admit New Student</span>
  </nav>
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('students.index')); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">Admit New Student</h1>
      <p class="page-subtitle"><?php echo e($academicYear?->name); ?></p>
    </div>
  </div>

  <form method="POST" action="<?php echo e(route('students.store')); ?>" class="space-y-6">
    <?php echo csrf_field(); ?>

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Personal Information</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">First Name <span class="text-red-500">*</span></label>
          <input type="text" name="first_name" value="<?php echo e(old('first_name')); ?>" class="input <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
          <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="field-error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
          <label class="label">Middle Name</label>
          <input type="text" name="middle_name" value="<?php echo e(old('middle_name')); ?>" class="input">
        </div>
        <div>
          <label class="label">Last Name <span class="text-red-500">*</span></label>
          <input type="text" name="last_name" value="<?php echo e(old('last_name')); ?>" class="input <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
          <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="field-error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
          <label class="label">Date of Birth <span class="text-red-500">*</span></label>
          <input type="date" name="dob" value="<?php echo e(old('dob')); ?>" class="input <?php $__errorArgs = ['dob'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        </div>
        <div>
          <label class="label">Gender <span class="text-red-500">*</span></label>
          <select name="gender" class="select <?php $__errorArgs = ['gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <option value="">Select</option>
            <option value="male"   <?php if(old('gender') === 'male'): echo 'selected'; endif; ?>>Male</option>
            <option value="female" <?php if(old('gender') === 'female'): echo 'selected'; endif; ?>>Female</option>
            <option value="other"  <?php if(old('gender') === 'other'): echo 'selected'; endif; ?>>Other</option>
          </select>
        </div>
        <div>
          <label class="label">Student Type <span class="text-red-500">*</span></label>
          <select name="student_type" class="select">
            <option value="day_scholar" <?php if(old('student_type','day_scholar') === 'day_scholar'): echo 'selected'; endif; ?>>Day Scholar</option>
            <option value="hosteller"   <?php if(old('student_type') === 'hosteller'): echo 'selected'; endif; ?>>Hosteller</option>
            <option value="day_boarder" <?php if(old('student_type') === 'day_boarder'): echo 'selected'; endif; ?>>Day Boarder</option>
          </select>
        </div>
        <div>
          <label class="label">Blood Group</label>
          <select name="blood_group" class="select">
            <option value="">Select</option>
            <?php $__currentLoopData = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($bg); ?>" <?php if(old('blood_group') === $bg): echo 'selected'; endif; ?>><?php echo e($bg); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Category</label>
          <select name="category" class="select">
            <option value="">Select</option>
            <option value="general"  <?php if(old('category') === 'general'): echo 'selected'; endif; ?>>General</option>
            <option value="obc"      <?php if(old('category') === 'obc'): echo 'selected'; endif; ?>>OBC</option>
            <option value="sc"       <?php if(old('category') === 'sc'): echo 'selected'; endif; ?>>SC</option>
            <option value="st"       <?php if(old('category') === 'st'): echo 'selected'; endif; ?>>ST</option>
            <option value="ews"      <?php if(old('category') === 'ews'): echo 'selected'; endif; ?>>EWS</option>
            <option value="minority" <?php if(old('category') === 'minority'): echo 'selected'; endif; ?>>Minority</option>
          </select>
        </div>
        <div>
          <label class="label">Religion</label>
          <input type="text" name="religion" value="<?php echo e(old('religion')); ?>" class="input" placeholder="e.g. Hindu">
        </div>
        <div>
          <label class="label">Mother Tongue</label>
          <input type="text" name="mother_tongue" value="<?php echo e(old('mother_tongue')); ?>" class="input">
        </div>
        <div>
          <label class="label">Aadhaar Number</label>
          <input type="text" name="aadhaar_number" value="<?php echo e(old('aadhaar_number')); ?>" class="input" placeholder="12-digit">
        </div>
        <div>
          <label class="label">Mobile</label>
          <input type="tel" name="mobile" value="<?php echo e(old('mobile')); ?>" class="input">
        </div>
        <div>
          <label class="label">Email</label>
          <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="input">
        </div>
      </div>

      
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
        <div>
          <label class="label">Residential Address</label>
          <textarea name="residential_address" rows="2" class="input" placeholder="Current residential address" x-model="residentialAddress"><?php echo e(old('residential_address')); ?></textarea>
        </div>
        <div>
          <label class="label flex items-center justify-between">
            <span>Permanent Address</span>
            <button type="button" @click="copyAddress()" class="text-xs text-blue-600 hover:underline font-normal">Same as residential</button>
          </label>
          <textarea name="permanent_address" rows="2" class="input" x-model="permanentAddress" placeholder="Permanent address"><?php echo e(old('permanent_address')); ?></textarea>
        </div>
        <div>
          <label class="label">Pincode</label>
          <input type="text" name="pincode" value="<?php echo e(old('pincode')); ?>" class="input w-36" placeholder="6-digit">
        </div>
      </div>

      
      <div class="mt-4 space-y-3">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="is_disabled" value="1" x-model="isDisabled" @change="" class="rounded" <?php if(old('is_disabled')): echo 'checked'; endif; ?>>
          <span class="text-sm text-slate-700">Person with Disability (PwD)</span>
        </label>
        <div x-show="isDisabled" x-transition>
          <label class="label">Disability Description</label>
          <input type="text" name="disability_description" value="<?php echo e(old('disability_description')); ?>" class="input" placeholder="Brief description">
        </div>
      </div>
    </div>

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Parent / Guardian Information</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Father's Name <span class="text-red-500">*</span></label>
          <input type="text" name="father_name" value="<?php echo e(old('father_name')); ?>" class="input <?php $__errorArgs = ['father_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        </div>
        <div>
          <label class="label">Father's Mobile</label>
          <input type="tel" name="father_mobile" value="<?php echo e(old('father_mobile')); ?>" class="input">
        </div>
        <div>
          <label class="label">Father's Occupation</label>
          <input type="text" name="father_occupation" value="<?php echo e(old('father_occupation')); ?>" class="input">
        </div>
        <div>
          <label class="label">Father's Email</label>
          <input type="email" name="father_email" value="<?php echo e(old('father_email')); ?>" class="input">
        </div>
        <div>
          <label class="label">Mother's Name <span class="text-red-500">*</span></label>
          <input type="text" name="mother_name" value="<?php echo e(old('mother_name')); ?>" class="input <?php $__errorArgs = ['mother_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        </div>
        <div>
          <label class="label">Mother's Mobile</label>
          <input type="tel" name="mother_mobile" value="<?php echo e(old('mother_mobile')); ?>" class="input">
        </div>
        <div>
          <label class="label">Mother's Occupation</label>
          <input type="text" name="mother_occupation" value="<?php echo e(old('mother_occupation')); ?>" class="input">
        </div>
        <div>
          <label class="label">Annual Family Income (₹)</label>
          <input type="number" name="annual_family_income" value="<?php echo e(old('annual_family_income')); ?>" class="input" placeholder="0" min="0" step="1000">
        </div>
        <div>
          <label class="label">Guardian Name</label>
          <input type="text" name="guardian_name" value="<?php echo e(old('guardian_name')); ?>" class="input">
        </div>
        <div>
          <label class="label">Guardian Mobile</label>
          <input type="tel" name="guardian_mobile" value="<?php echo e(old('guardian_mobile')); ?>" class="input">
        </div>
      </div>
    </div>

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Class Enrollment</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select <?php $__errorArgs = ['class_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                  @change="loadSections($event.target.value)">
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
          <label class="label">Section</label>
          <select name="section_id" class="select" id="section_select">
            <option value="">Select class first</option>
          </select>
        </div>
        <div>
          <label class="label">Roll Number</label>
          <input type="text" name="roll_number" value="<?php echo e(old('roll_number')); ?>" class="input" placeholder="Auto or manual">
        </div>
        <div>
          <label class="label">House</label>
          <input type="text" name="house" value="<?php echo e(old('house')); ?>" class="input" placeholder="e.g. Red, Blue">
        </div>
      </div>
    </div>

    <div class="flex items-center gap-3 justify-end">
      <a href="<?php echo e(route('students.index')); ?>" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Admit Student
      </button>
    </div>

  </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function admitForm() {
  return {
    residentialAddress: '<?php echo e(old('residential_address')); ?>',
    permanentAddress: '<?php echo e(old('permanent_address')); ?>',
    isDisabled: <?php echo e(old('is_disabled') ? 'true' : 'false'); ?>,
    copyAddress() {
      this.permanentAddress = this.residentialAddress;
    },
    async loadSections(classId) {
      const sel = document.getElementById('section_select');
      if (!classId) { sel.innerHTML = '<option value="">Select class first</option>'; return; }
      sel.innerHTML = '<option value="">Loading…</option>';
      try {
        const res = await fetch(`/api/sections?class_id=${classId}`);
        const data = await res.json();
        sel.innerHTML = '<option value="">Select section</option>' +
          data.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
      } catch {
        sel.innerHTML = '<option value="">No sections found</option>';
      }
    }
  }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\create.blade.php ENDPATH**/ ?>