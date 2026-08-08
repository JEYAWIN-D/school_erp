<?php $__env->startSection('title','Fuel Log'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Fuel Log</h1>
    <a href="<?php echo e(route('transport.fuel-summary')); ?>" class="btn btn-secondary btn-sm">Monthly Summary →</a>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Log Fuel Fill</h3>
      <form method="POST" action="<?php echo e(route('transport.fuel.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div><label class="label">Vehicle <span class="text-red-500">*</span></label>
          <select name="vehicle_id" class="select" required>
            <option value="">Select</option>
            <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($v->id); ?>"><?php echo e($v->vehicle_number); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Date <span class="text-red-500">*</span></label>
          <input type="date" name="fill_date" class="input" required value="<?php echo e(today()->toDateString()); ?>">
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="label">Litres <span class="text-red-500">*</span></label>
            <input type="number" name="litres" class="input" step="0.01" min="0" required>
          </div>
          <div><label class="label">Rate/L (₹)</label>
            <input type="number" name="rate_per_litre" class="input" step="0.01" min="0">
          </div>
        </div>
        <div><label class="label">Total Amount (₹)</label>
          <input type="number" name="amount" class="input" step="0.01" min="0">
        </div>
        <div><label class="label">Odometer (km)</label>
          <input type="number" name="odometer" class="input" min="0">
        </div>
        <div><label class="label">Filled By / Station</label>
          <input type="text" name="station" class="input" placeholder="Driver / Pump name">
        </div>
        <button type="submit" class="btn btn-primary">Log Entry</button>
      </form>
    </div>
    <div class="lg:col-span-2 space-y-4">
      <?php if(isset($summary)): ?>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="card text-center py-3"><p class="text-xs text-slate-400">Total Litres</p><p class="text-xl font-bold text-slate-800"><?php echo e(number_format($summary['litres'],1)); ?>L</p></div>
        <div class="card text-center py-3"><p class="text-xs text-slate-400">Total Cost</p><p class="text-xl font-bold text-slate-800">₹<?php echo e(number_format($summary['amount'],0)); ?></p></div>
        <div class="card text-center py-3"><p class="text-xs text-slate-400">Avg Mileage</p><p class="text-xl font-bold text-slate-800"><?php echo e($summary['mileage'] ? $summary['mileage'].' km/L' : '—'); ?></p></div>
        <div class="card text-center py-3"><p class="text-xs text-slate-400">Cost / km</p><p class="text-xl font-bold text-slate-800"><?php echo e($summary['cost_per_km'] ? '₹'.$summary['cost_per_km'] : '—'); ?></p></div>
      </div>
      <?php endif; ?>
      <form method="GET" class="card-flat py-3"><div class="flex gap-3">
        <select name="vehicle_id" class="select w-44">
          <option value="">All Vehicles</option>
          <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($v->id); ?>" <?php if(request('vehicle_id')==$v->id): echo 'selected'; endif; ?>><?php echo e($v->vehicle_number); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input type="month" name="month" value="<?php echo e(request('month', now()->format('Y-m'))); ?>" class="input w-36">
        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      </div></form>
      <div class="card overflow-hidden">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 border-b"><tr>
            <?php $__currentLoopData = ['Vehicle','Date','Litres','Rate','Amount','Odometer','Filled By']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium"><?php echo e($h); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr></thead>
          <tbody class="divide-y divide-slate-100">
            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3 font-mono text-xs text-indigo-700"><?php echo e($log->vehicle?->vehicle_number); ?></td>
              <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($log->log_date?->format('d M Y')); ?></td>
              <td class="px-4 py-3 font-semibold text-xs"><?php echo e(number_format($log->quantity_litres,1)); ?>L</td>
              <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($log->cost_per_litre ? '₹'.number_format($log->cost_per_litre,2) : '—'); ?></td>
              <td class="px-4 py-3 font-semibold text-xs"><?php echo e($log->total_cost ? '₹'.number_format($log->total_cost,0) : '—'); ?></td>
              <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($log->odometer_reading ? number_format($log->odometer_reading).' km' : '—'); ?></td>
              <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($log->filled_by ?? '—'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No fuel logs.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        <?php if($logs->hasPages()): ?><div class="px-4 pb-3"><?php echo e($logs->links()); ?></div><?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\fuel.blade.php ENDPATH**/ ?>