<?php $__env->startSection('title','Installment Plans'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Fee Installment Plans</h1>
      <p class="page-subtitle">Define monthly, quarterly, half-yearly or annual payment schedules</p>
    </div>
    <button x-data @click="$dispatch('open-modal','add-plan')" class="btn btn-primary btn-sm">+ Create Plan</button>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
  <?php if(session('error')): ?><div class="alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <?php $__currentLoopData = ['Plan Name','Frequency','Class','Installments','Late Fee Rule','Status','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <th class="th"><?php echo e($h); ?></th>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td font-semibold text-slate-800">
            <?php echo e($plan->name); ?>

            <?php if($plan->description): ?>
              <p class="text-xs text-slate-400 font-normal mt-0.5"><?php echo e($plan->description); ?></p>
            <?php endif; ?>
          </td>
          <td class="td">
            <?php $freqColors = ['one_time'=>'slate','monthly'=>'indigo','quarterly'=>'green','half_yearly'=>'amber','annually'=>'purple']; ?>
            <span class="badge-<?php echo e($freqColors[$plan->frequency ?? 'one_time'] ?? 'slate'); ?>">
              <?php echo e($plan->frequency_label); ?>

            </span>
          </td>
          <td class="td text-slate-500 text-xs"><?php echo e($plan->class?->name ?? 'All Classes'); ?></td>
          <td class="td text-center">
            <span class="bg-indigo-50 text-indigo-700 font-semibold text-xs px-2 py-0.5 rounded-full">
              <?php echo e($plan->installments->count()); ?>

            </span>
          </td>
          <td class="td text-slate-400 text-xs"><?php echo e($plan->lateFeeRule?->name ?? '—'); ?></td>
          <td class="td">
            <span class="<?php echo e($plan->is_active ? 'badge-green' : 'badge-slate'); ?>">
              <?php echo e($plan->is_active ? 'Active' : 'Off'); ?>

            </span>
          </td>
          <td class="td">
            <div class="flex gap-2">
              <a href="<?php echo e(route('fees.installments.show', $plan->id)); ?>" class="text-indigo-600 hover:underline text-xs">View</a>
              <a href="<?php echo e(route('fees.installments.edit', $plan->id)); ?>" class="text-amber-600 hover:underline text-xs">Edit</a>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr class="tr">
          <td colspan="7" class="td text-center text-slate-400 py-8">
            No installment plans yet. Create one to define payment schedules.
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  
  <div class="card-flat p-4">
    <h3 class="text-xs font-semibold text-slate-600 uppercase tracking-wide mb-3">Frequency Guide</h3>
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-xs text-slate-600">
      <div class="bg-white rounded-lg p-2 border border-slate-200">
        <p class="font-semibold text-slate-700">One Time</p>
        <p class="text-slate-400 mt-0.5">Single lump-sum payment</p>
      </div>
      <div class="bg-white rounded-lg p-2 border border-indigo-200">
        <p class="font-semibold text-indigo-700">Monthly</p>
        <p class="text-slate-400 mt-0.5">12 payments per year</p>
      </div>
      <div class="bg-white rounded-lg p-2 border border-green-200">
        <p class="font-semibold text-green-700">Quarterly</p>
        <p class="text-slate-400 mt-0.5">4 payments per year</p>
      </div>
      <div class="bg-white rounded-lg p-2 border border-amber-200">
        <p class="font-semibold text-amber-700">Half-Yearly</p>
        <p class="text-slate-400 mt-0.5">2 payments per year</p>
      </div>
      <div class="bg-white rounded-lg p-2 border border-purple-200">
        <p class="font-semibold text-purple-700">Annually</p>
        <p class="text-slate-400 mt-0.5">1 payment per year</p>
      </div>
    </div>
  </div>
</div>


<div x-data="{ show: false, freq: 'quarterly', count: 4 }"
     x-on:open-modal.window="show = ($event.detail === 'add-plan')"
     x-show="show" style="display:none"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg" @click.away="show = false">
    <h3 class="font-semibold text-slate-700 mb-4 text-lg">Create Installment Plan</h3>
    <form method="POST" action="<?php echo e(route('fees.installments.store')); ?>" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div>
        <label class="label">Plan Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="input" required placeholder="e.g. Quarterly 2025-26">
      </div>
      <div>
        <label class="label">Description</label>
        <input type="text" name="description" class="input text-sm" placeholder="Optional notes about this plan">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="label">Frequency <span class="text-red-500">*</span></label>
          <select name="frequency" class="select" x-model="freq"
                  @change="count = {one_time:1,monthly:12,quarterly:4,half_yearly:2,annually:1}[freq] ?? 4">
            <option value="one_time">One Time</option>
            <option value="monthly">Monthly</option>
            <option value="quarterly" selected>Quarterly</option>
            <option value="half_yearly">Half-Yearly</option>
            <option value="annually">Annually</option>
          </select>
        </div>
        <div>
          <label class="label">Number of Installments <span class="text-red-500">*</span></label>
          <input type="number" name="count" class="input" :value="count" x-model="count" min="1" max="24" required>
        </div>
      </div>
      <div>
        <label class="label">First Installment Date <span class="text-red-500">*</span></label>
        <input type="date" name="start_date" class="input" required>
        <p class="text-xs text-slate-400 mt-0.5">Subsequent dates are auto-calculated from this date based on frequency.</p>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="label">Class (leave blank for all)</label>
          <select name="class_id" class="select">
            <option value="">All Classes</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Late Fee Rule</label>
          <select name="late_fee_rule_id" class="select">
            <option value="">None</option>
            <?php $__currentLoopData = $lateFeeRules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r->id); ?>"><?php echo e($r->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div class="flex gap-2 pt-2 border-t border-slate-100">
        <button type="submit" class="btn btn-primary btn-sm">Create & Generate Dates</button>
        <button type="button" @click="show = false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\installment-plans.blade.php ENDPATH**/ ?>