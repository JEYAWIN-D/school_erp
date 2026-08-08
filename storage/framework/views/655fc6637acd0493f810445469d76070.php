<?php $__env->startSection('title', 'Vehicles'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Vehicles</h1>
    <a href="<?php echo e(route('transport.vehicles.create')); ?>" class="btn btn-primary">Add Vehicle</a>
  </div>

  <?php $overdueCount = $vehicles->filter(fn($v) => $v->next_service_overdue)->count(); ?>
  <?php if($overdueCount > 0): ?>
  <div class="alert-warning">
    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <strong><?php echo e($overdueCount); ?> vehicle<?php echo e($overdueCount > 1 ? 's' : ''); ?></strong> have overdue scheduled service. Check the Service Due column below and book maintenance.
  </div>
  <?php endif; ?>

  <div class="table-wrap">
    <table class="w-full">
      <thead><tr>
        <th class="th">Vehicle #</th><th class="th">Type</th><th class="th">Make/Model</th>
        <th class="th">Capacity</th><th class="th">Driver</th><th class="th">Route</th>
        <th class="th">Next Service</th><th class="th">Status</th><th class="th">Actions</th>
      </tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td font-mono font-semibold text-blue-600"><?php echo e($v->vehicle_number); ?></td>
            <td class="td"><?php echo e($v->vehicle_type); ?></td>
            <td class="td"><?php echo e($v->make); ?> <?php echo e($v->model); ?></td>
            <td class="td"><?php echo e($v->capacity ?: $v->seating_capacity); ?></td>
            <td class="td"><?php echo e($v->driver_name ?? '—'); ?></td>
            <td class="td"><?php echo e($v->route?->route_name ?? '—'); ?></td>
            <td class="td">
              <?php if($v->next_service_date): ?>
                <span class="<?php echo e($v->next_service_overdue ? 'text-red-600 font-semibold' : 'text-slate-600'); ?>">
                  <?php echo e(\Carbon\Carbon::parse($v->next_service_date)->format('d M Y')); ?>

                  <?php if($v->next_service_overdue): ?>
                    <br><span class="badge-red text-xs">Overdue</span>
                  <?php elseif(\Carbon\Carbon::parse($v->next_service_date)->diffInDays(now()) <= 14): ?>
                    <br><span class="badge-amber text-xs">Due Soon</span>
                  <?php endif; ?>
                </span>
              <?php else: ?>
                <span class="text-slate-300">—</span>
              <?php endif; ?>
            </td>
            <td class="td"><span class="<?php echo e($v->is_active ? 'badge-green' : 'badge-red'); ?>"><?php echo e($v->is_active ? 'Active' : 'Inactive'); ?></span></td>
            <td class="td">
              <a href="<?php echo e(route('transport.vehicle.edit', $v->id)); ?>" class="btn-xs text-indigo-600">Edit</a>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="9" class="td text-center py-10 text-slate-400">No vehicles added.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\vehicles.blade.php ENDPATH**/ ?>