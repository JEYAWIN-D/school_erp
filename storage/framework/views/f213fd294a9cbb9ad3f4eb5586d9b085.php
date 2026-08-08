<?php $__env->startSection('title', $event->name); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-3xl space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <a href="<?php echo e(route('events.index')); ?>" class="btn-sm btn-secondary">← Events</a>
    <div class="flex gap-2">
      <a href="<?php echo e(route('events.edit', $event->id)); ?>" class="btn-sm btn-secondary">Edit</a>
      <form method="POST" action="<?php echo e(route('events.destroy', $event->id)); ?>" onsubmit="return confirm('Delete this event?')">
        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button type="submit" class="btn-sm btn-secondary text-rose-600">Delete</button>
      </form>
    </div>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  
  <?php if($event->banner_image): ?>
  <img src="<?php echo e(Storage::url($event->banner_image)); ?>" alt="<?php echo e($event->name); ?>" class="w-full h-56 object-cover rounded-2xl">
  <?php endif; ?>

  <div class="card space-y-4">
    <div class="flex items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-slate-800"><?php echo e($event->name); ?></h1>
        <div class="flex flex-wrap gap-2 mt-2">
          <?php $typeColors=['academic'=>'badge-blue','cultural'=>'badge-pink','sports'=>'badge-green','holiday'=>'badge-amber','meeting'=>'badge-blue','other'=>'badge-slate']; ?>
          <span class="<?php echo e($typeColors[$event->event_type] ?? 'badge-slate'); ?>"><?php echo e(ucfirst($event->event_type)); ?></span>
          <?php if($event->is_published): ?> <span class="badge-green">Published</span> <?php else: ?> <span class="badge-slate">Draft</span> <?php endif; ?>
          <span class="badge-slate"><?php echo e(ucfirst($event->audience)); ?></span>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4 text-sm">
      <div>
        <p class="text-slate-400 text-xs">Date</p>
        <p class="font-semibold text-slate-700"><?php echo e($event->event_date->format('l, d F Y')); ?></p>
      </div>
      <div>
        <p class="text-slate-400 text-xs">Time</p>
        <p class="font-semibold text-slate-700">
          <?php if($event->start_time): ?> <?php echo e(substr($event->start_time,0,5)); ?><?php if($event->end_time): ?> – <?php echo e(substr($event->end_time,0,5)); ?><?php endif; ?> <?php else: ?> All Day <?php endif; ?>
        </p>
      </div>
      <?php if($event->venue): ?>
      <div class="col-span-2">
        <p class="text-slate-400 text-xs">Venue</p>
        <p class="font-semibold text-slate-700"><?php echo e($event->venue); ?></p>
      </div>
      <?php endif; ?>
    </div>

    <?php if($event->description): ?>
    <div class="prose text-sm text-slate-600 max-w-none"><?php echo e($event->description); ?></div>
    <?php endif; ?>
  </div>

  
  <?php if($event->allow_rsvp): ?>
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
      RSVP
      <?php if($rsvpCounts->isNotEmpty()): ?>
        <span class="text-xs font-normal text-slate-400">
          <?php echo e($rsvpCounts->get('attending',0)); ?> attending
          · <?php echo e($rsvpCounts->get('maybe',0)); ?> maybe
          · <?php echo e($rsvpCounts->get('not_attending',0)); ?> not attending
        </span>
      <?php endif; ?>
    </h3>
    <?php if(auth()->guard()->check()): ?>
    <form method="POST" action="<?php echo e(route('events.rsvp', $event->id)); ?>" class="flex flex-wrap gap-3 items-end">
      <?php echo csrf_field(); ?>
      <div>
        <label class="label">Your Response</label>
        <select name="status" class="select">
          <option value="attending" <?php if($myRsvp?->status==='attending'): echo 'selected'; endif; ?>>✅ Attending</option>
          <option value="maybe" <?php if($myRsvp?->status==='maybe'): echo 'selected'; endif; ?>>🤔 Maybe</option>
          <option value="not_attending" <?php if($myRsvp?->status==='not_attending'): echo 'selected'; endif; ?>>❌ Not Attending</option>
        </select>
      </div>
      <div>
        <label class="label">Guests</label>
        <input type="number" name="guest_count" value="<?php echo e($myRsvp?->guest_count ?? 0); ?>" class="input w-24" min="0">
      </div>
      <div class="flex-1 min-w-48">
        <label class="label">Note (optional)</label>
        <input type="text" name="note" value="<?php echo e($myRsvp?->note ?? ''); ?>" class="input" placeholder="Any message or dietary preference…">
      </div>
      <button type="submit" class="btn-primary btn-sm">Save RSVP</button>
    </form>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-700">Photo Gallery (<?php echo e($event->photos->count()); ?>)</h3>
    </div>

    <?php if($event->photos->isNotEmpty()): ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
      <?php $__currentLoopData = $event->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="relative group">
        <img src="<?php echo e(Storage::url($photo->photo_path)); ?>" alt="<?php echo e($photo->caption); ?>" class="w-full h-32 object-cover rounded-xl">
        <?php if($photo->caption): ?>
        <p class="text-xs text-slate-400 mt-1 truncate"><?php echo e($photo->caption); ?></p>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('events.photos.delete', $photo->id)); ?>" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100">
          <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
          <button type="submit" class="bg-white/90 rounded-lg px-2 py-1 text-xs text-rose-600 font-medium hover:bg-white">✕</button>
        </form>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('events.photos.upload', $event->id)); ?>" enctype="multipart/form-data" class="space-y-2">
      <?php echo csrf_field(); ?>
      <label class="label">Upload Photos</label>
      <input type="file" name="photos[]" multiple accept="image/*" class="input">
      <button type="submit" class="btn-sm btn-secondary">Upload</button>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\events\show.blade.php ENDPATH**/ ?>