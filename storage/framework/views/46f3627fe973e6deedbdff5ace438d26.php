<?php $__env->startSection('title', 'Hostel Fee Outstanding'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Hostel Fee Outstanding</h1>
      <p class="page-subtitle"><?php echo e($currentYear?->name); ?> — Students with pending hostel dues</p>
    </div>
    <a href="<?php echo e(route('hostel.index')); ?>" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Hostel</label>
        <select name="hostel_id" class="select">
          <option value="">All Hostels</option>
          <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hostel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($hostel->id); ?>" <?php echo e(request('hostel_id') == $hostel->id ? 'selected' : ''); ?>><?php echo e($hostel->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
  </div>

  <?php if($outstanding->isNotEmpty()): ?>
  <?php $totalBalance = $outstanding->sum('balance'); ?>
  <div class="grid grid-cols-2 gap-4">
    <div class="card text-center py-5"><p class="text-3xl font-bold text-red-600"><?php echo e($outstanding->count()); ?></p><p class="text-sm text-slate-500">Students with Dues</p></div>
    <div class="card text-center py-5"><p class="text-3xl font-bold text-red-600">₹<?php echo e(number_format($totalBalance, 2)); ?></p><p class="text-sm text-slate-500">Total Outstanding</p></div>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Student</th>
            <th class="th">Hostel / Room</th>
            <th class="th">Monthly Fee</th>
            <th class="th">Expected</th>
            <th class="th">Paid</th>
            <th class="th">Balance</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $outstanding; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td">
              <a href="<?php echo e(route('students.show', $row['allotment']->student_id)); ?>" class="font-medium text-indigo-600 hover:underline">
                <?php echo e($row['allotment']->student?->first_name); ?> <?php echo e($row['allotment']->student?->last_name); ?>

              </a>
            </td>
            <td class="td text-sm text-slate-500">
              <?php echo e($row['allotment']->room?->hostel?->name); ?> — Room <?php echo e($row['allotment']->room?->room_number); ?>

            </td>
            <td class="td">₹<?php echo e(number_format($row['allotment']->monthly_fee, 2)); ?></td>
            <td class="td">₹<?php echo e(number_format($row['totalExpected'], 2)); ?></td>
            <td class="td text-green-600">₹<?php echo e(number_format($row['totalPaid'], 2)); ?></td>
            <td class="td font-semibold text-red-600">₹<?php echo e(number_format($row['balance'], 2)); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php else: ?>
  <div class="card text-center py-12 text-slate-400">No outstanding hostel fees found.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\fee-outstanding.blade.php ENDPATH**/ ?>