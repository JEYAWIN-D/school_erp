<?php $__env->startSection('title', '360° Co-Scholastic Assessment'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Co-Scholastic Assessment</h1>
      <p class="page-subtitle">NEP 2020 — Arts, Sports, Values, Health & Work Education</p>
    </div>
    <a href="<?php echo e(route('examinations.competencies')); ?>" class="btn btn-secondary btn-sm">Competency Master</a>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <form method="GET" class="card-flat py-3 flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Student ID</label>
      <input type="text" name="student_id" value="<?php echo e(request('student_id')); ?>" placeholder="Student ID…" class="input w-44">
    </div>
    <div>
      <label class="label">Term</label>
      <select name="term" class="select w-32">
        <?php $__currentLoopData = ['Term 1', 'Term 2', 'Annual']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($t); ?>" <?php if(request('term', 'Term 1') === $t): echo 'selected'; endif; ?>><?php echo e($t); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </form>

  <?php if($student): ?>
    <form method="POST" action="<?php echo e(route('examinations.coscholastic.save-nep')); ?>">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="student_id" value="<?php echo e($student->id); ?>">
      <input type="hidden" name="academic_year_id" value="<?php echo e($academicYear?->id); ?>">
      <input type="hidden" name="term" value="<?php echo e(request('term', 'Term 1')); ?>">

      <div class="space-y-4">
        <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $areaKey => $subAreas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="card">
            <h3 class="font-semibold text-slate-700 mb-3 capitalize"><?php echo e(str_replace('_', ' ', $areaKey)); ?></h3>
            <div class="space-y-2">
              <?php $__currentLoopData = $subAreas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subArea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                  $existing = $assessments->get($areaKey)?->firstWhere('sub_area', $subArea);
                ?>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center py-1 border-b border-slate-50">
                  <label class="label mb-0"><?php echo e($subArea); ?></label>
                  <select name="areas[<?php echo e($areaKey); ?>][<?php echo e($subArea); ?>][grade]" class="select">
                    <option value="">— Not Assessed —</option>
                    <?php $__currentLoopData = ['A', 'B', 'C', 'D']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($g); ?>" <?php if($existing?->grade === $g): echo 'selected'; endif; ?>>Grade <?php echo e($g); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                  <input type="text" name="areas[<?php echo e($areaKey); ?>][<?php echo e($subArea); ?>][remarks]"
                         class="input" placeholder="Remarks…" value="<?php echo e($existing?->remarks); ?>">
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      <div class="flex justify-end mt-4">
        <button type="submit" class="btn btn-primary">Save Co-Scholastic Assessment</button>
      </div>
    </form>
  <?php else: ?>
    <div class="card text-center py-10 text-slate-400">Enter a Student ID to begin assessment.</div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\coscholastic-assessment.blade.php ENDPATH**/ ?>