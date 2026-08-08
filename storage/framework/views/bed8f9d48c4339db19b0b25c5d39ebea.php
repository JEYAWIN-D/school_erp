<?php $__env->startSection('title', 'Edit — ' . $student->full_name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">

  <nav class="text-sm text-slate-400 flex items-center gap-1.5 mb-1">
    <a href="<?php echo e(route('students.index')); ?>" class="hover:text-slate-600">Students</a>
    <span>/</span>
    <a href="<?php echo e(route('students.show', $student->id)); ?>" class="hover:text-slate-600"><?php echo e($student->full_name); ?></a>
    <span>/</span>
    <span class="text-slate-600">Edit</span>
  </nav>
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('students.show', $student->id)); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">Edit Student</h1>
      <p class="page-subtitle font-mono"><?php echo e($student->admission_number); ?></p>
    </div>
  </div>

  
  <div class="card" x-data="photoUploader()" x-init="init()">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Passport-size Photo</h3>
    <div class="flex flex-col sm:flex-row items-start gap-6">
      
      <div class="flex-shrink-0">
        <div class="w-28 h-32 rounded-lg border-2 border-dashed border-slate-300 overflow-hidden bg-slate-50 flex items-center justify-center">
          <?php if($student->photo && Storage::disk('public')->exists($student->photo)): ?>
            <img id="current-photo" src="<?php echo e(Storage::url($student->photo)); ?>?v=<?php echo e(time()); ?>" class="w-full h-full object-cover">
          <?php else: ?>
            <div id="current-photo-placeholder" class="text-center text-slate-300">
              <svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <p class="text-xs mt-1">No Photo</p>
            </div>
          <?php endif; ?>
        </div>
        <p class="text-xs text-slate-400 mt-1 text-center">Current</p>
      </div>

      
      <div class="flex-1 space-y-3">
        <div>
          <label class="label">Upload New Photo</label>
          <input type="file" id="photo-input" accept="image/*" class="input" @change="onFileSelect($event)">
          <p class="text-xs text-slate-400 mt-1">Recommended: 3x4 cm, JPG/PNG, max 2 MB</p>
        </div>

        <div x-show="showCropper" x-transition class="space-y-3">
          <div class="border border-slate-200 rounded-lg overflow-hidden bg-slate-900" style="max-height:300px;">
            <img id="cropper-image" style="max-width:100%;display:block;">
          </div>
          <div class="flex items-center gap-2 flex-wrap">
            <button type="button" @click="rotateLeft()" class="btn btn-secondary btn-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
              Rotate Left
            </button>
            <button type="button" @click="rotateRight()" class="btn btn-secondary btn-sm">Rotate Right</button>
            <button type="button" @click="cropAndUpload()" class="btn btn-primary btn-sm" :disabled="uploading">
              <span x-show="!uploading">Crop & Save Photo</span>
              <span x-show="uploading">Saving…</span>
            </button>
            <button type="button" @click="cancelCrop()" class="btn btn-ghost btn-sm text-slate-500">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <form method="POST" action="<?php echo e(route('students.update', $student->id)); ?>" class="space-y-6">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Personal Information</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">First Name <span class="text-red-500">*</span></label>
          <input type="text" name="first_name" value="<?php echo e(old('first_name', $student->first_name)); ?>" class="input <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        </div>
        <div>
          <label class="label">Last Name <span class="text-red-500">*</span></label>
          <input type="text" name="last_name" value="<?php echo e(old('last_name', $student->last_name)); ?>" class="input">
        </div>
        <div>
          <label class="label">Date of Birth <span class="text-red-500">*</span></label>
          <input type="date" name="dob" value="<?php echo e(old('dob', $student->dob?->toDateString())); ?>" class="input">
        </div>
        <div>
          <label class="label">Gender <span class="text-red-500">*</span></label>
          <select name="gender" class="select">
            <option value="male"   <?php if(old('gender', $student->gender) === 'male'): echo 'selected'; endif; ?>>Male</option>
            <option value="female" <?php if(old('gender', $student->gender) === 'female'): echo 'selected'; endif; ?>>Female</option>
            <option value="other"  <?php if(old('gender', $student->gender) === 'other'): echo 'selected'; endif; ?>>Other</option>
          </select>
        </div>
        <div>
          <label class="label">Blood Group</label>
          <select name="blood_group" class="select">
            <option value="">Select</option>
            <?php $__currentLoopData = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($bg); ?>" <?php if(old('blood_group', $student->blood_group) === $bg): echo 'selected'; endif; ?>><?php echo e($bg); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Category</label>
          <select name="category" class="select">
            <option value="">Select</option>
            <?php $__currentLoopData = ['general' => 'General', 'obc' => 'OBC', 'sc' => 'SC', 'st' => 'ST', 'ews' => 'EWS']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($val); ?>" <?php if(old('category', $student->category) === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        <div>
          <label class="label">Mobile</label>
          <input type="tel" name="mobile" value="<?php echo e(old('mobile', $student->mobile)); ?>" class="input">
        </div>
        <div>
          <label class="label">Email</label>
          <input type="email" name="email" value="<?php echo e(old('email', $student->email)); ?>" class="input">
        </div>
        <div class="sm:col-span-3" x-data="{ copyAddr() { $el.querySelector('[name=permanent_address]').value = $el.querySelector('[name=residential_address]').value } }">
          <label class="label">Residential Address</label>
          <input type="text" name="residential_address" id="resAddr"
              value="<?php echo e(old('residential_address', $student->residential_address)); ?>" class="input">
        </div>
        <div class="sm:col-span-3" x-data="{}">
          <div class="flex items-center justify-between mb-1">
            <label class="label mb-0">Permanent Address</label>
            <button type="button" class="text-xs text-indigo-600 hover:underline"
                onclick="document.querySelector('[name=permanent_address]').value = document.querySelector('[name=residential_address]').value">
              Same as Residential
            </button>
          </div>
          <input type="text" name="permanent_address"
              value="<?php echo e(old('permanent_address', $student->permanent_address)); ?>" class="input">
        </div>
        <div class="sm:col-span-3" x-data="{ disabled: <?php echo e($student->is_disabled ? 'true' : 'false'); ?> }">
          <div class="flex items-center gap-3 mt-2">
            <input type="checkbox" name="is_disabled" value="1" id="isDisabled" class="rounded w-4 h-4 text-indigo-600"
                x-model="disabled" <?php if($student->is_disabled): echo 'checked'; endif; ?>>
            <label for="isDisabled" class="label cursor-pointer mb-0">Physical Disability</label>
          </div>
          <div x-show="disabled" x-transition class="mt-2">
            <input type="text" name="disability_description" class="input"
                value="<?php echo e(old('disability_description', $student->disability_description)); ?>"
                placeholder="Brief description of disability">
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Parent Information</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Father's Name <span class="text-red-500">*</span></label>
          <input type="text" name="father_name" value="<?php echo e(old('father_name', $student->father_name)); ?>" class="input">
        </div>
        <div>
          <label class="label">Father's Mobile</label>
          <input type="tel" name="father_mobile" value="<?php echo e(old('father_mobile', $student->father_mobile)); ?>" class="input">
        </div>
        <div>
          <label class="label">Mother's Name <span class="text-red-500">*</span></label>
          <input type="text" name="mother_name" value="<?php echo e(old('mother_name', $student->mother_name)); ?>" class="input">
        </div>
        <div>
          <label class="label">Mother's Mobile</label>
          <input type="tel" name="mother_mobile" value="<?php echo e(old('mother_mobile', $student->mother_mobile)); ?>" class="input">
        </div>
        <div>
          <label class="label">Annual Family Income (₹)</label>
          <input type="number" name="annual_family_income" min="0" step="1000"
              value="<?php echo e(old('annual_family_income', $student->annual_family_income)); ?>" class="input"
              placeholder="e.g. 600000">
        </div>
      </div>
    </div>

    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Enrollment Details</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">Student Type</label>
          <select name="student_type" class="select">
            <option value="day_scholar"  <?php if(old('student_type', $student->student_type) === 'day_scholar'): echo 'selected'; endif; ?>>Day Scholar</option>
            <option value="hosteller"    <?php if(old('student_type', $student->student_type) === 'hosteller'): echo 'selected'; endif; ?>>Hosteller</option>
            <option value="day_boarder"  <?php if(old('student_type', $student->student_type) === 'day_boarder'): echo 'selected'; endif; ?>>Day Boarder</option>
          </select>
        </div>
        <div>
          <label class="label">House</label>
          <input type="text" name="house"
              value="<?php echo e(old('house', $student->currentEnrollment?->house)); ?>" class="input"
              placeholder="e.g. Red, Blue, Green, Yellow">
        </div>
        <div>
          <label class="label">Parent/Guardian is Staff Member</label>
          <select name="parent_employee_id" class="select">
            <option value="">None (not a staff ward)</option>
            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($emp->id); ?>" <?php if(old('parent_employee_id', $student->parent_employee_id) == $emp->id): echo 'selected'; endif; ?>>
                <?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?> (<?php echo e($emp->employee_number); ?>)
              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <p class="text-xs text-slate-400 mt-1">Selecting a staff member auto-applies staff ward fee concession.</p>
        </div>
      </div>
    </div>

    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Previous School / Academic History</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Previous School Name</label>
          <input type="text" name="previous_school_name"
              value="<?php echo e(old('previous_school_name', $student->previous_school_name)); ?>" class="input">
        </div>
        <div>
          <label class="label">Previous School Board</label>
          <input type="text" name="previous_school_board"
              value="<?php echo e(old('previous_school_board', $student->previous_school_board)); ?>" class="input"
              placeholder="e.g. CBSE, TNBSE">
        </div>
        <div>
          <label class="label">TC Number</label>
          <input type="text" name="tc_number"
              value="<?php echo e(old('tc_number', $student->tc_number)); ?>" class="input">
        </div>
        <div>
          <label class="label">TC Date</label>
          <input type="date" name="tc_date"
              value="<?php echo e(old('tc_date', $student->tc_date?->toDateString())); ?>" class="input">
        </div>
        <div>
          <label class="label">Previous School Marks / Percentage (%)</label>
          <input type="number" name="previous_percentage" step="0.01" min="0" max="100"
              value="<?php echo e(old('previous_percentage', $student->previous_percentage)); ?>" class="input"
              placeholder="e.g. 85.5">
        </div>
        <div>
          <label class="label">Migration Certificate Number</label>
          <input type="text" name="migration_certificate_number"
              value="<?php echo e(old('migration_certificate_number', $student->migration_certificate_number)); ?>" class="input">
        </div>
        <div>
          <label class="label">Migration Certificate Date</label>
          <input type="date" name="migration_certificate_date"
              value="<?php echo e(old('migration_certificate_date', $student->migration_certificate_date?->toDateString())); ?>" class="input">
        </div>
      </div>
    </div>

    <div class="flex items-center gap-3 justify-end">
      <a href="<?php echo e(route('students.show', $student->id)); ?>" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>

  </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
