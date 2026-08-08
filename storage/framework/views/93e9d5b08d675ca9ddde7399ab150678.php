<?php $__env->startSection('title', 'Staff ID Cards'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Staff ID Cards</h1>
    <a href="<?php echo e(route('hr.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  
  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Department</label>
        <select name="department_id" class="select text-sm">
          <option value="">All Departments</option>
          <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($dept->id); ?>" <?php if(request('department_id') == $dept->id): echo 'selected'; endif; ?>><?php echo e($dept->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <a href="<?php echo e(route('hr.staff-id-cards.pdf', request()->all())); ?>" target="_blank"
         class="btn btn-primary btn-sm">Print / Download PDF</a>
    </form>
  </div>

  
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <a href="<?php echo e(route('hr.employees.id-card', $emp->id)); ?>" class="group card border border-indigo-100 p-0 overflow-hidden hover:shadow-lg hover:border-indigo-300 transition-all duration-200 block">
      <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-3 py-2 text-xs font-bold flex items-center justify-between">
        <span>STAFF ID CARD</span>
        <span class="text-[10px] font-mono opacity-80"><?php echo e($emp->employee_code); ?></span>
      </div>
      <div class="p-3.5 flex items-center gap-3">
        <?php if($emp->photo): ?>
          <img src="<?php echo e(asset('storage/' . $emp->photo)); ?>" class="w-14 h-14 rounded-xl object-cover border border-slate-200 shrink-0 shadow-sm" alt="">
        <?php else: ?>
          <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-base shrink-0 shadow-sm">
            <?php echo e(strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1))); ?>

          </div>
        <?php endif; ?>
        <div class="min-w-0 flex-1">
          <p class="font-bold text-sm text-slate-800 truncate group-hover:text-indigo-600 transition-colors"><?php echo e($emp->full_name); ?></p>
          <p class="text-xs text-indigo-600 font-semibold"><?php echo e($emp->designation ?? 'Staff Member'); ?></p>
          <p class="text-xs text-slate-500 truncate"><?php echo e($emp->department ?? $emp->assigned_block ?? 'General'); ?></p>
          <span class="inline-block mt-1.5 text-[10px] font-bold text-indigo-600 hover:underline">Customize & Print Card &rarr;</span>
        </div>
      </div>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-span-full text-center text-slate-400 py-12">No active employees found.</div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\staff-id-cards.blade.php ENDPATH**/ ?>