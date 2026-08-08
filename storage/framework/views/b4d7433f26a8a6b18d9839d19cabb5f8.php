<?php $__env->startSection('title','Results — ' . $exam->name); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('examinations.index')); ?>" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <div>
      <h1 class="page-title"><?php echo e($exam->name); ?> — Results</h1>
      <p class="text-sm text-slate-500"><?php echo e($exam->start_date?->format('d M')); ?> – <?php echo e($exam->end_date?->format('d M Y')); ?> &nbsp;·&nbsp; Passing: <?php echo e($exam->passing_percentage ?? 35); ?>%</p>
    </div>
  </div>

  
  <div class="card" x-data="{open:false}">
    <div class="flex items-center justify-between cursor-pointer" @click="open=!open">
      <h3 class="font-semibold text-slate-700">Exam Schedules (<?php echo e($exam->schedules->count()); ?>)</h3>
      <button class="btn btn-primary btn-sm">+ Add Schedule</button>
    </div>
    <?php if($exam->schedules->count()): ?>
    <div class="mt-3 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100"><tr>
          <?php $__currentLoopData = ['Date','Subject','Class','Time','Max Marks','Min Marks','Room','']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <th class="text-left px-3 py-2 text-xs text-slate-500 font-medium uppercase tracking-wide"><?php echo e($h); ?></th>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
          <?php $__currentLoopData = $exam->schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="hover:bg-slate-50">
            <td class="px-3 py-2"><?php echo e($sch->exam_date?->format('d M Y')); ?></td>
            <td class="px-3 py-2 font-medium text-slate-800"><?php echo e($sch->subject?->name); ?></td>
            <td class="px-3 py-2 text-slate-600"><?php echo e($sch->class?->name); ?></td>
            <td class="px-3 py-2 text-slate-400 text-xs"><?php echo e($sch->start_time ?? '—'); ?> – <?php echo e($sch->end_time ?? '—'); ?></td>
            <td class="px-3 py-2 text-center"><?php echo e($sch->max_marks ?? 100); ?></td>
            <td class="px-3 py-2 text-center"><?php echo e($sch->pass_marks ?? 35); ?></td>
            <td class="px-3 py-2 text-slate-400"><?php echo e($sch->venue ?? '—'); ?></td>
            <td class="px-3 py-2">
              <form method="POST" action="<?php echo e(route('examinations.schedules.destroy', $sch->id)); ?>" onsubmit="return confirm('Remove this schedule?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="text-red-400 hover:text-red-600 text-xs">Delete</button>
              </form>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
    
    <div class="mt-4 border-t border-slate-100 pt-4" x-show="open" style="display:none">
      <form method="POST" action="<?php echo e(route('examinations.schedules.store', $exam->id)); ?>" class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <?php echo csrf_field(); ?>
        <div><label class="label">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select" required>
            <option value="">Select</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Subject <span class="text-red-500">*</span></label>
          <select name="subject_id" class="select" required>
            <option value="">Select</option>
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Date <span class="text-red-500">*</span></label>
          <input type="date" name="exam_date" class="input" required value="<?php echo e($exam->start_date?->toDateString()); ?>">
        </div>
        <div><label class="label">Start Time</label><input type="time" name="start_time" class="input"></div>
        <div><label class="label">End Time</label><input type="time" name="end_time" class="input"></div>
        <div><label class="label">Max Marks</label><input type="number" name="max_marks" class="input" value="100" min="1"></div>
        <div><label class="label">Pass Marks</label><input type="number" name="min_marks" class="input" value="35" min="0"></div>
        <div><label class="label">Venue/Room</label><input type="text" name="room_no" class="input" placeholder="e.g. A101"></div>
        <div class="col-span-2 md:col-span-4 flex gap-2">
          <button type="submit" class="btn btn-primary btn-sm">Add Schedule</button>
          <button type="button" @click="open=false" class="btn btn-secondary btn-sm">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
  <?php if(session('error')): ?><div class="alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

  <?php
    // Build schedule index: class_id -> [subject_id -> schedule]
    $schedulesByClass = $exam->schedules->groupBy('class_id');
  ?>

  <?php $__empty_1 = true; $__currentLoopData = $schedulesByClass; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classId => $schedules): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
  <?php
    $className = $schedules->first()?->class?->name ?? 'Class';
    // Collect all unique students who have any mark in this exam + class
    $studentIds = $schedules->flatMap(fn($s) => $s->marks->pluck('student_id'))->unique();
    $students   = \App\Models\Student::whereIn('id', $studentIds)->orderBy('first_name')->get()->keyBy('id');
    $passing    = $exam->passing_percentage ?? 35;
  ?>
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-700"><?php echo e($className); ?></h3>
      <span class="text-xs text-slate-400"><?php echo e($students->count()); ?> students · <?php echo e($schedules->count()); ?> subjects</span>
    </div>
    <?php if($students->isEmpty()): ?>
      <div class="px-4 py-6 text-slate-400 text-sm text-center">No marks entered yet for this class.</div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="text-left px-3 py-2 text-xs text-slate-500 font-medium uppercase tracking-wide">Student</th>
            <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <th class="text-center px-3 py-2 text-xs text-slate-500 font-medium uppercase tracking-wide" title="<?php echo e($sch->subject?->name); ?>">
              <?php echo e(Str::limit($sch->subject?->name ?? '—', 10)); ?><br>
              <span class="font-normal normal-case text-slate-400">/<?php echo e($sch->max_marks ?? 100); ?></span>
            </th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <th class="text-center px-3 py-2 text-xs text-slate-500 font-medium uppercase tracking-wide">Total</th>
            <th class="text-center px-3 py-2 text-xs text-slate-500 font-medium uppercase tracking-wide">%</th>
            <th class="text-center px-3 py-2 text-xs text-slate-500 font-medium uppercase tracking-wide">Result</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $totalObtained = 0;
            $totalMax      = 0;
            $anyAbsent     = false;
          ?>
          <tr class="hover:bg-slate-50">
            <td class="px-3 py-2">
              <p class="font-medium text-slate-800"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></p>
              <p class="text-xs text-slate-400"><?php echo e($student->admission_no); ?></p>
            </td>
            <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $mark = $sch->marks->firstWhere('student_id', $student->id);
              $max  = $sch->max_marks ?? 100;
              if ($mark) {
                $totalObtained += $mark->obtained_marks ?? 0;
                $totalMax += $max;
                if ($mark->is_absent ?? false) $anyAbsent = true;
              } else {
                $totalMax += $max;
              }
            ?>
            <td class="px-3 py-2 text-center">
              <?php if(!$mark): ?>
                <span class="text-slate-300">—</span>
              <?php elseif($mark->is_absent ?? false): ?>
                <span class="badge-red text-xs">AB</span>
              <?php else: ?>
                <?php $subPct = $max > 0 ? ($mark->obtained_marks / $max * 100) : 0; ?>
                <span class="<?php echo e($subPct < $passing ? 'text-red-600 font-semibold' : 'text-slate-700'); ?>">
                  <?php echo e($mark->obtained_marks); ?>

                </span>
              <?php endif; ?>
            </td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php
              $pct = $totalMax > 0 ? round($totalObtained / $totalMax * 100, 1) : 0;
              $pass = !$anyAbsent && $pct >= $passing;
            ?>
            <td class="px-3 py-2 text-center font-semibold text-slate-700"><?php echo e($totalObtained); ?>/<?php echo e($totalMax); ?></td>
            <td class="px-3 py-2 text-center font-semibold <?php echo e($pct < $passing ? 'text-red-600' : 'text-emerald-600'); ?>"><?php echo e($pct); ?>%</td>
            <td class="px-3 py-2 text-center">
              <?php if($anyAbsent): ?>
                <span class="badge-amber text-xs">AB</span>
              <?php elseif($pass): ?>
                <span class="badge-green text-xs">PASS</span>
              <?php else: ?>
                <span class="badge-red text-xs">FAIL</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  <div class="card text-center py-12 text-slate-400">No exam schedules or marks entered for this exam yet.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\results.blade.php ENDPATH**/ ?>