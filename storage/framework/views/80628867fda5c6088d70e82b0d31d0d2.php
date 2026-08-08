<?php $__env->startSection('title', 'Attendance Register'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Monthly Attendance Register</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('dashboard')); ?>" class="btn-sm btn-secondary">← Dashboard</a>
      <?php if($classId): ?>
      <button onclick="window.print()" class="btn-sm btn-primary">🖨 Print</button>
      <?php endif; ?>
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">Class <span class="text-red-500">*</span></label>
      <select name="class_id" class="select" required>
        <option value="">Select Class</option>
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($cls->id); ?>" <?php if($classId==$cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Month</label>
      <select name="month" class="select">
        <?php $__currentLoopData = range(1,12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($m); ?>" <?php if($month==$m): echo 'selected'; endif; ?>><?php echo e(\Carbon\Carbon::create()->month($m)->format('F')); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Year</label>
      <select name="year" class="select">
        <?php $__currentLoopData = range(now()->year-2, now()->year+1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($y); ?>" <?php if($year==$y): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <button type="submit" class="btn-primary btn-sm">Load</button>
  </form>

  <?php if($classId && $students->isNotEmpty()): ?>
  <?php
    $statusMap = ['present'=>'P','absent'=>'A','late'=>'L','half_day'=>'H/D','holiday'=>'','sunday'=>''];
    $colorMap  = ['present'=>'text-emerald-600','absent'=>'text-rose-600','late'=>'text-amber-600','half_day'=>'text-blue-600'];
  ?>
  <div class="overflow-x-auto">
    <div class="card p-0 min-w-max">
      <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
        <h3 class="font-semibold text-slate-700">
          <?php echo e($students->first()->class_name ?? ''); ?>

          — <?php echo e(\Carbon\Carbon::create()->month($month)->format('F')); ?> <?php echo e($year); ?>

        </h3>
        <div class="flex gap-3 text-xs text-slate-500">
          <span><span class="font-bold text-emerald-600">P</span> Present</span>
          <span><span class="font-bold text-rose-600">A</span> Absent</span>
          <span><span class="font-bold text-amber-600">L</span> Late</span>
          <span><span class="font-bold text-blue-600">H/D</span> Half Day</span>
        </div>
      </div>
      <table class="w-full text-xs">
        <thead>
          <tr class="bg-slate-50">
            <th class="px-3 py-2 text-left font-semibold text-slate-600 whitespace-nowrap sticky left-0 bg-slate-50 z-10">#</th>
            <th class="px-3 py-2 text-left font-semibold text-slate-600 whitespace-nowrap sticky left-8 bg-slate-50 z-10">Name</th>
            <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <th class="px-1.5 py-2 text-center font-semibold text-slate-500 w-8"><?php echo e($day); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <th class="px-3 py-2 text-center font-semibold text-slate-600">P</th>
            <th class="px-3 py-2 text-center font-semibold text-slate-600">A</th>
            <th class="px-3 py-2 text-center font-semibold text-slate-600">%</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $presentCount = 0; $absentCount = 0;
          ?>
          <tr class="tr">
            <td class="px-3 py-1.5 text-slate-400 sticky left-0 bg-white z-10"><?php echo e($i+1); ?></td>
            <td class="px-3 py-1.5 font-medium text-slate-700 whitespace-nowrap sticky left-8 bg-white z-10">
              <?php echo e($s->first_name); ?> <?php echo e($s->last_name); ?>

            </td>
            <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $key = $s->id . '_' . $day;
              $rec = $records->get($key)?->first();
              $status = $rec?->status ?? '';
              $label  = $statusMap[$status] ?? '';
              $color  = $colorMap[$status] ?? 'text-slate-300';
              if ($status === 'present' || $status === 'half_day') $presentCount++;
              elseif ($status === 'absent') $absentCount++;
            ?>
            <td class="px-1.5 py-1.5 text-center font-semibold <?php echo e($color); ?>"><?php echo e($label ?: '·'); ?></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <td class="px-3 py-1.5 text-center font-semibold text-emerald-600"><?php echo e($presentCount); ?></td>
            <td class="px-3 py-1.5 text-center font-semibold text-rose-600"><?php echo e($absentCount); ?></td>
            <td class="px-3 py-1.5 text-center font-semibold text-slate-600">
              <?php $total = $presentCount + $absentCount; ?>
              <?php echo e($total > 0 ? round($presentCount/$total*100) : '—'); ?><?php echo e($total > 0 ? '%' : ''); ?>

            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php elseif($classId): ?>
    <div class="card text-center py-10 text-slate-400">No students enrolled in the selected class.</div>
  <?php else: ?>
    <div class="card text-center py-10 text-slate-400">Select a class and month to view the attendance register.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\reports\attendance-register.blade.php ENDPATH**/ ?>