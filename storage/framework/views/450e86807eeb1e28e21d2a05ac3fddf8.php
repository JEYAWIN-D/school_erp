<?php $__env->startSection('title', 'Late Arrival Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Late Arrival Tracking — Staff</h1>
    <a href="<?php echo e(route('attendance.teacher-report')); ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  
  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Month</label>
        <input type="month" name="month" value="<?php echo e($month); ?>" class="input text-sm">
      </div>
      <div>
        <label class="label text-xs">Department</label>
        <select name="department_id" class="select text-sm">
          <option value="">All Departments</option>
          <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($dept->id); ?>" <?php if(request('department_id') == $dept->id): echo 'selected'; endif; ?>><?php echo e($dept->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </form>
  </div>

  
  <div class="grid grid-cols-3 gap-4">
    <div class="card text-center">
      <p class="text-2xl font-bold text-amber-600"><?php echo e($byEmployee->count()); ?></p>
      <p class="text-xs text-slate-500 mt-1">Staff with Late Arrivals</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-red-600"><?php echo e($records->count()); ?></p>
      <p class="text-xs text-slate-500 mt-1">Total Late Incidents</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-slate-700"><?php echo e($lateThreshold); ?></p>
      <p class="text-xs text-slate-500 mt-1">Late Threshold (HH:MM)</p>
    </div>
  </div>

  
  <?php if($byEmployee->isEmpty()): ?>
    <div class="card text-center py-12 text-slate-400">No late arrivals recorded for this period.</div>
  <?php else: ?>
  <div class="card overflow-x-auto">
    <h2 class="font-semibold text-slate-700 mb-3">Employee-wise Summary</h2>
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Employee</th>
          <th class="th">Department</th>
          <th class="th">Late Count</th>
          <th class="th">Total Late Mins</th>
          <th class="th">Late Dates</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $byEmployee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empId => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="tr <?php echo e($data['late_count'] >= 5 ? 'bg-red-50' : ''); ?>">
          <td class="td text-slate-400"><?php echo e($loop->iteration); ?></td>
          <td class="td font-medium"><?php echo e($data['employee']->name); ?></td>
          <td class="td text-slate-500"><?php echo e($data['employee']->department?->name ?? '—'); ?></td>
          <td class="td text-center">
            <span class="badge-<?php echo e($data['late_count'] >= 5 ? 'red' : ($data['late_count'] >= 3 ? 'amber' : 'blue')); ?>">
              <?php echo e($data['late_count']); ?>

            </span>
          </td>
          <td class="td text-center"><?php echo e($data['total_mins']); ?> min</td>
          <td class="td">
            <div class="flex flex-wrap gap-1">
              <?php $__currentLoopData = $data['records']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="text-xs bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded" title="<?php echo e($rec->late_minutes); ?> min late">
                  <?php echo e($rec->date->format('d')); ?>

                  <?php if($rec->check_in): ?>
                    <span class="text-amber-600"><?php echo e(\Carbon\Carbon::createFromTimeString($rec->check_in)->format('H:i')); ?></span>
                  <?php endif; ?>
                </span>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>

  
  <div class="card overflow-x-auto">
    <h2 class="font-semibold text-slate-700 mb-3">All Late Arrivals — <?php echo e(\Carbon\Carbon::parse($month.'-01')->format('F Y')); ?></h2>
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">Date</th>
          <th class="th">Employee</th>
          <th class="th">Department</th>
          <th class="th">Check-in Time</th>
          <th class="th">Late By</th>
          <th class="th">Remarks</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="tr">
          <td class="td"><?php echo e($rec->date->format('D, d M')); ?></td>
          <td class="td font-medium"><?php echo e($rec->employee->name); ?></td>
          <td class="td text-slate-500"><?php echo e($rec->employee->department?->name ?? '—'); ?></td>
          <td class="td font-mono text-amber-700"><?php echo e($rec->check_in ? \Carbon\Carbon::createFromTimeString($rec->check_in)->format('h:i A') : '—'); ?></td>
          <td class="td text-red-600 font-semibold"><?php echo e($rec->late_minutes); ?> min</td>
          <td class="td text-slate-500 text-xs"><?php echo e($rec->remarks ?? '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\late-arrival.blade.php ENDPATH**/ ?>