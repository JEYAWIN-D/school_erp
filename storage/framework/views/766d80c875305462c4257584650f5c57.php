<?php $__env->startSection('title','Staff Attendance'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Staff Attendance</h1>
    <a href="<?php echo e(route('attendance.staff.register')); ?>" class="btn btn-secondary btn-sm">Monthly Register</a>
  </div>
  <div class="card">
    <form method="POST" action="<?php echo e(route('attendance.staff.save')); ?>" class="space-y-4">
      <?php echo csrf_field(); ?>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div><label class="label">Department</label>
          <select name="department_id" class="select">
            <option value="">All Departments</option>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($d->id); ?>" <?php if(old('department_id')==$d->id): echo 'selected'; endif; ?>><?php echo e($d->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Date <span class="text-red-500">*</span></label>
          <input type="date" name="date" class="input" required value="<?php echo e(old('date',today()->toDateString())); ?>">
        </div>
        <div class="flex items-end">
          <button type="submit" name="action" value="load" class="btn btn-secondary btn-sm">Load Staff</button>
        </div>
      </div>
      <?php if(isset($employees) && $employees->count()): ?>
      <div class="overflow-x-auto border border-slate-100 rounded-lg">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 border-b"><tr>
            <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium">Employee</th>
            <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium">Dept</th>
            <th class="px-4 py-3 text-slate-500 text-xs uppercase font-medium text-center">Status</th>
            <th class="px-4 py-3 text-slate-500 text-xs uppercase font-medium">In Time</th>
            <th class="px-4 py-3 text-slate-500 text-xs uppercase font-medium">Out Time</th>
            <th class="px-4 py-3 text-slate-500 text-xs uppercase font-medium">Remarks</th>
          </tr></thead>
          <tbody class="divide-y divide-slate-100">
            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $existing = $attendances[$emp->id] ?? null; ?>
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-2.5">
                <p class="font-medium text-slate-800 text-sm"><?php echo e($emp->full_name); ?></p>
                <p class="text-xs text-slate-400"><?php echo e($emp->employee_id); ?></p>
                <input type="hidden" name="attendance[<?php echo e($emp->id); ?>][employee_id]" value="<?php echo e($emp->id); ?>">
              </td>
              <td class="px-4 py-2.5 text-xs text-slate-500"><?php echo e($emp->department?->name); ?></td>
              <td class="px-4 py-2.5">
                <select name="attendance[<?php echo e($emp->id); ?>][status]" class="text-xs border border-slate-200 rounded px-2 py-1 w-24 mx-auto block">
                  <?php $__currentLoopData = ['present'=>'Present','absent'=>'Absent','half_day'=>'Half Day','late'=>'Late','holiday'=>'Holiday','leave'=>'Leave']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($v); ?>" <?php if(($existing?->status??'present')===$v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </td>
              <td class="px-3 py-2.5"><input type="time" name="attendance[<?php echo e($emp->id); ?>][in_time]" value="<?php echo e($existing?->in_time); ?>" class="input text-xs w-28"></td>
              <td class="px-3 py-2.5"><input type="time" name="attendance[<?php echo e($emp->id); ?>][out_time]" value="<?php echo e($existing?->out_time); ?>" class="input text-xs w-28"></td>
              <td class="px-3 py-2.5"><input type="text" name="attendance[<?php echo e($emp->id); ?>][remarks]" value="<?php echo e($existing?->remarks); ?>" class="input text-xs" placeholder="Optional"></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
      <div class="flex gap-3">
        <button type="submit" name="action" value="save" class="btn btn-primary">Save Attendance</button>
        <button type="button" onclick="markAll('present')" class="btn btn-secondary btn-sm">Mark All Present</button>
      </div>
      <?php endif; ?>
    </form>
  </div>
</div>
<script>
function markAll(val){document.querySelectorAll('select[name*="[status]"]').forEach(s=>s.value=val);}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\staff-attendance.blade.php ENDPATH**/ ?>