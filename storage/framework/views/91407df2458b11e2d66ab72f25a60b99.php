<?php $__env->startSection('title', 'Fee Revision'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <h1 class="page-title">Fee Revision</h1>
    <p class="text-sm text-slate-500">Update fee amounts for a class mid-year. Changes affect future invoices.</p>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-error"><?php echo e(session('error')); ?></div><?php endif; ?>

    
    <form method="GET" class="card py-3">
        <div class="flex gap-3">
            <select name="class_id" class="select w-48" onchange="this.form.submit()">
                <option value="">Select Class</option>
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cls->id); ?>" <?php if(request('class_id') == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </form>

    <?php if($structures->count()): ?>
    <form method="POST" action="<?php echo e(route('fees.revision.save')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="class_id" value="<?php echo e(request('class_id')); ?>">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 bg-amber-50 border-b border-amber-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/></svg>
                <span class="text-sm text-amber-700 font-medium">Revising fee amounts will update the fee structure. Existing payments are not affected.</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr>
                        <th class="th">Fee Head</th>
                        <th class="th text-right">Current Amount (₹)</th>
                        <th class="th text-right">Revised Amount (₹)</th>
                    </tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $structures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $structure): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="tr">
                            <td class="td font-medium"><?php echo e($structure->head?->name ?? 'N/A'); ?></td>
                            <td class="td text-right text-slate-500"><?php echo e(number_format($structure->amount, 2)); ?></td>
                            <td class="td text-right">
                                <input type="number" name="amounts[<?php echo e($structure->id); ?>]"
                                    value="<?php echo e($structure->amount); ?>" min="0" step="0.01"
                                    class="input text-right w-36">
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button type="submit" class="btn btn-primary">Save Revised Amounts</button>
        </div>
    </form>
    <?php elseif(request('class_id')): ?>
    <div class="card text-center py-10 text-slate-400">No fee structure found for this class in the current year.</div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\revision.blade.php ENDPATH**/ ?>