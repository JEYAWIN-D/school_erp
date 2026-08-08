<?php $__env->startSection('title','Route Roster'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Route Roster</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('transport.roster.pdf')); ?>" target="_blank" class="btn btn-secondary btn-sm">Export PDF</a>
      <a href="<?php echo e(route('transport.roster.excel')); ?>" class="btn btn-secondary btn-sm">Export Excel</a>
    </div>
  </div>
  <?php $__empty_1 = true; $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
  <div class="card">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <div>
        <p class="font-semibold text-slate-800"><?php echo e($route->route_name); ?></p>
        <p class="text-xs text-slate-400"><?php echo e($route->vehicle?->vehicle_number); ?> | <?php echo e($route->allotments->count()); ?> students</p>
      </div>
      <span class="badge-green">Active</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
      <?php if($route->stops->count()): ?>
      <div>
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Stops</h4>
        <div class="space-y-1">
          <?php $__currentLoopData = $route->stops->sortBy('stop_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="flex items-center gap-2 text-sm text-slate-600">
            <span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-600 text-xs flex items-center justify-center font-bold"><?php echo e($stop->stop_order); ?></span>
            <?php echo e($stop->name); ?>

            <?php if($stop->arrival_time): ?><span class="text-slate-400 text-xs"><?php echo e($stop->arrival_time); ?></span><?php endif; ?>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
      <?php endif; ?>
      <div>
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Students (<?php echo e($route->allotments->count()); ?>)</h4>
        <div class="space-y-1 max-h-48 overflow-y-auto">
          <?php $__currentLoopData = $route->allotments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="flex items-center justify-between text-sm text-slate-600 py-1 border-b border-slate-50">
            <span><?php echo e($a->enrollment?->student?->full_name); ?></span>
            <span class="text-xs text-slate-400"><?php echo e($a->enrollment?->class?->name); ?></span>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  <div class="card text-center py-12 text-slate-400">No routes configured.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\roster.blade.php ENDPATH**/ ?>