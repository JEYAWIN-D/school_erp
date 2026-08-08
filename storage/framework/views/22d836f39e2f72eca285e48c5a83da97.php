<?php $__env->startSection('title', 'Evaluate — ' . $attempt->student?->full_name); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">

  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('online-exams.attempts', $attempt->online_exam_id)); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">Evaluate: <?php echo e($attempt->student?->full_name); ?></h1>
      <p class="page-subtitle"><?php echo e($attempt->exam?->title); ?> | Submitted <?php echo e($attempt->submitted_at?->format('d M Y, g:i A')); ?></p>
    </div>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <form method="POST" action="<?php echo e(route('online-exams.evaluate.save', $attempt->id)); ?>">
    <?php echo csrf_field(); ?>
    <div class="space-y-4">
      <?php $__currentLoopData = $attempt->responses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $resp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $q = $resp->question; ?>
        <div class="card">
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
              <p class="text-sm font-semibold text-slate-700 mb-2"><?php echo e($idx + 1); ?>. <?php echo e($q?->question); ?></p>
              <div class="text-sm text-slate-500 mb-2">
                <strong>Student Answer:</strong>
                <span class="ml-1 <?php echo e($resp->is_correct === true ? 'text-green-600 font-semibold' : ($resp->is_correct === false ? 'text-red-500' : 'text-amber-600')); ?>">
                  <?php echo e($resp->answer ?? '—'); ?>

                </span>
              </div>
              <?php if(in_array($q?->question_type, ['mcq', 'true_false']) && $q?->correct_answer): ?>
                <p class="text-xs text-green-600"><strong>Correct:</strong> <?php echo e($q->correct_answer); ?></p>
              <?php endif; ?>
            </div>
            <div class="flex-shrink-0 text-right">
              <?php if($resp->is_correct !== null): ?>
                <span class="<?php echo e($resp->is_correct ? 'badge-green' : 'badge-red'); ?>"><?php echo e($resp->is_correct ? 'Correct' : 'Wrong'); ?></span>
              <?php else: ?>
                <span class="badge-amber">Not Evaluated</span>
              <?php endif; ?>
              <p class="text-xs text-slate-400 mt-1">Max: <?php echo e($q?->marks); ?> marks</p>
            </div>
          </div>

          <?php if(in_array($q?->question_type, ['short_answer', 'fill_blank'])): ?>
            <div class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-slate-100">
              <div>
                <label class="label">Marks Awarded (0–<?php echo e($q?->marks); ?>)</label>
                <input type="number" name="marks[<?php echo e($resp->id); ?>]" class="input"
                       value="<?php echo e($resp->marks_awarded); ?>" step="0.5" min="0" max="<?php echo e($q?->marks); ?>">
              </div>
              <div>
                <label class="label">Remarks</label>
                <input type="text" name="remarks[<?php echo e($resp->id); ?>]" class="input"
                       value="<?php echo e($resp->evaluator_remarks); ?>" placeholder="Optional feedback…">
              </div>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($pending->count()): ?>
      <div class="flex justify-end mt-4">
        <button type="submit" class="btn btn-primary">Save Evaluation</button>
      </div>
    <?php else: ?>
      <div class="card-flat py-3 mt-4 text-center text-sm text-green-700">
        All responses evaluated.
        <strong>Final Score: <?php echo e($attempt->final_score); ?> / <?php echo e($attempt->exam?->total_marks); ?></strong>
        — <span class="<?php echo e($attempt->result === 'pass' ? 'text-green-600' : 'text-red-500'); ?> font-bold uppercase"><?php echo e($attempt->result); ?></span>
      </div>
    <?php endif; ?>
  </form>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\online-exams\evaluate.blade.php ENDPATH**/ ?>