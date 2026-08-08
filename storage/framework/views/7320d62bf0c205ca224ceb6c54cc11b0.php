<?php $__env->startSection('title','Grading Schemes'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{addOpen:false}">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Grading Schemes</h1>
    <button @click="addOpen=true" class="btn btn-primary btn-sm">+ Add Scheme</button>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    <?php $__empty_1 = true; $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card space-y-3">
      <div class="flex items-center justify-between pb-2 border-b border-slate-100">
        <div>
          <p class="font-semibold text-slate-800"><?php echo e($scheme->name); ?></p>
          <p class="text-xs text-slate-400"><?php echo e($scheme->type === 'percentage' ? 'Percentage-based' : 'Grade Point'); ?></p>
        </div>
        <?php if($scheme->is_default): ?><span class="badge-green text-xs">Default</span><?php endif; ?>
      </div>
      <div class="space-y-1">
        <?php $__currentLoopData = $scheme->ranges->sortByDesc('min_marks'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex items-center justify-between text-sm px-2 py-1 rounded <?php echo e($r->grade === 'F' ? 'bg-red-50' : 'bg-slate-50'); ?>">
          <span class="font-bold text-slate-700 w-8"><?php echo e($r->grade); ?></span>
          <span class="text-slate-500 text-xs"><?php echo e($r->min_marks); ?>—<?php echo e($r->max_marks); ?>%</span>
          <span class="text-slate-400 text-xs"><?php echo e($r->description); ?></span>
          <?php if($r->gpa_points): ?><span class="font-semibold text-indigo-600 text-xs"><?php echo e($r->gpa_points); ?> pts</span><?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div class="flex gap-2 pt-1">
        <a href="<?php echo e(route('examinations.grading.edit',$scheme->id)); ?>" class="btn btn-secondary btn-sm flex-1 text-center">Edit Ranges</a>
        <?php if(!$scheme->is_default): ?>
        <form method="POST" action="<?php echo e(route('examinations.grading.default',$scheme->id)); ?>"><?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-secondary btn-sm">Set Default</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-span-3 card text-center py-12 text-slate-400">No grading schemes configured.</div>
    <?php endif; ?>
  </div>
</div>


<div x-data="{addOpen:false}" x-on:open-modal.window="addOpen=($event.detail==='add-scheme')" x-show="addOpen" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="addOpen=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-700 mb-4">New Grading Scheme</h3>
    <form method="POST" action="<?php echo e(route('examinations.grading.store')); ?>" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div><label class="label">Scheme Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="input" required placeholder="e.g. CBSE 10-point Scale">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Type</label>
          <select name="type" class="select">
            <option value="percentage">Percentage</option>
            <option value="gpa">GPA</option>
          </select>
        </div>
        <div><label class="label">Pass Mark (%)</label>
          <input type="number" name="pass_percentage" class="input" value="35" min="0" max="100">
        </div>
      </div>
      <div class="flex items-center gap-2">
        <input type="checkbox" name="is_default" value="1" id="sch_default">
        <label for="sch_default" class="text-sm text-slate-600">Set as default</label>
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Create</button>
        <button type="button" @click="addOpen=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\grading.blade.php ENDPATH**/ ?>