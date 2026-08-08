<?php $__env->startSection('title','Attendance Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Attendance Report</h1>
  <form method="GET" class="card py-4"><div class="flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Class</label>
      <select name="class_id" class="select w-36">
        <option value="">Select Class</option>
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($cls->id); ?>" <?php if(request('class_id')==$cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Month</label>
      <input type="month" name="month" value="<?php echo e(request('month', now()->format('Y-m'))); ?>" class="input w-44">
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    <?php if($data->count() && request('class_id') && request('month')): ?>
    <a href="<?php echo e(request()->fullUrlWithQuery(['export'=>'excel'])); ?>" class="btn btn-secondary btn-sm">Export Excel</a>
    <?php endif; ?>
  </div></form>

  <?php if($data->count()): ?>
  <?php
    [$year, $mon] = explode('-', request('month'));
    $daysInMonth  = \Carbon\Carbon::create($year, $mon, 1)->daysInMonth;
    $dates        = range(1, $daysInMonth);
  ?>
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-700">
        <?php echo e($classes->firstWhere('id', request('class_id'))?->name); ?> — <?php echo e(\Carbon\Carbon::create($year, $mon, 1)->format('F Y')); ?>

      </h3>
      <span class="text-xs text-slate-400"><?php echo e($data->count()); ?> students</span>
    </div>
    <div class="overflow-x-auto">
      <table class="text-xs min-w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="text-left px-3 py-2 font-medium text-slate-500 uppercase tracking-wide sticky left-0 bg-slate-50 z-10 min-w-[140px]">Student</th>
            <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $weekday = \Carbon\Carbon::create($year, $mon, $d)->dayOfWeek; ?>
            <th class="text-center px-1 py-2 font-medium <?php echo e(in_array($weekday,[0,6]) ? 'text-slate-300' : 'text-slate-500'); ?> w-6"><?php echo e($d); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <th class="text-center px-2 py-2 font-medium text-green-600 uppercase tracking-wide">P</th>
            <th class="text-center px-2 py-2 font-medium text-red-500 uppercase tracking-wide">A</th>
            <th class="text-center px-2 py-2 font-medium text-amber-500 uppercase tracking-wide">L</th>
            <th class="text-center px-2 py-2 font-medium text-slate-500 uppercase tracking-wide">%</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentId => $records): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $student  = $records->first()?->student;
            $byDate   = $records->keyBy(fn($r) => \Carbon\Carbon::parse($r->date)->day);
            $present  = $records->whereIn('status', ['present', 'late'])->count();
            $absent   = $records->where('status', 'absent')->count();
            $leave    = $records->where('status', 'leave')->count();
            $total    = $present + $absent + $leave;
            $pct      = $total > 0 ? round($present / $total * 100, 1) : 0;
          ?>
          <tr class="hover:bg-slate-50">
            <td class="px-3 py-2 sticky left-0 bg-white z-10">
              <p class="font-medium text-slate-800"><?php echo e($student?->first_name); ?> <?php echo e($student?->last_name); ?></p>
              <p class="text-slate-400"><?php echo e($student?->admission_no); ?></p>
            </td>
            <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $rec     = $byDate[$d] ?? null;
              $status  = $rec?->status;
              $cell    = match($status) {
                'present' => ['P', 'text-green-600 font-semibold'],
                'late'    => ['L8', 'text-amber-500'],
                'absent'  => ['A', 'text-red-500 font-semibold'],
                'leave'   => ['LV', 'text-blue-500'],
                default   => ['·', 'text-slate-200'],
              };
              $weekday = \Carbon\Carbon::create($year, $mon, $d)->dayOfWeek;
            ?>
            <td class="text-center w-6 <?php echo e(in_array($weekday,[0,6]) ? 'bg-slate-50' : ''); ?>">
              <span class="<?php echo e($cell[1]); ?>"><?php echo e($cell[0]); ?></span>
            </td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <td class="text-center font-semibold text-green-600"><?php echo e($present); ?></td>
            <td class="text-center font-semibold text-red-500"><?php echo e($absent); ?></td>
            <td class="text-center text-blue-500"><?php echo e($leave); ?></td>
            <td class="text-center font-semibold <?php echo e($pct < 75 ? 'text-red-600' : 'text-slate-700'); ?>"><?php echo e($pct); ?>%</td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php elseif(request('class_id') && request('month')): ?>
  <div class="card text-center py-8 text-slate-400">No attendance records found for the selected class and month.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\report.blade.php ENDPATH**/ ?>