<?php $__env->startSection('title','Mess Rebate Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Mess Rebate for Absentees</h1>
      <p class="page-subtitle">Grant mess fee rebates to students who were absent for a period</p>
    </div>
    <div class="flex gap-2">
      <a href="<?php echo e(route('hostel.mess-attendance-summary')); ?>" class="btn btn-secondary btn-sm">Attendance Summary</a>
      <a href="<?php echo e(route('hostel.index')); ?>" class="btn btn-secondary btn-sm">← Hostel</a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Grant New Rebate</h3>
      <form method="POST" action="<?php echo e(route('hostel.mess-rebates.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">Student (Hostel Resident) <span class="text-red-500">*</span></label>
          <select name="allotment_id" class="select" required>
            <option value="">Select student</option>
            <?php $__currentLoopData = $allotments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($a->id); ?>"><?php echo e($a->student?->full_name); ?> — <?php echo e($a->room?->room_number); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label">Absent From <span class="text-red-500">*</span></label>
            <input type="date" name="from_date" class="input" required>
          </div>
          <div>
            <label class="label">Absent To <span class="text-red-500">*</span></label>
            <input type="date" name="to_date" class="input" required>
          </div>
        </div>
        <div>
          <label class="label">Rebate per Day (₹) <span class="text-red-500">*</span></label>
          <input type="number" name="rebate_per_day" class="input" step="0.50" min="0" placeholder="e.g. 80" required>
        </div>
        <div>
          <label class="label">Reason / Remarks</label>
          <input type="text" name="reason" class="input" placeholder="e.g. Medical leave, home visit...">
        </div>
        <button type="submit" class="btn btn-primary w-full">Create Rebate</button>
      </form>
    </div>

    
    <div class="lg:col-span-2">
      <div class="card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-slate-700">Rebate Records</h3>
          <form method="GET" class="flex gap-2">
            <select name="status" class="select text-sm py-1 w-36" onchange="this.form.submit()">
              <option value="">All Status</option>
              <option value="pending" <?php if(request('status')==='pending'): echo 'selected'; endif; ?>>Pending</option>
              <option value="approved" <?php if(request('status')==='approved'): echo 'selected'; endif; ?>>Approved</option>
              <option value="applied" <?php if(request('status')==='applied'): echo 'selected'; endif; ?>>Applied</option>
            </select>
          </form>
        </div>

        <?php if($rebates->isEmpty()): ?>
          <p class="text-center py-8 text-slate-400">No rebate records yet.</p>
        <?php else: ?>
        <div class="table-wrap">
          <table class="w-full text-sm">
            <thead><tr>
              <th class="th">Student</th>
              <th class="th">Room</th>
              <th class="th">Period</th>
              <th class="th">Days</th>
              <th class="th">Per Day</th>
              <th class="th">Total Rebate</th>
              <th class="th">Reason</th>
              <th class="th">Status</th>
              <th class="th"></th>
            </tr></thead>
            <tbody>
              <?php $__currentLoopData = $rebates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr class="tr">
                <td class="td font-medium"><?php echo e($r->allotment?->student?->full_name ?? '—'); ?></td>
                <td class="td text-xs"><?php echo e($r->allotment?->room?->room_number ?? '—'); ?></td>
                <td class="td text-xs text-slate-500">
                  <?php echo e($r->from_date?->format('d M')); ?> – <?php echo e($r->to_date?->format('d M Y')); ?>

                </td>
                <td class="td text-center font-semibold"><?php echo e($r->days_absent); ?></td>
                <td class="td text-right">₹<?php echo e(number_format($r->rebate_per_day, 0)); ?></td>
                <td class="td text-right font-semibold text-green-700">₹<?php echo e(number_format($r->total_rebate, 0)); ?></td>
                <td class="td text-xs text-slate-500"><?php echo e($r->reason ?? '—'); ?></td>
                <td class="td">
                  <span class="badge-<?php echo e($r->status==='approved'?'green':($r->status==='applied'?'indigo':'amber')); ?> text-xs capitalize">
                    <?php echo e($r->status); ?>

                  </span>
                </td>
                <td class="td">
                  <div class="flex gap-1">
                    <?php if($r->status === 'pending'): ?>
                    <form method="POST" action="<?php echo e(route('hostel.mess-rebates.approve', $r->id)); ?>">
                      <?php echo csrf_field(); ?>
                      <button class="btn btn-secondary btn-xs text-green-600">Approve</button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo e(route('hostel.mess-rebates.delete', $r->id)); ?>" onsubmit="return confirm('Delete this rebate?')">
                      <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                      <button class="btn btn-secondary btn-xs text-red-400">Del</button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
        <?php if($rebates->hasPages()): ?><div class="p-4"><?php echo e($rebates->links()); ?></div><?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\mess-rebates.blade.php ENDPATH**/ ?>