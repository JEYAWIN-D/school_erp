<?php $__env->startSection('title', 'Syllabus'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Syllabus</h1>
    <button x-data @click="$dispatch('open-modal','add-syllabus')" class="btn btn-primary btn-sm">Add Syllabus</button>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3">
    <select name="class_id" class="select w-36">
      <option value="">All Classes</option>
      <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="subject_id" class="select w-44">
      <option value="">All Subjects</option>
      <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('subject_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
  </div></form>
  <div class="space-y-3">
    <?php $__empty_1 = true; $__currentLoopData = $syllabus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card" x-data="{ showUpload: false }">
      <div class="flex items-start justify-between">
        <div>
          <p class="font-semibold text-slate-800"><?php echo e($item->topic ?? $item->chapter_title ?? '—'); ?></p>
          <p class="text-xs text-slate-400 mt-0.5"><?php echo e($item->subject?->name); ?> &mdash; <?php echo e($item->class?->name); ?>

            <?php if($item->term): ?><span class="ml-2 badge-blue text-xs"><?php echo e($item->term); ?></span><?php endif; ?>
          </p>
        </div>
        <div class="flex items-center gap-2">
          <span class="<?php echo e($item->status === 'completed' ? 'badge-green' : ($item->status === 'in_progress' ? 'badge-amber' : 'badge-slate')); ?> capitalize"><?php echo e(str_replace('_',' ', $item->status)); ?></span>
          <?php if($item->document_path): ?>
            <a href="<?php echo e(asset('storage/' . $item->document_path)); ?>" target="_blank"
               class="btn btn-secondary btn-xs flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              PDF
            </a>
          <?php endif; ?>
          <button @click="showUpload = !showUpload" class="btn btn-secondary btn-xs">
            <?php echo e($item->document_path ? 'Replace PDF' : 'Upload PDF'); ?>

          </button>
        </div>
      </div>
      <?php if($item->description): ?><p class="text-sm text-slate-500 mt-2"><?php echo e($item->description); ?></p><?php endif; ?>
      <div x-show="showUpload" x-transition class="mt-3 pt-3 border-t border-slate-100">
        <form method="POST" action="<?php echo e(route('academics.syllabus.document', $item->id)); ?>"
              enctype="multipart/form-data" class="flex items-end gap-3 flex-wrap">
          <?php echo csrf_field(); ?>
          <div class="flex-1 min-w-[200px]">
            <label class="label text-xs">PDF Document (max 10 MB)</label>
            <input type="file" name="document" accept=".pdf" class="input text-sm" required>
          </div>
          <div class="w-36">
            <label class="label text-xs">Term</label>
            <input type="text" name="term" value="<?php echo e($item->term); ?>" placeholder="e.g. Term 1" class="input text-sm">
          </div>
          <button type="submit" class="btn btn-primary btn-sm">Upload</button>
        </form>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="card text-center py-10 text-slate-400">No syllabus entries found. Select a class and subject to view.</div>
    <?php endif; ?>
  </div>
  <?php if(method_exists($syllabus,'hasPages') && $syllabus->hasPages()): ?><div class="text-sm"><?php echo e($syllabus->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\syllabus.blade.php ENDPATH**/ ?>