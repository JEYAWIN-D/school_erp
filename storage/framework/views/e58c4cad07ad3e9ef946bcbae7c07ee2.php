<?php $__env->startSection('title', 'Transport Routes'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Transport Routes</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(request()->fullUrlWithQuery(['show_inactive' => $showInactive ? 0 : 1])); ?>"
         class="btn btn-secondary btn-sm <?php echo e($showInactive ? 'bg-slate-200' : ''); ?>">
        <?php echo e($showInactive ? 'Hide Inactive' : 'Show Inactive'); ?>

      </a>
      <a href="<?php echo e(route('transport.stops')); ?>" class="btn btn-secondary">Manage Stops</a>
      <a href="<?php echo e(route('transport.routes.create')); ?>" class="btn btn-primary">Add Route</a>
    </div>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  <div class="table-wrap">
    <table class="w-full">
      <thead><tr>
        <th class="th">Route</th><th class="th">From</th><th class="th">To</th>
        <th class="th">Departure</th><th class="th">Fee/Month</th>
        <th class="th">Status</th><th class="th">Actions</th>
      </tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr <?php echo e(!$route->is_active ? 'opacity-60' : ''); ?>">
            <td class="td font-medium text-slate-800"><?php echo e($route->route_name); ?>

              <?php if($route->route_number): ?><span class="text-xs text-slate-400 ml-1">#<?php echo e($route->route_number); ?></span><?php endif; ?>
            </td>
            <td class="td"><?php echo e($route->from_location); ?></td>
            <td class="td"><?php echo e($route->to_location); ?></td>
            <td class="td"><?php echo e($route->departure_time ? \Carbon\Carbon::parse($route->departure_time)->format('h:i A') : '—'); ?></td>
            <td class="td">₹<?php echo e(number_format($route->fee, 2)); ?></td>
            <td class="td"><span class="<?php echo e($route->is_active ? 'badge-green' : 'badge-slate'); ?>"><?php echo e($route->is_active ? 'Active' : 'Inactive'); ?></span></td>
            <td class="td">
              <form method="POST" action="<?php echo e(route('transport.routes.toggle', $route->id)); ?>" class="inline">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <button type="submit"
                  class="btn-xs <?php echo e($route->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-green-600 hover:text-green-800'); ?>"
                  onclick="return confirm('<?php echo e($route->is_active ? 'Deactivate' : 'Activate'); ?> route?')">
                  <?php echo e($route->is_active ? 'Deactivate' : 'Activate'); ?>

                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="7" class="td text-center py-10 text-slate-400">No routes configured.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if($routes->hasPages()): ?><div class="text-sm mt-3"><?php echo e($routes->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\routes.blade.php ENDPATH**/ ?>