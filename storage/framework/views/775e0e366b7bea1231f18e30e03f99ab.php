<?php $__env->startSection('title','Teacher Subject Allocation'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Teacher-Subject Allocation</h1>
    <a href="<?php echo e(route('academics.allocation.history')); ?>" class="btn btn-secondary btn-sm">View History →</a>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    
    <form method="POST" action="<?php echo e(route('academics.allocation.save')); ?>" class="card space-y-4">
      <?php echo csrf_field(); ?>
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Assign Teacher to Subject</h3>
      <div>
        <label class="label">Teacher <span class="text-red-500">*</span></label>
        <select name="employee_id" class="select" required>
          <option value="">Select teacher</option>
          <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t->id); ?>"><?php echo e($t->full_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Subject <span class="text-red-500">*</span></label>
        <select name="subject_id" class="select" required>
          <option value="">Select subject</option>
          <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="label">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select" required>
            <option value="">Select class</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Section</label>
          <select name="section_id" class="select">
            <option value="">All sections</option>
            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <p class="text-xs text-slate-400">Leave section as "All sections" to assign one teacher for all sections. Select a specific section to allow different teachers per section of the same subject.</p>
      <button type="submit" class="btn btn-primary">Assign</button>
    </form>

    
    <div class="card space-y-3">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Current Allocations</h3>
      
      <form method="GET" class="flex gap-2 flex-wrap">
        <select name="academic_year_id" class="select text-xs py-1 w-36" onchange="this.form.submit()">
          <option value="">All Years</option>
          <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($y->id); ?>" <?php if($filterYearId==$y->id): echo 'selected'; endif; ?>><?php echo e($y->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="teacher_id" class="select text-xs py-1 w-44" onchange="this.form.submit()">
          <option value="">All Teachers</option>
          <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($t->id); ?>" <?php if(request('teacher_id')==$t->id): echo 'selected'; endif; ?>><?php echo e($t->full_name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="class_id" class="select text-xs py-1 w-32" onchange="this.form.submit()">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </form>

      <div class="overflow-y-auto max-h-96 divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $allocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex items-start justify-between py-2">
          <div>
            <p class="text-sm font-medium text-slate-800"><?php echo e($a->employee?->full_name); ?></p>
            <p class="text-xs text-slate-400">
              <?php echo e($a->subject?->name); ?> · <?php echo e($a->class?->name); ?>

              <?php echo e($a->section ? '/ '.$a->section->name : '(All sections)'); ?>

              <?php if($a->academicYear): ?><span class="ml-1 text-indigo-500"><?php echo e($a->academicYear->name); ?></span><?php endif; ?>
            </p>
          </div>
          <form method="POST" action="<?php echo e(route('academics.allocation.delete', $a->id)); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="text-red-400 hover:text-red-600 text-xs ml-3 shrink-0">Remove</button>
          </form>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-slate-400 text-sm text-center py-6">No allocations for the selected filters.</p>
        <?php endif; ?>
      </div>
      <?php if($allocations->hasPages()): ?><div class="text-xs mt-2"><?php echo e($allocations->links()); ?></div><?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\teacher-allocation.blade.php ENDPATH**/ ?>