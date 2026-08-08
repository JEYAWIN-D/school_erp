<?php $__env->startSection('title','Medical Records — '.$student->full_name); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <nav class="text-sm text-slate-400 flex items-center gap-1.5 mb-1">
    <a href="<?php echo e(route('students.index')); ?>" class="hover:text-slate-600">Students</a>
    <span>/</span>
    <a href="<?php echo e(route('students.show',$student->id)); ?>" class="hover:text-slate-600"><?php echo e($student->full_name); ?></a>
    <span>/</span>
    <span class="text-slate-600">Medical</span>
  </nav>
  <div class="flex items-center gap-3">
    <a href="<?php echo e(route('students.show',$student->id)); ?>" class="text-slate-400 hover:text-slate-700">←</a>
    <h1 class="page-title">Medical Records — <?php echo e($student->full_name); ?></h1>
  </div>
  <form method="POST" action="<?php echo e(route('students.medical.save',$student->id)); ?>" class="card space-y-5">
    <?php echo csrf_field(); ?>
    <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Medical Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <div><label class="label">Known Allergies</label>
        <textarea name="allergies" rows="2" class="input" placeholder="Food, medicine, environmental..."><?php echo e(old('allergies',$medical?->allergies)); ?></textarea>
      </div>
      <div><label class="label">Chronic Conditions</label>
        <textarea name="chronic_conditions" rows="2" class="input"><?php echo e(old('chronic_conditions',$medical?->chronic_conditions)); ?></textarea>
      </div>
      <div><label class="label">Current Medications</label>
        <textarea name="medications" rows="2" class="input"><?php echo e(old('medications',$medical?->medications)); ?></textarea>
      </div>
      <div class="space-y-3">
        <div><label class="label">Family Doctor Name</label><input type="text" name="family_doctor" class="input" value="<?php echo e(old('family_doctor',$medical?->family_doctor)); ?>"></div>
        <div><label class="label">Doctor Mobile</label><input type="text" name="doctor_mobile" class="input" value="<?php echo e(old('doctor_mobile',$medical?->doctor_mobile)); ?>"></div>
      </div>
      <div><label class="label">Nearest Hospital Preference</label><input type="text" name="nearest_hospital" class="input" value="<?php echo e(old('nearest_hospital',$medical?->nearest_hospital)); ?>"></div>
      <div><label class="label">Health Insurance Number</label><input type="text" name="health_insurance_no" class="input" value="<?php echo e(old('health_insurance_no',$medical?->health_insurance_no)); ?>"></div>
    </div>
    <button type="submit" class="btn btn-primary">Save Medical Info</button>
  </form>
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-700">Vaccination Records</h3>
      <button x-data @click="$dispatch('open-modal','add-vaccine')" class="btn btn-primary btn-sm">Add Vaccine</button>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <th class="text-left px-4 py-2 text-slate-500 text-xs uppercase font-medium">Vaccine</th>
        <th class="text-left px-4 py-2 text-slate-500 text-xs uppercase font-medium">Date Given</th>
        <th class="text-left px-4 py-2 text-slate-500 text-xs uppercase font-medium">Dose</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $vaccinations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr><td class="px-4 py-2"><?php echo e($v->vaccine_name); ?></td><td class="px-4 py-2"><?php echo e($v->date_given?->format('d M Y')); ?></td><td class="px-4 py-2 text-slate-400"><?php echo e($v->dose ?? '—'); ?></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="3" class="px-4 py-6 text-center text-slate-400">No vaccination records.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\medical.blade.php ENDPATH**/ ?>