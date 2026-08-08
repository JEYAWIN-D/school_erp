<?php $__env->startSection('title','Academic Year Archive'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Academic Year Archive</h1>
    <span class="badge-amber text-xs">Read-Only — Historical Data</span>
  </div>

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end flex-wrap">
      <div>
        <label class="label text-xs">Academic Year</label>
        <select name="year_id" class="select w-48">
          <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($yr->id); ?>" <?php if($selectedYear && $selectedYear->id === $yr->id): echo 'selected'; endif; ?>>
            <?php echo e($yr->name); ?><?php echo e($yr->is_current ? ' (Current)' : ''); ?>

          </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">View Archive</button>
    </div>
  </form>

  <?php if($selectedYear): ?>
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-lg font-semibold text-slate-800"><?php echo e($selectedYear->name); ?></h2>
        <p class="text-sm text-slate-400">
          <?php echo e($selectedYear->start_date?->format('d M Y')); ?> — <?php echo e($selectedYear->end_date?->format('d M Y')); ?>

        </p>
      </div>
      <a href="<?php echo e(route('academics.year-archive.students', $selectedYear->id)); ?>" class="btn btn-secondary btn-sm">
        View All Students →
      </a>
    </div>

    <?php if($classSummary->count()): ?>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Class</th>
            <th class="th">Total Students</th>
            <th class="th">Promoted</th>
            <th class="th">Detained / Held</th>
            <th class="th">Promotion %</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $classSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td font-medium"><?php echo e($row['class']->name); ?></td>
            <td class="td text-center"><?php echo e($row['total']); ?></td>
            <td class="td text-center text-green-600 font-semibold"><?php echo e($row['promoted']); ?></td>
            <td class="td text-center <?php echo e($row['detained'] > 0 ? 'text-red-500' : 'text-slate-300'); ?>">
              <?php echo e($row['detained']); ?>

            </td>
            <td class="td text-center">
              <?php if($row['total'] > 0): ?>
                <?php $pct = round($row['promoted'] / $row['total'] * 100, 1); ?>
                <span class="<?php echo e($pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-amber-500' : 'text-red-500')); ?> font-semibold">
                  <?php echo e($pct); ?>%
                </span>
              <?php else: ?>
                <span class="text-slate-300">—</span>
              <?php endif; ?>
            </td>
            <td class="td">
              <a href="<?php echo e(route('academics.year-archive.students', [$selectedYear->id, 'class_id' => $row['class']->id])); ?>"
                class="btn btn-ghost btn-xs">View Students</a>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="text-center py-8 text-slate-400">No student enrollment data found for this year.</div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\year-archive.blade.php ENDPATH**/ ?>