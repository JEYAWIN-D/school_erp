<?php $__env->startSection('title','Attendance Shortage'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Attendance Shortage (Below 75%)</h1>
  <form method="GET" class="card py-4"><div class="flex gap-3">
    <select name="class_id" class="select w-36">
      <option value="">Select Class</option>
      <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($cls->id); ?>" <?php if(request('class_id')==$cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Check</button>
  </div></form>
  <?php if($students->count()): ?>
  <div class="table-wrap"><table class="w-full"><thead><tr><th class="th">Student</th><th class="th">Present Days</th><th class="th">%</th></tr></thead><tbody>
    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr class="tr"><td class="td"><?php echo e($r->student?->full_name); ?></td><td class="td"><?php echo e($r->present_days); ?></td><td class="td text-red-600 font-semibold"><?php echo e(number_format($r->present_days / max(1,1) * 100,1)); ?>%</td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </tbody></table></div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\shortage.blade.php ENDPATH**/ ?>