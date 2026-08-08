<?php $__env->startSection('title', 'School Settings'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">School Settings</h1>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>
  <?php if($errors->any()): ?>
    <div class="alert-danger"><ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    
    <div class="lg:col-span-2 card space-y-5">
      <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">General Information</h2>
      <form method="POST" action="<?php echo e(route('settings.save')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="label">School Name <span class="text-red-500">*</span></label>
            <input type="text" name="school_name" class="input" required value="<?php echo e(old('school_name', $school?->school_name)); ?>">
          </div>
          <div>
            <label class="label">School Code</label>
            <input type="text" name="school_code" class="input" value="<?php echo e(old('school_code', $school?->school_code)); ?>">
          </div>
          <div>
            <label class="label">Affiliation No.</label>
            <input type="text" name="affiliation_no" class="input" value="<?php echo e(old('affiliation_no', $school?->affiliation_no)); ?>">
          </div>
          <div>
            <label class="label">Board</label>
            <select name="board" class="select">
              <?php $__currentLoopData = ['CBSE','ICSE','State Board','IB','NIOS']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option <?php if(old('board',$school?->board)===$b): echo 'selected'; endif; ?>><?php echo e($b); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="sm:col-span-2">
            <label class="label">Address</label>
            <textarea name="address" class="input h-14 text-sm"><?php echo e(old('address', $school?->address)); ?></textarea>
          </div>
          <div>
            <label class="label">City</label>
            <input type="text" name="city" class="input" value="<?php echo e(old('city', $school?->city)); ?>">
          </div>
          <div>
            <label class="label">State</label>
            <input type="text" name="state" class="input" value="<?php echo e(old('state', $school?->state)); ?>">
          </div>
          <div>
            <label class="label">Pincode</label>
            <input type="text" name="pincode" class="input" maxlength="10" value="<?php echo e(old('pincode', $school?->pincode)); ?>">
          </div>
          <div>
            <label class="label">Phone</label>
            <input type="text" name="phone" class="input" value="<?php echo e(old('phone', $school?->phone)); ?>">
          </div>
          <div>
            <label class="label">Email</label>
            <input type="email" name="email" class="input" value="<?php echo e(old('email', $school?->email)); ?>">
          </div>
          <div>
            <label class="label">Website</label>
            <input type="text" name="website" class="input" value="<?php echo e(old('website', $school?->website)); ?>">
          </div>
          <div>
            <label class="label">Principal Name</label>
            <input type="text" name="principal_name" class="input" value="<?php echo e(old('principal_name', $school?->principal_name)); ?>">
          </div>
          <div>
            <label class="label">Medium of Instruction</label>
            <input type="text" name="medium" class="input" value="<?php echo e(old('medium', $school?->medium)); ?>">
          </div>
          <div>
            <label class="label">GSTIN</label>
            <input type="text" name="gstin" class="input" maxlength="20" value="<?php echo e(old('gstin', $school?->gstin)); ?>">
          </div>
          <div>
            <label class="label">PAN</label>
            <input type="text" name="pan" class="input" maxlength="20" value="<?php echo e(old('pan', $school?->pan)); ?>">
          </div>
          <div>
            <label class="label">Primary Color</label>
            <div class="flex gap-2 items-center">
              <input type="color" name="primary_color" class="h-9 w-14 rounded border border-slate-200 cursor-pointer"
                     value="<?php echo e(old('primary_color', $school?->primary_color ?? '#3B82F6')); ?>">
              <span class="text-xs text-slate-400">Used in PDFs and badges</span>
            </div>
          </div>
        </div>
        <div class="pt-2">
          <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
      </form>
    </div>

    
    <div class="space-y-4">
      <div class="card space-y-4">
        <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Principal Signature</h2>
        <?php if($school?->principal_signature): ?>
          <div class="flex flex-col items-center gap-2">
            <img src="<?php echo e(Storage::disk('public')->url($school->principal_signature)); ?>"
                 alt="Signature" class="max-h-20 border border-slate-200 rounded p-1 bg-white">
            <form method="POST" action="<?php echo e(route('settings.delete-signature')); ?>"
                  onsubmit="return confirm('Remove signature?')">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button type="submit" class="btn-xs bg-red-50 text-red-600 hover:bg-red-100 rounded px-2 py-1 text-xs">Remove</button>
            </form>
          </div>
        <?php else: ?>
          <p class="text-slate-400 text-xs text-center py-3">No signature uploaded</p>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('settings.upload-signature')); ?>" enctype="multipart/form-data" class="space-y-2">
          <?php echo csrf_field(); ?>
          <label class="label text-xs">Upload Signature (PNG/JPG, max 1 MB)</label>
          <input type="file" name="signature" accept="image/png,image/jpeg"
                 class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
          <button type="submit" class="btn btn-secondary btn-sm w-full text-xs">Upload</button>
        </form>
        <p class="text-xs text-slate-400">Used on report cards and certificates. Transparent PNG recommended.</p>
      </div>

      <div class="card space-y-4">
        <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">School Stamp / Seal</h2>
        <?php if($school?->school_stamp): ?>
          <div class="flex flex-col items-center gap-2">
            <img src="<?php echo e(Storage::disk('public')->url($school->school_stamp)); ?>"
                 alt="Stamp" class="max-h-20 border border-slate-200 rounded p-1 bg-white">
            <form method="POST" action="<?php echo e(route('settings.delete-stamp')); ?>"
                  onsubmit="return confirm('Remove stamp?')">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button type="submit" class="btn-xs bg-red-50 text-red-600 hover:bg-red-100 rounded px-2 py-1 text-xs">Remove</button>
            </form>
          </div>
        <?php else: ?>
          <p class="text-slate-400 text-xs text-center py-3">No stamp uploaded</p>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('settings.upload-stamp')); ?>" enctype="multipart/form-data" class="space-y-2">
          <?php echo csrf_field(); ?>
          <label class="label text-xs">Upload Stamp (PNG/JPG, max 1 MB)</label>
          <input type="file" name="stamp" accept="image/png,image/jpeg"
                 class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
          <button type="submit" class="btn btn-secondary btn-sm w-full text-xs">Upload</button>
        </form>
        <p class="text-xs text-slate-400">Transparent PNG seal recommended.</p>
      </div>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\settings\index.blade.php ENDPATH**/ ?>