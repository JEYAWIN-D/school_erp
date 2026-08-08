<?php $__env->startSection('title', 'Visiting Hours Configuration'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Visiting Hours Configuration</h1>
    <a href="<?php echo e(route('hostel.visitors')); ?>" class="btn-secondary btn-sm">← Back to Visitors</a>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="card">
      <h2 class="text-sm font-semibold text-slate-700 mb-4">Add Visiting Hours Rule</h2>
      <form method="POST" action="<?php echo e(route('hostel.visiting-hours.store')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">Hostel <span class="text-slate-400 text-xs">(leave blank = applies to all)</span></label>
          <select name="hostel_id" class="select">
            <option value="">All Hostels</option>
            <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($h->id); ?>"><?php echo e($h->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Day Type</label>
          <select name="day_type" class="select">
            <option value="all">All Days</option>
            <option value="weekday">Weekdays (Mon–Fri)</option>
            <option value="weekend">Weekends (Sat–Sun)</option>
            <option value="holiday">Holidays</option>
          </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label">From Time</label>
            <input type="time" name="from_time" class="input" value="09:00" required>
          </div>
          <div>
            <label class="label">To Time</label>
            <input type="time" name="to_time" class="input" value="18:00" required>
          </div>
        </div>
        <div>
          <label class="label">Note (optional)</label>
          <input type="text" name="note" class="input" placeholder="e.g. Summer hours">
        </div>
        <button type="submit" class="btn-primary w-full">Add Rule</button>
      </form>
    </div>

    
    <div class="lg:col-span-2 card">
      <h2 class="text-sm font-semibold text-slate-700 mb-4">Active Rules</h2>
      <?php if($rules->isEmpty()): ?>
        <div class="alert-info">No rules configured. Without rules, visitors can be logged at any time.</div>
      <?php else: ?>
      <div class="table-wrap">
        <table class="w-full">
          <thead>
            <tr>
              <th class="th">Hostel</th>
              <th class="th">Day Type</th>
              <th class="th">From</th>
              <th class="th">To</th>
              <th class="th">Note</th>
              <th class="th">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="tr">
              <td class="td"><?php echo e($rule->hostel?->name ?? 'All Hostels'); ?></td>
              <td class="td"><span class="badge-blue capitalize"><?php echo e(str_replace('_', ' ', $rule->day_type)); ?></span></td>
              <td class="td font-mono"><?php echo e($rule->from_time); ?></td>
              <td class="td font-mono"><?php echo e($rule->to_time); ?></td>
              <td class="td text-slate-400 text-xs"><?php echo e($rule->note ?? '—'); ?></td>
              <td class="td">
                <form method="POST" action="<?php echo e(route('hostel.visiting-hours.delete', $rule->id)); ?>" class="inline">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn-xs btn-secondary text-red-600" onclick="return confirm('Delete rule?')">Delete</button>
                </form>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>

      <div class="mt-4 bg-slate-50 rounded-lg p-4">
        <p class="text-sm font-semibold text-slate-600 mb-1">Current Status</p>
        <?php $now = now(); $allowed = \App\Models\HostelVisitingHours::isCurrentlyAllowed(); ?>
        <div class="flex items-center gap-3">
          <span class="badge-<?php echo e($allowed ? 'green' : 'red'); ?>"><?php echo e($allowed ? 'Visiting Allowed' : 'Outside Hours'); ?></span>
          <span class="text-sm text-slate-500">Current time: <?php echo e($now->format('h:i A')); ?> (<?php echo e($now->isWeekend() ? 'Weekend' : 'Weekday'); ?>)</span>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\visiting-hours.blade.php ENDPATH**/ ?>