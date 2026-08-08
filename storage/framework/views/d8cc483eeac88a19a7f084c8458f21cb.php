
<?php $__env->startSection('title','Holiday Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Holiday Master</h1>
    <a href="<?php echo e(route('academics.calendar.pdf')); ?>" target="_blank" class="btn btn-secondary btn-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
      Academic Calendar PDF
    </a>
  </div>
  
  <?php if(count($academicYears) > 1): ?>
  <div class="card py-3 bg-blue-50 border border-blue-200">
    <form method="POST" action="<?php echo e(route('academics.holidays.copy')); ?>" class="flex flex-wrap items-center gap-3">
        <?php echo csrf_field(); ?>
        <span class="text-sm font-medium text-blue-700">Copy holidays from:</span>
        <select name="from_year_id" class="select text-sm w-40" required>
            <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(!$y->is_current): ?>
                <option value="<?php echo e($y->id); ?>"><?php echo e($y->name); ?></option>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input type="hidden" name="to_year_id" value="<?php echo e($currentYear?->id); ?>">
        <button type="submit" class="btn btn-sm bg-blue-600 text-white hover:bg-blue-700"
            onclick="return confirm('Copy holidays to <?php echo e($currentYear?->name); ?>?')">Copy</button>
    </form>
  </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form method="POST" action="<?php echo e(route('academics.holidays.add')); ?>" class="card space-y-4">
      <?php echo csrf_field(); ?>
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Add Holiday</h3>
      <div><label class="label">Holiday Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="input" required value="<?php echo e(old('name')); ?>">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Date <span class="text-red-500">*</span></label>
          <input type="date" name="date" class="input" required value="<?php echo e(old('date')); ?>">
        </div>
        <div><label class="label">Type</label>
          <select name="type" class="select">
            <?php $__currentLoopData = ['national'=>'National','state'=>'State','school'=>'School','optional'=>'Optional']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>"><?php echo e($v); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div><label class="label">Academic Year <span class="text-red-500">*</span></label>
        <select name="academic_year_id" class="select">
          <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($y->id); ?>" <?php if($y->is_current): echo 'selected'; endif; ?>><?php echo e($y->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Add Holiday</button>
    </form>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Holidays — <?php echo e($currentYear?->name); ?></h3>
      <?php $__empty_1 = true; $__currentLoopData = $holidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
        <div>
          <p class="text-sm font-medium text-slate-800"><?php echo e($h->name); ?></p>
          <p class="text-xs text-slate-400"><?php echo e(\Carbon\Carbon::parse($h->date)->format('D, d M Y')); ?></p>
        </div>
        <div class="flex items-center gap-2">
          <span class="badge-<?php echo e($h->type === 'national' ? 'red' : ($h->type === 'state' ? 'amber' : 'slate')); ?> capitalize text-xs"><?php echo e($h->type); ?></span>
          <form method="POST" action="<?php echo e(route('academics.holidays.delete', $h->id)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="text-red-400 hover:text-red-600 text-xs" onclick="return confirm('Delete?')">Delete</button>
          </form>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="text-slate-400 text-sm text-center py-6">No holidays configured.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\holidays.blade.php ENDPATH**/ ?>