<?php $__env->startSection('title','Question Bank'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{addOpen:false}">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Question Bank</h1>
    <button @click="addOpen=true" class="btn btn-primary btn-sm">+ Add Question</button>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="subject_id" class="select w-40">
      <option value="">All Subjects</option>
      <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('subject_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="class_id" class="select w-36">
      <option value="">All Classes</option>
      <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="question_type" class="select w-36">
      <option value="">All Types</option>
      <?php $__currentLoopData = ['mcq'=>'MCQ','short_answer'=>'Short Answer','long_answer'=>'Long Answer','true_false'=>'True/False','fill_blank'=>'Fill Blank']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option value="<?php echo e($k); ?>" <?php if(request('question_type')===$k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="difficulty" class="select w-32">
      <option value="">Any Difficulty</option>
      <option value="easy" <?php if(request('difficulty')==='easy'): echo 'selected'; endif; ?>>Easy</option>
      <option value="medium" <?php if(request('difficulty')==='medium'): echo 'selected'; endif; ?>>Medium</option>
      <option value="hard" <?php if(request('difficulty')==='hard'): echo 'selected'; endif; ?>>Hard</option>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
  </div></form>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['#','Question','Subject','Type','Difficulty','Marks','Chapter','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($questions->firstItem()+$i); ?></td>
          <td class="px-4 py-3 text-slate-800 max-w-xs">
            <p class="text-sm font-medium line-clamp-2"><?php echo e($q->question_text); ?></p>
            <?php if($q->question_type === 'mcq' && $q->options): ?>
            <div class="text-xs text-slate-400 mt-1">Options: <?php echo e(implode(' / ',array_slice((array)$q->options,0,4))); ?></div>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($q->subject?->name); ?></td>
          <td class="px-4 py-3"><span class="badge-slate capitalize text-xs"><?php echo e(str_replace('_',' ',$q->question_type)); ?></span></td>
          <td class="px-4 py-3">
            <span class="text-xs <?php echo e($q->difficulty==='easy'?'text-green-600':($q->difficulty==='hard'?'text-red-500':'text-amber-600')); ?> font-medium capitalize"><?php echo e($q->difficulty); ?></span>
          </td>
          <td class="px-4 py-3 text-center font-semibold text-slate-700"><?php echo e($q->marks); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($q->chapter); ?></td>
          <td class="px-4 py-3 flex gap-2">
            <a href="<?php echo e(route('examinations.qbank.edit',$q->id)); ?>" class="text-indigo-600 hover:underline text-xs">Edit</a>
            <form method="POST" action="<?php echo e(route('examinations.qbank.delete',$q->id)); ?>" class="inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button type="submit" class="text-red-400 hover:underline text-xs" onclick="return confirm('Delete?')">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No questions in bank.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($questions->hasPages()): ?><div class="px-4 pb-3"><?php echo e($questions->links()); ?></div><?php endif; ?>
  </div>
</div>


<div x-data="{show:false,qtype:'mcq'}" x-on:open-modal.window="show=($event.detail==='add-question')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-2xl max-h-screen overflow-y-auto">
    <h3 class="font-semibold text-slate-700 mb-4">Add Question</h3>
    <form method="POST" action="<?php echo e(route('examinations.qbank.store')); ?>" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Subject <span class="text-red-500">*</span></label>
          <select name="subject_id" class="select" required>
            <option value="">Select</option>
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Class</label>
          <select name="class_id" class="select">
            <option value="">All</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div><label class="label">Question Text <span class="text-red-500">*</span></label>
        <textarea name="question_text" class="input h-20" required></textarea>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div><label class="label">Type <span class="text-red-500">*</span></label>
          <select name="question_type" class="select" x-model="qtype">
            <?php $__currentLoopData = ['mcq'=>'MCQ','short_answer'=>'Short Answer','long_answer'=>'Long Answer','true_false'=>'True/False','fill_blank'=>'Fill Blank']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>"><?php echo e($v); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Difficulty</label>
          <select name="difficulty" class="select">
            <option value="easy">Easy</option>
            <option value="medium" selected>Medium</option>
            <option value="hard">Hard</option>
          </select>
        </div>
        <div><label class="label">Marks <span class="text-red-500">*</span></label>
          <input type="number" name="marks" class="input" required value="1" min="1">
        </div>
      </div>
      <div x-show="qtype==='mcq'" class="space-y-2">
        <label class="label">Options (one per line, prefix correct with *)</label>
        <textarea name="options_text" class="input h-24 font-mono text-sm" placeholder="Option A&#10;Option B&#10;*Option C (correct)&#10;Option D"></textarea>
      </div>
      <div><label class="label">Chapter / Unit</label>
        <input type="text" name="chapter" class="input" placeholder="e.g. Chapter 3: Photosynthesis">
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Add Question</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('[x-data]').forEach(el=>{if(el.getAttribute('@click')==='addOpen=true'){el.addEventListener('click',()=>el.dispatchEvent(new CustomEvent('open-modal',{detail:'add-question',bubbles:true})))}})});</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\question-bank.blade.php ENDPATH**/ ?>