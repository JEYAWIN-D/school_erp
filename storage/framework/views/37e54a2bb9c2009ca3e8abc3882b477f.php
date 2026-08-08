<?php $__env->startSection('title','Employee Loan Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Employee Loans</h1>
    <button x-data @click="$dispatch('open-modal','add-loan')" class="btn btn-primary btn-sm">+ New Loan</button>
  </div>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Employee</label>
        <select name="employee_id" class="select w-44">
          <option value="">All Employees</option>
          <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($emp->id); ?>" <?php if(request('employee_id')==$emp->id): echo 'selected'; endif; ?>><?php echo e($emp->full_name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Status</label>
        <select name="status" class="select w-32">
          <option value="">All</option>
          <option value="active" <?php if(request('status')==='active'): echo 'selected'; endif; ?>>Active</option>
          <option value="closed" <?php if(request('status')==='closed'): echo 'selected'; endif; ?>>Closed</option>
          <option value="paused" <?php if(request('status')==='paused'): echo 'selected'; endif; ?>>Paused</option>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </div>
  </form>

  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <?php $__currentLoopData = ['Employee','Type','Principal','EMI','Paid','Outstanding','Status','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50" x-data="{payment:false}">
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800"><?php echo e($loan->employee?->full_name); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($loan->employee?->department?->name); ?></p>
          </td>
          <td class="px-4 py-3 capitalize text-slate-600"><?php echo e(str_replace('_',' ',$loan->loan_type)); ?></td>
          <td class="px-4 py-3">₹<?php echo e(number_format($loan->principal_amount, 0)); ?></td>
          <td class="px-4 py-3">
            ₹<?php echo e(number_format($loan->emi_amount, 0)); ?>/mo
            <div class="text-xs text-slate-400"><?php echo e($loan->emi_months); ?> months</div>
          </td>
          <td class="px-4 py-3 text-green-600">₹<?php echo e(number_format($loan->total_paid, 0)); ?></td>
          <td class="px-4 py-3">
            <span class="<?php echo e($loan->outstanding_balance > 0 ? 'text-red-600 font-semibold' : 'text-slate-400'); ?>">
              ₹<?php echo e(number_format($loan->outstanding_balance, 0)); ?>

            </span>
            <?php if($loan->principal_amount > 0): ?>
            <div class="w-24 h-1.5 bg-slate-200 rounded mt-1">
              <div class="h-1.5 bg-green-500 rounded" style="width:<?php echo e(min(100, round($loan->total_paid/$loan->principal_amount*100))); ?>%"></div>
            </div>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3">
            <span class="badge-<?php echo e($loan->status==='active' ? 'green' : ($loan->status==='closed' ? 'slate' : 'amber')); ?> text-xs capitalize">
              <?php echo e($loan->status); ?>

            </span>
          </td>
          <td class="px-4 py-3">
            <div class="flex flex-wrap gap-1">
              <?php if($loan->status === 'active'): ?>
              <button @click="payment=!payment" class="btn btn-secondary btn-xs">Record Payment</button>
              <form method="POST" action="<?php echo e(route('hr.loans.close', $loan->id)); ?>" class="inline" onsubmit="return confirm('Mark loan as closed?')">
                <?php echo csrf_field(); ?> <button class="btn btn-ghost btn-xs text-red-500">Close</button>
              </form>
              <?php endif; ?>
            </div>
            <div x-show="payment" x-transition class="mt-2">
              <form method="POST" action="<?php echo e(route('hr.loans.payment', $loan->id)); ?>" class="flex gap-2 items-center">
                <?php echo csrf_field(); ?>
                <input type="number" name="amount" class="input py-1 w-28 text-sm" placeholder="Amount" min="1" max="<?php echo e($loan->outstanding_balance); ?>" step="0.01">
                <button type="submit" class="btn btn-primary btn-xs">Pay</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No loans found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($loans->hasPages()): ?>
    <div class="px-4 pb-3"><?php echo e($loans->links()); ?></div>
    <?php endif; ?>
  </div>
</div>


<div x-data="{open:false}" @open-modal.window="if($event.detail==='add-loan')open=true"
  x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
  <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
  <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6 z-10 space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="font-semibold text-slate-800">New Employee Loan</h3>
      <button @click="open=false" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
    </div>
    <form method="POST" action="<?php echo e(route('hr.loans.store')); ?>" class="space-y-4">
      <?php echo csrf_field(); ?>
      <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
          <label class="label">Employee <span class="text-red-500">*</span></label>
          <select name="employee_id" class="select" required>
            <option value="">Select Employee</option>
            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->full_name); ?> — <?php echo e($emp->department?->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Loan Type</label>
          <select name="loan_type" class="select">
            <?php $__currentLoopData = ['personal'=>'Personal','vehicle'=>'Vehicle','house'=>'House','education'=>'Education','emergency'=>'Emergency']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>"><?php echo e($v); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Principal Amount <span class="text-red-500">*</span></label>
          <input type="number" name="principal_amount" class="input" min="1" step="0.01" required>
        </div>
        <div>
          <label class="label">Interest Rate % (annual)</label>
          <input type="number" name="interest_rate" class="input" min="0" max="100" step="0.01" placeholder="0 = interest-free">
        </div>
        <div>
          <label class="label">EMI Months <span class="text-red-500">*</span></label>
          <input type="number" name="emi_months" class="input" min="1" required placeholder="e.g. 12">
        </div>
        <div>
          <label class="label">Disbursement Date</label>
          <input type="date" name="disbursement_date" class="input" value="<?php echo e(today()->toDateString()); ?>">
        </div>
        <div>
          <label class="label">First EMI Month</label>
          <input type="date" name="emi_start_month" class="input" value="<?php echo e(today()->addMonth()->startOfMonth()->toDateString()); ?>">
        </div>
        <div class="col-span-2">
          <label class="label">Purpose</label>
          <input type="text" name="purpose" class="input" placeholder="Reason for loan">
        </div>
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" @click="open=false" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Loan</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\loans.blade.php ENDPATH**/ ?>