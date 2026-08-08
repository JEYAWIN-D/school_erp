<?php $__env->startSection('title','Period-wise Attendance'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Period-wise Attendance</h1>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="class_id" class="select w-36">
      <option value="">Select Class</option>
      <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="section_id" class="select w-36">
      <option value="">Section</option>
      <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('section_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <input type="date" name="date" value="<?php echo e(request('date', today()->toDateString())); ?>" class="input w-36">
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </div></form>
  <?php if(isset($students) && $students->count()): ?>
  <form method="POST" action="<?php echo e(route('attendance.period.save')); ?>" class="card overflow-hidden">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="class_id" value="<?php echo e(request('class_id')); ?>">
    <input type="hidden" name="section_id" value="<?php echo e(request('section_id')); ?>">
    <input type="hidden" name="date" value="<?php echo e(request('date')); ?>">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b"><tr>
          <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium">Student</th>
          <?php $__currentLoopData = range(1,8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><th class="px-3 py-3 text-slate-500 text-xs font-medium text-center">P<?php echo e($p); ?></th><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-2.5 font-medium text-slate-800"><?php echo e($s->student?->full_name); ?></td>
            <?php $__currentLoopData = range(1,8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td class="px-2 py-2 text-center">
              <select name="periods[<?php echo e($s->student_id); ?>][<?php echo e($p); ?>]" class="text-xs border border-slate-200 rounded px-1 py-0.5 w-16">
                <option value="present">P</option>
                <option value="absent">A</option>
                <option value="late">L</option>
              </select>
            </td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <div class="px-4 py-3 border-t border-slate-100">
      <button type="submit" class="btn btn-primary">Save Period Attendance</button>
    </div>
  </form>
  <?php else: ?>
  <div class="card text-center py-12 text-slate-400">Select class, section and date to load students.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\period-wise.blade.php ENDPATH**/ ?>