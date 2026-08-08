<?php $__env->startSection('title', 'Homework'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Homework</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('academics.homework.completion-report')); ?>" class="btn btn-secondary btn-sm">Completion Report</a>
      <button x-data @click="$dispatch('open-modal','add-homework')" class="btn btn-primary btn-sm">Assign Homework</button>
    </div>
  </div>

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 flex-wrap">
      <select name="class_id" class="select w-36">
        <option value="">All Classes</option>
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <select name="subject_id" class="select w-36">
        <option value="">All Subjects</option>
        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('subject_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <input type="date" name="date" value="<?php echo e(request('date')); ?>" class="input w-36" placeholder="Filter by date">
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </div>
  </form>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $homework; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card border-l-4 <?php echo e($hw->due_date < today() ? 'border-l-red-400' : 'border-l-blue-400'); ?>">
      <div class="flex items-start justify-between mb-2">
        <div>
          <p class="font-semibold text-slate-800"><?php echo e($hw->title ?: $hw->subject?->name); ?></p>
          <p class="text-xs text-slate-400"><?php echo e($hw->class?->name); ?> <?php echo e($hw->section?->name); ?></p>
          <p class="text-xs text-slate-400"><?php echo e($hw->subject?->name); ?></p>
        </div>
        <div class="text-right">
          <span class="text-xs <?php echo e($hw->due_date < today() ? 'text-red-500 font-semibold' : 'text-slate-400'); ?>">
            Due: <?php echo e(\Carbon\Carbon::parse($hw->due_date)->format('d M Y')); ?>

          </span>
          <?php if($hw->max_score): ?>
          <p class="text-xs text-slate-400">Max: <?php echo e($hw->max_score); ?></p>
          <?php endif; ?>
        </div>
      </div>
      <p class="text-sm text-slate-600 line-clamp-3 mb-2"><?php echo e($hw->description); ?></p>
      <?php if($hw->attachment): ?>
      <a href="<?php echo e(Storage::url($hw->attachment)); ?>" target="_blank"
        class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
        </svg>
        Attachment
      </a>
      <?php endif; ?>
      <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100">
        <span class="text-xs text-slate-400"><?php echo e($hw->submissions_count ?? 0); ?> submitted</span>
        <a href="<?php echo e(route('academics.homework.submissions', $hw->id)); ?>" class="btn btn-ghost btn-xs">View Submissions</a>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-span-3 card text-center py-10 text-slate-400">No homework assigned for this filter.</div>
    <?php endif; ?>
  </div>
  <?php if($homework->hasPages()): ?><div class="text-sm"><?php echo e($homework->links()); ?></div><?php endif; ?>
</div>


<div x-data="{open:false}" @open-modal.window="if($event.detail==='add-homework')open=true"
  x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
  <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
  <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between">
      <h3 class="font-semibold text-slate-800">Assign Homework</h3>
      <button @click="open=false" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
    </div>
    <form method="POST" action="<?php echo e(route('academics.homework.save')); ?>" enctype="multipart/form-data" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="label text-xs">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select" required>
            <option value="">Select Class</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label text-xs">Subject <span class="text-red-500">*</span></label>
          <select name="subject_id" class="select" required>
            <option value="">Select Subject</option>
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="col-span-2">
          <label class="label text-xs">Title</label>
          <input type="text" name="title" class="input" placeholder="Brief title (optional)">
        </div>
        <div class="col-span-2">
          <label class="label text-xs">Description / Instructions <span class="text-red-500">*</span></label>
          <textarea name="description" rows="3" class="input" required placeholder="Homework instructions..."></textarea>
        </div>
        <div>
          <label class="label text-xs">Due Date <span class="text-red-500">*</span></label>
          <input type="date" name="due_date" class="input" value="<?php echo e(today()->addDay()->toDateString()); ?>" required>
        </div>
        <div>
          <label class="label text-xs">Max Score</label>
          <input type="number" name="max_score" class="input" min="0" placeholder="e.g. 10">
        </div>
        <div class="col-span-2">
          <label class="label text-xs">Attachment (PDF/Image/Doc, max 5MB)</label>
          <input type="file" name="attachment" class="input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
          <p class="text-xs text-slate-400 mt-1">Attach question paper, reference material, or image.</p>
        </div>
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" @click="open=false" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">Assign</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\homework.blade.php ENDPATH**/ ?>