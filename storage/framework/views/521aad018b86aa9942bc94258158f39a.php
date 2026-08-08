<?php $__env->startSection('title','CGPA Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">CGPA Report (CBSE)</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Exam</label>
        <select name="exam_id" class="select w-48">
          <option value="">Select Exam</option>
          <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($e->id); ?>" <?php if(request('exam_id') == $e->id): echo 'selected'; endif; ?>><?php echo e($e->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Class</label>
        <select name="class_id" class="select w-36">
          <option value="">Select Class</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($c->id); ?>" <?php if(request('class_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  <?php if($report->count()): ?>
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-700">CGPA Results — <?php echo e($exam?->name); ?></h3>
      <span class="text-xs text-slate-400"><?php echo e($report->count()); ?> students</span>
    </div>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">Rank</th>
            <th class="th">Student Name</th>
            <th class="th">Adm No</th>
            <th class="th">Subjects</th>
            <th class="th">CGPA</th>
            <th class="th">Equiv %</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr <?php echo e($i < 3 ? 'bg-amber-50/30' : ''); ?>">
            <td class="td text-center font-semibold <?php echo e($i === 0 ? 'text-amber-500' : ($i === 1 ? 'text-slate-500' : ($i === 2 ? 'text-orange-400' : ''))); ?>">
              <?php echo e($i + 1); ?>

            </td>
            <td class="td font-medium"><?php echo e($row['student']?->full_name); ?></td>
            <td class="td text-slate-400 text-xs"><?php echo e($row['student']?->admission_number); ?></td>
            <td class="td">
              <div class="flex flex-wrap gap-1">
                <?php $__currentLoopData = $row['subjects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="text-xs bg-slate-100 rounded px-1.5 py-0.5" title="<?php echo e($sub['marks']); ?>/<?php echo e($sub['max']); ?>">
                  <?php echo e($sub['subject']); ?>: <strong><?php echo e($sub['grade']); ?></strong> (<?php echo e($sub['gp']); ?>)
                </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
            </td>
            <td class="td text-center">
              <span class="text-lg font-bold <?php echo e($row['cgpa'] >= 9 ? 'text-green-600' : ($row['cgpa'] >= 7 ? 'text-blue-600' : ($row['cgpa'] >= 5 ? 'text-amber-500' : 'text-red-500'))); ?>">
                <?php echo e(number_format($row['cgpa'], 1)); ?>

              </span>
            </td>
            <td class="td text-center text-slate-600">
              <?php echo e(number_format($row['cgpa'] * 9.5, 1)); ?>%
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <div class="mt-3 p-3 bg-slate-50 rounded-lg text-xs text-slate-500">
      <strong>Note:</strong> CGPA = Average of Grade Points across all scholastic subjects.
      Equivalent percentage = CGPA × 9.5 (CBSE formula).
      Grade points are based on the default grading scheme configured in the system.
    </div>
  </div>
  <?php elseif(request('exam_id')): ?>
  <div class="card text-center py-10 text-slate-400">No marks data found for this exam and class.</div>
  <?php else: ?>
  <div class="card text-center py-10 text-slate-400">Select an exam and class to generate CGPA report.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\cgpa-report.blade.php ENDPATH**/ ?>