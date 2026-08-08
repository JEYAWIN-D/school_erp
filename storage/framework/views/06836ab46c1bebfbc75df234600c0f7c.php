<?php $__env->startSection('title', 'Attempt Log — ' . $exam->title); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('online-exams.index')); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">Student Attempts</h1>
      <p class="page-subtitle"><?php echo e($exam->title); ?> — <?php echo e($exam->class?->name); ?></p>
    </div>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">Student</th>
          <th class="th">Started</th>
          <th class="th">Submitted</th>
          <th class="th">Score</th>
          <th class="th">Final Score</th>
          <th class="th">Result</th>
          <th class="th text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $attempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td font-medium text-slate-800">
              <?php echo e($att->student?->full_name); ?>

              <p class="text-xs text-slate-400"><?php echo e($att->student?->admission_number); ?></p>
            </td>
            <td class="td text-xs text-slate-500">
              <?php echo e($att->started_at?->format('d M Y, g:i A') ?? '—'); ?>

            </td>
            <td class="td text-xs text-slate-500">
              <?php if($att->submitted_at): ?>
                <?php echo e($att->submitted_at->format('d M Y, g:i A')); ?>

                <?php if($att->auto_submitted): ?>
                  <span class="badge-amber text-xs ml-1">Auto</span>
                <?php endif; ?>
              <?php else: ?>
                <span class="text-amber-500">In Progress</span>
              <?php endif; ?>
            </td>
            <td class="td text-sm"><?php echo e($att->score ?? '—'); ?></td>
            <td class="td text-sm font-semibold">
              <?php echo e($att->final_score ?? '—'); ?> / <?php echo e($exam->total_marks); ?>

              <?php if($att->negative_marks > 0): ?>
                <p class="text-xs text-red-400">-<?php echo e($att->negative_marks); ?> negative</p>
              <?php endif; ?>
            </td>
            <td class="td">
              <?php if($att->result === 'pass'): ?>
                <span class="badge-green">Pass</span>
              <?php elseif($att->result === 'fail'): ?>
                <span class="badge-red">Fail</span>
              <?php else: ?>
                <span class="badge-slate">Pending</span>
              <?php endif; ?>
            </td>
            <td class="td text-right">
              <?php if($att->submitted_at): ?>
                <a href="<?php echo e(route('online-exams.evaluate', $att->id)); ?>" class="btn btn-secondary btn-xs">Evaluate</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="7" class="td text-center py-10 text-slate-400">No attempts yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if($attempts->hasPages()): ?>
    <div class="flex justify-between items-center text-sm text-slate-500">
      <span>Showing <?php echo e($attempts->firstItem()); ?>–<?php echo e($attempts->lastItem()); ?> of <?php echo e($attempts->total()); ?></span>
      <?php echo e($attempts->links()); ?>

    </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\online-exams\attempts.blade.php ENDPATH**/ ?>