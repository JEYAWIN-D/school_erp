<?php $__env->startSection('title', 'Leave History'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('hr.employees.show', $employee->id)); ?>" class="text-blue-600 hover:underline">← <?php echo e($employee->full_name); ?></a>
        <span class="text-gray-400">/</span>
        <h1 class="page-title mb-0">Leave History</h1>
    </div>

    <div class="card bg-blue-50 border-blue-200">
        <div class="font-semibold text-blue-900"><?php echo e($employee->full_name); ?></div>
        <div class="text-sm text-blue-700"><?php echo e($employee->designation?->name); ?> — <?php echo e($employee->department?->name); ?></div>
    </div>

    <?php if($leaves->count()): ?>
    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Leave Type</th>
                        <th class="th">From</th>
                        <th class="th">To</th>
                        <th class="th text-center">Days</th>
                        <th class="th">Status</th>
                        <th class="th">Reason</th>
                        <th class="th">Applied On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="tr">
                        <td class="td font-medium"><?php echo e($leave->leaveType?->name ?? '—'); ?></td>
                        <td class="td"><?php echo e($leave->from_date?->format('d M Y')); ?></td>
                        <td class="td"><?php echo e($leave->to_date?->format('d M Y')); ?></td>
                        <td class="td text-center"><?php echo e($leave->days); ?></td>
                        <td class="td">
                            <span class="badge-<?php echo e($leave->status === 'approved' ? 'success' :
                                ($leave->status === 'rejected' ? 'danger' :
                                ($leave->status === 'cancelled' ? 'secondary' : 'warning'))); ?>"><?php echo e(ucfirst($leave->status)); ?></span>
                        </td>
                        <td class="td text-gray-600 text-sm max-w-xs truncate"><?php echo e($leave->reason); ?></td>
                        <td class="td text-sm text-gray-500"><?php echo e($leave->created_at->format('d M Y')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo e($leaves->links()); ?>

    <?php else: ?>
    <div class="alert-info">No leave records found for this employee.</div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\leave-history.blade.php ENDPATH**/ ?>