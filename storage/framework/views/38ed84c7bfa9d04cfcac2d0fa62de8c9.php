<?php $__env->startSection('title', 'Cumulative Marks'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Cumulative Marks — Term-wise</h1>
  <p class="text-slate-500 text-sm">Combines marks from all term exams using configured weightages to compute a final cumulative score.</p>

  <?php if($termExams->isEmpty()): ?>
  <div class="alert-amber">
    No exams are marked as "cumulative components" for the current year.
    To enable: when creating/editing an exam, check "Include in Cumulative" and set a weightage %.
  </div>
  <?php endif; ?>

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label">Class *</label>
        <select name="class_id" required class="select w-40">
          <option value="">Select Class</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Calculate</button>
    </div>
  </form>

  <?php if($termExams->count()): ?>
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex flex-wrap gap-4">
      <?php $__currentLoopData = $termExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="text-xs">
        <span class="font-medium text-slate-700"><?php echo e($e->name); ?></span>
        <span class="text-slate-400 ml-1">(<?php echo e($e->weightage_percent); ?>% weight)</span>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php if($result->count()): ?>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <th class="th">Rank</th>
        <th class="th">Student</th>
        <?php $__currentLoopData = $termExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="th text-center"><?php echo e($e->term_label ?: $e->name); ?><div class="text-xs text-slate-400 font-normal"><?php echo e($e->weightage_percent); ?>%</div></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <th class="th text-center">Cumulative %</th>
        <th class="th">Grade</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $cum = $row['cumulative'];
          $grade = match(true) {
            $cum >= 91 => 'A1', $cum >= 81 => 'A2', $cum >= 71 => 'B1',
            $cum >= 61 => 'B2', $cum >= 51 => 'C1', $cum >= 41 => 'C2',
            $cum >= 33 => 'D', default => 'E'
          };
        ?>
        <tr class="hover:bg-slate-50">
          <td class="td text-center font-bold text-slate-400"><?php echo e($i + 1); ?></td>
          <td class="td font-medium text-slate-800"><?php echo e($row['student']?->full_name); ?>

            <div class="text-xs text-slate-400"><?php echo e($row['student']?->admission_number); ?></div>
          </td>
          <?php $__currentLoopData = $termExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $ts = $row['term_scores'][$e->id] ?? ['pct'=>0,'weighted'=>0]; ?>
          <td class="td text-center">
            <div class="font-medium text-slate-700"><?php echo e($ts['pct']); ?>%</div>
            <div class="text-xs text-slate-400">+<?php echo e($ts['weighted']); ?></div>
          </td>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <td class="td text-center">
            <span class="font-bold text-lg <?php echo e($cum >= 60 ? 'text-green-600' : ($cum >= 33 ? 'text-amber-600' : 'text-red-500')); ?>"><?php echo e($cum); ?>%</span>
          </td>
          <td class="td"><span class="badge-<?php echo e($cum >= 60 ? 'green' : ($cum >= 33 ? 'amber' : 'red')); ?>"><?php echo e($grade); ?></span></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
    <?php elseif(request('class_id')): ?>
    <div class="px-4 py-8 text-center text-slate-400">No marks data found for this class.</div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\cumulative-marks.blade.php ENDPATH**/ ?>