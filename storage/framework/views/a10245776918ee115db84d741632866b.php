<?php $__env->startSection('title', 'TC Register'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Transfer Certificate Register</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('dashboard')); ?>" class="btn-sm btn-secondary">← Dashboard</a>
      <button onclick="window.print()" class="btn-sm btn-primary">🖨 Print</button>
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">From Date</label>
      <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="input">
    </div>
    <div>
      <label class="label">To Date</label>
      <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="input">
    </div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="<?php echo e(route('reports.tc-register')); ?>" class="btn-sm btn-secondary">Reset</a>
    <span class="text-sm text-slate-500 ml-auto self-center">Total: <strong><?php echo e($students->total()); ?></strong> TCs</span>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">TC No.</th>
          <th class="th">Adm. No.</th>
          <th class="th">Student Name</th>
          <th class="th">Father</th>
          <th class="th">DOB</th>
          <th class="th">Gender</th>
          <th class="th">Last Class</th>
          <th class="th">Leaving Date</th>
          <th class="th">Issue Date</th>
          <th class="th">Reason</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td font-mono font-semibold"><?php echo e($s->tc_number); ?></td>
          <td class="td font-mono text-xs"><?php echo e($s->admission_number); ?></td>
          <td class="td font-medium"><?php echo e($s->first_name); ?> <?php echo e($s->last_name); ?></td>
          <td class="td"><?php echo e($s->father_name ?? '—'); ?></td>
          <td class="td"><?php echo e($s->dob ? \Carbon\Carbon::parse($s->dob)->format('d/m/Y') : '—'); ?></td>
          <td class="td capitalize"><?php echo e($s->gender); ?></td>
          <td class="td"><?php echo e($s->last_class ?? '—'); ?></td>
          <td class="td"><?php echo e($s->leaving_date ? \Carbon\Carbon::parse($s->leaving_date)->format('d/m/Y') : '—'); ?></td>
          <td class="td"><?php echo e($s->issue_date ? \Carbon\Carbon::parse($s->issue_date)->format('d/m/Y') : '—'); ?></td>
          <td class="td text-xs text-slate-500"><?php echo e($s->reason ?? '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-slate-400 text-center" colspan="10">No TC records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-4"><?php echo e($students->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\reports\tc-register.blade.php ENDPATH**/ ?>