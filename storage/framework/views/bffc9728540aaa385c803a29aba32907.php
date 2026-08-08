<?php $__env->startSection('title', 'Assignments'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Assignments — <?php echo e($course->title); ?></h1>
    <a href="<?php echo e(route('lms.courses.show', $course->id)); ?>" class="btn-secondary btn-sm">← Course</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    
    <?php if(auth()->user()->hasRole(['Super Admin', 'School Admin', 'Teacher', 'HOD'])): ?>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">New Assignment</h3>
      <form method="POST" action="<?php echo e(route('lms.assignments.store', $course->id)); ?>" enctype="multipart/form-data" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">Title</label>
          <input type="text" name="title" value="<?php echo e(old('title')); ?>" class="input w-full" required>
        </div>
        <div>
          <label class="label">Instructions</label>
          <textarea name="instructions" rows="4" class="input w-full"><?php echo e(old('instructions')); ?></textarea>
        </div>
        <div>
          <label class="label">Due Date &amp; Time</label>
          <input type="datetime-local" name="due_at" value="<?php echo e(old('due_at')); ?>" class="input w-full text-sm">
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="label">Max Marks</label>
            <input type="number" name="max_marks" value="<?php echo e(old('max_marks', 100)); ?>" min="0" class="input w-full">
          </div>
        </div>
        <div>
          <label class="label">Attachment (optional)</label>
          <input type="file" name="attachment" class="input w-full text-sm py-1.5">
        </div>
        <button type="submit" class="btn-primary w-full">Create Assignment</button>
      </form>
    </div>
    <?php endif; ?>

    
    <div class="<?php echo e(auth()->user()->hasRole(['Super Admin', 'School Admin', 'Teacher', 'HOD']) ? 'lg:col-span-2' : 'lg:col-span-3'); ?> space-y-4">
      <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="card">
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <?php if($assignment->isOverdue()): ?>
                <span class="badge-red text-xs">Overdue</span>
              <?php elseif($assignment->due_at): ?>
                <span class="badge-blue text-xs">Open</span>
              <?php else: ?>
                <span class="badge-green text-xs">No Deadline</span>
              <?php endif; ?>
              <h3 class="font-semibold text-slate-800"><?php echo e($assignment->title); ?></h3>
            </div>
            <?php if($assignment->instructions): ?>
              <p class="text-sm text-slate-600 mt-1"><?php echo e(Str::limit($assignment->instructions, 120)); ?></p>
            <?php endif; ?>
            <div class="flex items-center gap-4 mt-2 text-xs text-slate-400 flex-wrap">
              <?php if($assignment->due_at): ?>
                <span>Due: <?php echo e($assignment->due_at->format('d M Y H:i')); ?></span>
              <?php endif; ?>
              <span>Max: <?php echo e($assignment->max_marks); ?> marks</span>
              <span><?php echo e($assignment->submissions_count ?? 0); ?> submission(s)</span>
            </div>
          </div>
          <div class="flex gap-2 flex-shrink-0 flex-wrap">
            <?php if($assignment->attachment): ?>
              <a href="<?php echo e(Storage::url($assignment->attachment)); ?>" target="_blank" class="btn-xs btn-secondary">Download</a>
            <?php endif; ?>
            <?php if(auth()->user()->hasRole(['Super Admin', 'School Admin', 'Teacher', 'HOD'])): ?>
              <a href="<?php echo e(route('lms.assignments.submissions', $assignment->id)); ?>" class="btn-xs btn-primary">Submissions</a>
              <form method="POST" action="<?php echo e(route('lms.assignments.delete', $assignment->id)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" onclick="return confirm('Delete assignment?')" class="btn-xs text-red-500 border border-red-200 rounded px-1.5 py-0.5 hover:bg-red-50">Delete</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="card text-center py-10">
        <p class="text-slate-400 text-sm">No assignments for this course yet.</p>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\lms\assignments.blade.php ENDPATH**/ ?>