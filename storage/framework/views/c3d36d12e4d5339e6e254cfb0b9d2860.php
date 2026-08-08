<?php $__env->startSection('title', 'Security Settings'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Security Settings</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('system.audit-log')); ?>" class="btn-sm btn-secondary">Audit Log</a>
      <a href="<?php echo e(route('system.backup')); ?>" class="btn-sm btn-secondary">Backup</a>
    </div>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="card text-center">
      <p class="text-3xl font-bold text-rose-600"><?php echo e($failedLogins); ?></p>
      <p class="text-sm text-slate-500 mt-1">Failed Logins (24h)</p>
    </div>
    <div class="card text-center">
      <p class="text-3xl font-bold text-indigo-600"><?php echo e($whitelist->count()); ?></p>
      <p class="text-sm text-slate-500 mt-1">Whitelisted IPs</p>
    </div>
    <div class="card text-center">
      <p class="text-3xl font-bold text-emerald-600"><?php echo e($recentAttempts->where('success',true)->count()); ?></p>
      <p class="text-sm text-slate-500 mt-1">Successful Logins (50 recent)</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">IP Whitelist</h3>
      <form method="POST" action="<?php echo e(route('system.whitelist.add')); ?>" class="flex gap-2 mb-4">
        <?php echo csrf_field(); ?>
        <input type="text" name="ip_address" class="input flex-1" placeholder="e.g. 192.168.1.1" required>
        <input type="text" name="description" class="input flex-1" placeholder="Description">
        <button type="submit" class="btn-primary btn-sm">Add</button>
      </form>
      <?php if($whitelist->isEmpty()): ?>
        <p class="text-slate-400 text-sm">No IPs whitelisted. All IPs can access.</p>
      <?php else: ?>
      <div class="space-y-2">
        <?php $__currentLoopData = $whitelist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2">
          <div>
            <p class="font-mono text-sm font-medium"><?php echo e($ip->ip_address); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($ip->description ?? '—'); ?></p>
          </div>
          <form method="POST" action="<?php echo e(route('system.whitelist.remove', $ip->id)); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-xs btn-secondary text-rose-600">Remove</button>
          </form>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php endif; ?>
    </div>

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Recent Login Attempts</h3>
      <div class="space-y-1 max-h-80 overflow-y-auto">
        <?php $__currentLoopData = $recentAttempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex items-center justify-between text-xs py-1 border-b border-slate-100">
          <div>
            <p class="font-medium text-slate-700"><?php echo e($att->email); ?></p>
            <p class="text-slate-400"><?php echo e(\Carbon\Carbon::parse($att->attempted_at)->format('d M H:i')); ?> · <?php echo e($att->ip_address); ?></p>
          </div>
          <?php if($att->success): ?> <span class="badge-green">OK</span>
          <?php else: ?> <span class="badge-red">Failed</span> <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\system\security.blade.php ENDPATH**/ ?>