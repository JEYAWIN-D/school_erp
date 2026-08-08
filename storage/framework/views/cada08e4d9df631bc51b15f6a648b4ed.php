<?php $__env->startSection('title', 'Quiz Result'); ?>
<?php $__env->startSection('content'); ?>

<?php
  $pct = $attempt->percentage();
  $passed = $pct >= 50;
  $badgeBg = $passed ? '#f0fdf4' : '#fef2f2';
  $badgeClr = $passed ? '#16a34a' : '#dc2626';
?>

<div class="portal-card" style="text-align:center;padding:2rem 1rem;margin-bottom:1rem;">
  <div style="width:5rem;height:5rem;border-radius:50%;background:<?php echo e($badgeBg); ?>;border:3px solid <?php echo e($badgeClr); ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
    <span style="font-size:1.75rem;font-weight:800;color:<?php echo e($badgeClr); ?>;"><?php echo e($pct); ?>%</span>
  </div>
  <h2 style="font-size:1.25rem;font-weight:700;color:#0f172a;margin-bottom:.25rem;"><?php echo e($passed ? 'Well done!' : 'Keep practising'); ?></h2>
  <p style="font-size:.875rem;color:#64748b;"><?php echo e($attempt->quiz->title); ?></p>
  <p style="font-size:.875rem;color:#334155;margin-top:.5rem;">
    Score: <strong><?php echo e($attempt->score); ?></strong> / <?php echo e($attempt->total_marks); ?>

    &bull; <?php echo e($attempt->timeUsedMinutes()); ?> min
  </p>
</div>


<div class="portal-card" style="margin-bottom:1rem;">
  <p style="font-size:.875rem;font-weight:700;color:#0f172a;margin-bottom:.875rem;">Answers Review</p>
  <?php $__currentLoopData = $attempt->quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php
    $ans = $attempt->answers->firstWhere('question_id', $q->id);
    $correct = $ans?->is_correct;
    $rowBg = $correct ? '#f0fdf4' : '#fef2f2';
    $icon  = $correct ? '✓' : '✗';
    $iconClr = $correct ? '#16a34a' : '#dc2626';
  ?>
  <div style="padding:.75rem;background:<?php echo e($rowBg); ?>;border-radius:.5rem;margin-bottom:.5rem;">
    <p style="font-size:.8rem;font-weight:600;color:#1e293b;margin-bottom:.375rem;">
      <span style="color:<?php echo e($iconClr); ?>;margin-right:.25rem;"><?php echo e($icon); ?></span>
      Q<?php echo e($i+1); ?>. <?php echo e($q->question); ?>

    </p>
    <p style="font-size:.75rem;color:#64748b;">Your answer: <strong><?php echo e($ans?->given_answer ?: '—'); ?></strong></p>
    <?php if(!$correct): ?>
      <p style="font-size:.75rem;color:#16a34a;">Correct: <strong><?php echo e($q->correct_answer); ?></strong></p>
    <?php endif; ?>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div style="text-align:center;">
  <a href="<?php echo e(route('portal.student.academics')); ?>" style="font-size:.875rem;color:#7c3aed;font-weight:600;">← Back to Academics</a>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\portal\student\quiz-result.blade.php ENDPATH**/ ?>