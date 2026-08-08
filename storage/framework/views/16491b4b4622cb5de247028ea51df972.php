<?php $__env->startSection('title', 'Co-Scholastic Grading'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Co-Scholastic Grading</h1>
      <p class="text-slate-500 text-sm mt-0.5"><?php echo e($exam->name); ?></p>
    </div>
    <a href="<?php echo e(route('examinations.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <form method="GET" class="card-flat py-3">
    <input type="hidden" name="exam_id" value="<?php echo e($exam->id); ?>">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label">Class</label>
        <select name="class_id" required class="select w-40">
          <option value="">Select Class</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Load</button>
    </div>
  </form>

  <?php if($students->count() && $subjects->count()): ?>
  <form method="POST" action="<?php echo e(route('examinations.coscholastic.save', $exam->id)); ?>">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="class_id" value="<?php echo e(request('class_id')); ?>">
    <div class="card overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr>
            <th class="th sticky left-0 bg-white z-10">Student</th>
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <th class="th text-center"><?php echo e($s->subject?->name); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="hover:bg-slate-50">
            <td class="td sticky left-0 bg-white z-10 font-medium">
              <?php echo e($student->full_name); ?>

              <div class="text-xs text-slate-400"><?php echo e($student->admission_number); ?></div>
            </td>
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $existingGrade = $entries[$student->id][$schedule->id]['grade'] ?? ($entries[$student->id]?->where('exam_schedule_id', $schedule->id)->first()?->grade ?? '');
            ?>
            <td class="td text-center">
              <select name="grades[<?php echo e($student->id); ?>][<?php echo e($schedule->id); ?>]" class="select text-center w-20 py-1 text-sm">
                <option value="">—</option>
                <?php $__currentLoopData = ['A1','A2','B1','B2','C1','C2','D','E']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($g); ?>" <?php if($existingGrade===$g): echo 'selected'; endif; ?>><?php echo e($g); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <div class="flex justify-end mt-4">
      <button type="submit" class="btn btn-primary">Save Co-Scholastic Grades</button>
    </div>
  </form>
  <?php elseif(request('class_id')): ?>
    <div class="card text-center py-10 text-slate-400">
      <?php if(!$subjects->count()): ?>
        No co-scholastic subjects found for this class in this exam.
        <p class="text-xs mt-1">Mark subjects as "Co-Scholastic" in Subject Management to enable grade entry.</p>
      <?php else: ?>
        No students found for this class.
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\coscholastic-entry.blade.php ENDPATH**/ ?>