<?php $__env->startSection('title','Leave Calendar'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Staff Leave Calendar</h1>
    <form method="GET" class="flex gap-2">
      <select name="department_id" class="select w-40">
        <option value="">All Depts</option>
        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($d->id); ?>" <?php if(request('department_id')==$d->id): echo 'selected'; endif; ?>><?php echo e($d->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <input type="month" name="month" value="<?php echo e(request('month', now()->format('Y-m'))); ?>" class="input w-36">
      <button type="submit" class="btn btn-primary btn-sm">Load</button>
    </form>
  </div>

  <div class="card overflow-x-auto">
    <table class="text-xs min-w-max">
      <thead class="bg-slate-50 border-b">
        <tr>
          <th class="sticky left-0 bg-slate-50 text-left px-4 py-3 text-slate-500 uppercase font-medium min-w-[160px]">Employee</th>
          <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <th class="px-1.5 py-3 text-center font-medium text-slate-500 w-8 <?php echo e($d->isWeekend() ? 'bg-slate-100' : ''); ?>">
            <?php echo e($d->format('d')); ?><br><span class="<?php echo e($d->isWeekend() ? 'text-red-300' : 'text-slate-300'); ?>"><?php echo e($d->format('D')[0]); ?></span>
          </th>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <th class="px-3 py-3 text-center font-medium text-slate-500">Leave</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $empLeaves = $leaveMap[$emp->id] ?? []; ?>
        <tr class="hover:bg-slate-50">
          <td class="sticky left-0 bg-white px-4 py-2 font-medium text-slate-800">
            <?php echo e($emp->full_name); ?><br>
            <span class="text-xs text-slate-400"><?php echo e($emp->department?->name); ?></span>
          </td>
          <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $status = $empLeaves[$d->toDateString()] ?? null;
            $isWeekend = $d->isWeekend();
          ?>
          <td class="px-1 py-2 text-center <?php echo e($isWeekend ? 'bg-slate-50' : ''); ?>">
            <?php if($isWeekend): ?><span class="text-slate-200">W</span>
            <?php elseif($status === 'approved'): ?><span class="text-red-500 font-bold" title="<?php echo e($status); ?>">L</span>
            <?php elseif($status === 'half_day'): ?><span class="text-amber-500 font-bold">H</span>
            <?php elseif($status === 'holiday'): ?><span class="text-blue-300">H</span>
            <?php else: ?><span class="text-slate-100">·</span><?php endif; ?>
          </td>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php $leaveCount = collect($empLeaves)->filter(fn($v)=>$v==='approved')->count(); ?>
          <td class="px-3 py-2 text-center font-semibold <?php echo e($leaveCount > 0 ? 'text-red-600' : 'text-slate-400'); ?>"><?php echo e($leaveCount ?: '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>

  <div class="flex gap-4 text-xs text-slate-500">
    <span><span class="font-bold text-red-500">L</span> = Leave</span>
    <span><span class="font-bold text-amber-500">H</span> = Half Day</span>
    <span><span class="font-bold text-blue-300">H</span> = Holiday</span>
    <span><span class="text-slate-200 font-bold">W</span> = Weekend</span>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\leave-calendar.blade.php ENDPATH**/ ?>