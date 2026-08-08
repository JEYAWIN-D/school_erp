<?php $__env->startSection('title', 'Failed Students'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Failed Students</h1>
      <p class="page-subtitle">Students who failed in one or more subjects</p>
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
        <label class="label">Class (Optional)</label>
        <select name="class_id" class="select">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($class->id); ?>" <?php echo e(request('class_id') == $class->id ? 'selected' : ''); ?>><?php echo e($class->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Show Failed</button>
      <?php if(request('exam_id')): ?>
      <a href="<?php echo e(route('examinations.failed.pdf', request()->only('exam_id','class_id'))); ?>"
         target="_blank" class="btn btn-secondary btn-sm">PDF</a>
      <a href="<?php echo e(route('examinations.failed.excel', request()->only('exam_id','class_id'))); ?>"
         class="btn btn-secondary btn-sm">Excel</a>
      <?php endif; ?>
    </form>
  </div>

  <?php if($failed->isNotEmpty()): ?>
  <?php $suppCount = $failed->where('supp_eligible', true)->count(); ?>
  <div class="card">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
      <div>
        <h2 class="font-semibold text-slate-800">
          Failed Students
          <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 text-sm rounded-full"><?php echo e($failed->count()); ?></span>
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">
          Supplementary Eligible (≤<?php echo e($threshold); ?> subjects failed):
          <span class="font-medium text-amber-600"><?php echo e($suppCount); ?></span>
        </p>
      </div>
    </div>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">Student Name</th>
            <th class="th">Adm No</th>
            <th class="th">Failed Subjects</th>
            <th class="th">Count</th>
            <th class="th">Supp. Eligible</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $failed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td text-slate-500"><?php echo e($i + 1); ?></td>
            <td class="td font-medium">
              <a href="<?php echo e(route('students.show', $row['student']->id)); ?>" class="text-indigo-600 hover:underline">
                <?php echo e($row['student']->first_name); ?> <?php echo e($row['student']->last_name); ?>

              </a>
            </td>
            <td class="td text-slate-500"><?php echo e($row['student']->admission_no ?? $row['student']->admission_number ?? '—'); ?></td>
            <td class="td">
              <div class="flex flex-wrap gap-1">
                <?php $__currentLoopData = $row['subjects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full"><?php echo e($subject); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
            </td>
            <td class="td">
              <span class="font-semibold text-red-600"><?php echo e($row['subjects']->count()); ?></span>
            </td>
            <td class="td">
              <?php if($row['supp_eligible']): ?>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-medium rounded-full">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  Eligible
                </span>
              <?php else: ?>
                <span class="text-slate-400 text-xs">—</span>
              <?php endif; ?>
            </td>
            <td class="td">
              <a href="<?php echo e(route('students.show', $row['student']->id)); ?>" class="text-sm text-indigo-600 hover:underline">View Profile</a>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php elseif(request('exam_id')): ?>
  <div class="alert-success">No failed students found for the selected filters.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\failed-students.blade.php ENDPATH**/ ?>