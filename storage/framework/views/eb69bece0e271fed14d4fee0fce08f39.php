
<?php $__env->startSection('title', 'Notice Board'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Notice Board & Circulars</h1>
      <p class="page-subtitle">Manage school notices, circulars, and announcements</p>
    </div>
    <a href="<?php echo e(route('academics.notices.create')); ?>" class="btn btn-primary btn-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      New Notice
    </a>
  </div>

  <form method="GET" class="card-flat py-4">
    <div class="flex flex-wrap gap-3">
      <select name="type" class="select w-40">
        <option value="">All Types</option>
        <?php $__currentLoopData = ['general','circular','academic','exam','fee','event']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($t); ?>" <?php if(request('type') === $t): echo 'selected'; endif; ?>><?php echo e(ucfirst($t)); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <select name="audience" class="select w-40">
        <option value="">All Audiences</option>
        <?php $__currentLoopData = ['all','students','staff','parents','class_specific']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($a); ?>" <?php if(request('audience') === $a): echo 'selected'; endif; ?>><?php echo e(ucfirst(str_replace('_',' ',$a))); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </div>
  </form>

  <div class="space-y-4">
    <?php $__empty_1 = true; $__currentLoopData = $notices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card hover:shadow-md transition">
      <div class="flex items-start justify-between gap-4">
        <div class="flex-1">
          <div class="flex items-center gap-2 mb-1">
            <?php
              $typeColors = ['general'=>'badge-slate','circular'=>'badge-blue','academic'=>'badge-indigo','exam'=>'badge-amber','fee'=>'badge-red','event'=>'badge-green'];
            ?>
            <span class="<?php echo e($typeColors[$notice->notice_type] ?? 'badge-slate'); ?>"><?php echo e(ucfirst($notice->notice_type)); ?></span>
            <span class="badge-purple text-xs"><?php echo e(ucfirst(str_replace('_',' ', $notice->target_audience))); ?></span>
            <?php if(!$notice->is_published): ?> <span class="badge-amber text-xs">Draft</span> <?php endif; ?>
          </div>
          <h3 class="font-semibold text-slate-800 text-base"><?php echo e($notice->title); ?></h3>
          <p class="text-sm text-slate-500 mt-1 line-clamp-2"><?php echo e(Str::limit($notice->content, 150)); ?></p>
          <div class="flex items-center gap-4 mt-2 text-xs text-slate-400">
            <span>Published: <?php echo e($notice->publish_date->format('d M Y')); ?></span>
            <?php if($notice->expiry_date): ?> <span>Expires: <?php echo e($notice->expiry_date->format('d M Y')); ?></span> <?php endif; ?>
            <span>By: <?php echo e($notice->createdBy?->name); ?></span>
            <span><?php echo e($notice->reads->count()); ?> reads</span>
          </div>
        </div>
        <div class="flex items-center gap-1 flex-shrink-0">
          <a href="<?php echo e(route('academics.notices.read-receipts', $notice->id)); ?>" class="btn-xs btn-secondary" title="Read Receipts">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
          </a>
          <form method="POST" action="<?php echo e(route('academics.notices.delete', $notice->id)); ?>" onsubmit="return confirm('Delete this notice?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-icon text-red-400 hover:text-red-600">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="card text-center py-12 text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        No notices yet. <a href="<?php echo e(route('academics.notices.create')); ?>" class="text-blue-600 hover:underline">Create one</a>.
      </div>
    <?php endif; ?>
  </div>

  <?php if($notices->hasPages()): ?>
    <div><?php echo e($notices->links()); ?></div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\notices.blade.php ENDPATH**/ ?>