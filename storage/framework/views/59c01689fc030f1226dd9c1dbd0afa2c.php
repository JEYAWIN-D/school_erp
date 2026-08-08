<?php $__env->startSection('title', 'Quiz Questions'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-5" x-data="{ qtype: 'mcq_single' }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title"><?php echo e($quiz->title); ?></h1>
      <p class="text-sm text-slate-400"><?php echo e($quiz->questions->count()); ?> questions &bull; <?php echo e($quiz->duration_minutes); ?> min</p>
    </div>
    <a href="<?php echo e(route('lms.quiz.builder', $quiz->course_id)); ?>" class="btn-secondary btn-sm">← Quiz Builder</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Add Question</h3>
      <form method="POST" action="<?php echo e(route('lms.quiz.question.store', $quiz->id)); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">Question Type</label>
          <select name="type" x-model="qtype" class="select w-full">
            <option value="mcq_single">MCQ — Single Correct</option>
            <option value="mcq_multi">MCQ — Multiple Correct</option>
            <option value="true_false">True / False</option>
            <option value="fill_blank">Fill in the Blank</option>
          </select>
        </div>
        <div>
          <label class="label">Question</label>
          <textarea name="question" rows="3" class="input w-full" required></textarea>
        </div>

        
        <div x-show="qtype === 'mcq_single' || qtype === 'mcq_multi'" class="space-y-2">
          <label class="label">Options (one per line)</label>
          <input type="text" name="options[]" placeholder="Option A" class="input w-full text-sm">
          <input type="text" name="options[]" placeholder="Option B" class="input w-full text-sm">
          <input type="text" name="options[]" placeholder="Option C" class="input w-full text-sm">
          <input type="text" name="options[]" placeholder="Option D" class="input w-full text-sm">
        </div>

        <div>
          <label class="label">
            Correct Answer
            <span x-show="qtype === 'mcq_single' || qtype === 'mcq_multi'" class="text-xs font-normal text-slate-400">(enter exact option text)</span>
            <span x-show="qtype === 'true_false'" class="text-xs font-normal text-slate-400">(True or False)</span>
          </label>
          <input type="text" name="correct_answer" class="input w-full" required>
        </div>
        <div>
          <label class="label">Marks</label>
          <input type="number" name="marks" value="<?php echo e($quiz->marks_per_question); ?>" step="0.5" min="0" class="input w-full">
        </div>
        <button type="submit" class="btn-primary w-full">Add Question</button>
      </form>
    </div>

    
    <div class="lg:col-span-2 space-y-3">
      <?php $__empty_1 = true; $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="card">
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <span class="text-xs font-medium text-slate-400">Q<?php echo e($loop->iteration); ?></span>
              <span class="badge-blue text-xs"><?php echo e(str_replace('_', ' ', $q->type)); ?></span>
              <span class="text-xs text-slate-400"><?php echo e($q->marks); ?> marks</span>
            </div>
            <p class="text-sm font-medium text-slate-700"><?php echo e($q->question); ?></p>
            <?php if($q->options): ?>
              <div class="mt-2 grid grid-cols-2 gap-1">
                <?php $__currentLoopData = $q->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="text-xs px-2 py-1 rounded <?php echo e($opt === $q->correct_answer ? 'bg-green-50 text-green-700 font-medium' : 'bg-slate-50 text-slate-500'); ?>">
                    <?php echo e($opt); ?>

                    <?php if($opt === $q->correct_answer): ?> ✓ <?php endif; ?>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
            <?php else: ?>
              <p class="text-xs text-green-600 mt-1">Answer: <?php echo e($q->correct_answer); ?></p>
            <?php endif; ?>
          </div>
          <form method="POST" action="<?php echo e(route('lms.quiz.question.delete', $q->id)); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" onclick="return confirm('Delete question?')" class="text-red-400 hover:text-red-600 text-lg leading-none px-1">×</button>
          </form>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="card text-center py-10">
        <p class="text-slate-400 text-sm">No questions yet. Add questions on the left.</p>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\lms\quiz\questions.blade.php ENDPATH**/ ?>