function photoUploader() {
  return {
    showCropper: false,
    uploading: false,
    cropper: null,
    uploadUrl: '<?php echo e(route("students.photo.upload", $student->id)); ?>',
    csrfToken: '<?php echo e(csrf_token()); ?>',

    init() {},

    onFileSelect(e) {
      const file = e.target.files[0];
      if (!file) return;
      if (file.size > 2 * 1024 * 1024) {
        alert('File too large. Maximum size is 2 MB.');
        e.target.value = '';
        return;
      }
      const reader = new FileReader();
      reader.onload = (ev) => {
        const img = document.getElementById('cropper-image');
        img.src = ev.target.result;
        this.showCropper = true;
        this.$nextTick(() => {
          if (this.cropper) this.cropper.destroy();
          this.cropper = new Cropper(img, {
            aspectRatio: 3 / 4,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.9,
          });
        });
      };
      reader.readAsDataURL(file);
    },

    rotateLeft()  { this.cropper?.rotate(-90); },
    rotateRight() { this.cropper?.rotate(90);  },

    cancelCrop() {
      this.showCropper = false;
      if (this.cropper) { this.cropper.destroy(); this.cropper = null; }
      document.getElementById('photo-input').value = '';
    },

    async cropAndUpload() {
      if (!this.cropper) return;
      this.uploading = true;
      const canvas = this.cropper.getCroppedCanvas({ width: 300, height: 400 });
      const dataUrl = canvas.toDataURL('image/jpeg', 0.92);

      const form = new FormData();
      form.append('_token', this.csrfToken);
      form.append('cropped_image', dataUrl);

      try {
        const resp = await fetch(this.uploadUrl, { method: 'POST', body: form });
        if (resp.ok || resp.redirected) {
          // Update displayed photo without full page reload
          const currentPhoto = document.getElementById('current-photo');
          const placeholder  = document.getElementById('current-photo-placeholder');
          if (currentPhoto) {
            currentPhoto.src = dataUrl;
          } else if (placeholder) {
            placeholder.outerHTML = `<img id="current-photo" src="${dataUrl}" class="w-full h-full object-cover">`;
          }
          this.cancelCrop();
          // Show brief success flash
          const flash = document.createElement('div');
          flash.className = 'fixed top-4 right-4 z-50 alert-success px-4 py-2 rounded shadow text-sm';
          flash.textContent = 'Photo updated successfully.';
          document.body.appendChild(flash);
          setTimeout(() => flash.remove(), 3000);
        } else {
          alert('Upload failed. Please try again.');
        }
      } catch (err) {
        alert('Upload error: ' + err.message);
      } finally {
        this.uploading = false;
      }
    }
  }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\edit.blade.php ENDPATH**/ ?>