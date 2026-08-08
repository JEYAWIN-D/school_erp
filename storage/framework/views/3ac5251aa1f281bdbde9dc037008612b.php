<?php $__env->startSection('title','Seat Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Seat Management</h1>
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Class</th>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Category</th>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Total Seats</th>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Filled</th>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Available</th>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Fill %</th>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">Action</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $seats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($seat->class?->name); ?></td>
          <td class="px-4 py-3 capitalize text-slate-500"><?php echo e($seat->category); ?></td>
          <td class="px-4 py-3"><?php echo e($seat->total_seats); ?></td>
          <td class="px-4 py-3 text-red-600"><?php echo e($seat->filled_seats); ?></td>
          <td class="px-4 py-3 text-green-600 font-semibold"><?php echo e($seat->available_seats); ?></td>
          <td class="px-4 py-3">
            <?php $pct = $seat->total_seats > 0 ? round($seat->filled_seats / $seat->total_seats * 100) : 0; ?>
            <div class="w-24 bg-slate-200 rounded-full h-1.5"><div class="h-1.5 rounded-full <?php echo e($pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-green-500')); ?>" style="width:<?php echo e($pct); ?>%"></div></div>
            <span class="text-xs text-slate-400 ml-1"><?php echo e($pct); ?>%</span>
          </td>
          <td class="px-4 py-3">
            <button x-data @click="$dispatch('open-modal','edit-seat-<?php echo e($seat->id); ?>')" class="text-indigo-600 hover:underline text-xs">Edit</button>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No seat capacities configured.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  
  <div class="card max-w-lg">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Configure Seats</h3>
    <form method="POST" action="<?php echo e(route('admissions.seats.save')); ?>" class="space-y-4">
      <?php echo csrf_field(); ?>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select">
            <option value="">Select</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Category</label>
          <select name="category" class="select">
            <?php $__currentLoopData = ['general','sc','st','obc','ews','minority']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($cat); ?>"><?php echo e(strtoupper($cat)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div><label class="label">Total Seats</label>
        <input type="number" name="total_seats" class="input" min="1" value="30">
      </div>
      <button type="submit" class="btn btn-primary">Save</button>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\admissions\seats.blade.php ENDPATH**/ ?>