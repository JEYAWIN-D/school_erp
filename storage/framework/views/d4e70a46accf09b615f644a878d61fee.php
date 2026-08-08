<?php $__env->startSection('title', 'Manage Questions — ' . $exam->title); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="<?php echo e(route('online-exams.index')); ?>" class="btn-icon">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h1 class="page-title"><?php echo e($exam->title); ?></h1>
        <p class="page-subtitle"><?php echo e($exam->class?->name); ?> | <?php echo e($exam->start_time->format('d M Y, g:i A')); ?> | <?php echo e($exam->duration_minutes); ?> min</p>
      </div>
    </div>
    <div class="flex gap-2">
      <span class="badge-slate">Total: <?php echo e($totalMarks); ?> marks</span>
      <?php if($exam->status === 'draft'): ?>
        <form method="POST" action="<?php echo e(route('online-exams.publish', $exam->id)); ?>">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-primary btn-sm">Publish Exam</button>
        </form>
      <?php else: ?>
        <span class="badge-green capitalize"><?php echo e($exam->status); ?></span>
      <?php endif; ?>
    </div>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>
  <?php if(session('error')): ?>
    <div class="alert-error"><?php echo e(session('error')); ?></div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-3 pb-2 border-b border-slate-100">
        Questions in Exam (<?php echo e($exam->examQuestions->count()); ?>)
      </h3>
      <?php $__empty_1 = true; $__currentLoopData = $exam->examQuestions->sortBy('sort_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex items-start gap-3 py-2 border-b border-slate-50">
          <span class="text-xs text-slate-400 w-5 text-right mt-1"><?php echo e($loop->iteration); ?>.</span>
          <div class="flex-1 min-w-0">
            <p class="text-sm text-slate-700"><?php echo e(Str::limit($eq->question?->question, 120)); ?></p>
            <div class="flex items-center gap-2 mt-1 flex-wrap">
              <span class="badge-slate text-xs capitalize"><?php echo e($eq->question?->question_type); ?></span>
              <span class="text-xs text-slate-400"><?php echo e($eq->question?->marks); ?> marks</span>
              <?php if($eq->question?->difficulty): ?>
                <span class="text-xs text-slate-400 capitalize"><?php echo e($eq->question->difficulty); ?></span>
              <?php endif; ?>
            </div>
          </div>
          <form method="POST" action="<?php echo e(route('online-exams.question.remove', [$exam->id, $eq->question_bank_id])); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-icon text-red-400 hover:text-red-600 mt-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </form>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-slate-400 text-sm py-4 text-center">No questions added yet.</p>
      <?php endif; ?>
    </div>

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-3 pb-2 border-b border-slate-100">Add from Question Bank</h3>

      <form method="GET" class="flex flex-wrap gap-2 mb-4">
        <input type="hidden" name="id" value="<?php echo e($exam->id); ?>">
        <select name="subject_id" class="select w-36 text-sm">
          <option value="">All Subjects</option>
          <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($sub->id); ?>" <?php if(request('subject_id') == $sub->id): echo 'selected'; endif; ?>><?php echo e($sub->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="question_type" class="select w-32 text-sm">
          <option value="">All Types</option>
          <?php $__currentLoopData = ['mcq' => 'MCQ', 'true_false' => 'True/False', 'fill_blank' => 'Fill Blank', 'short_answer' => 'Short Answer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>" <?php if(request('question_type') === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      </form>

      <form method="POST" action="<?php echo e(route('online-exams.question.add', $exam->id)); ?>">
        <?php echo csrf_field(); ?>
        <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
          <?php $__empty_1 = true; $__currentLoopData = $bank; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <label class="flex items-start gap-3 py-2 border border-transparent hover:border-slate-200 rounded-lg px-2 cursor-pointer">
              <input type="checkbox" name="question_ids[]" value="<?php echo e($q->id); ?>" class="mt-1 rounded">
              <div class="flex-1 min-w-0">
                <p class="text-sm text-slate-700"><?php echo e(Str::limit($q->question, 100)); ?></p>
                <span class="badge-slate text-xs capitalize"><?php echo e($q->question_type); ?></span>
                <span class="text-xs text-slate-400 ml-1"><?php echo e($q->marks); ?> marks</span>
              </div>
            </label>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-slate-400 text-sm text-center py-4">All questions from this class/filter are already added or none exist.</p>
          <?php endif; ?>
        </div>

        <?php if($bank->count()): ?>
          <div class="mt-4 flex justify-between items-center">
            <button type="button" onclick="this.closest('form').querySelectorAll('input[type=checkbox]').forEach(c=>c.checked=true)" class="text-xs text-blue-600 hover:underline">Select All</button>
            <button type="submit" class="btn btn-primary btn-sm">Add Selected</button>
          </div>
        <?php endif; ?>
      </form>

      <?php if($bank->hasPages()): ?>
        <div class="mt-3 text-xs text-slate-400 flex justify-between">
          <span><?php echo e($bank->firstItem()); ?>–<?php echo e($bank->lastItem()); ?> of <?php echo e($bank->total()); ?></span>
          <?php echo e($bank->links()); ?>

        </div>
      <?php endif; ?>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\online-exams\questions.blade.php ENDPATH**/ ?>