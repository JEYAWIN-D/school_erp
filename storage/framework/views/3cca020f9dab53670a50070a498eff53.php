<?php $__env->startSection('title', 'Competency Assessment'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Competency Assessment</h1>
      <p class="page-subtitle">NEP 2020 — Achieved / Partially Achieved / Not Achieved</p>
    </div>
    <a href="<?php echo e(route('examinations.competencies')); ?>" class="btn btn-secondary btn-sm">Manage Competencies</a>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  
  <form method="GET" class="card-flat py-4 flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Student (Admission No / Name)</label>
      <input type="text" name="student_search" value="<?php echo e(request('student_search', $student?->admission_number)); ?>"
             placeholder="Search student…" class="input w-56"
             list="student-list" id="student-search-field">
      <input type="hidden" name="student_id" id="student-id-field" value="<?php echo e(request('student_id')); ?>">
    </div>
    <div>
      <label class="label">Term</label>
      <select name="term" class="select w-36">
        <?php $__currentLoopData = ['Term 1', 'Term 2', 'Annual']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($t); ?>" <?php if(request('term', 'Term 1') === $t): echo 'selected'; endif; ?>><?php echo e($t); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </form>

  <?php if($student && $competencies->count()): ?>
    <form method="POST" action="<?php echo e(route('examinations.competency-assessment.save')); ?>">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="student_id" value="<?php echo e($student->id); ?>">
      <input type="hidden" name="academic_year_id" value="<?php echo e($academicYear?->id); ?>">
      <input type="hidden" name="term" value="<?php echo e(request('term', 'Term 1')); ?>">

      <div class="card">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="font-semibold text-slate-700"><?php echo e($student->full_name); ?></h3>
            <p class="text-sm text-slate-400"><?php echo e($student->currentEnrollment?->class?->name); ?> | <?php echo e(request('term', 'Term 1')); ?></p>
          </div>
          <button type="submit" class="btn btn-primary btn-sm">Save All</button>
        </div>

        <?php $currentSubject = null; ?>
        <div class="space-y-1">
          <?php $__currentLoopData = $competencies->groupBy('subject_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subjectId => $comps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $subject = $comps->first()->subject; ?>
            <div class="pt-3 pb-1">
              <p class="text-xs font-bold text-slate-500 uppercase tracking-wide"><?php echo e($subject?->name); ?></p>
            </div>
            <?php $__currentLoopData = $comps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                $key = $comp->id . '_' . request('term', 'Term 1');
                $existing = $assessments[$key] ?? null;
              ?>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-2 border-b border-slate-50 items-start">
                <div class="sm:col-span-1">
                  <?php if($comp->code): ?>
                    <span class="text-xs text-blue-500 font-mono"><?php echo e($comp->code); ?></span><br>
                  <?php endif; ?>
                  <span class="text-sm text-slate-700"><?php echo e($comp->name); ?></span>
                </div>
                <div>
                  <select name="assessments[<?php echo e($comp->id); ?>][level]" class="select w-full text-sm">
                    <?php $__currentLoopData = ['not_assessed' => 'Not Assessed', 'achieved' => 'Achieved', 'partially_achieved' => 'Partially Achieved', 'not_achieved' => 'Not Achieved']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($val); ?>" <?php if(($existing?->level ?? 'not_assessed') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
                <div>
                  <input type="text" name="assessments[<?php echo e($comp->id); ?>][remarks]" class="input w-full text-sm"
                         placeholder="Remarks (optional)" value="<?php echo e($existing?->remarks); ?>">
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="flex justify-end mt-4">
          <button type="submit" class="btn btn-primary">Save Assessment</button>
        </div>
      </div>
    </form>
  <?php elseif($student): ?>
    <div class="card text-center py-10 text-slate-400">
      No competencies defined for <?php echo e($student->currentEnrollment?->class?->name); ?>.
      <a href="<?php echo e(route('examinations.competencies')); ?>" class="text-blue-600 hover:underline">Add competencies first</a>.
    </div>
  <?php else: ?>
    <div class="card text-center py-10 text-slate-400">
      Search for a student above to begin assessment.
    </div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\competency-assessment.blade.php ENDPATH**/ ?>