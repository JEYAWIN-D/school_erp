<?php $__env->startSection('title', 'Concession Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Concession Report</h1>

  
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card text-center">
      <div class="text-xl font-bold text-slate-800"><?php echo e($s->count); ?></div>
      <div class="text-xs text-slate-500 mt-1 capitalize"><?php echo e(str_replace('_', ' ', $s->concession_type)); ?></div>
      <div class="text-sm font-semibold text-indigo-600 mt-1">₹<?php echo e(number_format($s->total_value, 2)); ?></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  
  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select w-36">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Concession Type</label>
        <select name="concession_type" class="select w-40">
          <option value="">All Types</option>
          <?php $__currentLoopData = ['percentage'=>'Percentage','flat'=>'Flat Amount','sibling'=>'Sibling','merit'=>'Merit','staff_ward'=>'Staff Ward','government'=>'Government']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($v); ?>" <?php if(request('concession_type')===$v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    </div>
  </form>

  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <?php $__currentLoopData = ['Student','Class','Concession Type','Value','Scheme','Valid From','Valid To','Granted By','Status']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $concessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($c->student?->full_name); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($c->student?->currentEnrollment?->class?->name ?? '—'); ?></td>
          <td class="px-4 py-3 capitalize text-slate-600"><?php echo e(str_replace('_', ' ', $c->concession_type)); ?></td>
          <td class="px-4 py-3 font-medium">
            <?php if($c->value_type === 'percentage'): ?>
              <?php echo e($c->value); ?>%
            <?php else: ?>
              ₹<?php echo e(number_format($c->value, 2)); ?>

            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($c->scheme?->name ?? '—'); ?></td>
          <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($c->valid_from ? \Carbon\Carbon::parse($c->valid_from)->format('d M Y') : '—'); ?></td>
          <td class="px-4 py-3 text-xs <?php if($c->valid_to && \Carbon\Carbon::parse($c->valid_to)->isPast()): ?> text-red-500 <?php else: ?> text-slate-500 <?php endif; ?>">
            <?php echo e($c->valid_to ? \Carbon\Carbon::parse($c->valid_to)->format('d M Y') : 'Ongoing'); ?>

          </td>
          <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($c->grantedBy?->name ?? '—'); ?></td>
          <td class="px-4 py-3"><span class="<?php echo e($c->status === 'active' ? 'badge-green' : 'badge-slate'); ?> capitalize"><?php echo e($c->status); ?></span></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No concessions found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($concessions->hasPages()): ?>
    <div class="px-4 pb-3 text-sm"><?php echo e($concessions->links()); ?></div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\concession-report.blade.php ENDPATH**/ ?>