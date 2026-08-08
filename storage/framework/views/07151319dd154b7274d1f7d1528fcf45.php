<?php $__env->startSection('title','Concessions — '.$student->full_name); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <nav class="text-sm text-slate-400 flex items-center gap-1.5 mb-1">
    <a href="<?php echo e(route('students.index')); ?>" class="hover:text-slate-600">Students</a>
    <span>/</span>
    <a href="<?php echo e(route('students.show',$student->id)); ?>" class="hover:text-slate-600"><?php echo e($student->full_name); ?></a>
    <span>/</span>
    <span class="text-slate-600">Concessions</span>
  </nav>
  <div class="flex items-center gap-3">
    <a href="<?php echo e(route('students.show',$student->id)); ?>" class="text-slate-400 hover:text-slate-700">←</a>
    <h1 class="page-title">Concessions & Scholarships — <?php echo e($student->full_name); ?></h1>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form method="POST" action="<?php echo e(route('students.concessions.save',$student->id)); ?>" class="card space-y-4">
      <?php echo csrf_field(); ?>
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Grant Concession</h3>
      <div><label class="label">Concession Type</label>
        <select name="concession_type" class="select">
          <?php $__currentLoopData = ['scholarship'=>'Scholarship','sibling'=>'Sibling Discount','staff_ward'=>'Staff Ward','manual'=>'Manual']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($k); ?>"><?php echo e($v); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div><label class="label">Scholarship Scheme</label>
        <select name="scholarship_id" class="select">
          <option value="">None</option>
          <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Value Type</label>
          <select name="value_type" class="select">
            <option value="percentage">Percentage (%)</option>
            <option value="flat">Flat Amount (₹)</option>
          </select>
        </div>
        <div><label class="label">Value</label><input type="number" name="value" class="input" min="0" step="0.01"></div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Valid From</label><input type="date" name="valid_from" class="input" value="<?php echo e(today()->toDateString()); ?>"></div>
        <div><label class="label">Valid To</label><input type="date" name="valid_to" class="input"></div>
      </div>
      <div><label class="label">Remarks</label><textarea name="remarks" rows="2" class="input"></textarea></div>
      <button type="submit" class="btn btn-primary">Grant Concession</button>
    </form>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Active Concessions</h3>
      <?php $__empty_1 = true; $__currentLoopData = $concessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
        <div>
          <p class="text-sm font-medium text-slate-800"><?php echo e($c->scholarship?->name ?? ucfirst($c->concession_type)); ?></p>
          <p class="text-xs text-slate-400"><?php echo e($c->value_type === 'percentage' ? $c->value.'%' : '₹'.number_format($c->value,2)); ?> · Valid till <?php echo e($c->valid_to?->format('d M Y') ?? 'No expiry'); ?></p>
        </div>
        <span class="badge-green text-xs">Active</span>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="text-slate-400 text-sm text-center py-6">No concessions.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\concessions.blade.php ENDPATH**/ ?>