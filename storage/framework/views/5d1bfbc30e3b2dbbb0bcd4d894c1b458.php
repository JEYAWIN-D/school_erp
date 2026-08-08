<?php $__env->startSection('title','Statutory Challan Generation'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Statutory Challan Generation</h1>
    <?php if($challanData->count()): ?>
    <div class="flex gap-2">
      <a href="<?php echo e(route('hr.payroll.challan', array_merge(request()->all(), ['format' => 'pdf']))); ?>"
        class="btn btn-secondary btn-sm">Download PDF</a>
    </div>
    <?php endif; ?>
  </div>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Month</label>
        <input type="month" name="month" value="<?php echo e($month); ?>" class="input w-40">
      </div>
      <div>
        <label class="label text-xs">Challan Type</label>
        <select name="type" class="select w-32">
          <option value="pf"  <?php if($type === 'pf'): echo 'selected'; endif; ?>>PF (EPF)</option>
          <option value="esi" <?php if($type === 'esi'): echo 'selected'; endif; ?>>ESI</option>
          <option value="pt"  <?php if($type === 'pt'): echo 'selected'; endif; ?>>PT (Professional Tax)</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  <?php if($challanData->count()): ?>
  
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    <?php if($type === 'pf'): ?>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-blue-600">₹<?php echo e(number_format($totals['pf_employee'], 2)); ?></p>
      <p class="text-xs text-slate-400 mt-1">Employee PF (12%)</p>
    </div>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-indigo-600">₹<?php echo e(number_format($totals['pf_employer'], 2)); ?></p>
      <p class="text-xs text-slate-400 mt-1">Employer PF (12%)</p>
    </div>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-slate-800">₹<?php echo e(number_format($totals['pf_total'], 2)); ?></p>
      <p class="text-xs text-slate-400 mt-1">Total PF Contribution</p>
    </div>
    <?php elseif($type === 'esi'): ?>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-blue-600">₹<?php echo e(number_format($totals['esi_employee'], 2)); ?></p>
      <p class="text-xs text-slate-400 mt-1">Employee ESI (0.75%)</p>
    </div>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-indigo-600">₹<?php echo e(number_format($totals['esi_employer'], 2)); ?></p>
      <p class="text-xs text-slate-400 mt-1">Employer ESI (3.25%)</p>
    </div>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-slate-800">₹<?php echo e(number_format($totals['esi_total'], 2)); ?></p>
      <p class="text-xs text-slate-400 mt-1">Total ESI Contribution</p>
    </div>
    <?php else: ?>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-green-600">₹<?php echo e(number_format($totals['pt_total'], 2)); ?></p>
      <p class="text-xs text-slate-400 mt-1">Total PT Payable</p>
    </div>
    <?php endif; ?>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-slate-600"><?php echo e($challanData->count()); ?></p>
      <p class="text-xs text-slate-400 mt-1">Employees</p>
    </div>
  </div>

  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <?php if($type === 'pf'): ?>
          <?php $__currentLoopData = ['#','Employee','EMP ID','UAN','Basic','Emp PF (12%)','Employer PF (12%)','Total PF']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php elseif($type === 'esi'): ?>
          <?php $__currentLoopData = ['#','Employee','ESI Number','Gross','Emp ESI (0.75%)','Employer ESI (3.25%)','Total ESI']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
          <?php $__currentLoopData = ['#','Employee','Designation','Gross Salary','PT Amount']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__currentLoopData = $challanData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($i + 1); ?></td>
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800"><?php echo e($row['employee']?->full_name); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($row['employee']?->employee_number); ?></p>
          </td>
          <?php if($type === 'pf'): ?>
          <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($row['employee']?->employee_number); ?></td>
          <td class="px-4 py-3 text-xs text-slate-400"><?php echo e($row['employee']?->uan_number ?? '—'); ?></td>
          <td class="px-4 py-3">₹<?php echo e(number_format($row['record']->basic_salary ?? 0, 2)); ?></td>
          <td class="px-4 py-3 text-blue-600">₹<?php echo e(number_format($row['pf_employee'], 2)); ?></td>
          <td class="px-4 py-3 text-indigo-600">₹<?php echo e(number_format($row['pf_employer'], 2)); ?></td>
          <td class="px-4 py-3 font-semibold">₹<?php echo e(number_format($row['pf_total'], 2)); ?></td>
          <?php elseif($type === 'esi'): ?>
          <td class="px-4 py-3 text-xs text-slate-400"><?php echo e($row['employee']?->esi_number ?? '—'); ?></td>
          <td class="px-4 py-3">₹<?php echo e(number_format($row['record']->gross_salary ?? 0, 2)); ?></td>
          <td class="px-4 py-3 text-blue-600">₹<?php echo e(number_format($row['esi_employee'], 2)); ?></td>
          <td class="px-4 py-3 text-indigo-600">₹<?php echo e(number_format($row['esi_employer'], 2)); ?></td>
          <td class="px-4 py-3 font-semibold">₹<?php echo e(number_format($row['esi_total'], 2)); ?></td>
          <?php else: ?>
          <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($row['employee']?->designation); ?></td>
          <td class="px-4 py-3">₹<?php echo e(number_format($row['record']->gross_salary ?? 0, 2)); ?></td>
          <td class="px-4 py-3 font-semibold text-green-700">₹<?php echo e(number_format($row['pt'], 2)); ?></td>
          <?php endif; ?>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
        <tr class="bg-slate-100 font-bold">
          <td colspan="<?php echo e($type === 'pf' ? 5 : ($type === 'esi' ? 4 : 3)); ?>" class="px-4 py-3 text-right text-sm">Total</td>
          <?php if($type === 'pf'): ?>
          <td class="px-4 py-3 text-blue-700">₹<?php echo e(number_format($totals['pf_employee'], 2)); ?></td>
          <td class="px-4 py-3 text-indigo-700">₹<?php echo e(number_format($totals['pf_employer'], 2)); ?></td>
          <td class="px-4 py-3">₹<?php echo e(number_format($totals['pf_total'], 2)); ?></td>
          <?php elseif($type === 'esi'): ?>
          <td class="px-4 py-3 text-blue-700">₹<?php echo e(number_format($totals['esi_employee'], 2)); ?></td>
          <td class="px-4 py-3 text-indigo-700">₹<?php echo e(number_format($totals['esi_employer'], 2)); ?></td>
          <td class="px-4 py-3">₹<?php echo e(number_format($totals['esi_total'], 2)); ?></td>
          <?php else: ?>
          <td class="px-4 py-3 text-green-700">₹<?php echo e(number_format($totals['pt_total'], 2)); ?></td>
          <?php endif; ?>
        </tr>
      </tbody>
    </table>
  </div>
  <?php elseif(request('month')): ?>
  <div class="card text-center py-10 text-slate-400">No approved payroll records found for <?php echo e($month); ?>.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\challan.blade.php ENDPATH**/ ?>