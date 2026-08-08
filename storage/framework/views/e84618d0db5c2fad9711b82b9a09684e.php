<?php $__env->startSection('title','Fee Concessions'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Fee Concessions</h1>
    <button x-data @click="$dispatch('open-modal','add-concession')" class="btn btn-primary btn-sm">+ Grant Concession</button>
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Student','Class','Type','Value','Period','Scheme','Status','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $concessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($c->student?->full_name); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($c->student?->currentEnrollment?->class?->name); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs capitalize"><?php echo e(str_replace('_',' ',$c->concession_type)); ?></td>
          <td class="px-4 py-3 font-semibold text-indigo-700 text-xs">
            <?php echo e($c->value_type === 'percentage' ? $c->value.'%' : '₹'.number_format($c->value,0)); ?>

          </td>
          <td class="px-4 py-3 text-slate-400 text-xs">
            <?php echo e($c->valid_from?->format('d M Y') ?? '—'); ?> — <?php echo e($c->valid_to?->format('d M Y') ?? 'Ongoing'); ?>

          </td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($c->scheme?->name ?? '—'); ?></td>
          <td class="px-4 py-3"><span class="badge-<?php echo e($c->status === 'active' ? 'green' : 'slate'); ?> capitalize text-xs"><?php echo e($c->status); ?></span></td>
          <td class="px-4 py-3">
            <?php if($c->status === 'active'): ?>
            <form method="POST" action="<?php echo e(route('fees.concessions.delete',$c->id)); ?>" class="inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button type="submit" class="text-red-400 hover:underline text-xs">Revoke</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No concessions granted.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($concessions->hasPages()): ?><div class="px-4 pb-3"><?php echo e($concessions->links()); ?></div><?php endif; ?>
  </div>
</div>

<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='add-concession')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg">
    <h3 class="font-semibold text-slate-700 mb-4">Grant Concession</h3>
    <form method="POST" action="<?php echo e(route('fees.concessions.save')); ?>" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div><label class="label">Student <span class="text-red-500">*</span></label>
        <select name="student_id" class="select" required>
          <option value="">Select class first, then student</option>
        </select>
      </div>
      <div><label class="label">Concession Type <span class="text-red-500">*</span></label>
        <select name="concession_type" class="select" required>
          <?php $__currentLoopData = ['scholarship'=>'Scholarship','sibling_discount'=>'Sibling Discount','staff_ward'=>'Staff Ward','merit'=>'Merit','financial_aid'=>'Financial Aid','other'=>'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($k); ?>"><?php echo e($v); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Value Type</label>
          <select name="value_type" class="select">
            <option value="percentage">Percentage (%)</option>
            <option value="amount">Flat Amount (₹)</option>
          </select>
        </div>
        <div><label class="label">Value <span class="text-red-500">*</span></label>
          <input type="number" name="value" class="input" step="0.01" min="0" required>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Valid From</label><input type="date" name="valid_from" class="input"></div>
        <div><label class="label">Valid To</label><input type="date" name="valid_to" class="input"></div>
      </div>
      <div><label class="label">Remarks</label><input type="text" name="remarks" class="input"></div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Grant</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\concessions.blade.php ENDPATH**/ ?>