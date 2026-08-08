<?php $__env->startSection('title', $quiz->title); ?>
<?php $__env->startSection('content'); ?>

<?php if($existing): ?>
<div class="portal-card" style="margin-bottom:1rem;background:#fffbeb;border:1px solid #fcd34d;">
  <p style="font-size:.875rem;color:#92400e;font-weight:600;">You already completed this quiz.</p>
  <p style="font-size:.8rem;color:#78350f;margin-top:.25rem;">Score: <?php echo e($existing->score); ?>/<?php echo e($existing->total_marks); ?> (<?php echo e($existing->percentage()); ?>%)</p>
  <a href="<?php echo e(route('portal.quiz.result', $existing->id)); ?>" style="display:inline-block;margin-top:.75rem;font-size:.8rem;color:#7c3aed;font-weight:600;">View Result →</a>
</div>
<?php endif; ?>

<div class="portal-card" style="margin-bottom:1rem;">
  <h2 style="font-size:1.1rem;font-weight:700;color:#0f172a;margin-bottom:.25rem;"><?php echo e($quiz->title); ?></h2>
  <?php if($quiz->course): ?>
    <p style="font-size:.8rem;color:#64748b;margin-bottom:.5rem;"><?php echo e($quiz->course->title); ?></p>
  <?php endif; ?>
  <div style="display:flex;gap:1rem;flex-wrap:wrap;">
    <span style="font-size:.75rem;color:#64748b;"><?php echo e($questions->count()); ?> questions</span>
    <?php if($quiz->duration_minutes): ?>
      <span style="font-size:.75rem;color:#64748b;">⏱ <?php echo e($quiz->duration_minutes); ?> min</span>
    <?php endif; ?>
    <?php if($quiz->marks_per_question): ?>
      <span style="font-size:.75rem;color:#64748b;"><?php echo e($quiz->marks_per_question); ?> mark(s) per question</span>
    <?php endif; ?>
    <?php if($quiz->negative_marks): ?>
      <span style="font-size:.75rem;color:#dc2626;">-<?php echo e($quiz->negative_marks); ?> for wrong</span>
    <?php endif; ?>
  </div>
</div>

<form method="POST" action="<?php echo e(route('portal.quiz.submit', $quiz->id)); ?>"
      onsubmit="return confirm('Submit quiz? You cannot change answers after submission.');">
  <?php echo csrf_field(); ?>

  <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div class="portal-card" style="margin-bottom:.75rem;">
    <p style="font-size:.875rem;font-weight:600;color:#1e293b;margin-bottom:.75rem;">
      <span style="color:#7c3aed;">Q<?php echo e($i+1); ?>.</span> <?php echo e($q->question); ?>

    </p>

    <?php if($q->type === 'mcq' && $q->options): ?>
      <?php $__currentLoopData = $q->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <label style="display:flex;align-items:center;gap:.5rem;padding:.5rem .625rem;margin-bottom:.375rem;border:1px solid #e2e8f0;border-radius:.5rem;cursor:pointer;font-size:.85rem;color:#334155;">
        <input type="radio" name="answers[<?php echo e($q->id); ?>]" value="<?php echo e($opt); ?>"
               style="width:1rem;height:1rem;accent-color:#7c3aed;">
        <?php echo e($opt); ?>

      </label>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php elseif($q->type === 'true_false'): ?>
      <?php $__currentLoopData = ['True', 'False']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <label style="display:flex;align-items:center;gap:.5rem;padding:.5rem .625rem;margin-bottom:.375rem;border:1px solid #e2e8f0;border-radius:.5rem;cursor:pointer;font-size:.85rem;color:#334155;">
        <input type="radio" name="answers[<?php echo e($q->id); ?>]" value="<?php echo e($opt); ?>"
               style="width:1rem;height:1rem;accent-color:#7c3aed;">
        <?php echo e($opt); ?>

      </label>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
      <input type="text" name="answers[<?php echo e($q->id); ?>]" placeholder="Type your answer…"
             style="width:100%;padding:.5rem .75rem;border:1px solid #e2e8f0;border-radius:.5rem;font-size:.875rem;color:#1e293b;background:#fff;box-sizing:border-box;">
    <?php endif; ?>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  <div style="text-align:center;margin-top:1.25rem;">
    <button type="submit" style="background:#7c3aed;color:#fff;border:none;padding:.75rem 2.5rem;border-radius:.625rem;font-size:.9rem;font-weight:700;cursor:pointer;">
      Submit Quiz
    </button>
  </div>
</form>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\portal\student\quiz.blade.php ENDPATH**/ ?>