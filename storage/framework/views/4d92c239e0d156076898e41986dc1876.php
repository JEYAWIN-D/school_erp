<?php $__env->startSection('title', 'Visitor Blacklist'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Visitor Blacklist</h1>
    <a href="<?php echo e(route('gate.index')); ?>" class="btn-sm btn-secondary">← Gate</a>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Add to Blacklist</h3>
      <form method="POST" action="<?php echo e(route('gate.blacklist.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div><label class="label">Name <span class="text-red-500">*</span></label><input type="text" name="name" class="input" required></div>
        <div><label class="label">Phone</label><input type="tel" name="phone" class="input"></div>
        <div><label class="label">ID Number</label><input type="text" name="id_number" class="input"></div>
        <div><label class="label">Reason <span class="text-red-500">*</span></label><textarea name="reason" rows="2" class="input" required></textarea></div>
        <button type="submit" class="btn-primary w-full">Add to Blacklist</button>
      </form>
    </div>

    
    <div class="lg:col-span-2">
      <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input max-w-xs" placeholder="Search name, phone, or ID…">
        <button type="submit" class="btn-primary btn-sm">Search</button>
      </form>

      <div class="table-wrap">
        <table class="w-full text-sm">
          <thead><tr>
            <th class="th">Name</th>
            <th class="th">Phone</th>
            <th class="th">ID No.</th>
            <th class="th">Reason</th>
            <th class="th">Added By</th>
            <th class="th">Actions</th>
          </tr></thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="tr <?php echo e(!$b->is_active ? 'opacity-50' : ''); ?>">
              <td class="td font-medium"><?php echo e($b->name); ?></td>
              <td class="td"><?php echo e($b->phone ?? '—'); ?></td>
              <td class="td text-xs"><?php echo e($b->id_number ?? '—'); ?></td>
              <td class="td text-xs text-slate-500"><?php echo e(Str::limit($b->reason, 60)); ?></td>
              <td class="td text-xs"><?php echo e($b->addedBy?->name ?? '—'); ?></td>
              <td class="td">
                <?php if($b->is_active): ?>
                <form method="POST" action="<?php echo e(route('gate.blacklist.remove', $b->id)); ?>"
                      onsubmit="return confirm('Remove <?php echo e(addslashes($b->name)); ?> from blacklist?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                  <button type="submit" class="btn-xs btn-secondary text-rose-600">Remove</button>
                </form>
                <?php else: ?> <span class="badge-slate text-xs">Removed</span> <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td class="td text-center text-slate-400" colspan="6">No blacklist entries.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="mt-3"><?php echo e($list->withQueryString()->links()); ?></div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\gate\blacklist.blade.php ENDPATH**/ ?>