<?php $__env->startSection('title', 'Course Progress'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title"><?php echo e($course->title); ?> — Progress</h1>
      <p class="text-sm text-slate-400"><?php echo e($totalLessons); ?> published lessons total</p>
    </div>
    <a href="<?php echo e(route('lms.courses.show', $course->id)); ?>" class="btn-secondary btn-sm">← Course</a>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Student</th>
          <th class="th">Adm No.</th>
          <th class="th text-center">Lessons Done</th>
          <th class="th text-center">Progress</th>
        </tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $done = $completedCounts[$e->student_id] ?? 0;
            $pct  = $totalLessons > 0 ? round($done / $totalLessons * 100) : 0;
          ?>
          <tr class="tr">
            <td class="td font-medium text-slate-700"><?php echo e($e->student_name); ?></td>
            <td class="td font-mono text-xs"><?php echo e($e->admission_no); ?></td>
            <td class="td text-center text-slate-700"><?php echo e($done); ?>/<?php echo e($totalLessons); ?></td>
            <td class="td">
              <div class="flex items-center gap-2">
                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                  <div class="h-full rounded-full <?php echo e($pct >= 80 ? 'bg-green-500' : ($pct >= 40 ? 'bg-amber-500' : 'bg-red-400')); ?>"
                       style="width: <?php echo e($pct); ?>%"></div>
                </div>
                <span class="text-xs text-slate-500 w-8 text-right"><?php echo e($pct); ?>%</span>
              </div>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="4" class="td text-center text-slate-400 py-6">No enrolled students found for this class</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\lms\progress.blade.php ENDPATH**/ ?>