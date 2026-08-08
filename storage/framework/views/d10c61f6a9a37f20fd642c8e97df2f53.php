<?php $__env->startSection('title', 'My Online Exams'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">

  <div>
    <h1 class="page-title">My Online Exams</h1>
    <p class="page-subtitle">Upcoming and past online examinations for your class</p>
  </div>

  <?php if(session('error')): ?>
    <div class="alert-error"><?php echo e(session('error')); ?></div>
  <?php endif; ?>
  <?php if(session('info')): ?>
    <div class="alert-info"><?php echo e(session('info')); ?></div>
  <?php endif; ?>

  <?php $__empty_1 = true; $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php $attempt = $exam->attempt; $live = $exam->isLive(); ?>
    <div class="card flex flex-col sm:flex-row sm:items-center gap-4">
      <div class="flex-1">
        <div class="flex items-center gap-2 mb-1">
          <h3 class="font-semibold text-slate-800"><?php echo e($exam->title); ?></h3>
          <?php if($live): ?>
            <span class="badge-green">LIVE</span>
          <?php elseif($exam->start_time->isFuture()): ?>
            <span class="badge-blue">Upcoming</span>
          <?php else: ?>
            <span class="badge-slate">Ended</span>
          <?php endif; ?>
        </div>
        <p class="text-sm text-slate-500"><?php echo e($exam->subject?->name ?? $exam->class?->name); ?></p>
        <p class="text-xs text-slate-400 mt-1">
          <?php echo e($exam->start_time->format('d M Y, g:i A')); ?> – <?php echo e($exam->end_time->format('g:i A')); ?>

          | <?php echo e($exam->duration_minutes); ?> min | <?php echo e($exam->total_marks); ?> marks
        </p>
      </div>
      <div class="text-right flex-shrink-0">
        <?php if($attempt?->submitted_at): ?>
          <p class="text-sm font-semibold text-slate-700 mb-1"><?php echo e($attempt->final_score ?? '?'); ?> / <?php echo e($exam->total_marks); ?></p>
          <?php if($attempt->result === 'pass'): ?>
            <span class="badge-green">Pass</span>
          <?php elseif($attempt->result === 'fail'): ?>
            <span class="badge-red">Fail</span>
          <?php else: ?>
            <span class="badge-amber">Pending</span>
          <?php endif; ?>
          <div class="mt-2">
            <a href="<?php echo e(route('online-exams.result', $attempt->id)); ?>" class="btn btn-secondary btn-xs">View Result</a>
          </div>
        <?php elseif($live): ?>
          <a href="<?php echo e(route('online-exams.start', $exam->id)); ?>" class="btn btn-primary">
            <?php echo e($attempt ? 'Continue Exam' : 'Start Exam'); ?>

          </a>
        <?php elseif($exam->start_time->isFuture()): ?>
          <p class="text-xs text-slate-400">Starts <?php echo e($exam->start_time->diffForHumans()); ?></p>
        <?php else: ?>
          <p class="text-xs text-red-400">Exam ended</p>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="card text-center py-12 text-slate-400">
      <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      No online exams scheduled for your class yet.
    </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\online-exams\student-list.blade.php ENDPATH**/ ?>