<?php $__env->startSection('title', 'Quiz Attempts'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title"><?php echo e($quiz->title); ?> — Attempts</h1>
      <p class="text-sm text-slate-400"><?php echo e($attempts->total()); ?> attempt(s)</p>
    </div>
    <a href="<?php echo e(route('lms.quiz.builder', $quiz->course_id)); ?>" class="btn-secondary btn-sm">← Quiz Builder</a>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Student</th>
          <th class="th">Adm No.</th>
          <th class="th text-center">Score</th>
          <th class="th text-center">%</th>
          <th class="th text-center">Time Used</th>
          <th class="th">Submitted</th>
          <th class="th">Status</th>
        </tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $attempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td font-medium text-slate-700"><?php echo e($a->student_name); ?></td>
            <td class="td font-mono text-xs"><?php echo e($a->admission_no); ?></td>
            <td class="td text-center font-semibold"><?php echo e($a->score); ?>/<?php echo e($a->total_marks); ?></td>
            <td class="td text-center">
              <?php $pct = $a->total_marks > 0 ? round($a->score/$a->total_marks*100,1) : 0; ?>
              <span class="<?php echo e($pct >= 50 ? 'text-green-600' : 'text-red-600'); ?> font-medium"><?php echo e($pct); ?>%</span>
            </td>
            <td class="td text-center text-slate-500 text-xs">
              <?php echo e($a->submitted_at && $a->started_at ? \Carbon\Carbon::parse($a->started_at)->diffInMinutes($a->submitted_at) . ' min' : '—'); ?>

            </td>
            <td class="td text-xs text-slate-400">
              <?php echo e($a->submitted_at ? \Carbon\Carbon::parse($a->submitted_at)->format('d M Y H:i') : '—'); ?>

            </td>
            <td class="td">
              <span class="badge-<?php echo e($a->status === 'submitted' ? 'green' : ($a->status === 'in_progress' ? 'blue' : 'purple')); ?>">
                <?php echo e(ucfirst(str_replace('_', ' ', $a->status))); ?>

              </span>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="7" class="td text-center text-slate-400 py-6">No attempts yet</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-4"><?php echo e($attempts->links()); ?></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\lms\quiz\attempts.blade.php ENDPATH**/ ?>