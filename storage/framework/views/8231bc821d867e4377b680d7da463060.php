<?php $__env->startSection('title','Vehicle Maintenance Log'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Vehicle Maintenance Log</h1>
    <a href="<?php echo e(route('transport.maintenance-cost')); ?>" class="btn btn-secondary btn-sm">Cost Report →</a>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Log Maintenance</h3>
      <form method="POST" action="<?php echo e(route('transport.maintenance.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div><label class="label">Vehicle <span class="text-red-500">*</span></label>
          <select name="vehicle_id" class="select" required>
            <option value="">Select vehicle</option>
            <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($v->id); ?>" <?php if(old('vehicle_id')==$v->id): echo 'selected'; endif; ?>><?php echo e($v->vehicle_number); ?> – <?php echo e($v->make); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Maintenance Type <span class="text-red-500">*</span></label>
          <select name="maintenance_type" class="select" required>
            <?php $__currentLoopData = ['service'=>'Service','repair'=>'Repair','tyre'=>'Tyre Change','oil_change'=>'Oil Change','battery'=>'Battery','other'=>'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>"><?php echo e($v); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Date <span class="text-red-500">*</span></label>
          <input type="date" name="service_date" class="input" required value="<?php echo e(old('service_date',today()->toDateString())); ?>">
        </div>
        <div><label class="label">Odometer Reading (km)</label>
          <input type="number" name="odometer" class="input" min="0" value="<?php echo e(old('odometer')); ?>">
        </div>
        <div><label class="label">Cost (₹)</label>
          <input type="number" name="cost" class="input" step="0.01" min="0" value="<?php echo e(old('cost')); ?>">
        </div>
        <div><label class="label">Vendor / Garage</label>
          <input type="text" name="vendor" class="input" value="<?php echo e(old('vendor')); ?>">
        </div>
        <div><label class="label">Description</label>
          <textarea name="description" class="input h-16"><?php echo e(old('description')); ?></textarea>
        </div>
        <div><label class="label">Next Service Date</label>
          <input type="date" name="next_service_date" class="input" value="<?php echo e(old('next_service_date')); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Log Entry</button>
      </form>
    </div>
    <div class="lg:col-span-2 space-y-4">
      <form method="GET" class="card-flat py-3"><div class="flex gap-3">
        <select name="vehicle_id" class="select w-44">
          <option value="">All Vehicles</option>
          <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($v->id); ?>" <?php if(request('vehicle_id')==$v->id): echo 'selected'; endif; ?>><?php echo e($v->vehicle_number); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input type="month" name="month" value="<?php echo e(request('month')); ?>" class="input w-36">
        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      </div></form>
      <div class="card overflow-hidden">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 border-b"><tr>
            <?php $__currentLoopData = ['Vehicle','Type','Date','Odometer','Cost','Vendor','Next Service']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium"><?php echo e($h); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr></thead>
          <tbody class="divide-y divide-slate-100">
            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3 font-mono text-xs text-indigo-700"><?php echo e($log->vehicle?->vehicle_number); ?></td>
              <td class="px-4 py-3"><span class="badge-slate capitalize text-xs"><?php echo e(str_replace('_',' ',$log->maintenance_type)); ?></span></td>
              <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($log->service_date?->format('d M Y')); ?></td>
              <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($log->odometer ? number_format($log->odometer).' km' : '—'); ?></td>
              <td class="px-4 py-3 font-semibold text-xs"><?php echo e($log->cost ? '₹'.number_format($log->cost,0) : '—'); ?></td>
              <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($log->vendor ?? '—'); ?></td>
              <td class="px-4 py-3 text-xs <?php echo e($log->next_service_date?->isPast() ? 'text-red-500 font-semibold' : 'text-slate-400'); ?>"><?php echo e($log->next_service_date?->format('d M Y') ?? '—'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No maintenance logs.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        <?php if($logs->hasPages()): ?><div class="px-4 pb-3"><?php echo e($logs->links()); ?></div><?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\maintenance.blade.php ENDPATH**/ ?>