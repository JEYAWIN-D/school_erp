<?php $__env->startSection('title', 'Competency Progress Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Competency Progress Report</h1>
      <p class="page-subtitle">Student-wise competency progress across terms</p>
    </div>
    <a href="<?php echo e(route('examinations.competency-assessment')); ?>" class="btn btn-secondary btn-sm">Enter Assessment</a>
  </div>

  <form method="GET" class="card-flat py-3 flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Student (Admission No)</label>
      <input type="text" name="student_id" value="<?php echo e(request('student_id')); ?>" placeholder="Student ID…" class="input w-44">
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </form>

  <?php if($student && $report->count()): ?>
    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="font-semibold text-slate-700"><?php echo e($student->full_name); ?></h3>
          <p class="text-xs text-slate-400"><?php echo e($student->currentEnrollment?->class?->name); ?> | <?php echo e($academicYear?->name); ?></p>
        </div>
      </div>

      <div class="table-wrap">
        <table class="w-full">
          <thead>
            <tr>
              <th class="th">Subject</th>
              <th class="th">Competency</th>
              <th class="th">Term 1</th>
              <th class="th">Term 2</th>
              <th class="th">Annual</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr class="tr">
                <td class="td text-xs text-slate-500"><?php echo e($row['subject']); ?></td>
                <td class="td font-medium text-slate-700 text-sm"><?php echo e($row['competency']->name); ?></td>
                <?php $__currentLoopData = ['term1', 'term2', 'annual']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <td class="td">
                    <?php if($row[$t]): ?>
                      <span class="<?php echo e($row[$t]->level_color); ?>"><?php echo e($row[$t]->level_label); ?></span>
                      <?php if($row[$t]->remarks): ?>
                        <p class="text-xs text-slate-400 mt-0.5"><?php echo e($row[$t]->remarks); ?></p>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="text-slate-300 text-xs">—</span>
                    <?php endif; ?>
                  </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php elseif($student): ?>
    <div class="card text-center py-10 text-slate-400">No competency assessments found for this student.</div>
  <?php else: ?>
    <div class="card text-center py-10 text-slate-400">Enter a Student ID above to view their competency progress report.</div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\competency-report.blade.php ENDPATH**/ ?>