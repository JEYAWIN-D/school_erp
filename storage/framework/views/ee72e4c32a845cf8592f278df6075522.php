<?php $__env->startSection('title','Vehicle Documents & Expiry'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Vehicle Documents & Expiry Tracking</h1>
  <?php
    $expiredCount = $vehicles->filter(fn($v)=>$v->expiring_soon)->count();
  ?>
  <?php if($expiredCount > 0): ?>
  <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 flex items-center gap-3">
    <span class="text-red-500 font-bold text-lg">⚠</span>
    <p class="text-red-700 text-sm"><strong><?php echo e($expiredCount); ?> vehicles</strong> have documents expiring within 30 days.</p>
  </div>
  <?php endif; ?>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Vehicle','Registration','Fitness Expiry','Insurance Expiry','Permit Expiry','PUC Expiry','Tax Expiry','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $today = now();
          $checkDate = fn($d) => $d ? ($d->isPast() ? 'expired' : ($d->diffInDays($today) < 30 ? 'soon' : 'ok')) : 'missing';
          $classes = ['expired'=>'text-red-600 font-bold','soon'=>'text-amber-600 font-semibold','ok'=>'text-green-600','missing'=>'text-slate-300'];
          $badges = ['expired'=>'<span class="text-xs bg-red-100 text-red-600 px-1 rounded">Expired</span>','soon'=>'<span class="text-xs bg-amber-100 text-amber-600 px-1 rounded">Soon</span>','ok'=>'','missing'=>'<span class="text-xs bg-slate-100 text-slate-400 px-1 rounded">NA</span>'];
        ?>
        <tr class="<?php echo e($v->expiring_soon ? 'bg-amber-50/30' : 'hover:bg-slate-50'); ?>">
          <td class="px-4 py-3">
            <p class="font-semibold text-slate-800 text-sm"><?php echo e($v->make); ?> <?php echo e($v->model); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($v->vehicle_type); ?></p>
          </td>
          <td class="px-4 py-3 font-mono text-xs text-indigo-700"><?php echo e($v->vehicle_number); ?></td>
          <?php $__currentLoopData = ['fitness_expiry','insurance_expiry','permit_expiry','puc_expiry','tax_expiry']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $s = $checkDate($v->$field); ?>
          <td class="px-4 py-3">
            <span class="<?php echo e($classes[$s]); ?> text-xs"><?php echo e($v->$field ? $v->$field->format('d M Y') : '—'); ?></span>
            <?php echo $badges[$s]; ?>

          </td>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <td class="px-4 py-3">
            <a href="<?php echo e(route('transport.vehicle.edit',$v->id)); ?>" class="text-indigo-600 hover:underline text-xs">Update Docs</a>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No vehicles.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\documents.blade.php ENDPATH**/ ?>