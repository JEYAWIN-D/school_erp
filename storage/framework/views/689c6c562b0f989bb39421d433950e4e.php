<?php $__env->startSection('title', 'Add New Employee'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6" x-data="{ category: '<?php echo e(old('employee_type', 'teaching')); ?>' }">

  
  <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
    <div class="flex items-center gap-3">
      <a href="<?php echo e(route('hr.employees')); ?>" class="btn-icon" title="Back to Employee Directory">
        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h1 class="page-title text-xl font-bold text-slate-800">Add New Employee</h1>
        <p class="page-subtitle text-xs text-slate-500 mt-0.5">Fill details below and select employee role category</p>
      </div>
    </div>
  </div>

  <form method="POST" action="<?php echo e(route('hr.employees.store')); ?>" enctype="multipart/form-data" class="space-y-6">
    <?php echo csrf_field(); ?>

    
    <div class="card p-6 border-2 border-indigo-100 bg-gradient-to-r from-indigo-50/40 via-white to-purple-50/40">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center">1</span>
            Select Staff Category / Role <span class="text-red-500">*</span>
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">The employee will be automatically placed into the corresponding card tab</p>
        </div>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">

        
        <label @click="category = 'teaching'"
               :class="category === 'teaching' ? 'border-indigo-600 bg-indigo-50/80 ring-2 ring-indigo-200' : 'border-slate-200 bg-white hover:border-indigo-300'"
               class="cursor-pointer p-3.5 rounded-xl border text-center transition-all duration-150 flex flex-col items-center">
          <input type="radio" name="employee_type" value="teaching" x-model="category" class="sr-only">
          <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
          </div>
          <span class="text-xs font-bold text-slate-800">Teaching Staff</span>
          <span class="text-[10px] text-slate-500 mt-0.5">Teachers</span>
        </label>

        
        <label @click="category = 'non_teaching'"
               :class="category === 'non_teaching' ? 'border-purple-600 bg-purple-50/80 ring-2 ring-purple-200' : 'border-slate-200 bg-white hover:border-purple-300'"
               class="cursor-pointer p-3.5 rounded-xl border text-center transition-all duration-150 flex flex-col items-center">
          <input type="radio" name="employee_type" value="non_teaching" x-model="category" class="sr-only">
          <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"/></svg>
          </div>
          <span class="text-xs font-bold text-slate-800">Non-Teaching</span>
          <span class="text-[10px] text-slate-500 mt-0.5">Admin & Accounts</span>
        </label>

        
        <label @click="category = 'driver'"
               :class="category === 'driver' ? 'border-amber-600 bg-amber-50/80 ring-2 ring-amber-200' : 'border-slate-200 bg-white hover:border-amber-300'"
               class="cursor-pointer p-3.5 rounded-xl border text-center transition-all duration-150 flex flex-col items-center">
          <input type="radio" name="employee_type" value="driver" x-model="category" class="sr-only">
          <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 4h8m-4 4h4m1 4H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2z"/></svg>
          </div>
          <span class="text-xs font-bold text-slate-800">Driver</span>
          <span class="text-[10px] text-slate-500 mt-0.5">Bus & Van</span>
        </label>

        
        <label @click="category = 'cleaner'"
               :class="category === 'cleaner' ? 'border-teal-600 bg-teal-50/80 ring-2 ring-teal-200' : 'border-slate-200 bg-white hover:border-teal-300'"
               class="cursor-pointer p-3.5 rounded-xl border text-center transition-all duration-150 flex flex-col items-center">
          <input type="radio" name="employee_type" value="cleaner" x-model="category" class="sr-only">
          <div class="w-10 h-10 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
          </div>
          <span class="text-xs font-bold text-slate-800">Cleaner</span>
          <span class="text-[10px] text-slate-500 mt-0.5">Housekeeping</span>
        </label>

        
        <label @click="category = 'nanny'"
               :class="category === 'nanny' ? 'border-rose-600 bg-rose-50/80 ring-2 ring-rose-200' : 'border-slate-200 bg-white hover:border-rose-300'"
               class="cursor-pointer p-3.5 rounded-xl border text-center transition-all duration-150 flex flex-col items-center">
          <input type="radio" name="employee_type" value="nanny" x-model="category" class="sr-only">
          <div class="w-10 h-10 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
          </div>
          <span class="text-xs font-bold text-slate-800">Nanny (Naani)</span>
          <span class="text-[10px] text-slate-500 mt-0.5">Caretaker</span>
        </label>

      </div>
    </div>

    
    
    <div x-show="category === 'driver'" class="card p-5 border-amber-200 bg-amber-50/50 space-y-4" style="display:none">
      <h3 class="font-bold text-amber-900 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 4h8m-4 4h4m1 4H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2z"/></svg>
        Driver Specific Details
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">Driving License Number <span class="text-red-500">*</span></label>
          <input type="text" name="license_number" value="<?php echo e(old('license_number')); ?>" class="input uppercase" placeholder="e.g. DL-042019881234">
        </div>
        <div>
          <label class="label">License Expiry Date</label>
          <input type="date" name="license_expiry" value="<?php echo e(old('license_expiry')); ?>" class="input">
        </div>
        <div>
          <label class="label">Assigned Vehicle / Route</label>
          <input type="text" name="assigned_vehicle" value="<?php echo e(old('assigned_vehicle')); ?>" class="input" placeholder="e.g. Bus No. 1 / Route 4">
        </div>
      </div>
    </div>

    
    <div x-show="category === 'nanny'" class="card p-5 border-rose-200 bg-rose-50/50 space-y-4" style="display:none">
      <h3 class="font-bold text-rose-900 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        Nanny (Naani) Specific Details
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Assigned Section / Block</label>
          <input type="text" name="assigned_block" value="<?php echo e(old('assigned_block')); ?>" class="input" placeholder="e.g. Nursery & LKG Section">
        </div>
        <div>
          <label class="label">Shift Timings</label>
          <input type="text" name="shift_timing" value="<?php echo e(old('shift_timing')); ?>" class="input" placeholder="e.g. School Hours (8:00 AM - 2:30 PM)">
        </div>
      </div>
    </div>

    
    <div x-show="category === 'cleaner'" class="card p-5 border-teal-200 bg-teal-50/50 space-y-4" style="display:none">
      <h3 class="font-bold text-teal-900 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
        Cleaner / Housekeeping Details
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Assigned Zone / Building</label>
          <input type="text" name="assigned_block" value="<?php echo e(old('assigned_block')); ?>" class="input" placeholder="e.g. Academic Block A">
        </div>
        <div>
          <label class="label">Work Shift</label>
          <input type="text" name="shift_timing" value="<?php echo e(old('shift_timing')); ?>" class="input" placeholder="e.g. Morning Shift (7:00 AM - 3:30 PM)">
        </div>
      </div>
    </div>

    
    <div class="card p-5 space-y-4">
      <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2 flex items-center gap-2">
        <span class="w-6 h-6 rounded-full bg-slate-700 text-white text-xs font-bold flex items-center justify-center">2</span>
        Personal Information
      </h3>

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
unset($__errorArgs, $__bag); ?>" required placeholder="First name">
          <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
unset($__errorArgs, $__bag); ?>" required placeholder="Last name">
          <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
          <label class="label">Gender <span class="text-red-500">*</span></label>
          <select name="gender" class="select" required>
            <option value="male" <?php if(old('gender')==='male'): echo 'selected'; endif; ?>>Male</option>
            <option value="female" <?php if(old('gender')==='female'): echo 'selected'; endif; ?>>Female</option>
            <option value="other" <?php if(old('gender')==='other'): echo 'selected'; endif; ?>>Other</option>
          </select>
        </div>

        <div>
          <label class="label">Mobile Number <span class="text-red-500">*</span></label>
          <input type="tel" name="mobile" value="<?php echo e(old('mobile')); ?>" class="input <?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="10-digit mobile">
          <?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
          <label class="label">Email Address</label>
          <input type="email" name="official_email" value="<?php echo e(old('official_email')); ?>" class="input" placeholder="email@schoolerp.in">
        </div>

        <div>
          <label class="label">Date of Birth</label>
          <input type="date" name="dob" value="<?php echo e(old('dob')); ?>" class="input">
        </div>

        <div>
          <label class="label">Aadhaar Number</label>
          <input type="text" name="aadhaar_no" value="<?php echo e(old('aadhaar_no')); ?>" class="input" placeholder="12-digit Aadhaar" maxlength="14">
        </div>

        <div>
          <label class="label">PAN Number</label>
          <input type="text" name="pan_no" value="<?php echo e(old('pan_no')); ?>" class="input uppercase" placeholder="ABCDE1234F" maxlength="15">
        </div>

        <div>
          <label class="label">Profile Photo</label>
          <input type="file" name="photo" accept="image/*" class="input text-xs">
        </div>
      </div>
    </div>

    
    <div class="card p-5 space-y-4">
      <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2 flex items-center gap-2">
        <span class="w-6 h-6 rounded-full bg-slate-700 text-white text-xs font-bold flex items-center justify-center">3</span>
        Job & Salary Details
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">Designation <span class="text-red-500">*</span></label>
          <input type="text" name="designation" value="<?php echo e(old('designation')); ?>" class="input <?php $__errorArgs = ['designation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="e.g. Physics Teacher / Driver">
        </div>

        <div>
          <label class="label">Department</label>
          <input type="text" name="department" value="<?php echo e(old('department')); ?>" class="input" placeholder="e.g. Science / Transport / Housekeeping">
        </div>

        <div>
          <label class="label">Joining Date <span class="text-red-500">*</span></label>
          <input type="date" name="joining_date" value="<?php echo e(old('joining_date', date('Y-m-d'))); ?>" class="input" required>
        </div>

        <div>
          <label class="label">Basic Salary (₹/month)</label>
          <input type="number" name="basic_salary" value="<?php echo e(old('basic_salary')); ?>" class="input" min="0" step="0.01" placeholder="e.g. 25000">
        </div>

        <div>
          <label class="label">Qualification</label>
          <input type="text" name="qualification" value="<?php echo e(old('qualification')); ?>" class="input" placeholder="e.g. M.Sc., 10th Pass">
        </div>

        <div>
          <label class="label">Emergency Contact Mobile</label>
          <input type="tel" name="emergency_contact_mobile" value="<?php echo e(old('emergency_contact_mobile')); ?>" class="input" placeholder="Emergency contact number">
        </div>

        <div class="sm:col-span-3">
          <label class="label">Residential Address</label>
          <textarea name="address" rows="2" class="input" placeholder="Current address details"><?php echo e(old('address')); ?></textarea>
        </div>
      </div>
    </div>

    
    <div class="flex items-center justify-end gap-3 bg-white p-4 rounded-2xl border border-slate-200">
      <a href="<?php echo e(route('hr.employees')); ?>" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary px-6 shadow-lg shadow-indigo-100 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Save & Add Employee
      </button>
    </div>

  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\employees-create.blade.php ENDPATH**/ ?>