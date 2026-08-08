<?php $__env->startSection('title','Disciplinary Records — '.$student->full_name); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <nav class="text-sm text-slate-400 flex items-center gap-1.5 mb-1">
    <a href="<?php echo e(route('students.index')); ?>" class="hover:text-slate-600">Students</a>
    <span>/</span>
    <a href="<?php echo e(route('students.show',$student->id)); ?>" class="hover:text-slate-600"><?php echo e($student->full_name); ?></a>
    <span>/</span>
    <span class="text-slate-600">Disciplinary</span>
  </nav>
  <div class="flex items-center gap-3">
    <a href="<?php echo e(route('students.show',$student->id)); ?>" class="text-slate-400 hover:text-slate-700">←</a>
    <h1 class="page-title">Disciplinary Records — <?php echo e($student->full_name); ?></h1>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form method="POST" action="<?php echo e(route('students.disciplinary.add',$student->id)); ?>" class="card space-y-4">
      <?php echo csrf_field(); ?>
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Log Incident</h3>
      <div><label class="label">Incident Date</label><input type="date" name="incident_date" class="input" value="<?php echo e(today()->toDateString()); ?>"></div>
      <div><label class="label">Incident Type</label>
        <select name="incident_type" class="select">
          <?php $__currentLoopData = ['misconduct'=>'Misconduct','absenteeism'=>'Absenteeism','bullying'=>'Bullying','property_damage'=>'Property Damage','cheating'=>'Cheating','other'=>'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($k); ?>"><?php echo e($v); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div><label class="label">Description <span class="text-red-500">*</span></label>
        <textarea name="description" rows="3" class="input" required></textarea>
      </div>
      <div><label class="label">Action Taken</label>
        <input type="text" name="action_taken" class="input" placeholder="Warning, suspension, etc.">
      </div>
      <div x-data="{type:''}" >
        <label class="label">Action Type</label>
        <select name="action_type" x-model="type" class="select w-full">
          <option value="">Select Type</option>
          <option value="warning">Warning</option>
          <option value="suspension">Suspension</option>
          <option value="expulsion">Expulsion</option>
          <option value="positive_award">Positive Award</option>
          <option value="other">Other</option>
        </select>
        <div x-show="type==='suspension'" x-transition class="grid grid-cols-2 gap-3 mt-3">
          <div><label class="label text-xs">Suspension From</label><input type="date" name="suspension_from" class="input"></div>
          <div><label class="label text-xs">Suspension To</label><input type="date" name="suspension_to" class="input"></div>
        </div>
        <div x-show="type==='positive_award'" x-transition class="mt-3">
          <label class="label text-xs">Award Name</label>
          <input type="text" name="award_name" class="input" placeholder="e.g. Best Student Award">
        </div>
      </div>
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="parent_notified" value="1"> Parent Notified
      </label>
      <button type="submit" class="btn btn-primary">Save Record</button>
    </form>
    <div class="card space-y-3">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Incident History</h3>
      <?php $__empty_1 = true; $__currentLoopData = $incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="py-2 border-b border-slate-100 last:border-0">
        <div class="flex items-start justify-between gap-2">
          <div class="flex gap-1 flex-wrap">
            <?php if($rec->action_type === 'positive_award'): ?>
              <span class="badge-green text-xs capitalize"><?php echo e($rec->award_name ?: 'Award'); ?></span>
            <?php elseif($rec->action_type === 'suspension'): ?>
              <span class="badge-red text-xs">Suspension</span>
            <?php elseif($rec->action_type === 'expulsion'): ?>
              <span class="badge-red text-xs font-bold">EXPULSION</span>
            <?php else: ?>
              <span class="badge-<?php echo e(in_array($rec->incident_type, ['misconduct','bullying']) ? 'red' : 'amber'); ?> capitalize text-xs"><?php echo e(str_replace('_',' ',$rec->incident_type)); ?></span>
            <?php endif; ?>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400"><?php echo e($rec->incident_date->format('d M Y')); ?></span>
            <?php if($rec->action_type !== 'positive_award'): ?>
            <a href="<?php echo e(route('students.warning-letter', [$student->id, $rec->id])); ?>" target="_blank"
               class="text-xs text-red-500 hover:text-red-700 font-medium" title="Warning Letter PDF">
              ⚠ Letter
            </a>
            <?php endif; ?>
          </div>
        </div>
        <p class="text-sm text-slate-600 mt-1"><?php echo e($rec->description); ?></p>
        <?php if($rec->action_taken): ?><p class="text-xs text-slate-400 mt-0.5">Action: <?php echo e($rec->action_taken); ?></p><?php endif; ?>
        <?php if($rec->suspension_from): ?><p class="text-xs text-red-400 mt-0.5">Suspended: <?php echo e($rec->suspension_from->format('d M')); ?> – <?php echo e($rec->suspension_to?->format('d M Y')); ?></p><?php endif; ?>
        <?php if($rec->parent_notified): ?><span class="text-xs text-green-500">✓ Parent notified</span><?php endif; ?>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="text-slate-400 text-sm text-center py-6">No disciplinary records.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\disciplinary.blade.php ENDPATH**/ ?>