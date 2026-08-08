<?php $__env->startSection('title','Organisation Chart'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Reporting Hierarchy / Org Chart</h1>
      <p class="page-subtitle">View the management chain and set reporting managers for employees</p>
    </div>
    <a href="<?php echo e(route('hr.employees')); ?>" class="btn btn-secondary btn-sm">← Employees</a>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
  <?php if(session('error')): ?><div class="alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

  
  <div class="card" x-data="{ open: false }">
    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
      <h2 class="font-semibold text-slate-700">Assign Reporting Managers</h2>
      <svg class="w-5 h-5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </div>
    <div x-show="open" x-transition class="mt-4 overflow-x-auto">
      <table class="table-wrap w-full text-sm">
        <thead>
          <tr>
            <th class="th">Employee</th>
            <th class="th">Designation</th>
            <th class="th">Department</th>
            <th class="th">Current Manager</th>
            <th class="th">Change Manager</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $allEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td font-medium text-slate-800"><?php echo e($emp->full_name); ?></td>
            <td class="td text-slate-500 text-xs"><?php echo e($emp->designation?->name ?? '—'); ?></td>
            <td class="td text-slate-500 text-xs"><?php echo e($emp->department?->name ?? '—'); ?></td>
            <td class="td text-slate-600"><?php echo e($emp->manager?->full_name ?? '—'); ?></td>
            <td class="td">
              <form method="POST" action="<?php echo e(route('hr.employees.set-manager', $emp->id)); ?>" class="flex gap-2 items-center">
                <?php echo csrf_field(); ?>
                <select name="manager_id" class="select text-xs w-44">
                  <option value="">— No Manager —</option>
                  <?php $__currentLoopData = $allEmployees->where('id', '!=', $emp->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mgr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($mgr->id); ?>" <?php if($emp->manager_id == $mgr->id): echo 'selected'; endif; ?>>
                      <?php echo e($mgr->full_name); ?> (<?php echo e($mgr->designation?->name ?? '—'); ?>)
                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="btn-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded px-2 py-0.5 text-xs whitespace-nowrap">Set</button>
              </form>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>

  
  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-5 pb-2 border-b border-slate-100">Organisational Chart</h2>

    <?php
      function renderOrgNode($employee, $allEmployees, $depth = 0): string {
          $reports = $allEmployees->where('manager_id', $employee->id);
          $indent  = $depth * 24;
          $html = '<div class="flex items-start gap-0 mt-3" style="margin-left:' . $indent . 'px">';
          if ($depth > 0) {
              $html .= '<div class="flex flex-col items-center mr-2 pt-1"><div class="w-px h-4 bg-slate-300"></div><div class="w-3 h-px bg-slate-300"></div></div>';
          }
          $html .= '<div class="rounded-xl border border-slate-200 bg-white shadow-sm px-3 py-2 min-w-0" style="max-width:220px">';
          $html .= '<p class="font-semibold text-slate-800 text-sm truncate">' . e($employee->full_name) . '</p>';
          $html .= '<p class="text-xs text-indigo-600 truncate">' . e($employee->designation?->name ?? '—') . '</p>';
          $html .= '<p class="text-xs text-slate-400 truncate">' . e($employee->department?->name ?? '—') . '</p>';
          if ($reports->isNotEmpty()) {
              $html .= '<p class="text-xs text-slate-400 mt-1">' . $reports->count() . ' direct report' . ($reports->count() !== 1 ? 's' : '') . '</p>';
          }
          $html .= '</div></div>';
          foreach ($reports as $report) {
              $html .= renderOrgNode($report, $allEmployees, $depth + 1);
          }
          return $html;
      }
    ?>

    <?php if($roots->isEmpty()): ?>
      <p class="text-slate-400 text-sm text-center py-8">No employees found. Add employees and assign managers to build the org chart.</p>
    <?php else: ?>
    <div class="overflow-x-auto">
      <?php $__currentLoopData = $roots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $root): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo renderOrgNode($root, $allEmployees); ?>

      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
  </div>

  
  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Department-wise Hierarchy</h2>
    <div class="space-y-4">
      <?php $__empty_1 = true; $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="border border-slate-200 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold text-slate-800"><?php echo e($dept->name); ?></h3>
          <span class="text-xs text-slate-400"><?php echo e($dept->employees->count()); ?> staff</span>
        </div>
        <?php if($dept->employees->isEmpty()): ?>
          <p class="text-slate-400 text-xs">No active employees in this department.</p>
        <?php else: ?>
        <div class="space-y-2">
          <?php $__currentLoopData = $dept->employees->sortBy('designation.grade'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="flex items-center gap-3 text-sm">
            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0">
              <?php echo e(strtoupper(substr($emp->first_name, 0, 1))); ?><?php echo e(strtoupper(substr($emp->last_name, 0, 1))); ?>

            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-slate-800 truncate"><?php echo e($emp->full_name); ?></p>
              <p class="text-xs text-slate-400"><?php echo e($emp->designation?->name ?? '—'); ?>

                <?php if($emp->manager): ?>
                  · Reports to: <span class="text-indigo-600"><?php echo e($emp->manager->full_name); ?></span>
                <?php endif; ?>
                <?php if($emp->directReports->isNotEmpty()): ?>
                  · <span class="text-green-600"><?php echo e($emp->directReports->count()); ?> direct report<?php echo e($emp->directReports->count() !== 1 ? 's' : ''); ?></span>
                <?php endif; ?>
              </p>
            </div>
            <?php if($emp->designation?->pay_band_min): ?>
            <div class="text-right shrink-0">
              <p class="text-xs text-slate-400">Pay Band</p>
              <p class="text-xs font-mono text-slate-600">
                ₹<?php echo e(number_format($emp->designation->pay_band_min/1000, 0)); ?>k–<?php echo e(number_format($emp->designation->pay_band_max/1000, 0)); ?>k
              </p>
            </div>
            <?php endif; ?>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="text-slate-400 text-sm text-center py-4">No departments configured.</p>
      <?php endif; ?>
    </div>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\org-chart.blade.php ENDPATH**/ ?>