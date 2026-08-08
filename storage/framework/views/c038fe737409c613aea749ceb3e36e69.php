<?php $__env->startSection('title', 'Bulk Fee Assignment'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <h1 class="page-title">Bulk Fee Assignment</h1>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-error"><?php echo e(session('error')); ?></div><?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="card">
                <h2 class="font-semibold text-slate-700 mb-4">Assign Fee Structure</h2>
                <form method="POST" action="<?php echo e(route('fees.bulk-assign.process')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="space-y-4">
                        <div>
                            <label class="label">Class</label>
                            <select name="class_id" class="select" required>
                                <option value="">Select Class</option>
                                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cls->id); ?>"><?php echo e($cls->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-3 text-sm text-blue-700">
                            This will link the current academic year's fee structure to all active students in the selected class. Already-assigned fees are not duplicated.
                        </div>
                        <button type="submit" class="btn btn-primary w-full">Assign Fee Structure</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="card p-0 overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-100">
                    <p class="font-semibold text-slate-700">Fee Structures — <?php echo e($currentYear?->name); ?></p>
                </div>
                <?php if($structures->count()): ?>
                <div class="table-wrap">
                    <table>
                        <thead><tr>
                            <th class="th">Class</th>
                            <th class="th">Fee Heads</th>
                            <th class="th text-right">Total</th>
                        </tr></thead>
                        <tbody>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(isset($structures[$cls->id])): ?>
                                <tr class="tr">
                                    <td class="td font-semibold"><?php echo e($cls->name); ?></td>
                                    <td class="td text-sm text-slate-500">
                                        <?php echo e($structures[$cls->id]->pluck('head.name')->implode(', ')); ?>

                                    </td>
                                    <td class="td text-right font-semibold">
                                        ₹<?php echo e(number_format($structures[$cls->id]->sum('amount'), 2)); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-8 text-center text-slate-400">No fee structures defined for current year.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\bulk-assign.blade.php ENDPATH**/ ?>