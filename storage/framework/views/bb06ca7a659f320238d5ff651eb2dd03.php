<?php $__env->startSection('title', $course->title); ?>
<?php $__env->startSection('content'); ?>

<div style="margin-bottom: 1rem;">
  <a href="<?php echo e(route('portal.student.academics')); ?>" style="font-size: .8125rem; color: #2563eb; text-decoration: none;">
    &#8592; Back to Academics
  </a>
</div>

<div class="portal-card" style="margin-bottom: 1rem;">
  <?php if($course->thumbnail ?? null): ?>
    <img src="<?php echo e(Storage::url($course->thumbnail)); ?>" style="width:100%;height:12rem;object-fit:cover;border-radius:.75rem;margin-bottom:1rem;">
  <?php else: ?>
    <div style="width:100%;height:8rem;background:linear-gradient(135deg,#6366f1,#3b82f6);border-radius:.75rem;margin-bottom:1rem;display:flex;align-items:center;justify-content:center;">
      <svg style="width:3rem;height:3rem;color:rgba(255,255,255,.6)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    </div>
  <?php endif; ?>
  <h1 style="font-size: 1.1875rem; font-weight: 700; color: #1e293b; margin-bottom: .5rem;"><?php echo e($course->title); ?></h1>
  <?php if($course->description ?? null): ?>
    <p style="font-size: .875rem; color: #64748b; line-height: 1.6;"><?php echo e($course->description); ?></p>
  <?php endif; ?>
  <?php if($course->units->count()): ?>
  <div style="display:flex;gap:.875rem;margin-top:.875rem;font-size:.75rem;color:#94a3b8;">
    <span><?php echo e($course->units->count()); ?> unit(s)</span>
    <span>&bull;</span>
    <span><?php echo e($course->units->sum(fn($u) => $u->lessons->count())); ?> lesson(s)</span>
  </div>
  <?php endif; ?>
</div>

<?php if($course->units->count()): ?>
<div style="display:flex;flex-direction:column;gap:.75rem;">
  <?php $__currentLoopData = $course->units->sortBy('sort_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div class="portal-card">
    <div style="font-size:.9375rem;font-weight:700;color:#1e293b;margin-bottom:.625rem;display:flex;align-items:center;gap:.5rem;">
      <span style="width:1.5rem;height:1.5rem;border-radius:50%;background:#ede9fe;color:#7c3aed;font-size:.7rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?php echo e($loop->iteration); ?></span>
      <?php echo e($unit->title); ?>

    </div>
    <?php if($unit->description ?? null): ?>
      <p style="font-size:.8rem;color:#64748b;margin-bottom:.75rem;"><?php echo e($unit->description); ?></p>
    <?php endif; ?>
    <?php if($unit->lessons->count()): ?>
    <div style="display:flex;flex-direction:column;gap:.375rem;">
      <?php $__currentLoopData = $unit->lessons->sortBy('sort_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div style="display:flex;align-items:center;gap:.75rem;padding:.5rem .75rem;background:#f8fafc;border-radius:.5rem;">
        <?php $ctype = $lesson->content_type ?? 'text'; ?>
        <?php if($ctype === 'video'): ?>
          <svg style="width:.875rem;height:.875rem;color:#94a3b8;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <?php elseif($ctype === 'pdf'): ?>
          <svg style="width:.875rem;height:.875rem;color:#94a3b8;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        <?php elseif($ctype === 'quiz'): ?>
          <svg style="width:.875rem;height:.875rem;color:#94a3b8;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <?php else: ?>
          <svg style="width:.875rem;height:.875rem;color:#94a3b8;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <?php endif; ?>
        <span style="font-size:.8rem;color:#334155;flex:1;"><?php echo e($lesson->title); ?></span>
        <?php if($lesson->duration_minutes ?? null): ?>
          <span style="font-size:.72rem;color:#94a3b8;flex-shrink:0;"><?php echo e($lesson->duration_minutes); ?>min</span>
        <?php endif; ?>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <p style="font-size:.8rem;color:#94a3b8;">No lessons in this unit yet.</p>
    <?php endif; ?>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php else: ?>
<div class="portal-card" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
  <p>No content added to this course yet.</p>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\portal\student\course.blade.php ENDPATH**/ ?>