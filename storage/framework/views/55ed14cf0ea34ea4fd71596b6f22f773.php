<?php $__env->startSection('title','Enter Marks — ' . $exam->name); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-4">
      <a href="<?php echo e(route('examinations.index')); ?>" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
      <div>
        <h1 class="page-title"><?php echo e($exam->name); ?> — Enter Marks</h1>
        <?php if($exam->marks_locked): ?>
        <p class="text-sm text-red-600 font-medium mt-0.5">Locked on <?php echo e($exam->marks_locked_at?->format('d M Y, h:i A')); ?></p>
        <?php endif; ?>
      </div>
    </div>
    <div class="flex gap-2 flex-wrap">
      <?php if($exam->marks_locked): ?>
      <form method="POST" action="<?php echo e(route('examinations.marks.unlock', $exam->id)); ?>">
        <?php echo csrf_field(); ?> <button class="btn btn-secondary btn-sm">Unlock Marks</button>
      </form>
      <?php else: ?>
      <form method="POST" action="<?php echo e(route('examinations.marks.lock', $exam->id)); ?>">
        <?php echo csrf_field(); ?> <button class="btn btn-primary btn-sm" onclick="return confirm('Lock marks?')">Lock Marks</button>
      </form>
      <?php endif; ?>
      <?php if(request('class_id')): ?>
      <a href="<?php echo e(route('examinations.recheck-requests')); ?>" class="btn btn-secondary btn-sm">Recheck Requests</a>
      <?php endif; ?>
    </div>
  </div>

  <?php if($exam->marks_locked): ?>
  <div class="alert-warning">Marks are locked. Unlock first to make changes.</div>
  <?php endif; ?>

  
  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label text-xs">Select Class</label>
        <select name="class_id" class="select w-40">
          <option value="">— Choose Class —</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Load Students</button>
    </div>
  </form>

  <?php if(request('class_id') && $schedules->count() && $enrollments->count()): ?>
  
  <?php if(!$exam->marks_locked): ?>
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-3 pb-2 border-b border-slate-100 text-sm">Grace Marks Configuration (per subject)</h3>
    <form method="POST" action="<?php echo e(route('examinations.grace-marks', $exam->id)); ?>" class="flex flex-wrap gap-4 items-end">
      <?php echo csrf_field(); ?>
      <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sched): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div>
        <label class="label text-xs"><?php echo e($sched->subject?->name); ?></label>
        <input type="number" name="grace[<?php echo e($sched->id); ?>]" class="input w-20 text-sm"
          value="<?php echo e($sched->grace_marks ?? 0); ?>" min="0" max="10" step="1">
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <button type="submit" class="btn btn-secondary btn-sm mt-4">Save Grace Marks</button>
    </form>
  </div>
  <?php endif; ?>

  
  <?php
    $totalStudents = $enrollments->count();
    $markedPerSched = [];
    foreach($schedules as $sched) {
      $markedPerSched[$sched->id] = collect($existingMarks)->filter(
        fn($m,$k) => str_starts_with((string)$k, $sched->id . '_') && ($m->marks_obtained !== null || $m->is_absent)
      )->count();
    }
  ?>
  <div class="card overflow-x-auto">
    <form method="POST" action="<?php echo e(route('examinations.marks.save', $exam->id)); ?>">
      <?php echo csrf_field(); ?>
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase sticky left-0 bg-slate-50">Roll</th>
            <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase sticky left-12 bg-slate-50 min-w-40">Student</th>
            <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sched): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $markedCount = $markedPerSched[$sched->id] ?? 0; $allDone = $markedCount >= $totalStudents; ?>
            <th class="text-center px-3 py-3 text-slate-500 font-medium text-xs uppercase min-w-28">
              <?php echo e($sched->subject?->name); ?>

              <div class="text-slate-400 font-normal">Max: <?php echo e($sched->max_marks); ?></div>
              <?php if($sched->grace_marks > 0): ?>
              <div class="text-green-600 text-xs font-normal">+<?php echo e($sched->grace_marks); ?> grace</div>
              <?php endif; ?>
              <div class="mt-1 text-xs font-normal <?php echo e($allDone ? 'text-green-600' : 'text-amber-600'); ?>">
                <?php echo e($markedCount); ?>/<?php echo e($totalStudents); ?> marked<?php echo e($allDone ? ' ✓' : ''); ?>

              </div>
            </th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-2 text-slate-500 sticky left-0 bg-white"><?php echo e($enrollment->roll_number); ?></td>
            <td class="px-4 py-2 font-medium text-slate-800 sticky left-12 bg-white min-w-40">
              <?php echo e($enrollment->student?->first_name); ?> <?php echo e($enrollment->student?->last_name); ?>

            </td>
            <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sched): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $key = $sched->id . '_' . $enrollment->student_id; $m = $existingMarks[$key] ?? null; ?>
            <td class="px-2 py-1 text-center" x-data="{absent:<?php echo e($m?->is_absent ? 'true' : 'false'); ?>}">
              <div class="flex flex-col items-center gap-1">
                <input type="number"
                  name="marks[<?php echo e($sched->id); ?>][<?php echo e($enrollment->student_id); ?>][marks]"
                  class="w-20 border border-slate-200 rounded px-2 py-1 text-center text-sm focus:ring-2 focus:ring-indigo-300"
                  value="<?php echo e($m?->marks_obtained); ?>"
                  min="0" max="<?php echo e($sched->max_marks); ?>" step="0.5"
                  :disabled="absent"
                  <?php echo e($exam->marks_locked ? 'disabled' : ''); ?>>
                <label class="flex items-center gap-1 text-xs text-slate-400 cursor-pointer">
                  <input type="checkbox"
                    name="marks[<?php echo e($sched->id); ?>][<?php echo e($enrollment->student_id); ?>][absent]"
                    value="1"
                    x-model="absent"
                    <?php echo e($m?->is_absent ? 'checked' : ''); ?>

                    <?php echo e($exam->marks_locked ? 'disabled' : ''); ?>>
                  Absent
                </label>
              </div>
            </td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
      <?php if(!$exam->marks_locked): ?>
      <div class="px-4 pb-4 pt-3 border-t border-slate-100">
        <button type="submit" class="btn btn-primary">Save Marks</button>
      </div>
      <?php endif; ?>
    </form>
  </div>
  <?php elseif(request('class_id') && !$schedules->count()): ?>
  <div class="card text-center py-8 text-slate-400">No exam schedules for this class.</div>
  <?php elseif(request('class_id') && !$enrollments->count()): ?>
  <div class="card text-center py-8 text-slate-400">No active enrollments for this class.</div>
  <?php elseif(!request('class_id')): ?>
  <div class="card text-center py-8 text-slate-400">Select a class to start entering marks.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\marks.blade.php ENDPATH**/ ?>