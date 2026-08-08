<?php $__env->startSection('title','Departments & Designations'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Departments & Designations</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <div class="card space-y-4">
      <div class="flex items-center justify-between pb-2 border-b border-slate-100">
        <h3 class="font-semibold text-slate-700">Departments</h3>
        <button x-data @click="$dispatch('open-modal','add-dept')" class="btn btn-primary btn-sm">+ Add</button>
      </div>
      <div class="space-y-2">
        <?php $__empty_1 = true; $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
          <div>
            <p class="font-semibold text-slate-800 text-sm"><?php echo e($dept->name); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($dept->code); ?> | <?php echo e($dept->designations_count); ?> designations | <?php echo e($dept->employees_count); ?> staff</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="<?php echo e($dept->is_active ? 'badge-green' : 'badge-slate'); ?> text-xs"><?php echo e($dept->is_active ? 'Active' : 'Off'); ?></span>
            <button x-data @click="$dispatch('open-modal','edit-dept-<?php echo e($dept->id); ?>')" class="text-indigo-600 hover:underline text-xs">Edit</button>
          </div>
        </div>
        
        <div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='edit-dept-<?php echo e($dept->id); ?>')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
          <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <h3 class="font-semibold text-slate-700 mb-4">Edit Department</h3>
            <form method="POST" action="<?php echo e(route('hr.departments.update',$dept->id)); ?>" class="space-y-3">
              <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
              <div><label class="label">Name</label><input type="text" name="name" class="input" value="<?php echo e($dept->name); ?>" required></div>
              <div><label class="label">Code</label><input type="text" name="code" class="input" value="<?php echo e($dept->code); ?>"></div>
              <div><label class="label">Description</label><textarea name="description" class="input h-16"><?php echo e($dept->description); ?></textarea></div>
              <div class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" <?php if($dept->is_active): echo 'checked'; endif; ?>><label class="text-sm">Active</label></div>
              <div class="flex gap-2"><button type="submit" class="btn btn-primary btn-sm">Save</button><button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button></div>
            </form>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-slate-400 text-sm text-center py-4">No departments.</p>
        <?php endif; ?>
      </div>
    </div>
    
    <div class="card space-y-4">
      <div class="flex items-center justify-between pb-2 border-b border-slate-100">
        <h3 class="font-semibold text-slate-700">Designations & Pay Bands</h3>
        <button x-data @click="$dispatch('open-modal','add-desig')" class="btn btn-primary btn-sm">+ Add</button>
      </div>
      <div class="space-y-2">
        <?php $__empty_1 = true; $__currentLoopData = $designations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex items-start justify-between py-2 border-b border-slate-100 last:border-0">
          <div>
            <p class="font-semibold text-slate-800 text-sm"><?php echo e($d->name); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($d->department?->name); ?>

              <?php if($d->grade): ?> &nbsp;|&nbsp; Grade: <span class="text-indigo-600 font-medium"><?php echo e($d->grade); ?></span><?php endif; ?>
              <?php if($d->pay_scale): ?> &nbsp;|&nbsp; Scale: <span class="text-amber-600 font-medium"><?php echo e($d->pay_scale); ?></span><?php endif; ?>
            </p>
            <?php if($d->pay_band_min || $d->pay_band_max): ?>
            <p class="text-xs text-green-700 font-medium mt-0.5">
              Pay Band: ₹<?php echo e(number_format($d->pay_band_min ?? 0, 0)); ?> – ₹<?php echo e(number_format($d->pay_band_max ?? 0, 0)); ?> / month
            </p>
            <?php endif; ?>
            <?php if($d->spatie_role): ?>
              <span class="badge-indigo text-xs mt-0.5">Role: <?php echo e($d->spatie_role); ?></span>
            <?php endif; ?>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <span class="<?php echo e($d->is_active ? 'badge-green' : 'badge-slate'); ?> text-xs"><?php echo e($d->is_active ? 'Active' : 'Off'); ?></span>
            <button x-data @click="$dispatch('open-modal','edit-desig-<?php echo e($d->id); ?>')" class="text-indigo-600 hover:underline text-xs">Edit</button>
          </div>
        </div>
        
        <div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='edit-desig-<?php echo e($d->id); ?>')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
          <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg overflow-y-auto max-h-screen">
            <h3 class="font-semibold text-slate-700 mb-4">Edit Designation</h3>
            <form method="POST" action="<?php echo e(route('hr.designations.update', $d->id)); ?>" class="space-y-3">
              <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
              <div><label class="label">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="input" value="<?php echo e($d->name); ?>" required>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div><label class="label">Department</label>
                  <select name="department_id" class="select">
                    <option value="">None</option>
                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($dept->id); ?>" <?php if($d->department_id==$dept->id): echo 'selected'; endif; ?>><?php echo e($dept->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
                <div><label class="label">Grade</label>
                  <input type="text" name="grade" class="input" value="<?php echo e($d->grade); ?>" placeholder="e.g. A1, PB-2">
                </div>
              </div>
              <div><label class="label">Pay Scale / Level</label>
                <input type="text" name="pay_scale" class="input" value="<?php echo e($d->pay_scale); ?>" placeholder="e.g. Level-7, Scale 5400-20200">
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div><label class="label">Min Pay Band (₹/month)</label>
                  <input type="number" name="pay_band_min" class="input" value="<?php echo e($d->pay_band_min); ?>" step="100" min="0" placeholder="e.g. 25000">
                </div>
                <div><label class="label">Max Pay Band (₹/month)</label>
                  <input type="number" name="pay_band_max" class="input" value="<?php echo e($d->pay_band_max); ?>" step="100" min="0" placeholder="e.g. 60000">
                </div>
              </div>
              <div><label class="label">Description</label>
                <textarea name="description" class="input h-14"><?php echo e($d->description); ?></textarea>
              </div>
              <div>
                <label class="label">Access Role (Spatie)</label>
                <input type="text" name="spatie_role" class="input" value="<?php echo e($d->spatie_role); ?>" placeholder="e.g. teacher, principal, accountant">
                <p class="text-xs text-slate-400 mt-1">Employees with this designation will be auto-assigned this role for module access control.</p>
              </div>
              <div class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" <?php if($d->is_active): echo 'checked'; endif; ?>><label class="text-sm">Active</label></div>
              <div class="flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
              </div>
            </form>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-slate-400 text-sm text-center py-4">No designations.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>


