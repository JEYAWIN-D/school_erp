<?php $__env->startSection('title', 'Audit Trail'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Audit Trail</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('system.roles')); ?>" class="btn-sm btn-secondary">Roles & Permissions</a>
      <a href="<?php echo e(route('system.security')); ?>" class="btn-sm btn-secondary">Security</a>
      <a href="<?php echo e(route('system.backup')); ?>" class="btn-sm btn-secondary">Backup</a>
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">User</label>
      <select name="user_id" class="select">
        <option value="">All Users</option>
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($u->id); ?>" <?php if(request('user_id')==$u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Action</label>
      <select name="action" class="select">
        <option value="">All Actions</option>
        <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($a); ?>" <?php if(request('action')===$a): echo 'selected'; endif; ?>><?php echo e($a); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div><label class="label">From</label><input type="date" name="from" value="<?php echo e(request('from')); ?>" class="input"></div>
    <div><label class="label">To</label><input type="date" name="to" value="<?php echo e(request('to')); ?>" class="input"></div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead><tr>
        <th class="th">Time</th>
        <th class="th">User</th>
        <th class="th">Action</th>
        <th class="th">Model</th>
        <th class="th">IP</th>
        <th class="th">Details</th>
      </tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr" x-data="{ open: false }">
          <td class="td text-xs text-slate-400"><?php echo e($log->created_at->format('d/m/Y H:i:s')); ?></td>
          <td class="td text-xs"><?php echo e($log->user?->name ?? 'System'); ?></td>
          <td class="td">
            <?php $ac=['login'=>'badge-blue','logout'=>'badge-slate','created'=>'badge-green','updated'=>'badge-amber','deleted'=>'badge-red']; ?>
            <span class="<?php echo e($ac[$log->action] ?? 'badge-slate'); ?> text-xs"><?php echo e($log->action); ?></span>
          </td>
          <td class="td text-xs text-slate-500">
            <?php if($log->model_type): ?>
              <?php echo e(class_basename($log->model_type)); ?> #<?php echo e($log->model_id); ?>

            <?php else: ?> — <?php endif; ?>
          </td>
          <td class="td font-mono text-xs"><?php echo e($log->ip_address ?? '—'); ?></td>
          <td class="td">
            <?php if($log->old_values || $log->new_values): ?>
            <button @click="open=!open" class="btn-xs btn-secondary">Changes</button>
            <?php endif; ?>
          </td>
        </tr>
        <?php if($log->old_values || $log->new_values): ?>
        <tr x-show="open" x-cloak class="bg-slate-50">
          <td colspan="6" class="px-6 py-3">
            <div class="grid grid-cols-2 gap-4 text-xs">
              <?php if($log->old_values): ?>
              <div>
                <p class="font-semibold text-slate-500 mb-1">Before:</p>
                <pre class="text-slate-600 whitespace-pre-wrap"><?php echo e(json_encode($log->old_values, JSON_PRETTY_PRINT)); ?></pre>
              </div>
              <?php endif; ?>
              <?php if($log->new_values): ?>
              <div>
                <p class="font-semibold text-slate-500 mb-1">After:</p>
                <pre class="text-slate-600 whitespace-pre-wrap"><?php echo e(json_encode($log->new_values, JSON_PRETTY_PRINT)); ?></pre>
              </div>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-center text-slate-400" colspan="6">No audit logs found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div><?php echo e($logs->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\system\audit-log.blade.php ENDPATH**/ ?>