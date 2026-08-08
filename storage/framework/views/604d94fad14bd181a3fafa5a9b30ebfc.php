<?php $__env->startSection('title', 'Drivers'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Drivers</h1>
    <div class="flex gap-2 flex-wrap">
      <a href="<?php echo e(route('transport.attendants')); ?>" class="btn btn-secondary">Attendants</a>
      <a href="<?php echo e(route('transport.incidents')); ?>" class="btn btn-secondary">Incident Log</a>
      <a href="<?php echo e(route('transport.vehicle-utilisation')); ?>" class="btn btn-secondary">Utilisation</a>
      <a href="<?php echo e(route('transport.drivers.create')); ?>" class="btn btn-primary">Assign Driver</a>
    </div>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  
  <?php if($expiredCount > 0): ?>
  <div class="alert-danger flex items-center gap-2">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    <span><strong><?php echo e($expiredCount); ?> driver(s)</strong> have expired driving licences. Immediate renewal required.</span>
  </div>
  <?php endif; ?>
  <?php if($dueSoonCount > 0): ?>
  <div class="alert-warning flex items-center gap-2">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span><strong><?php echo e($dueSoonCount); ?> driver(s)</strong> have licences expiring within 30 days.</span>
  </div>
  <?php endif; ?>
  <?php if($unverified > 0): ?>
  <div class="bg-orange-50 border border-orange-200 text-orange-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
    <span><strong><?php echo e($unverified); ?> driver(s)</strong> have pending or expired police verification.</span>
  </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="card <?php echo e($v->license_expired ? 'border-red-300' : ($v->license_due_soon ? 'border-amber-300' : '')); ?>">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center">
            <span class="text-white text-sm font-bold"><?php echo e(strtoupper(substr($v->driver_name ?? 'DR', 0, 2))); ?></span>
          </div>
          <div>
            <p class="font-semibold text-slate-800"><?php echo e($v->driver_name); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($v->vehicle_number); ?></p>
          </div>
          
          <?php $pvs = $v->police_verification_status ?? 'pending'; ?>
          <span class="ml-auto text-xs px-2 py-0.5 rounded-full font-medium
            <?php echo e($pvs === 'verified' ? 'bg-green-100 text-green-700' : ($pvs === 'expired' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')); ?>">
            <?php echo e(ucfirst($pvs)); ?>

          </span>
        </div>

        <dl class="text-sm space-y-1">
          <div class="flex justify-between"><dt class="text-slate-400">Mobile</dt><dd class="font-mono"><?php echo e($v->driver_mobile ?? '—'); ?></dd></div>
          <div class="flex justify-between">
            <dt class="text-slate-400">License No</dt>
            <dd><?php echo e($v->driver_license ?? '—'); ?></dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-slate-400">License Expiry</dt>
            <dd class="<?php echo e($v->license_expired ? 'text-red-600 font-semibold' : ($v->license_due_soon ? 'text-amber-600 font-semibold' : '')); ?>">
              <?php if($v->driver_license_expiry): ?>
                <?php echo e(\Carbon\Carbon::parse($v->driver_license_expiry)->format('d M Y')); ?>

                <?php if($v->license_expired): ?> <span class="badge-red ml-1">Expired</span>
                <?php elseif($v->license_due_soon): ?> <span class="badge-amber ml-1">Due Soon</span>
                <?php endif; ?>
              <?php else: ?> —
              <?php endif; ?>
            </dd>
          </div>
          <div class="flex justify-between"><dt class="text-slate-400">Route</dt><dd><?php echo e($v->route?->route_name ?? '—'); ?></dd></div>
        </dl>

        
        <div x-data="{ pvOpen: false }" class="mt-3 border-t border-slate-100 pt-3">
          <button type="button" @click="pvOpen=!pvOpen" class="text-xs text-indigo-600 hover:underline">Update Police Verification</button>
          <form x-show="pvOpen" x-transition method="POST" action="<?php echo e(route('transport.drivers.police-verification', $v->id)); ?>" class="mt-2 space-y-2">
            <?php echo csrf_field(); ?>
            <select name="status" class="select text-xs py-1">
              <option value="pending"  <?php if(($v->police_verification_status ?? 'pending') === 'pending'): echo 'selected'; endif; ?>>Pending</option>
              <option value="verified" <?php if(($v->police_verification_status ?? '') === 'verified'): echo 'selected'; endif; ?>>Verified</option>
              <option value="expired"  <?php if(($v->police_verification_status ?? '') === 'expired'): echo 'selected'; endif; ?>>Expired</option>
            </select>
            <input type="date" name="verification_date" value="<?php echo e($v->police_verification_date); ?>" class="input text-xs py-1" placeholder="Verification date">
            <input type="text"  name="notes" value="<?php echo e($v->police_verification_notes); ?>" class="input text-xs py-1" placeholder="Notes">
            <button type="submit" class="btn btn-primary btn-xs">Save</button>
          </form>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="col-span-3 text-center py-10 text-slate-400">No driver records found.</div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\drivers.blade.php ENDPATH**/ ?>