<?php $__env->startSection('title','ID Card Generation'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Student ID Cards</h1>
    <a href="<?php echo e(route('students.id-card-template')); ?>" class="btn btn-secondary btn-sm">
      <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
      </svg>
      Template Designer
    </a>
  </div>
  <div class="card">
    <form method="GET" action="<?php echo e(route('students.id-cards.download')); ?>" class="flex gap-4 flex-wrap">
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select w-36">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Section</label>
        <select name="section_id" class="select w-36">
          <option value="">All Sections</option>
          <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('section_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="flex items-end">
        <button type="submit" class="btn btn-primary">Generate PDF</button>
      </div>
    </form>
  </div>
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">Preview (<?php echo e($students->count()); ?> students)</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <?php $e = $s->currentEnrollment; ?>
      <div class="border border-slate-200 rounded-lg overflow-hidden text-center">
        <div class="bg-indigo-700 text-white py-2 px-2 text-xs font-semibold truncate"><?php echo e($school->school_name ?? 'DASA EduERP'); ?></div>
        <div class="p-3">
          <div class="w-14 h-14 rounded-full bg-slate-100 mx-auto mb-2 flex items-center justify-center text-slate-400 text-xs">Photo</div>
          <p class="font-semibold text-slate-800 text-sm"><?php echo e($s->full_name); ?></p>
          <p class="text-xs text-slate-400"><?php echo e($s->admission_number); ?></p>
          <p class="text-xs text-slate-500"><?php echo e($e?->class?->name); ?> <?php echo e($e?->section?->name); ?></p>
          <p class="text-xs text-slate-400 mt-1"><?php echo e($s->blood_group); ?></p>
        </div>
        <div class="bg-indigo-700 text-white py-1 text-xs"><?php echo e($academicYear->name ?? ''); ?></div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="col-span-4 text-center py-8 text-slate-400">No students found.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\id-cards.blade.php ENDPATH**/ ?>