<?php $__env->startSection('title', 'Comparative Analysis'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Comparative Analysis</h1>
    <div class="flex gap-2 items-center">
      <?php if(request('exam_id')): ?>
      <a href="<?php echo e(route('examinations.comparative-analysis.pdf', request()->only('exam_id'))); ?>"
         target="_blank" class="btn btn-secondary btn-sm">Export PDF</a>
      <?php endif; ?>
      <a href="<?php echo e(route('examinations.index')); ?>" class="btn btn-secondary btn-sm">Back to Exams</a>
    </div>
  </div>

  
  <div class="card">
    <form method="GET" class="flex gap-3 items-end flex-wrap">
      <div class="flex-1 min-w-[200px]">
        <label class="label text-xs">Select Exam <span class="text-red-500">*</span></label>
        <select name="exam_id" class="select text-sm" onchange="this.form.submit()">
          <option value="">-- Choose Exam --</option>
          <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($e->id); ?>" <?php if(request('exam_id') == $e->id): echo 'selected'; endif; ?>><?php echo e($e->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </form>
  </div>

  <?php if($exam): ?>
  
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="card text-center">
      <p class="text-xs text-slate-500 uppercase tracking-wide">Exam</p>
      <p class="font-bold text-slate-800 text-lg mt-1"><?php echo e($exam->name); ?></p>
    </div>
    <div class="card text-center">
      <p class="text-xs text-slate-500 uppercase tracking-wide">Classes Analysed</p>
      <p class="font-bold text-slate-800 text-3xl mt-1"><?php echo e($classData->count()); ?></p>
    </div>
    <div class="card text-center">
      <p class="text-xs text-slate-500 uppercase tracking-wide">School Overall Average</p>
      <p class="font-bold text-<?php echo e($schoolAvg >= 60 ? 'green' : ($schoolAvg >= 40 ? 'amber' : 'red')); ?>-600 text-3xl mt-1">
        <?php echo e($schoolAvg ?? '—'); ?>%
      </p>
    </div>
  </div>

  <?php if($classData->isEmpty()): ?>
    <div class="card text-center py-12 text-slate-400">No marks data found for this exam.</div>
  <?php else: ?>

  
  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4">Class vs School Average</h2>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th text-left">Class</th>
            <th class="th text-center">Class Average</th>
            <th class="th text-center">School Average</th>
            <th class="th text-center">Difference</th>
            <th class="th text-center">Performance Bar</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $classData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $diff     = round($row['overall_avg'] - ($schoolAvg ?? 0), 1);
            $aboveAvg = $diff >= 0;
            $barPct   = min(100, (int) $row['overall_avg']);
          ?>
          <tr class="tr">
            <td class="td font-medium text-slate-800"><?php echo e($row['class']->name); ?></td>
            <td class="td text-center font-semibold text-slate-700"><?php echo e($row['overall_avg']); ?>%</td>
            <td class="td text-center text-slate-500"><?php echo e($schoolAvg ?? '—'); ?>%</td>
            <td class="td text-center">
              <span class="inline-flex items-center gap-1 font-medium <?php echo e($aboveAvg ? 'text-green-600' : 'text-red-500'); ?>">
                <?php if($aboveAvg): ?>
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                  +<?php echo e($diff); ?>%
                <?php else: ?>
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                  <?php echo e($diff); ?>%
                <?php endif; ?>
              </span>
            </td>
            <td class="td">
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-100 rounded-full h-2">
                  <div class="h-2 rounded-full <?php echo e($aboveAvg ? 'bg-green-500' : 'bg-amber-400'); ?>"
                       style="width: <?php echo e($barPct); ?>%"></div>
                </div>
                <span class="text-xs text-slate-500 w-8 text-right"><?php echo e($barPct); ?>%</span>
              </div>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>

  
  <?php if(!empty($subjectSchoolAvg)): ?>
  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4">Subject-wise: Class Average vs School Average</h2>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th text-left">Class</th>
            <?php $__currentLoopData = array_keys($subjectSchoolAvg); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <th class="th text-center" colspan="2"><?php echo e($subj); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr>
          <tr>
            <th class="th"></th>
            <?php $__currentLoopData = array_keys($subjectSchoolAvg); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <th class="th text-center text-xs text-slate-500 font-normal">Class</th>
              <th class="th text-center text-xs text-slate-500 font-normal">School</th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $classData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td font-medium text-slate-800"><?php echo e($row['class']->name); ?></td>
            <?php $__currentLoopData = array_keys($subjectSchoolAvg); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $classSubjAvg  = $row['subject_avgs'][$subj] ?? null;
              $schoolSubjAvg = $subjectSchoolAvg[$subj];
              $above         = $classSubjAvg !== null && $classSubjAvg >= $schoolSubjAvg;
            ?>
            <td class="td text-center <?php echo e($classSubjAvg !== null ? ($above ? 'text-green-600 font-semibold' : 'text-red-500 font-semibold') : 'text-slate-300'); ?>">
              <?php echo e($classSubjAvg ?? '—'); ?>

            </td>
            <td class="td text-center text-slate-400 text-xs"><?php echo e($schoolSubjAvg); ?></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          
          <tr class="bg-slate-50 border-t-2 border-slate-200">
            <td class="td font-bold text-slate-700">School Average</td>
            <?php $__currentLoopData = $subjectSchoolAvg; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $avg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td class="td text-center font-bold text-blue-600"><?php echo e($avg); ?></td>
            <td class="td"></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr>
        </tbody>
      </table>
    </div>
    <p class="text-xs text-slate-400 mt-2">
      <span class="text-green-600 font-medium">Green</span> = above school average &nbsp;|&nbsp;
      <span class="text-red-500 font-medium">Red</span> = below school average
    </p>
  </div>
  <?php endif; ?>

  <?php endif; ?> 
  <?php endif; ?> 
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\comparative-analysis.blade.php ENDPATH**/ ?>