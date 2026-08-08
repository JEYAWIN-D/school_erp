<?php $__env->startSection('title', 'Timetable Conflict Detection'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Timetable Conflict Detection</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('academics.timetable')); ?>" class="btn btn-secondary btn-sm">Back to Timetable</a>
      <button onclick="window.location.reload()" class="btn btn-primary btn-sm">Re-scan</button>
    </div>
  </div>

  <?php if($totalConflicts === 0): ?>
    <div class="card bg-green-50 border border-green-200">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </div>
        <div>
          <p class="font-semibold text-green-800">No conflicts detected!</p>
          <p class="text-sm text-green-600 mt-0.5">The timetable has no teacher double-bookings or class scheduling conflicts.</p>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="alert-danger flex items-center gap-2">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <span><strong><?php echo e($totalConflicts); ?> conflict(s) detected.</strong> Please resolve them to avoid scheduling issues.</span>
    </div>

    <div class="card overflow-x-auto">
      <table class="table-wrap w-full text-sm">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">Conflict Type</th>
            <th class="th">Day</th>
            <th class="th">Period</th>
            <th class="th">Teacher</th>
            <th class="th">Class(es)</th>
            <th class="th">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $conflicts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $conflict): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr bg-red-50 border-l-4 border-l-red-400">
            <td class="td text-slate-400"><?php echo e($i + 1); ?></td>
            <td class="td">
              <span class="badge-red"><?php echo e($conflict['type']); ?></span>
            </td>
            <td class="td font-medium"><?php echo e(ucfirst($conflict['day'])); ?></td>
            <td class="td text-center"><?php echo e($conflict['period']); ?></td>
            <td class="td"><?php echo e($conflict['teacher']); ?></td>
            <td class="td"><?php echo e($conflict['classes']); ?></td>
            <td class="td">
              <a href="<?php echo e(route('academics.timetable')); ?>" class="btn-xs btn-secondary">Fix Timetable</a>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>

    <div class="card bg-amber-50 border border-amber-200 text-sm text-amber-800">
      <p class="font-semibold mb-1">How to fix conflicts:</p>
      <ol class="list-decimal list-inside space-y-1 text-amber-700">
        <li>Go to the Timetable page and select the affected class.</li>
        <li>Change the teacher assignment for the conflicting period to a different teacher.</li>
        <li>Return here to re-scan after making changes.</li>
      </ol>
    </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\timetable-conflicts.blade.php ENDPATH**/ ?>