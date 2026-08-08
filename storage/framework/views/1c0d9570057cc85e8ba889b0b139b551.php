<?php $__env->startSection('title', 'Fee Balance Carry-Forwards'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Fee Balance Carry-Forwards</h1>
            <p class="page-subtitle">Roll outstanding fee balances to the next academic year</p>
        </div>
    </div>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

    
    <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Record New Carry-Forward</h3>
        <form method="POST" action="<?php echo e(route('fees.carry-forwards.store')); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="label">Student Admission No.</label>
                <input type="text" name="student_id" id="student_search" class="input" placeholder="Type to search…" autocomplete="off">
                <input type="hidden" name="student_id" id="student_id_hidden">
                <p class="text-xs text-slate-400 mt-1">Enter admission number or name and select</p>
            </div>
            <div>
                <label class="label">From Academic Year</label>
                <select name="from_academic_year_id" class="select" required>
                    <option value="">Select</option>
                    <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($y->id); ?>"><?php echo e($y->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label">To Academic Year</label>
                <select name="to_academic_year_id" class="select" required>
                    <option value="">Select</option>
                    <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($y->id); ?>" <?php if($currentYear && $y->id === $currentYear->id): echo 'selected'; endif; ?>><?php echo e($y->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label">Outstanding Amount (₹)</label>
                <input type="number" name="outstanding_amount" class="input" min="0.01" step="0.01" required>
            </div>
            <div>
                <label class="label">Student ID (numeric)</label>
                <input type="number" name="student_id" class="input" min="1" required placeholder="Student DB ID">
            </div>
            <div>
                <label class="label">Note</label>
                <input type="text" name="note" class="input" placeholder="e.g. Previous year dues">
            </div>
            <div class="md:col-span-3">
                <button type="submit" class="btn btn-primary">Record Carry-Forward</button>
            </div>
        </form>
    </div>

    
    <form method="GET" class="card flex flex-wrap gap-4 items-end">
        <div>
            <label class="label">To Academic Year</label>
            <select name="to_year_id" class="select">
                <option value="">All Years</option>
                <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($y->id); ?>" <?php if(request('to_year_id') == $y->id): echo 'selected'; endif; ?>><?php echo e($y->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Filter</button>
    </form>

    
    <div class="card">
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Student</th>
                    <th class="th">From Year</th>
                    <th class="th">To Year</th>
                    <th class="th">Outstanding</th>
                    <th class="th">Recovered</th>
                    <th class="th">Remaining</th>
                    <th class="th">Note</th>
                    <th class="th">Action</th>
                </tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $carryForwards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="tr">
                        <td class="td">
                            <p class="font-medium"><?php echo e($cf->student?->first_name); ?> <?php echo e($cf->student?->last_name); ?></p>
                            <p class="text-xs text-slate-400"><?php echo e($cf->student?->admission_number); ?></p>
                        </td>
                        <td class="td text-slate-500"><?php echo e($cf->fromYear?->name); ?></td>
                        <td class="td text-slate-500"><?php echo e($cf->toYear?->name); ?></td>
                        <td class="td">₹<?php echo e(number_format($cf->outstanding_amount, 2)); ?></td>
                        <td class="td text-green-600">₹<?php echo e(number_format($cf->recovered_amount, 2)); ?></td>
                        <td class="td <?php echo e($cf->remaining > 0 ? 'text-red-600 font-semibold' : 'text-green-600'); ?>">
                            ₹<?php echo e(number_format($cf->remaining, 2)); ?>

                        </td>
                        <td class="td text-slate-400 text-xs"><?php echo e($cf->note ?? '—'); ?></td>
                        <td class="td">
                            <?php if($cf->remaining > 0): ?>
                            <form method="POST" action="<?php echo e(route('fees.carry-forwards.recovery', $cf->id)); ?>" class="flex gap-1 items-center">
                                <?php echo csrf_field(); ?>
                                <input type="number" name="amount" class="input w-24 text-sm py-1"
                                       placeholder="₹" min="0.01" max="<?php echo e($cf->remaining); ?>" step="0.01">
                                <button class="btn btn-success btn-xs">Record</button>
                            </form>
                            <?php else: ?>
                                <span class="badge badge-success text-xs">Cleared</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="td text-center text-slate-400 py-8">No carry-forward records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($carryForwards->hasPages()): ?>
        <div class="px-4 pb-4"><?php echo e($carryForwards->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\carry-forwards.blade.php ENDPATH**/ ?>