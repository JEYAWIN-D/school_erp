<?php $__env->startSection('title', 'Exam Result'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">

  <div class="card text-center py-8">
    <?php if($attempt->result === 'pass'): ?>
      <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      </div>
      <h2 class="text-2xl font-bold text-green-700 mb-1">Congratulations! You Passed</h2>
    <?php elseif($attempt->result === 'fail'): ?>
      <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </div>
      <h2 class="text-2xl font-bold text-red-600 mb-1">Better Luck Next Time</h2>
    <?php else: ?>
      <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <h2 class="text-2xl font-bold text-amber-700 mb-1">Exam Submitted</h2>
    <?php endif; ?>

    <p class="text-slate-500 text-sm mb-6"><?php echo e($attempt->exam?->title); ?></p>

    <div class="grid grid-cols-3 gap-4 max-w-md mx-auto">
      <div class="bg-slate-50 rounded-xl p-4">
        <p class="text-xs text-slate-400 mb-1">Score</p>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($attempt->final_score ?? '—'); ?></p>
        <p class="text-xs text-slate-400">/ <?php echo e($attempt->exam?->total_marks); ?></p>
      </div>
      <div class="bg-slate-50 rounded-xl p-4">
        <p class="text-xs text-slate-400 mb-1">Pass Marks</p>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($attempt->exam?->pass_marks); ?></p>
      </div>
      <div class="bg-slate-50 rounded-xl p-4">
        <p class="text-xs text-slate-400 mb-1">Percentage</p>
        <?php $pct = $attempt->exam?->total_marks > 0 ? round(($attempt->final_score / $attempt->exam->total_marks) * 100, 1) : 0; ?>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($pct); ?>%</p>
      </div>
    </div>

    <?php if($attempt->auto_submitted): ?>
      <p class="text-xs text-amber-500 mt-4">Auto-submitted when time expired.</p>
    <?php endif; ?>

    <?php if($attempt->negative_marks > 0): ?>
      <p class="text-xs text-red-400 mt-2">Negative marks deducted: <?php echo e($attempt->negative_marks); ?></p>
    <?php endif; ?>
  </div>

  
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-3 pb-2 border-b border-slate-100">Answer Review</h3>
    <div class="space-y-3">
      <?php $__currentLoopData = $attempt->responses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $resp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $q = $resp->question; ?>
        <div class="p-3 rounded-lg <?php echo e($resp->is_correct === true ? 'bg-green-50 border border-green-200' : ($resp->is_correct === false ? 'bg-red-50 border border-red-200' : 'bg-slate-50 border border-slate-200')); ?>">
          <p class="text-sm font-medium text-slate-700 mb-1"><?php echo e($idx + 1); ?>. <?php echo e($q?->question); ?></p>
          <p class="text-sm">
            <span class="text-slate-500">Your answer: </span>
            <strong><?php echo e($resp->answer ?? 'Not answered'); ?></strong>
          </p>
          <?php if($resp->is_correct !== null && $q?->correct_answer && in_array($q->question_type, ['mcq', 'true_false'])): ?>
            <p class="text-xs text-green-600 mt-1">Correct answer: <?php echo e($q->correct_answer); ?></p>
          <?php endif; ?>
          <div class="flex items-center gap-3 mt-1">
            <?php if($resp->is_correct === true): ?>
              <span class="badge-green text-xs">+<?php echo e($resp->marks_awarded); ?> marks</span>
            <?php elseif($resp->is_correct === false): ?>
              <span class="badge-red text-xs">0 marks</span>
            <?php else: ?>
              <span class="badge-amber text-xs">Pending evaluation: <?php echo e($resp->marks_awarded); ?> marks</span>
            <?php endif; ?>
            <?php if($resp->evaluator_remarks): ?>
              <span class="text-xs text-slate-500 italic"><?php echo e($resp->evaluator_remarks); ?></span>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>

  <div class="flex justify-center">
    <a href="<?php echo e(route('online-exams.student')); ?>" class="btn btn-secondary">Back to My Exams</a>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\online-exams\result.blade.php ENDPATH**/ ?>