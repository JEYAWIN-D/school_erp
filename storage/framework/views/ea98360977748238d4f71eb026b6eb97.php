<?php $__env->startSection('title', 'Salary Advances'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Salary Advances</h1>
            <p class="page-subtitle">Record, approve, and track advance salary recovery</p>
        </div>
    </div>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

    
    <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">New Advance Request</h3>
        <form method="POST" action="<?php echo e(route('hr.salary-advances.store')); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="label">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" class="select" required>
                    <option value="">Select Employee</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?> (<?php echo e($emp->employee_number); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label">Amount (₹) <span class="text-red-500">*</span></label>
                <input type="number" name="amount" class="input" min="1" step="0.01" required placeholder="0.00">
            </div>
            <div>
                <label class="label">Advance Date <span class="text-red-500">*</span></label>
                <input type="date" name="advance_date" class="input" value="<?php echo e(date('Y-m-d')); ?>" required>
            </div>
            <div>
                <label class="label">Recovery Months <span class="text-red-500">*</span></label>
                <input type="number" name="recovery_months" class="input" min="1" max="24" value="3" required>
            </div>
            <div>
                <label class="label">Reason</label>
                <input type="text" name="reason" class="input" placeholder="Medical / Personal / Other">
            </div>
            <div>
                <label class="label">Remarks</label>
                <input type="text" name="remarks" class="input">
            </div>
            <div class="md:col-span-3">
                <button type="submit" class="btn btn-primary">Submit Advance Request</button>
            </div>
        </form>
    </div>

    
    <div class="card">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="label">Employee</label>
                <select name="employee_id" class="select">
                    <option value="">All Employees</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php if(request('employee_id') == $emp->id): echo 'selected'; endif; ?>>
                            <?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label">Status</label>
                <select name="status" class="select">
                    <option value="">All</option>
                    <option value="pending"   <?php if(request('status')=='pending'): echo 'selected'; endif; ?>>Pending</option>
                    <option value="approved"  <?php if(request('status')=='approved'): echo 'selected'; endif; ?>>Approved</option>
                    <option value="rejected"  <?php if(request('status')=='rejected'): echo 'selected'; endif; ?>>Rejected</option>
                    <option value="recovered" <?php if(request('status')=='recovered'): echo 'selected'; endif; ?>>Fully Recovered</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>

    
    <div class="card">
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Employee</th>
                    <th class="th">Amount</th>
                    <th class="th">Date</th>
                    <th class="th">Months</th>
                    <th class="th">Monthly Deduction</th>
                    <th class="th">Recovered</th>
                    <th class="th">Remaining</th>
                    <th class="th">Status</th>
                    <th class="th">Actions</th>
                </tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $advances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="tr">
                        <td class="td font-medium"><?php echo e($adv->employee?->first_name); ?> <?php echo e($adv->employee?->last_name); ?></td>
                        <td class="td">₹<?php echo e(number_format($adv->amount, 2)); ?></td>
                        <td class="td text-slate-500"><?php echo e($adv->advance_date->format('d M Y')); ?></td>
                        <td class="td text-center"><?php echo e($adv->recovery_months); ?></td>
                        <td class="td">₹<?php echo e(number_format($adv->monthly_deduction, 2)); ?></td>
                        <td class="td text-green-600">₹<?php echo e(number_format($adv->recovered_amount, 2)); ?></td>
                        <td class="td <?php echo e($adv->remaining > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                            ₹<?php echo e(number_format($adv->remaining, 2)); ?>

                        </td>
                        <td class="td">
                            <?php $badgeMap = ['pending'=>'badge-warning','approved'=>'badge-info','rejected'=>'badge-danger','recovered'=>'badge-success']; ?>
                            <span class="<?php echo e($badgeMap[$adv->status] ?? 'badge'); ?>"><?php echo e(ucfirst($adv->status)); ?></span>
                        </td>
                        <td class="td">
                            <div class="flex gap-1 flex-wrap">
                                <?php if($adv->status === 'pending'): ?>
                                <form method="POST" action="<?php echo e(route('hr.salary-advances.approve', $adv->id)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-primary btn-xs">Approve</button>
                                </form>
                                <form method="POST" action="<?php echo e(route('hr.salary-advances.reject', $adv->id)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-secondary btn-xs">Reject</button>
                                </form>
                                <?php elseif($adv->status === 'approved' && $adv->remaining > 0): ?>
                                <form method="POST" action="<?php echo e(route('hr.salary-advances.recovery', $adv->id)); ?>" class="inline flex gap-1 items-center">
                                    <?php echo csrf_field(); ?>
                                    <input type="number" name="recovery_amount" class="input w-24 text-sm py-1"
                                           placeholder="Amount" min="0.01" max="<?php echo e($adv->remaining); ?>" step="0.01">
                                    <button class="btn btn-success btn-xs">Record</button>
                                </form>
                                <?php else: ?>
                                <span class="text-slate-400 text-xs">—</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="9" class="td text-center text-slate-400 py-8">No salary advance records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($advances->hasPages()): ?>
        <div class="px-4 pb-4"><?php echo e($advances->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\salary-advances.blade.php ENDPATH**/ ?>