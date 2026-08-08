<?php $__env->startSection('title', 'Leave Encashment (EL)'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Leave Encashment</h1>
            <p class="page-subtitle">Process Earned Leave (EL) encashment for employees</p>
        </div>
        <a href="<?php echo e(route('hr.leaves')); ?>" class="btn btn-secondary">Back to Leaves</a>
    </div>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="card space-y-4">
            <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Calculate Encashment</h3>
            <form method="GET" action="<?php echo e(route('hr.leave-encashment')); ?>" class="space-y-4">
                <div>
                    <label class="label">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" class="select" required>
                        <option value="">Select Employee</option>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($emp->id); ?>" <?php if(request('employee_id') == $emp->id): echo 'selected'; endif; ?>>
                                <?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?> (<?php echo e($emp->employee_number); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="label">Leave Type <span class="text-red-500">*</span></label>
                    <select name="leave_type_id" class="select" required>
                        <option value="">Select Type</option>
                        <?php $__currentLoopData = $elTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($lt->id); ?>" <?php if(request('leave_type_id') == $lt->id): echo 'selected'; endif; ?>><?php echo e($lt->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Year</label>
                        <input type="number" name="year" class="input" value="<?php echo e(request('year', date('Y'))); ?>" min="2000" max="2099">
                    </div>
                    <div>
                        <label class="label">Days to Encash</label>
                        <input type="number" name="days" class="input" min="1" value="<?php echo e(request('days', 1)); ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary w-full">Calculate</button>
            </form>

            <?php if($calculation): ?>
            <div class="mt-4 p-4 bg-indigo-50 border border-indigo-200 rounded-lg space-y-2">
                <p class="text-sm font-semibold text-indigo-800">Calculation Result</p>
                <div class="text-sm text-slate-700 space-y-1">
                    <div class="flex justify-between"><span>Employee:</span><span class="font-medium"><?php echo e($calculation['emp']->full_name); ?></span></div>
                    <div class="flex justify-between"><span>Basic Salary / 26:</span><span class="font-medium">₹<?php echo e(number_format($calculation['basicPerDay'], 2)); ?></span></div>
                    <div class="flex justify-between"><span>EL Balance (<?php echo e($calculation['year']); ?>):</span><span class="font-medium"><?php echo e($calculation['balance']); ?> days</span></div>
                    <div class="flex justify-between"><span>Days Encashed:</span><span class="font-medium"><?php echo e($calculation['days']); ?> days</span></div>
                    <div class="flex justify-between border-t border-indigo-200 pt-2 mt-2">
                        <span class="font-semibold">Encashment Amount:</span>
                        <span class="font-bold text-indigo-700 text-base">₹<?php echo e(number_format($calculation['amount'], 2)); ?></span>
                    </div>
                </div>

                <form method="POST" action="<?php echo e(route('hr.leave-encashment.process')); ?>" class="mt-3 space-y-3 border-t border-indigo-200 pt-3">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="employee_id" value="<?php echo e($calculation['emp']->id); ?>">
                    <input type="hidden" name="leave_type_id" value="<?php echo e($calculation['lt']->id); ?>">
                    <input type="hidden" name="days_encashed" value="<?php echo e($calculation['days']); ?>">
                    <input type="hidden" name="year" value="<?php echo e($calculation['year']); ?>">
                    <div>
                        <label class="label">Encashment Date</label>
                        <input type="date" name="encashment_date" class="input" value="<?php echo e(date('Y-m-d')); ?>" required>
                    </div>
                    <div>
                        <label class="label">Remarks (Optional)</label>
                        <input type="text" name="remarks" class="input" placeholder="e.g. Annual EL encashment">
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Process Encashment</button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="card">
            <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100 mb-4">Encashment History</h3>
            <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
                <div>
                    <p class="text-sm font-medium text-slate-800"><?php echo e($enc->employee?->full_name ?? '—'); ?></p>
                    <p class="text-xs text-slate-400"><?php echo e($enc->leaveType?->name); ?> · <?php echo e($enc->days_encashed); ?> days · <?php echo e($enc->year); ?></p>
                    <p class="text-xs text-slate-400"><?php echo e($enc->encashment_date?->format('d M Y')); ?></p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-green-700 text-sm">₹<?php echo e(number_format($enc->amount, 2)); ?></p>
                    <?php if($enc->remarks): ?>
                        <p class="text-xs text-slate-400 max-w-[150px] text-right truncate"><?php echo e($enc->remarks); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-slate-400 text-sm text-center py-6">No encashments recorded yet.</p>
            <?php endif; ?>
            <?php echo e($history->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\leave-encashment.blade.php ENDPATH**/ ?>