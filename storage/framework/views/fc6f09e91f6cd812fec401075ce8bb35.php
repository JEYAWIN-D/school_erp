<?php $__env->startSection('title', 'Night Duty Assignment'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Night Duty Staff Assignment</h1>
      <p class="page-subtitle">Assign warden / staff for night duty at each hostel</p>
    </div>
    <a href="<?php echo e(route('hostel.index')); ?>" class="btn btn-secondary btn-sm">← Hostel</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Assign Night Duty</h3>
      <form method="POST" action="<?php echo e(route('hostel.night-duty.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">Hostel <span class="text-red-500">*</span></label>
          <select name="hostel_id" class="select" required>
            <option value="">Select hostel</option>
            <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($h->id); ?>"><?php echo e($h->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Staff / Employee <span class="text-red-500">*</span></label>
          <select name="employee_id" class="select" required>
            <option value="">Select employee</option>
            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?> (<?php echo e($emp->employee_number); ?>)
                <?php if($emp->designation): ?> — <?php echo e($emp->designation); ?><?php endif; ?>
              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Duty Date <span class="text-red-500">*</span></label>
          <input type="date" name="duty_date" class="input" value="<?php echo e(today()->toDateString()); ?>" required>
        </div>
        <div>
          <label class="label">Shift</label>
          <select name="shift" class="select">
            <option value="night">Night Only</option>
            <option value="full">Full Day</option>
          </select>
        </div>
        <div>
          <label class="label">Notes</label>
          <input type="text" name="notes" class="input" placeholder="Any specific instructions...">
        </div>
        <button type="submit" class="btn btn-primary w-full">Assign Duty</button>
      </form>
    </div>

    
    <div class="lg:col-span-2">
      <div class="card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-slate-700">Duty Schedule</h3>
          <form method="GET" class="flex gap-2 items-end">
            <input type="month" name="month" value="<?php echo e($month); ?>" class="input text-sm py-1" onchange="this.form.submit()">
            <select name="hostel_id" class="select text-sm py-1" onchange="this.form.submit()">
              <option value="">All Hostels</option>
              <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($h->id); ?>" <?php if(request('hostel_id')==$h->id): echo 'selected'; endif; ?>><?php echo e($h->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </form>
        </div>

        <?php if($duties->isEmpty()): ?>
          <p class="text-center py-8 text-slate-400">No night duty assigned for this month.</p>
        <?php else: ?>
        <div class="table-wrap">
          <table class="w-full text-sm">
            <thead><tr>
              <th class="th">Date</th>
              <th class="th">Hostel</th>
              <th class="th">Staff</th>
              <th class="th">Shift</th>
              <th class="th">Notes</th>
              <th class="th"></th>
            </tr></thead>
            <tbody>
              <?php $__currentLoopData = $duties->sortBy('duty_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $duty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr class="tr">
                <td class="td font-medium"><?php echo e($duty->duty_date->format('d M Y')); ?>

                  <?php if($duty->duty_date->isToday()): ?> <span class="badge-green ml-1 text-xs">Today</span><?php endif; ?>
                </td>
                <td class="td"><?php echo e($duty->hostel?->name ?? '—'); ?></td>
                <td class="td">
                  <?php echo e($duty->employee?->first_name); ?> <?php echo e($duty->employee?->last_name); ?>

                  <span class="text-xs text-slate-400 block"><?php echo e($duty->employee?->employee_number); ?></span>
                </td>
                <td class="td"><span class="badge-slate capitalize text-xs"><?php echo e($duty->shift); ?></span></td>
                <td class="td text-slate-500 text-xs"><?php echo e($duty->notes ?? '—'); ?></td>
                <td class="td">
                  <form method="POST" action="<?php echo e(route('hostel.night-duty.delete', $duty->id)); ?>"
                    onsubmit="return confirm('Remove this duty assignment?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="text-xs text-red-400 hover:text-red-600">Remove</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\night-duty.blade.php ENDPATH**/ ?>