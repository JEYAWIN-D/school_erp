<?php $__env->startSection('title', 'Bus Attendance Register'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Bus Attendance Register</h1>
    <a href="<?php echo e(route('transport.bus-attendance.report')); ?>" class="btn-sm btn-secondary">Attendance Report</a>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  
  <form method="GET" class="card">
    <div class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Vehicle</label>
        <select name="vehicle_id" class="select w-44" required>
          <option value="">Select Vehicle</option>
          <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($v->id); ?>" <?php echo e($vehicleId == $v->id ? 'selected' : ''); ?>>
              <?php echo e($v->vehicle_number); ?> <?php echo e($v->name ? '— '.$v->name : ''); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Date</label>
        <input type="date" name="date" class="input w-36" value="<?php echo e($date); ?>">
      </div>
      <div>
        <label class="label">Trip</label>
        <select name="trip_type" class="select w-36">
          <option value="morning" <?php echo e($tripType=='morning'?'selected':''); ?>>Morning</option>
          <option value="afternoon" <?php echo e($tripType=='afternoon'?'selected':''); ?>>Afternoon</option>
          <option value="both" <?php echo e($tripType=='both'?'selected':''); ?>>Both</option>
        </select>
      </div>
      <button type="submit" class="btn-primary">Load Students</button>
    </div>
  </form>

  <?php if($vehicleId && $students->isNotEmpty()): ?>
  <form method="POST" action="<?php echo e(route('transport.bus-attendance.save')); ?>" class="card">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="vehicle_id" value="<?php echo e($vehicleId); ?>">
    <input type="hidden" name="date" value="<?php echo e($date); ?>">
    <input type="hidden" name="trip_type" value="<?php echo e($tripType); ?>">

    <div class="flex items-center justify-between mb-4">
      <h2 class="text-sm font-semibold text-slate-700">
        <?php echo e($students->count()); ?> students — <?php echo e(\Carbon\Carbon::parse($date)->format('d M Y')); ?>

        <span class="badge-blue ml-1"><?php echo e(ucfirst($tripType)); ?></span>
      </h2>
      <div class="flex gap-2">
        <button type="button" onclick="markAll('present')" class="btn-xs btn-secondary text-green-700">Mark All Present</button>
        <button type="button" onclick="markAll('absent')" class="btn-xs btn-secondary text-red-700">Mark All Absent</button>
      </div>
    </div>

    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">Student</th>
            <th class="th">Boarding Stop</th>
            <th class="th">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $stu = $row['student'];
            $markedStatus = $existing->has($stu->id) ? $existing[$stu->id] : null;
          ?>
          <tr class="tr <?php echo e(is_null($markedStatus) ? 'bg-amber-50/40' : ''); ?>">
            <td class="td text-slate-400 text-xs"><?php echo e($i + 1); ?></td>
            <td class="td">
              <div class="font-medium"><?php echo e($stu->full_name); ?></div>
              <div class="text-xs text-slate-400"><?php echo e($stu->admission_number); ?></div>
            </td>
            <td class="td text-slate-500 text-xs"><?php echo e($row['boarding_stop'] ?? '—'); ?></td>
            <td class="td">
              <div class="flex gap-2 items-center attendance-row">
                <?php if(is_null($markedStatus)): ?>
                  <span class="text-xs text-amber-600 font-medium mr-1">Not marked</span>
                <?php endif; ?>
                <label class="flex items-center gap-1 text-sm cursor-pointer">
                  <input type="radio" name="attendance[<?php echo e($stu->id); ?>]" value="present"
                    class="w-4 h-4 text-green-600" <?php echo e($markedStatus === 'present' ? 'checked' : ''); ?>>
                  <span class="text-green-700">Present</span>
                </label>
                <label class="flex items-center gap-1 text-sm cursor-pointer">
                  <input type="radio" name="attendance[<?php echo e($stu->id); ?>]" value="absent"
                    class="w-4 h-4 text-red-500" <?php echo e($markedStatus === 'absent' ? 'checked' : ''); ?>>
                  <span class="text-red-600">Absent</span>
                </label>
              </div>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>

    <div class="flex justify-end pt-4">
      <button type="submit" class="btn-primary">Save Attendance</button>
    </div>
  </form>
  <?php elseif($vehicleId): ?>
    <div class="card"><p class="text-slate-500 text-sm">No students allotted to this vehicle. Go to <a href="<?php echo e(route('transport.allotment')); ?>" class="text-indigo-600">Transport Allotment</a> to assign students.</p></div>
  <?php else: ?>
    <div class="card"><p class="text-slate-400 text-sm">Select a vehicle and date to load the attendance register.</p></div>
  <?php endif; ?>
</div>

<script>
function markAll(status) {
  document.querySelectorAll('.attendance-row input[value="' + status + '"]').forEach(r => r.checked = true);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\bus-attendance.blade.php ENDPATH**/ ?>