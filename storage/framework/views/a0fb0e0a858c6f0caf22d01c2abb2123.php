<?php $__env->startSection('title', 'Mess Attendance'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Mess Attendance</h1>
      <p class="page-subtitle">Mark daily meal attendance for hostel students</p>
    </div>
    <div class="flex gap-2">
      <a href="<?php echo e(route('hostel.mess-attendance-summary')); ?>" class="btn btn-secondary btn-sm">Monthly Summary</a>
      <a href="<?php echo e(route('hostel.index')); ?>" class="btn btn-secondary btn-sm">← Hostel</a>
    </div>
  </div>

  
  <div class="card-flat py-4">
    <form method="GET" class="flex gap-3 flex-wrap items-end">
      <div>
        <label class="label">Date</label>
        <input type="date" name="date" value="<?php echo e($date->toDateString()); ?>" class="input" onchange="this.form.submit()">
      </div>
      <div>
        <label class="label">Meal</label>
        <select name="meal" class="select" onchange="this.form.submit()">
          <?php $__currentLoopData = ['breakfast'=>'Breakfast','lunch'=>'Lunch','snacks'=>'Evening Snacks','dinner'=>'Dinner']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($v); ?>" <?php if($meal===$v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <?php if($hostels->count() > 1): ?>
      <div>
        <label class="label">Hostel</label>
        <select name="hostel_id" class="select" onchange="this.form.submit()">
          <option value="">All Hostels</option>
          <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($h->id); ?>" <?php if(request('hostel_id')==$h->id): echo 'selected'; endif; ?>><?php echo e($h->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <?php endif; ?>
    </form>
  </div>

  <?php if($allotments->isEmpty()): ?>
    <div class="card text-center py-12 text-slate-400">No active hostel allotments found.</div>
  <?php else: ?>
  <form method="POST" action="<?php echo e(route('hostel.mess-attendance.save')); ?>">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="date" value="<?php echo e($date->toDateString()); ?>">
    <input type="hidden" name="meal" value="<?php echo e($meal); ?>">

    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-700">
          <?php echo e(ucfirst($meal)); ?> — <?php echo e($date->format('l, d M Y')); ?>

          <span class="badge-slate ml-2"><?php echo e($allotments->count()); ?> students</span>
        </h3>
        <div class="flex gap-2">
          <button type="button" onclick="toggleAll(true)" class="btn-xs btn-secondary">Mark All Present</button>
          <button type="button" onclick="toggleAll(false)" class="btn-xs bg-red-50 text-red-600 hover:bg-red-100">Mark All Absent</button>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <?php $__currentLoopData = $allotments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allotment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $isPresent = $existingAttendance->get($allotment->id, true); ?>
        <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer hover:bg-slate-50 transition
                      <?php echo e($isPresent ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'); ?>"
               x-data="{ checked: <?php echo e($isPresent ? 'true' : 'false'); ?> }"
               :class="checked ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'">
          <input type="checkbox" name="present_ids[]" value="<?php echo e($allotment->id); ?>"
                 <?php if($isPresent): echo 'checked'; endif; ?> x-model="checked" class="w-4 h-4 text-green-600">
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-slate-800 truncate"><?php echo e($allotment->student?->full_name); ?></p>
            <p class="text-xs text-slate-400">Room <?php echo e($allotment->room?->room_number ?? '—'); ?></p>
          </div>
          <span x-show="checked" class="text-xs text-green-700 font-medium">Present</span>
          <span x-show="!checked" class="text-xs text-red-500 font-medium">Absent</span>
        </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      <div class="flex justify-end mt-4 pt-4 border-t border-slate-100">
        <button type="submit" class="btn btn-primary">Save Attendance</button>
      </div>
    </div>
  </form>
  <?php endif; ?>

</div>

<script>
function toggleAll(val) {
  document.querySelectorAll('input[name="present_ids[]"]').forEach(cb => {
    cb.checked = val;
    cb.dispatchEvent(new Event('change'));
  });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\mess-attendance.blade.php ENDPATH**/ ?>