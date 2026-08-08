<?php $__env->startSection('title','Attendance Register'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Attendance Register</h1>
    <form method="GET" action="<?php echo e(route('attendance.register.pdf')); ?>" class="flex gap-2">
      <?php $__currentLoopData = request()->query(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <button type="submit" class="btn btn-secondary btn-sm">Download PDF</button>
    </form>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="class_id" class="select w-36">
      <option value="">Select Class</option>
      <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="section_id" class="select w-36">
      <option value="">Section</option>
      <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('section_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <input type="month" name="month" value="<?php echo e(request('month', now()->format('Y-m'))); ?>" class="input w-36">
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </div></form>
  <?php if(isset($students) && $students->count()): ?>
  <div class="card overflow-x-auto">
    <table class="text-xs min-w-max">
      <thead class="bg-slate-50 border-b"><tr>
        <th class="sticky left-0 bg-slate-50 text-left px-4 py-3 text-slate-500 uppercase font-medium min-w-[160px]">Student</th>
        <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="px-2 py-3 text-center font-medium text-slate-500 w-8">
          <?php echo e($d->format('d')); ?><br><span class="text-slate-300"><?php echo e($d->format('D')[0]); ?></span>
        </th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <th class="px-3 py-3 text-center font-medium text-slate-500">P</th>
        <th class="px-3 py-3 text-center font-medium text-slate-500">A</th>
        <th class="px-3 py-3 text-center font-medium text-slate-500">%</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $rec = $attendanceMap[$s->student_id] ?? []; ?>
        <tr class="hover:bg-slate-50">
          <td class="sticky left-0 bg-white px-4 py-2 font-medium text-slate-800"><?php echo e($s->student?->full_name); ?></td>
          <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $status = $rec[$d->toDateString()] ?? null; ?>
          <td class="px-1 py-2 text-center">
            <?php if($status === 'present'): ?><span class="text-green-600 font-bold">P</span>
            <?php elseif($status === 'absent'): ?><span class="text-red-500 font-bold">A</span>
            <?php elseif($status === 'late'): ?><span class="text-amber-500 font-bold">L</span>
            <?php elseif($status === 'holiday'): ?><span class="text-slate-300">H</span>
            <?php else: ?><span class="text-slate-200">—</span><?php endif; ?>
          </td>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php
            $p = collect($rec)->filter(fn($v)=>in_array($v,['present','late']))->count();
            $a = collect($rec)->filter(fn($v)=>$v==='absent')->count();
            $total = $p + $a;
            $pct = $total > 0 ? round($p/$total*100) : 0;
          ?>
          <td class="px-3 py-2 text-center font-semibold text-green-700"><?php echo e($p); ?></td>
          <td class="px-3 py-2 text-center font-semibold text-red-500"><?php echo e($a); ?></td>
          <td class="px-3 py-2 text-center font-semibold <?php echo e($pct < 75 ? 'text-red-600' : 'text-slate-700'); ?>"><?php echo e($pct); ?>%</td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="card text-center py-12 text-slate-400">Select class, section and month to load register.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\register.blade.php ENDPATH**/ ?>