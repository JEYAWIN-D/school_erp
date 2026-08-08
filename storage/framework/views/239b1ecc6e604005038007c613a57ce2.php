<?php $__env->startSection('title', 'Import Students'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Import Students</h1>
      <p class="page-subtitle">Upload an Excel or CSV file to bulk import student records into the ERP.</p>
    </div>
    <a href="<?php echo e(route('students.index')); ?>" class="btn btn-secondary btn-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      Back to Students
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="importUploader()">
    <div class="lg:col-span-2 space-y-6">
      <div class="card space-y-5">
        <h3 class="font-semibold text-slate-800 pb-3 border-b border-slate-100 flex items-center gap-2">
          <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
          Upload Excel / CSV File
        </h3>

        <?php if(session('error')): ?>
          <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm font-medium">
            <?php echo e(session('error')); ?>

          </div>
        <?php endif; ?>

        <?php if(session('success')): ?>
          <div class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
            <?php echo e(session('success')); ?>

          </div>
        <?php endif; ?>

        <div x-show="errorMessage" x-transition class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm font-medium" style="display: none;">
          <span x-text="errorMessage"></span>
        </div>

        <div x-show="successMessage" x-transition class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium" style="display: none;">
          <span x-text="successMessage"></span>
        </div>

        <form id="importForm" method="POST" action="<?php echo e(route('students.import.process')); ?>" enctype="multipart/form-data" @submit.prevent="submitForm($event)" class="space-y-4">
          <?php echo csrf_field(); ?>

          <div>
            <label class="label mb-1">Select Excel / CSV File <span class="text-red-500">*</span></label>
            <input type="file" name="file" id="fileInput" accept=".xlsx,.xls,.csv" class="input w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" required :disabled="isUploading">
            <p class="text-xs text-slate-500 mt-1">Accepted: <strong>.xlsx, .xls, .csv</strong> &nbsp;&bull;&nbsp; Max size: <strong>20 MB</strong></p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="label mb-1">Default Class <span class="text-xs text-slate-400 font-normal">(Optional)</span></label>
              <select name="class_id" class="select w-full" :disabled="isUploading">
                <option value="">Map automatically from file</option>
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($cls->id); ?>"><?php echo e($cls->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div>
              <label class="label mb-1">Default Section <span class="text-xs text-slate-400 font-normal">(Optional)</span></label>
              <select name="section_id" class="select w-full" :disabled="isUploading">
                <option value="">Map automatically from file</option>
                <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($sec->id); ?>"><?php echo e($sec->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          </div>

          
          <div x-show="isUploading" x-transition class="space-y-2 pt-2" style="display: none;">
            <div class="flex items-center justify-between text-xs font-semibold text-indigo-900">
              <span x-text="statusText">Uploading file...</span>
              <span x-text="progressPercent + '%'">0%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200">
              <div class="bg-indigo-600 h-full rounded-full transition-all duration-200 ease-out flex items-center justify-end pr-1 text-[9px] text-white font-bold"
                   :style="'width: ' + progressPercent + '%'">
              </div>
            </div>
          </div>

          <div class="pt-3 flex items-center justify-between border-t border-slate-100">
            <a href="<?php echo e(route('students.import.template')); ?>" class="btn btn-secondary btn-sm" :class="{ 'pointer-events-none opacity-50': isUploading }">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
              Download Sample Template
            </a>

            <button type="submit" class="btn btn-primary" :disabled="isUploading">
              <template x-if="!isUploading">
                <span class="flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"/></svg>
                  Upload &amp; Process Import
                </span>
              </template>
              <template x-if="isUploading">
                <span class="flex items-center gap-2">
                  <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                  Processing...
                </span>
              </template>
            </button>
          </div>
        </form>
      </div>
    </div>

    <div>
      <div class="card space-y-4 bg-indigo-50/70 border-indigo-100">
        <h3 class="font-semibold text-indigo-900 pb-2 border-b border-indigo-200 flex items-center gap-2">
          <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Smart Import Features
        </h3>

        <ul class="text-xs text-indigo-900 space-y-3 leading-relaxed">
          <li class="flex items-start gap-2">
            <svg class="w-4 h-4 text-indigo-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span><strong>31 Fields Supported:</strong> First/Middle/Last Name, DOB, Gender, Student Type, Blood Group, Category, Religion, Mother Tongue, Aadhaar, Mobile, Email, Addresses, Disability (PwD), Parents, Income, Guardian, Class, Section, Roll No (alphanumeric like <code>PKGA001</code>), House.</span>
          </li>
          <li class="flex items-start gap-2">
            <svg class="w-4 h-4 text-indigo-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span><strong>No Data Alteration:</strong> Alphanumeric Roll Numbers (e.g. <code>PKGA001 -10</code>) are stored exactly as typed.</span>
          </li>
          <li class="flex items-start gap-2">
            <svg class="w-4 h-4 text-indigo-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span><strong>Real-time Progress Tracker:</strong> Visual percentage indicator tracks file upload and database record creation.</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<script>
function importUploader() {
  return {
    isUploading: false,
    progressPercent: 0,
    statusText: 'Uploading file...',
    errorMessage: '',
    successMessage: '',

    submitForm(e) {
      const fileInput = document.getElementById('fileInput');
      if (!fileInput || !fileInput.files.length) {
        this.errorMessage = 'Please select a file to import.';
        return;
      }

      this.isUploading = true;
      this.progressPercent = 0;
      this.statusText = 'Uploading file...';
      this.errorMessage = '';
      this.successMessage = '';

      const form = e.target;
      const formData = new FormData(form);
      const xhr = new XMLHttpRequest();

      // Track file upload progress
      xhr.upload.addEventListener('progress', (event) => {
        if (event.lengthComputable) {
          const percent = Math.round((event.loaded / event.total) * 90);
          this.progressPercent = percent;
          if (percent >= 90) {
            this.statusText = 'Processing & saving student records...';
          }
        }
      });

      xhr.addEventListener('load', () => {
        this.progressPercent = 100;
        if (xhr.status >= 200 && xhr.status < 400) {
          this.statusText = 'Import Completed!';
          // Redirect or display result
          if (xhr.responseURL && xhr.responseURL !== window.location.href) {
            window.location.href = xhr.responseURL;
          } else {
            window.location.reload();
          }
        } else {
          this.isUploading = false;
          this.errorMessage = 'An error occurred during import. Please check your file format and try again.';
        }
      });

      xhr.addEventListener('error', () => {
        this.isUploading = false;
        this.errorMessage = 'Network error occurred while uploading. Please try again.';
      });

      xhr.open('POST', form.action, true);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      xhr.send(formData);
    }
  }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\import.blade.php ENDPATH**/ ?>