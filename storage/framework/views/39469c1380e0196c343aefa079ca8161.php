<?php $__env->startSection('title', 'Database Backup'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Database Backup</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('system.audit-log')); ?>" class="btn-sm btn-secondary">Audit Log</a>
      <a href="<?php echo e(route('system.security')); ?>" class="btn-sm btn-secondary">Security</a>
    </div>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>
  <?php if(session('error')): ?> <div class="alert-danger"><?php echo e(session('error')); ?></div> <?php endif; ?>

  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-3">Create New Backup</h3>
    <p class="text-sm text-slate-500 mb-4">
      Triggers a MySQL dump of the database. Requires <code>mysqldump</code> to be available on the server PATH.
    </p>
    <form method="POST" action="<?php echo e(route('system.backup.trigger')); ?>">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn-primary" onclick="return confirm('Create database backup now?')">
        💾 Backup Database Now
      </button>
    </form>
  </div>

  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">Existing Backup Files</h3>
    <?php if($files->isEmpty()): ?>
      <p class="text-slate-400 text-sm">No backup files found in <code>storage/app/backups/</code>.</p>
    <?php else: ?>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr><th class="th">Filename</th><th class="th">Size</th><th class="th">Date</th></tr></thead>
        <tbody>
          <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td font-mono text-xs"><?php echo e($f['name']); ?></td>
            <td class="td text-xs"><?php echo e($f['size']); ?></td>
            <td class="td text-xs"><?php echo e($f['date']); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\system\backup.blade.php ENDPATH**/ ?>