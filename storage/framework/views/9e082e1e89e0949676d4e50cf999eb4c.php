<?php $__env->startSection('title', 'Quiz Builder'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Quiz Builder — <?php echo e($course->title); ?></h1>
    <a href="<?php echo e(route('lms.courses.show', $course->id)); ?>" class="btn-secondary btn-sm">← Course</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">New Quiz</h3>
      <form method="POST" action="<?php echo e(route('lms.quiz.store', $course->id)); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">Quiz Title</label>
          <input type="text" name="title" value="<?php echo e(old('title')); ?>" class="input w-full" required>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="label">Duration (min)</label>
            <input type="number" name="duration_minutes" value="<?php echo e(old('duration_minutes', 30)); ?>" class="input w-full" min="1">
          </div>
          <div>
            <label class="label">Marks/Question</label>
            <input type="number" name="marks_per_question" value="<?php echo e(old('marks_per_question', 1)); ?>" class="input w-full" step="0.5" min="0">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="label">Available From</label>
            <input type="datetime-local" name="available_from" value="<?php echo e(old('available_from')); ?>" class="input w-full text-sm">
          </div>
          <div>
            <label class="label">Available To</label>
            <input type="datetime-local" name="available_to" value="<?php echo e(old('available_to')); ?>" class="input w-full text-sm">
          </div>
        </div>
        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" name="randomise" value="1" checked class="rounded">
          Randomise question order
        </label>
        <button type="submit" class="btn-primary w-full">Create Quiz</button>
      </form>
    </div>

    
    <div class="lg:col-span-2 space-y-4">
      <?php $__empty_1 = true; $__currentLoopData = $quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="card">
        <div class="flex items-center justify-between mb-3">
          <div>
            <h3 class="font-semibold text-slate-700"><?php echo e($quiz->title); ?></h3>
            <p class="text-xs text-slate-400"><?php echo e($quiz->duration_minutes); ?> min &bull; <?php echo e($quiz->questions->count()); ?> questions &bull; <?php echo e($quiz->marks_per_question); ?> marks each</p>
          </div>
          <div class="flex gap-2">
            <a href="<?php echo e(route('lms.quiz.questions', $quiz->id)); ?>" class="btn-xs btn-primary">Manage Questions</a>
            <a href="<?php echo e(route('lms.quiz.attempts', $quiz->id)); ?>" class="btn-xs btn-secondary">Attempts</a>
          </div>
        </div>
        <?php if($quiz->available_from || $quiz->available_to): ?>
        <p class="text-xs text-slate-400">
          Available: <?php echo e($quiz->available_from?->format('d M Y H:i') ?? 'Always'); ?>

          → <?php echo e($quiz->available_to?->format('d M Y H:i') ?? 'No end'); ?>

        </p>
        <?php endif; ?>
        <div class="mt-2 flex items-center gap-2">
          <?php if($quiz->isAvailable()): ?>
            <span class="badge-green text-xs">Open</span>
          <?php elseif($quiz->available_from && now()->lt($quiz->available_from)): ?>
            <span class="badge-blue text-xs">Upcoming</span>
          <?php else: ?>
            <span class="badge-red text-xs">Closed</span>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="card text-center py-10">
        <p class="text-slate-400 text-sm">No quizzes yet. Create one on the left.</p>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\lms\quiz\builder.blade.php ENDPATH**/ ?>