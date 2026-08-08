<?php $__env->startSection('title', 'Hall Ticket Blocks'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Hall Ticket Blocks</h1>
  <p class="text-slate-500 text-sm">Check which students should be blocked from receiving hall tickets due to fee dues or attendance shortage.</p>

  <form method="GET" class="card">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="lg:col-span-2">
        <label class="label">Select Exam *</label>
        <select name="exam_id" required class="select w-full">
          <option value="">— Select Exam —</option>
          <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($exam->id); ?>" <?php if(request('exam_id')==$exam->id): echo 'selected'; endif; ?>>
              <?php echo e($exam->name); ?> (<?php echo e($exam->academicYear?->name); ?>)
            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Block Criteria</label>
        <div class="space-y-2 mt-1">
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="block_fee_defaulters" value="1" <?php if(request('block_fee_defaulters', '1')=='1'): echo 'checked'; endif; ?>> Fee Defaulters
          </label>
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="block_attendance_shortage" value="1" <?php if(request('block_attendance_shortage', '1')=='1'): echo 'checked'; endif; ?>> Attendance Shortage
          </label>
        </div>
      </div>
      <div class="flex items-end">
        <button type="submit" class="btn btn-primary btn-sm w-full">Check Blocks</button>
      </div>
    </div>
  </form>

  <?php if(request('exam_id')): ?>
    <?php if($blocked->count()): ?>
    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-slate-800">Blocked Students <span class="badge-red ml-2"><?php echo e($blocked->count()); ?></span></h2>
        <p class="text-xs text-slate-500">These students will NOT receive hall tickets unless admin overrides.</p>
      </div>
      <div class="table-wrap">
        <table class="min-w-full text-sm">
          <thead><tr>
            <th class="th">Student</th>
            <th class="th">Adm. No.</th>
            <th class="th">Class</th>
            <th class="th">Section</th>
            <th class="th">Block Reason(s)</th>
          </tr></thead>
          <tbody>
            <?php $__currentLoopData = $blocked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="tr">
              <td class="td font-medium"><?php echo e($e->student?->full_name); ?></td>
              <td class="td font-mono text-xs"><?php echo e($e->student?->admission_number); ?></td>
              <td class="td"><?php echo e($e->class?->name); ?></td>
              <td class="td"><?php echo e($e->section?->name); ?></td>
              <td class="td">
                <?php $__currentLoopData = $e->block_reasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <span class="badge-red text-xs mr-1"><?php echo e($reason); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php else: ?>
    <div class="card text-center py-10">
      <div class="text-4xl mb-2">✓</div>
      <p class="text-slate-600 font-medium">No students are blocked for this exam.</p>
      <p class="text-slate-400 text-sm mt-1">All students meet the fee and attendance criteria.</p>
    </div>
    <?php endif; ?>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\hall-ticket-blocks.blade.php ENDPATH**/ ?>