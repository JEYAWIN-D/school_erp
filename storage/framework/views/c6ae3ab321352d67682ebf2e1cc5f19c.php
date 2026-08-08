<?php $__env->startSection('title', 'Room Allotment'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Room Allotment</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form method="POST" action="<?php echo e(route('hostel.allotment.save')); ?>" class="card space-y-4">
      <?php echo csrf_field(); ?>
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">New Allotment</h3>
      <div><label class="label">Student <span class="text-red-500">*</span></label>
        <select name="student_id" class="select">
          <option value="">Select student</option>
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(old('student_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->full_name); ?> (<?php echo e($s->admission_number); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div><label class="label">Room <span class="text-red-500">*</span></label>
        <select name="room_id" class="select">
          <option value="">Select room</option>
          <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r->id); ?>" <?php if(old('room_id')==$r->id): echo 'selected'; endif; ?>><?php echo e($r->hostel?->name); ?> — Room <?php echo e($r->room_number); ?> (<?php echo e($r->capacity - $r->occupied); ?> spots left)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Allotment Date</label><input type="date" name="allotment_date" value="<?php echo e(old('allotment_date', today()->toDateString())); ?>" class="input"></div>
        <div><label class="label">Monthly Fee</label><input type="number" name="monthly_fee" value="<?php echo e(old('monthly_fee', 0)); ?>" class="input" min="0" step="0.01"></div>
      </div>
      <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 space-y-2" x-data="{ messIncluded: true }">
        <div class="flex items-center gap-2">
          <input type="checkbox" name="mess_included" value="1" id="mess_included" x-model="messIncluded"
                 <?php if(old('mess_included', true)): echo 'checked'; endif; ?> class="w-4 h-4 text-amber-600 rounded">
          <label for="mess_included" class="text-sm font-medium text-slate-700">Include Mess Fee for this Student</label>
        </div>
        <div x-show="!messIncluded" x-transition>
          <label class="label text-xs">Reason for Mess Exclusion</label>
          <input type="text" name="mess_exclusion_reason" class="input text-sm" placeholder="e.g. Day scholar meals, personal diet, medical reason">
        </div>
      </div>
      <div class="flex justify-end"><button type="submit" class="btn btn-primary">Allot Room</button></div>
    </form>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Current Allotments</h3>
      <?php $__empty_1 = true; $__currentLoopData = $allotments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0 text-sm">
          <div>
            <p class="font-medium text-slate-800"><?php echo e($a->student?->full_name); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($a->room?->hostel?->name); ?> — Room <?php echo e($a->room?->room_number); ?></p>
          </div>
          <div class="flex items-center gap-2">
            <span class="badge-green text-xs">Active</span>
            <a href="<?php echo e(route('hostel.vacate', ['allotment_id' => $a->id])); ?>" class="text-xs text-red-500 hover:underline">Vacate</a>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-slate-400 text-sm text-center py-6">No allotments yet.</p>
      <?php endif; ?>
      <?php if($allotments->hasPages()): ?>
        <div class="pt-3"><?php echo e($allotments->links()); ?></div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\allotment.blade.php ENDPATH**/ ?>