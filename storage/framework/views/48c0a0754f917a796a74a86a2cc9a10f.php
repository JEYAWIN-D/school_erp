<?php $__env->startSection('title', $alumni->full_name); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl space-y-6">
  <div class="flex items-center justify-between">
    <a href="<?php echo e(route('alumni.index')); ?>" class="btn-sm btn-secondary">← Alumni</a>
    <div class="flex gap-2">
      <a href="<?php echo e(route('alumni.edit', $alumni->id)); ?>" class="btn-sm btn-secondary">Edit</a>
      <form method="POST" action="<?php echo e(route('alumni.destroy', $alumni->id)); ?>" onsubmit="return confirm('Delete this alumni record permanently?')">
        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button class="btn-sm btn-danger">Delete</button>
      </form>
    </div>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  <div class="card">
    <div class="flex items-start gap-5">
      <?php if($alumni->profile_photo): ?>
      <img src="<?php echo e(Storage::url($alumni->profile_photo)); ?>" class="w-24 h-24 rounded-2xl object-cover">
      <?php else: ?>
      <div class="w-24 h-24 rounded-2xl bg-indigo-100 flex items-center justify-center text-3xl font-bold text-indigo-600">
        <?php echo e(strtoupper(substr($alumni->first_name,0,1))); ?>

      </div>
      <?php endif; ?>
      <div class="flex-1">
        <h1 class="text-2xl font-bold text-slate-800"><?php echo e($alumni->full_name); ?></h1>
        <p class="text-indigo-600 font-medium">Class of <?php echo e($alumni->passing_year); ?> <?php if($alumni->last_class): ?> — <?php echo e($alumni->last_class); ?><?php endif; ?></p>
        <div class="flex gap-2 mt-2 flex-wrap">
          <?php if($alumni->is_verified): ?>
            <span class="badge-green relative group cursor-default">
              ✓ Verified
              <span class="absolute hidden group-hover:flex -top-9 left-0 bg-slate-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">Identity verified by school administration</span>
            </span>
          <?php endif; ?>
          <?php if($alumni->current_city): ?> <span class="badge-slate">📍 <?php echo e($alumni->current_city); ?></span> <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mt-5 text-sm">
      <?php if($alumni->current_occupation): ?>
      <div><p class="text-slate-400 text-xs">Occupation</p><p class="font-medium text-slate-700"><?php echo e($alumni->current_occupation); ?></p></div>
      <?php endif; ?>
      <?php if($alumni->current_employer): ?>
      <div><p class="text-slate-400 text-xs">Employer</p><p class="font-medium text-slate-700"><?php echo e($alumni->current_employer); ?></p></div>
      <?php endif; ?>
      <?php if($alumni->email): ?>
      <div><p class="text-slate-400 text-xs">Email</p><a href="mailto:<?php echo e($alumni->email); ?>" class="font-medium text-indigo-600 hover:underline"><?php echo e($alumni->email); ?></a></div>
      <?php endif; ?>
      <?php if($alumni->phone): ?>
      <div><p class="text-slate-400 text-xs">Phone</p><a href="tel:<?php echo e($alumni->phone); ?>" class="font-medium text-blue-600 hover:underline font-mono"><?php echo e($alumni->phone); ?></a></div>
      <?php endif; ?>
      <?php if($alumni->linkedin_url): ?>
      <div class="col-span-2"><p class="text-slate-400 text-xs">LinkedIn</p><a href="<?php echo e($alumni->linkedin_url); ?>" target="_blank" class="text-indigo-600 hover:underline"><?php echo e($alumni->linkedin_url); ?></a></div>
      <?php endif; ?>
    </div>

    <?php if($alumni->achievements): ?>
    <div class="mt-4 pt-4 border-t border-slate-100">
      <p class="text-xs text-slate-400 mb-1">Achievements</p>
      <p class="text-sm text-slate-600"><?php echo e($alumni->achievements); ?></p>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\alumni\show.blade.php ENDPATH**/ ?>