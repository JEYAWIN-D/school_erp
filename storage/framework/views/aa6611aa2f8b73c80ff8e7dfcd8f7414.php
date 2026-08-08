<?php $__env->startSection('title', 'Library Fine Defaulters'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <h1 class="page-title">Library Fine Defaulters</h1>

    <?php if($defaulters->count()): ?>
    <div class="card p-0 overflow-hidden">
        <div class="p-3 bg-red-50 border-b border-red-100 flex items-center gap-2">
            <span class="text-red-600 font-medium"><?php echo e($defaulters->total()); ?> records with unpaid fine</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Student</th>
                        <th class="th">Book</th>
                        <th class="th">Issue Date</th>
                        <th class="th">Due Date</th>
                        <th class="th">Return Date</th>
                        <th class="th text-right">Fine (₹)</th>
                        <th class="th text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $defaulters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="tr">
                        <td class="td">
                            <div class="font-medium text-gray-800"><?php echo e($issue->student?->full_name); ?></div>
                            <div class="text-xs text-gray-500">Adm# <?php echo e($issue->student?->admission_number); ?></div>
                        </td>
                        <td class="td">
                            <div class="font-medium"><?php echo e($issue->book?->title); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($issue->book?->author); ?></div>
                        </td>
                        <td class="td"><?php echo e($issue->issue_date?->format('d M Y')); ?></td>
                        <td class="td"><?php echo e($issue->due_date?->format('d M Y')); ?></td>
                        <td class="td"><?php echo e($issue->return_date?->format('d M Y') ?? '—'); ?></td>
                        <td class="td text-right font-semibold text-red-600">₹<?php echo e(number_format($issue->fine_amount, 2)); ?></td>
                        <td class="td text-center">
                            <form method="POST" action="<?php echo e(route('library.fine.waive', $issue->id)); ?>" class="inline"
                                  onsubmit="return confirm('Waive fine for this record?')">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="reason" value="Admin waiver">
                                <button class="text-xs text-blue-600 hover:underline">Waive</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo e($defaulters->links()); ?>

    <?php else: ?>
    <div class="alert-success">No outstanding library fines.</div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\fine-defaulters.blade.php ENDPATH**/ ?>