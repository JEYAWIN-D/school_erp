<?php $__env->startSection('title','Syllabus Coverage Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Syllabus Coverage Report</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Class <span class="text-red-500">*</span></label>
        <select name="class_id" class="select w-36" required>
          <option value="">Select Class</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Subject (all if blank)</label>
        <select name="subject_id" class="select w-40">
          <option value="">All Subjects</option>
          <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('subject_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  <?php if($coverage->count()): ?>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">Subject</th>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">Total Topics</th>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">Completed</th>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">Pending</th>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">Coverage %</th>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">Progress</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__currentLoopData = $coverage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($row['subject']?->name); ?></td>
          <td class="px-4 py-3 text-slate-600"><?php echo e($row['total']); ?></td>
          <td class="px-4 py-3 text-green-600 font-semibold"><?php echo e($row['completed']); ?></td>
          <td class="px-4 py-3 text-amber-600"><?php echo e($row['pending']); ?></td>
          <td class="px-4 py-3">
            <span class="font-semibold <?php echo e($row['percentage'] >= 75 ? 'text-green-600' : ($row['percentage'] >= 50 ? 'text-amber-600' : 'text-red-600')); ?>">
              <?php echo e($row['percentage']); ?>%
            </span>
          </td>
          <td class="px-4 py-3 w-40">
            <div class="w-full bg-slate-200 rounded h-2">
              <div class="h-2 rounded <?php echo e($row['percentage'] >= 75 ? 'bg-green-500' : ($row['percentage'] >= 50 ? 'bg-amber-400' : 'bg-red-500')); ?>"
                style="width:<?php echo e($row['percentage']); ?>%"></div>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
  <?php elseif(request('class_id')): ?>
  <div class="card text-center py-8 text-slate-400">No syllabus configured for this class.</div>
  <?php else: ?>
  <div class="card text-center py-8 text-slate-400">Select a class to view coverage.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\syllabus-coverage.blade.php ENDPATH**/ ?>