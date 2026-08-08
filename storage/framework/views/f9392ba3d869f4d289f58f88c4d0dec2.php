<?php $__env->startSection('title', 'Alumni Events'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('alumni.index')); ?>" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="page-title">Alumni Events</h1>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    
    <div class="lg:col-span-1">
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">New Event</h3>
        <form method="POST" action="<?php echo e(route('alumni.events.store')); ?>" class="space-y-3">
          <?php echo csrf_field(); ?>
          <div>
            <label class="label">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" class="input" value="<?php echo e(old('title')); ?>" required>
            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div>
            <label class="label">Event Date <span class="text-red-500">*</span></label>
            <input type="date" name="event_date" class="input" value="<?php echo e(old('event_date')); ?>" required>
            <?php $__errorArgs = ['event_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div>
            <label class="label">Venue</label>
            <input type="text" name="venue" class="input" value="<?php echo e(old('venue')); ?>" placeholder="Location / Online">
          </div>
          <div>
            <label class="label">Description</label>
            <textarea name="description" rows="4" class="input"><?php echo e(old('description')); ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary w-full">Create Event</button>
        </form>
      </div>
    </div>

    
    <div class="lg:col-span-2">
      <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="card mb-4">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-1">
              <h3 class="font-semibold text-slate-800"><?php echo e($event->title); ?></h3>
              <?php if($event->is_published): ?>
                <span class="badge-green">Published</span>
              <?php else: ?>
                <span class="badge-slate">Draft</span>
              <?php endif; ?>
            </div>
            <p class="text-sm text-slate-500 mb-1">
              <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <?php echo e(\Carbon\Carbon::parse($event->event_date)->format('d M Y')); ?>

              <?php if($event->venue): ?>
                &bull;
                <svg class="w-3.5 h-3.5 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <?php echo e($event->venue); ?>

              <?php endif; ?>
            </p>
            <?php if($event->description): ?>
              <p class="text-sm text-slate-500 line-clamp-2"><?php echo e($event->description); ?></p>
            <?php endif; ?>
          </div>
          <div class="flex items-center gap-2 flex-shrink-0">
            <form method="POST" action="<?php echo e(route('alumni.events.toggle', $event->id)); ?>">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn btn-secondary btn-sm">
                <?php echo e($event->is_published ? 'Unpublish' : 'Publish'); ?>

              </button>
            </form>
            <form method="POST" action="<?php echo e(route('alumni.events.destroy', $event->id)); ?>" onsubmit="return confirm('Delete this event?')">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button type="submit" class="btn btn-secondary btn-sm text-red-600 hover:bg-red-50">Delete</button>
            </form>
          </div>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="card text-center py-12 text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <p class="text-sm">No alumni events yet. Create one to get started.</p>
      </div>
      <?php endif; ?>

      <?php if($events->hasPages()): ?>
        <div class="mt-4"><?php echo e($events->links()); ?></div>
      <?php endif; ?>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\alumni\events.blade.php ENDPATH**/ ?>