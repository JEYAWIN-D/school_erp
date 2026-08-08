<?php $__env->startSection('title', 'Attendance Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Attendance Report</h1>
      <p class="text-sm text-slate-500 mt-0.5"><?php echo e($year?->name ?? 'All Years'); ?></p>
    </div>
    <div class="flex gap-2">
      <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline">← Back</a>
      <a href="<?php echo e(route('reports.attendance.excel', request()->query())); ?>" class="btn btn-secondary btn-sm">Export Excel</a>
    </div>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">Month</label>
        <input type="month" name="month" value="<?php echo e($month); ?>" class="input">
      </div>
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($c->id); ?>" <?php if(request('class_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button class="btn btn-primary">Filter</button>
      <?php if(request()->hasAny(['class_id'])): ?>
      <a href="<?php echo e(route('reports.attendance', ['month' => $month])); ?>" class="btn btn-secondary btn-sm">Reset</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4">Class-wise Attendance — <?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y')); ?></h2>
    <?php if(count($classwise)): ?>
    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr><th>Class</th><th class="text-right">Present</th><th class="text-right">Absent</th><th class="text-right">Total</th><th class="text-right">%</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $classwise; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="font-medium"><?php echo e($row['class']); ?></td>
            <td class="text-right text-emerald-700"><?php echo e($row['present']); ?></td>
            <td class="text-right text-rose-600"><?php echo e($row['absent']); ?></td>
            <td class="text-right"><?php echo e($row['total']); ?></td>
            <td class="text-right font-semibold <?php echo e($row['pct'] >= 75 ? 'text-emerald-700' : 'text-rose-600'); ?>"><?php echo e($row['pct']); ?>%</td>
            <td><span class="badge <?php echo e($row['pct'] >= 75 ? 'badge-success' : 'badge-danger'); ?>"><?php echo e($row['pct'] >= 75 ? 'Good' : 'Low'); ?></span></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php
            $totalPresent = collect($classwise)->sum('present');
            $totalAbsent  = collect($classwise)->sum('absent');
            $totalAll     = collect($classwise)->sum('total');
            $overallPct   = $totalAll > 0 ? round($totalPresent / $totalAll * 100, 1) : 0;
          ?>
          <tr class="font-bold bg-slate-50 border-t-2 border-slate-200">
            <td>School Total</td>
            <td class="text-right text-emerald-700"><?php echo e($totalPresent); ?></td>
            <td class="text-right text-rose-600"><?php echo e($totalAbsent); ?></td>
            <td class="text-right"><?php echo e($totalAll); ?></td>
            <td class="text-right <?php echo e($overallPct >= 75 ? 'text-emerald-700' : 'text-rose-600'); ?>"><?php echo e($overallPct); ?>%</td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </div>
    <?php else: ?>
      <p class="text-slate-400 text-center py-8">No attendance data for this month.</p>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\reports\attendance.blade.php ENDPATH**/ ?>