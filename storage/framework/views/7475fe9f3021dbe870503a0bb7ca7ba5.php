<?php $__env->startSection('title', isset($event) ? 'Edit Event' : 'New Event'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title"><?php echo e(isset($event) ? 'Edit Event' : 'Create Event'); ?></h1>
    <a href="<?php echo e(route('events.index')); ?>" class="btn-sm btn-secondary">← Back</a>
  </div>

  <form method="POST"
        action="<?php echo e(isset($event) ? route('events.update', $event->id) : route('events.store')); ?>"
        enctype="multipart/form-data"
        class="card space-y-4">
    <?php echo csrf_field(); ?>
    <?php if(isset($event)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="grid grid-cols-2 gap-4">
      <div class="col-span-2">
        <label class="label">Event Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="<?php echo e(old('name', $event->name ?? '')); ?>" class="input" required>
      </div>
      <div>
        <label class="label">Type <span class="text-red-500">*</span></label>
        <select name="event_type" class="select" required>
          <?php $__currentLoopData = ['academic','cultural','sports','holiday','meeting','other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($t); ?>" <?php if(old('event_type', $event->event_type ?? '')===$t): echo 'selected'; endif; ?>><?php echo e(ucfirst($t)); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Audience</label>
        <select name="audience" class="select">
          <?php $__currentLoopData = ['all','students','staff','parents']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($a); ?>" <?php if(old('audience', $event->audience ?? 'all')===$a): echo 'selected'; endif; ?>><?php echo e(ucfirst($a)); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Date <span class="text-red-500">*</span></label>
        <input type="date" name="event_date" value="<?php echo e(old('event_date', isset($event) ? $event->event_date->toDateString() : '')); ?>" class="input" required>
      </div>
      <div class="grid grid-cols-2 gap-2">
        <div>
          <label class="label">Start Time</label>
          <input type="time" name="start_time" value="<?php echo e(old('start_time', $event->start_time ?? '')); ?>" class="input">
        </div>
        <div>
          <label class="label">End Time</label>
          <input type="time" name="end_time" value="<?php echo e(old('end_time', $event->end_time ?? '')); ?>" class="input">
        </div>
      </div>
      <div class="col-span-2">
        <label class="label">Venue</label>
        <input type="text" name="venue" value="<?php echo e(old('venue', $event->venue ?? '')); ?>" class="input">
      </div>
      <div class="col-span-2">
        <label class="label">Description</label>
        <div class="flex gap-1 mb-1">
          <button type="button" onclick="fmtDoc('bold')" class="btn-xs btn-secondary" title="Bold"><strong>B</strong></button>
          <button type="button" onclick="fmtDoc('italic')" class="btn-xs btn-secondary" title="Italic"><em>I</em></button>
          <button type="button" onclick="fmtDoc('underline')" class="btn-xs btn-secondary" title="Underline"><u>U</u></button>
          <button type="button" onclick="fmtDoc('insertUnorderedList')" class="btn-xs btn-secondary" title="Bullet list">• List</button>
          <button type="button" onclick="fmtDoc('insertOrderedList')" class="btn-xs btn-secondary" title="Numbered list">1. List</button>
        </div>
        <div id="desc-editor" contenteditable="true"
             class="input min-h-[100px] text-sm leading-relaxed"
             style="white-space:pre-wrap;"><?php echo old('description', $event->description ?? ''); ?></div>
        <textarea name="description" id="desc-hidden" class="hidden"><?php echo e(old('description', $event->description ?? '')); ?></textarea>
        <script>
          function fmtDoc(cmd) { document.execCommand(cmd, false, null); document.getElementById('desc-editor').focus(); }
          document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('desc-hidden').value = document.getElementById('desc-editor').innerHTML;
          });
        </script>
      </div>
      <div class="col-span-2">
        <label class="label">Banner Image</label>
        <input type="file" name="banner_image" accept="image/*" class="input">
        <?php if(isset($event) && $event->banner_image): ?>
        <img src="<?php echo e(Storage::url($event->banner_image)); ?>" class="h-20 mt-2 rounded-lg">
        <?php endif; ?>
      </div>
    </div>

    <div class="flex flex-wrap gap-4">
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_published" value="1" <?php if(old('is_published', $event->is_published ?? false)): echo 'checked'; endif; ?> class="rounded">
        Publish Event
      </label>
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="allow_rsvp" value="1" <?php if(old('allow_rsvp', $event->allow_rsvp ?? false)): echo 'checked'; endif; ?> class="rounded">
        Allow RSVP
      </label>
      <div class="flex items-center gap-2">
        <label class="text-sm text-slate-600 whitespace-nowrap">Max RSVP</label>
        <input type="number" name="max_rsvp" value="<?php echo e(old('max_rsvp', $event->max_rsvp ?? '')); ?>"
               class="input w-24 text-sm" min="1" placeholder="No limit">
      </div>
    </div>

    <?php if($errors->any()): ?>
    <div class="alert-danger text-sm"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
    <?php endif; ?>

    <div class="flex gap-2">
      <button type="submit" class="btn-primary"><?php echo e(isset($event) ? 'Update' : 'Create Event'); ?></button>
      <a href="<?php echo e(route('events.index')); ?>" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\events\form.blade.php ENDPATH**/ ?>