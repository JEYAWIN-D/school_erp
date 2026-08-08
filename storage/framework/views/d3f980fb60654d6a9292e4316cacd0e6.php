<?php $__env->startSection('title','Assemble Question Paper'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ selected: [] }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Manual Question Paper Assembly</h1>
      <p class="page-subtitle">Select questions from the bank to assemble a custom question paper</p>
    </div>
    <a href="<?php echo e(route('examinations.question-papers')); ?>" class="btn btn-secondary btn-sm">← Question Papers</a>
  </div>

  
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap items-end">
    <div>
      <label class="label">Class</label>
      <select name="class_id" class="select w-36">
        <option value="">All</option>
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Subject <span class="text-red-400">*</span></label>
      <select name="subject_id" class="select w-40">
        <option value="">Select subject</option>
        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('subject_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Type</label>
      <select name="question_type" class="select w-36">
        <option value="">All Types</option>
        <?php $__currentLoopData = ['mcq'=>'MCQ','true_false'=>'True/False','short'=>'Short Answer','long'=>'Long Answer','descriptive'=>'Descriptive']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($k); ?>" <?php if(request('question_type')===$k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Difficulty</label>
      <select name="difficulty" class="select w-32">
        <option value="">All</option>
        <option value="easy" <?php if(request('difficulty')==='easy'): echo 'selected'; endif; ?>>Easy</option>
        <option value="medium" <?php if(request('difficulty')==='medium'): echo 'selected'; endif; ?>>Medium</option>
        <option value="hard" <?php if(request('difficulty')==='hard'): echo 'selected'; endif; ?>>Hard</option>
      </select>
    </div>
    <div>
      <label class="label">Chapter</label>
      <input type="text" name="chapter" value="<?php echo e(request('chapter')); ?>" class="input w-36" placeholder="Chapter name">
    </div>
    <button type="submit" class="btn btn-secondary btn-sm">Load Questions</button>
  </div></form>

  <?php if($questions->isEmpty() && !request()->hasAny(['subject_id','class_id'])): ?>
    <div class="card text-center py-12 text-slate-400">
      <p class="text-lg font-medium mb-2">Select a Subject to Load Questions</p>
      <p class="text-sm">Filter by subject and class above, then select questions to assemble your paper.</p>
    </div>
  <?php else: ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    
    <div class="lg:col-span-2 space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold text-slate-700">
          <?php echo e($questions->count()); ?> questions found
          <?php if($questions->isNotEmpty()): ?>
          <span x-text="' — ' + selected.length + ' selected (' + selected.reduce((sum, id) => {
            const el = document.querySelector(`[data-q=\"${id}\"]`);
            return sum + (el ? parseInt(el.dataset.marks) : 0);
          }, 0) + \" marks)\""></span>
          <?php endif; ?>
        </h3>
        <?php if($questions->isNotEmpty()): ?>
        <button type="button"
          @click="selected = selected.length === <?php echo e($questions->count()); ?> ? [] : [<?php echo e($questions->pluck('id')->join(',')); ?>]"
          class="btn btn-secondary btn-sm text-xs">Toggle All</button>
        <?php endif; ?>
      </div>

      <?php $__empty_1 = true; $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="card cursor-pointer border-2 transition-colors"
           :class="selected.includes(<?php echo e($q->id); ?>) ? 'border-indigo-400 bg-indigo-50/40' : 'border-transparent'"
           @click="selected.includes(<?php echo e($q->id); ?>) ? selected = selected.filter(x => x !== <?php echo e($q->id); ?>) : selected.push(<?php echo e($q->id); ?>)"
           data-q="<?php echo e($q->id); ?>" data-marks="<?php echo e($q->marks); ?>">
        <div class="flex items-start gap-3">
          <div class="mt-1 shrink-0">
            <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors"
                 :class="selected.includes(<?php echo e($q->id); ?>) ? 'border-indigo-500 bg-indigo-500' : 'border-slate-300'">
              <svg x-show="selected.includes(<?php echo e($q->id); ?>)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <span class="badge-<?php echo e(['mcq'=>'indigo','short'=>'green','long'=>'amber','true_false'=>'slate','descriptive'=>'red'][$q->question_type] ?? 'slate'); ?> text-xs capitalize">
                <?php echo e(str_replace('_',' ',$q->question_type)); ?>

              </span>
              <span class="badge-<?php echo e($q->difficulty_level==='easy'?'green':($q->difficulty_level==='hard'?'red':'amber')); ?> text-xs capitalize">
                <?php echo e($q->difficulty_level); ?>

              </span>
              <span class="text-xs text-slate-400"><?php echo e($q->marks); ?> mark<?php echo e($q->marks != 1 ? 's' : ''); ?></span>
              <?php if($q->chapter): ?><span class="text-xs text-slate-400">| <?php echo e($q->chapter); ?></span><?php endif; ?>
            </div>
            <p class="text-sm text-slate-800"><?php echo e(\Illuminate\Support\Str::limit($q->question_text, 150)); ?></p>
            <?php if($q->question_type === 'mcq' && $q->options): ?>
              <?php $opts = is_array($q->options) ? $q->options : json_decode($q->options, true); ?>
              <?php if(is_array($opts)): ?>
              <div class="mt-1 grid grid-cols-2 gap-1">
                <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p class="text-xs <?php echo e(($opt['correct'] ?? false) ? 'text-green-600 font-medium' : 'text-slate-400'); ?>">
                  <?php echo e(chr(65+$i)); ?>. <?php echo e($opt['text'] ?? ''); ?>

                </p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="card text-center py-8 text-slate-400">No questions match the filters.</div>
      <?php endif; ?>
    </div>

    
    <div class="space-y-4">
      <div class="card sticky top-4">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Paper Settings</h3>
        <form method="POST" action="<?php echo e(route('examinations.assemble-paper.generate')); ?>" target="_blank">
          <?php echo csrf_field(); ?>
          
          <template x-for="id in selected" :key="id">
            <input type="hidden" name="question_ids[]" :value="id">
          </template>
          <div class="space-y-3">
            <div>
              <label class="label">Paper Title <span class="text-red-500">*</span></label>
              <input type="text" name="paper_title" class="input" required
                placeholder="e.g. Half-Yearly Science Exam — Class X">
            </div>
            <div>
              <label class="label">Exam</label>
              <select name="exam_id" class="select">
                <option value="">Select exam (optional)</option>
                <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($e->id); ?>"><?php echo e($e->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div>
              <label class="label">Duration (minutes)</label>
              <input type="number" name="duration" class="input" value="180" min="30" step="30">
            </div>
            <div>
              <label class="label">Template</label>
              <select name="template_id" class="select text-sm">
                <option value="">Default Format</option>
                <?php $__currentLoopData = \App\Models\QuestionPaperTemplate::orderByDesc('is_default')->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($tpl->id); ?>" <?php if($tpl->is_default): echo 'selected'; endif; ?>><?php echo e($tpl->name); ?><?php if($tpl->is_default): ?> (Default)<?php endif; ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <a href="<?php echo e(route('examinations.paper-templates')); ?>" class="text-xs text-indigo-500 hover:underline mt-1 block">Manage templates →</a>
            </div>
            <div>
              <label class="label">Instructions</label>
              <textarea name="instructions" class="input h-16 text-xs" placeholder="General instructions for students...">All questions are compulsory. Write clearly. No calculators allowed.</textarea>
            </div>
            <div class="pt-2 border-t border-slate-100">
              <div class="flex justify-between text-sm mb-1">
                <span class="text-slate-500">Selected Questions</span>
                <span class="font-bold text-indigo-600" x-text="selected.length"></span>
              </div>
            </div>
            <button type="submit"
                    :disabled="selected.length === 0"
                    :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                    class="btn btn-primary w-full">
              Generate PDF Paper
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\assemble-paper.blade.php ENDPATH**/ ?>