<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='add-dept')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-700 mb-4">Add Department</h3>
    <form method="POST" action="<?php echo e(route('hr.departments.store')); ?>" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div><label class="label">Name <span class="text-red-500">*</span></label><input type="text" name="name" class="input" required></div>
      <div><label class="label">Code</label><input type="text" name="code" class="input" placeholder="DEPT01"></div>
      <div><label class="label">Description</label><textarea name="description" class="input h-16"></textarea></div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>


<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='add-desig')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg overflow-y-auto max-h-screen">
    <h3 class="font-semibold text-slate-700 mb-4">Add Designation</h3>
    <form method="POST" action="<?php echo e(route('hr.designations.store')); ?>" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div><label class="label">Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="input" required placeholder="e.g. Head of Department">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Department <span class="text-red-500">*</span></label>
          <select name="department_id" class="select" required>
            <option value="">Select</option>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($dept->id); ?>"><?php echo e($dept->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Grade</label>
          <input type="text" name="grade" class="input" placeholder="e.g. A1, PB-2">
        </div>
      </div>
      <div><label class="label">Pay Scale / Level</label>
        <input type="text" name="pay_scale" class="input" placeholder="e.g. Level-7, 5400-20200">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Min Pay Band (₹/month)</label>
          <input type="number" name="pay_band_min" class="input" step="100" min="0" placeholder="e.g. 25000">
        </div>
        <div><label class="label">Max Pay Band (₹/month)</label>
          <input type="number" name="pay_band_max" class="input" step="100" min="0" placeholder="e.g. 60000">
        </div>
      </div>
      <div><label class="label">Description</label>
        <textarea name="description" class="input h-14"></textarea>
      </div>
      <div>
        <label class="label">Access Role (Spatie)</label>
        <input type="text" name="spatie_role" class="input" placeholder="e.g. teacher, principal, accountant">
        <p class="text-xs text-slate-400 mt-1">Employees assigned this designation will auto-receive this role for module access control.</p>
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\departments.blade.php ENDPATH**/ ?>