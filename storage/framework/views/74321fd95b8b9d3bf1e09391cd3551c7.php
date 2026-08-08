<?php $__env->startSection('title','Tabulation Register'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Tabulation Register</h1>
    <?php if(isset($enrollments) && $enrollments->count()): ?>
    <div class="flex gap-2">
      <a href="<?php echo e(route('examinations.tabulation.export', request()->query())); ?>" class="btn btn-secondary btn-sm">Export Excel</a>
      <a href="<?php echo e(route('examinations.tabulation.pdf', request()->query())); ?>" class="btn btn-secondary btn-sm">Tabulation PDF</a>
    </div>
    <?php endif; ?>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="exam_id" class="select w-44">
      <option value="">Select Exam</option>
      <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($e->id); ?>" <?php if(request('exam_id')==$e->id): echo 'selected'; endif; ?>><?php echo e($e->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="class_id" class="select w-36">
      <option value="">Select Class</option>
      <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="section_id" class="select w-36">
      <option value="">Section</option>
      <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('section_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </div></form>

  <?php if(isset($enrollments) && $enrollments->count()): ?>
  <div class="card overflow-x-auto">
    <table class="text-xs min-w-max">
      <thead class="bg-slate-50 border-b">
        <tr>
          <th class="sticky left-0 bg-slate-50 text-left px-4 py-3 text-slate-500 uppercase font-medium">Student</th>
          <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <th class="px-3 py-3 text-center text-slate-500 font-medium min-w-[80px]">
            <?php echo e($sub->name); ?><br><span class="text-slate-300 font-normal">/ <?php echo e($sub->pivot->max_marks); ?></span>
          </th>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <th class="px-3 py-3 text-center text-slate-500 font-medium">Total</th>
          <th class="px-3 py-3 text-center text-slate-500 font-medium">%</th>
          <th class="px-3 py-3 text-center text-slate-500 font-medium">Grade</th>
          <th class="px-3 py-3 text-center text-slate-500 font-medium">Result</th>
          <th class="px-3 py-3 text-center text-slate-500 font-medium">Rank</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $sm = $marksMap[$enr->id] ?? []; ?>
        <tr class="hover:bg-slate-50">
          <td class="sticky left-0 bg-white px-4 py-2 font-medium text-slate-800"><?php echo e($enr->student?->full_name); ?></td>
          <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $m = $sm[$sub->id] ?? null; ?>
          <td class="px-3 py-2 text-center">
            <?php if($m): ?>
              <span class="<?php echo e($m->marks_obtained < ($sub->pivot->pass_marks ?? 0) ? 'text-red-500 font-semibold' : 'text-slate-700'); ?>"><?php echo e($m->marks_obtained); ?></span>
              <?php if($m->is_absent): ?><span class="text-xs text-slate-400 ml-1">AB</span><?php endif; ?>
            <?php else: ?><span class="text-slate-300">—</span><?php endif; ?>
          </td>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php
            $total = collect($sm)->sum('marks_obtained');
            $maxTotal = $subjects->sum(fn($s)=>$s->pivot->max_marks??100);
            $pct = $maxTotal > 0 ? round($total/$maxTotal*100,1) : 0;
          ?>
          <td class="px-3 py-2 text-center font-semibold"><?php echo e($total); ?></td>
          <td class="px-3 py-2 text-center font-semibold <?php echo e($pct < 35 ? 'text-red-500' : ''); ?>"><?php echo e($pct); ?>%</td>
          <td class="px-3 py-2 text-center font-bold text-indigo-600"><?php echo e($gradingScheme?->gradeFor($pct) ?? '—'); ?></td>
          <td class="px-3 py-2 text-center">
            <?php $hasFail = collect($sm)->filter(fn($m)=>$m->marks_obtained < ($subjects->find($m->exam_schedule?->subject_id)?->pivot->pass_marks??35))->count(); ?>
            <span class="<?php echo e($hasFail ? 'text-red-500' : 'text-green-600'); ?> font-semibold"><?php echo e($hasFail ? 'FAIL' : 'PASS'); ?></span>
          </td>
          <td class="px-3 py-2 text-center text-slate-500"><?php echo e($ranks[$enr->id] ?? '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="card text-center py-12 text-slate-400">Select exam and class to load tabulation register.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\tabulation.blade.php ENDPATH**/ ?>