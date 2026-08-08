<?php $__env->startSection('title', 'Class Result Summary'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Class Result Summary</h1>
      <p class="page-subtitle">Rank-wise breakdown for an exam &amp; class</p>
    </div>
    <a href="<?php echo e(route('examinations.index')); ?>" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Exam</label>
        <select name="exam_id" class="select" required>
          <option value="">Select Exam</option>
          <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($exam->id); ?>" <?php echo e(request('exam_id') == $exam->id ? 'selected' : ''); ?>><?php echo e($exam->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select" required>
          <option value="">Select Class</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($class->id); ?>" <?php echo e(request('class_id') == $class->id ? 'selected' : ''); ?>><?php echo e($class->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">View Summary</button>
      <?php if(request('exam_id') && request('class_id')): ?>
      <a href="<?php echo e(route('examinations.class-result.pdf', request()->only('exam_id','class_id'))); ?>"
         target="_blank" class="btn btn-secondary btn-sm">PDF</a>
      <a href="<?php echo e(route('examinations.class-result.excel', request()->only('exam_id','class_id'))); ?>"
         class="btn btn-secondary btn-sm">Excel</a>
      <?php endif; ?>
    </form>
  </div>

  <?php if($summary->isNotEmpty()): ?>
  <?php
    $passCount    = $summary->where('hasFail', false)->count();
    $failCount    = $summary->where('hasFail', true)->count();
    $classAverage = round($summary->avg('percentage'), 1);
    $topPct       = $summary->first()['percentage'];
  ?>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-slate-700"><?php echo e($summary->count()); ?></p>
      <p class="text-sm text-slate-500 mt-1">Total Students</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-green-600"><?php echo e($passCount); ?></p>
      <p class="text-sm text-slate-500 mt-1">Passed</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-red-500"><?php echo e($failCount); ?></p>
      <p class="text-sm text-slate-500 mt-1">Failed</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-indigo-600"><?php echo e($classAverage); ?>%</p>
      <p class="text-sm text-slate-500 mt-1">Class Average</p>
    </div>
  </div>

  <div class="card">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
      <h2 class="font-semibold text-slate-800">Result Rank List</h2>
      <a href="<?php echo e(route('examinations.tabulation.pdf', request()->only(['exam_id','class_id']))); ?>" class="btn btn-secondary text-sm">
        Download Tabulation PDF
      </a>
    </div>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Rank</th>
            <th class="th">Student Name</th>
            <th class="th">Adm No</th>
            <th class="th">Marks Obtained</th>
            <th class="th">Total Marks</th>
            <th class="th">Percentage</th>
            <th class="th">Result</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td">
              <?php if($i < 3): ?>
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white text-xs font-bold <?php echo e(['bg-amber-500','bg-slate-400','bg-orange-700'][$i]); ?>"><?php echo e($i+1); ?></span>
              <?php else: ?>
                <span class="text-slate-500"><?php echo e($i+1); ?></span>
              <?php endif; ?>
            </td>
            <td class="td font-medium">
              <a href="<?php echo e(route('students.show', $row['student']->id)); ?>" class="text-indigo-600 hover:underline">
                <?php echo e($row['student']->first_name); ?> <?php echo e($row['student']->last_name); ?>

              </a>
            </td>
            <td class="td text-slate-500"><?php echo e($row['student']->admission_no ?? $row['student']->admission_number ?? '—'); ?></td>
            <td class="td"><?php echo e($row['obtained']); ?></td>
            <td class="td text-slate-500"><?php echo e($row['totalMarks']); ?></td>
            <td class="td">
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-200 rounded-full h-2 max-w-[80px]">
                  <div class="h-2 rounded-full <?php echo e($row['percentage'] >= 75 ? 'bg-green-500' : ($row['percentage'] >= 50 ? 'bg-amber-500' : 'bg-red-500')); ?>" style="width:<?php echo e($row['percentage']); ?>%"></div>
                </div>
                <span class="text-sm font-medium"><?php echo e($row['percentage']); ?>%</span>
              </div>
            </td>
            <td class="td">
              <span class="badge-<?php echo e($row['hasFail'] ? 'red' : 'green'); ?>"><?php echo e($row['hasFail'] ? 'Fail' : 'Pass'); ?></span>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php elseif(request('exam_id') && request('class_id')): ?>
  <div class="alert-info">No enrollment records found for the selected exam and class.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\class-result-summary.blade.php ENDPATH**/ ?>