<?php $__env->startSection('title','Student Route Allotment'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Student Route Allotment</h1>
    <a href="<?php echo e(route('transport.allotment.export')); ?>" class="btn btn-secondary btn-sm">Export Excel</a>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Assign Route</h3>
      <form method="POST" action="<?php echo e(route('transport.allotment.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div><label class="label">Student <span class="text-red-500">*</span></label>
          <select name="enrollment_id" class="select" required>
            <option value="">Search student</option>
            <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($e->id); ?>"><?php echo e($e->student?->full_name); ?> (<?php echo e($e->class?->name); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Route <span class="text-red-500">*</span></label>
          <select name="route_id" class="select" required>
            <option value="">Select route</option>
            <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r->id); ?>"><?php echo e($r->route_name); ?> (<?php echo e($r->vehicle?->vehicle_number); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Pickup Stop</label>
          <select name="stop_id" class="select">
            <option value="">Select stop</option>
            <?php $__currentLoopData = $stops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="label">Pickup Time</label><input type="time" name="pickup_time" class="input"></div>
          <div><label class="label">Drop Time</label><input type="time" name="drop_time" class="input"></div>
        </div>
        <div><label class="label">Monthly Fee (₹)</label><input type="number" name="fee" class="input" step="0.01" min="0"></div>
        <button type="submit" class="btn btn-primary">Assign Route</button>
      </form>
    </div>
    <div class="lg:col-span-2 card overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-100 flex gap-3">
        <form method="GET" class="flex gap-3 flex-1">
          <select name="route_id" class="select w-44">
            <option value="">All Routes</option>
            <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r->id); ?>" <?php if(request('route_id')==$r->id): echo 'selected'; endif; ?>><?php echo e($r->route_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <select name="class_id" class="select w-32">
            <option value="">All Classes</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        </form>
      </div>
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b"><tr>
          <?php $__currentLoopData = ['Student','Class','Route','Stop','Pickup','Drop','Fee','Action']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium"><?php echo e($h); ?></th>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
          <?php $__empty_1 = true; $__currentLoopData = $allotments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 font-medium text-slate-800 text-sm"><?php echo e($a->enrollment?->student?->full_name); ?></td>
            <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($a->enrollment?->class?->name); ?></td>
            <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($a->route?->route_name); ?></td>
            <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($a->stop?->name ?? '—'); ?></td>
            <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($a->pickup_time ?? '—'); ?></td>
            <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($a->drop_time ?? '—'); ?></td>
            <td class="px-4 py-3 font-semibold text-xs"><?php echo e($a->fee ? '₹'.number_format($a->fee,0) : '—'); ?></td>
            <td class="px-4 py-3">
              <form method="POST" action="<?php echo e(route('transport.allotment.delete',$a->id)); ?>"
                    onsubmit="return confirm('Remove <?php echo e(addslashes($a->enrollment?->student?->full_name)); ?> from <?php echo e(addslashes($a->route?->route_name)); ?>?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="text-red-400 hover:underline text-xs">Remove</button>
              </form>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No allotments.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      <?php if($allotments->hasPages()): ?><div class="px-4 pb-3"><?php echo e($allotments->links()); ?></div><?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\allotment.blade.php ENDPATH**/ ?>