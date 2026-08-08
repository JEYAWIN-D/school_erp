<?php $__env->startSection('title', 'Notification Templates'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ activeEvent: '', activeChannel: 'email' }">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Notification Templates</h1>
    <a href="<?php echo e(route('settings.index')); ?>" class="btn-sm btn-secondary">← Settings</a>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  <div class="card bg-slate-50 border border-slate-200 text-sm text-slate-600">
    <p class="font-semibold mb-1">Available Placeholders</p>
    <p class="text-xs font-mono text-slate-500">
      <?php echo e('{{'); ?>parent_name<?php echo e('); ?>'}}, <?php echo e('{{'); ?>student_name<?php echo e('); ?>'}}, <?php echo e('{{'); ?>class<?php echo e('); ?>'}}, <?php echo e('{{'); ?>amount<?php echo e('); ?>'}}, <?php echo e('{{'); ?>balance<?php echo e('); ?>'}}, <?php echo e('{{'); ?>due_date<?php echo e('); ?>'}}, <?php echo e('{{'); ?>date<?php echo e('); ?>'}}, <?php echo e('{{'); ?>exam_name<?php echo e('); ?>'}}, <?php echo e('{{'); ?>percentage<?php echo e('); ?>'}}, <?php echo e('{{'); ?>grade<?php echo e('); ?>'}}, <?php echo e('{{'); ?>result<?php echo e('); ?>'}}, <?php echo e('{{'); ?>action_type<?php echo e('); ?>'}}, <?php echo e('{{'); ?>total_fine<?php echo e('); ?>'}}, <?php echo e('{{'); ?>school_name<?php echo e('); ?>'}}
    </p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    <div class="card space-y-2">
      <h2 class="text-sm font-semibold text-slate-700 mb-3">Event Types</h2>
      <?php $__currentLoopData = $eventTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <button type="button" @click="activeEvent='<?php echo e($key); ?>'"
        :class="activeEvent==='<?php echo e($key); ?>' ? 'bg-indigo-50 border-indigo-300 text-indigo-700' : 'border-slate-200 text-slate-600'"
        class="w-full text-left px-3 py-2 rounded-lg border text-sm hover:bg-indigo-50 transition-colors">
        <?php echo e($label); ?>

        <?php if($templates->has($key.'_email')): ?>
          <span class="float-right badge-green text-xs">✓</span>
        <?php endif; ?>
      </button>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="md:col-span-2">
      <?php $__currentLoopData = $eventTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div x-show="activeEvent==='<?php echo e($key); ?>'" x-cloak class="card space-y-4">
        <h2 class="text-base font-semibold text-slate-700"><?php echo e($label); ?></h2>

        <div class="flex gap-2 mb-2">
          <button type="button" @click="activeChannel='email'"
            :class="activeChannel==='email' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600'"
            class="btn-xs rounded-full px-3">Email</button>
          <button type="button" @click="activeChannel='sms'"
            :class="activeChannel==='sms' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'"
            class="btn-xs rounded-full px-3">SMS</button>
          <button type="button" @click="activeChannel='whatsapp'"
            :class="activeChannel==='whatsapp' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'"
            class="btn-xs rounded-full px-3">WhatsApp</button>
        </div>

        <?php $__currentLoopData = ['email', 'sms', 'whatsapp']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $tpl = $templates->get($key.'_'.$ch); ?>
        <div x-show="activeChannel==='<?php echo e($ch); ?>'">
          <form method="POST" action="<?php echo e(route('settings.notification-templates.save')); ?>" class="space-y-3">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="event_type" value="<?php echo e($key); ?>">
            <input type="hidden" name="channel" value="<?php echo e($ch); ?>">

            <?php if($ch === 'email'): ?>
            <div>
              <label class="label">Subject</label>
              <input type="text" name="subject" class="input"
                value="<?php echo e(old('subject', $tpl?->subject)); ?>"
                placeholder="<?php echo e($label); ?> — <?php echo e(config('app.name')); ?>">
            </div>
            <?php endif; ?>

            <div>
              <label class="label">Message Body</label>
              <textarea name="body" rows="8" class="input font-mono text-sm"><?php echo e(old('body', $tpl?->body ?? ($defaultBodies[$key] ?? ''))); ?></textarea>
            </div>

            <?php if($ch === 'email'): ?>
            <div>
              <label class="label">Trigger Time <span class="text-slate-400 text-xs">(for scheduled sends, e.g. 09:30)</span></label>
              <input type="time" name="trigger_time" class="input w-32"
                value="<?php echo e(old('trigger_time', $tpl?->trigger_time)); ?>">
            </div>
            <?php endif; ?>

            <div class="flex items-center gap-3">
              <input type="checkbox" name="is_active" id="active_<?php echo e($key); ?>_<?php echo e($ch); ?>" class="w-4 h-4"
                <?php echo e(($tpl?->is_active ?? true) ? 'checked' : ''); ?>>
              <label for="active_<?php echo e($key); ?>_<?php echo e($ch); ?>" class="label mb-0">Active</label>
            </div>

            <div class="flex justify-end">
              <button type="submit" class="btn-primary btn-sm">Save Template</button>
            </div>
          </form>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      <div x-show="!activeEvent" class="card text-center py-12 text-slate-400">
        <p>Select an event type on the left to configure its template.</p>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\settings\notification-templates.blade.php ENDPATH**/ ?>