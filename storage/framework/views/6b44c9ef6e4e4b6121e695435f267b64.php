<?php $__env->startSection('title', 'Alumni Directory'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Alumni Directory</h1>
    <a href="<?php echo e(route('alumni.index')); ?>" class="btn-sm btn-secondary">← Admin View</a>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div><label class="label">Search</label><input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input" placeholder="Name…"></div>
    <div>
      <label class="label">Batch Year</label>
      <select name="passing_year" class="select">
        <option value="">All Batches</option>
        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($y); ?>" <?php if(request('passing_year')==$y): echo 'selected'; endif; ?>>Class of <?php echo e($y); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <button type="submit" class="btn-primary btn-sm">Search</button>
  </form>

  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $alumni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <a href="<?php echo e(route('alumni.show', $a->id)); ?>" class="card text-center hover:shadow-md transition-shadow">
      <?php if($a->profile_photo): ?>
      <img src="<?php echo e(Storage::url($a->profile_photo)); ?>" class="w-16 h-16 rounded-full object-cover mx-auto mb-2">
      <?php else: ?>
      <div class="w-16 h-16 rounded-full bg-indigo-100 text-indigo-600 font-bold text-xl flex items-center justify-center mx-auto mb-2">
        <?php echo e(strtoupper(substr($a->first_name,0,1))); ?>

      </div>
      <?php endif; ?>
      <p class="font-semibold text-slate-700 text-sm leading-tight"><?php echo e($a->full_name); ?></p>
      <p class="text-xs text-indigo-500 font-medium mt-0.5">Class of <?php echo e($a->passing_year); ?></p>
      <?php if($a->current_occupation): ?>
      <p class="text-xs text-slate-400 mt-0.5 line-clamp-1"><?php echo e($a->current_occupation); ?></p>
      <?php endif; ?>
      <?php if($a->current_city): ?>
      <p class="text-xs text-slate-400">📍 <?php echo e($a->current_city); ?></p>
      <?php endif; ?>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-span-6 card text-center py-12 text-slate-400">No alumni found.</div>
    <?php endif; ?>
  </div>
  <div><?php echo e($alumni->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\alumni\directory.blade.php ENDPATH**/ ?>