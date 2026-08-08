<?php $__env->startSection('title', 'Pending Cheque Clearances'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Pending Cheques</h1>
            <p class="page-subtitle">Mark cheques as cleared or bounced</p>
        </div>
        <a href="<?php echo e(route('fees.bank-reconciliation')); ?>" class="btn btn-secondary btn-sm">Bank Reconciliation →</a>
    </div>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

    <div class="card">
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Receipt No.</th>
                    <th class="th">Student</th>
                    <th class="th">Fee Head</th>
                    <th class="th">Amount</th>
                    <th class="th">Cheque No.</th>
                    <th class="th">Bank / Branch</th>
                    <th class="th">Cheque Date</th>
                    <th class="th">Action</th>
                </tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="tr" x-data="{bounceOpen:false}">
                        <td class="td font-mono text-blue-600"><?php echo e($p->receipt_number); ?></td>
                        <td class="td">
                            <p class="font-medium"><?php echo e($p->student?->first_name); ?> <?php echo e($p->student?->last_name); ?></p>
                            <p class="text-xs text-slate-400"><?php echo e($p->student?->admission_number); ?></p>
                        </td>
                        <td class="td"><?php echo e($p->feeHead?->name); ?></td>
                        <td class="td">₹<?php echo e(number_format($p->amount, 2)); ?></td>
                        <td class="td font-mono"><?php echo e($p->cheque_number); ?></td>
                        <td class="td text-slate-500"><?php echo e($p->cheque_bank); ?> / <?php echo e($p->cheque_branch); ?></td>
                        <td class="td"><?php echo e($p->cheque_date?->format('d M Y')); ?></td>
                        <td class="td">
                            <div class="flex gap-2 flex-wrap">
                                <form method="POST" action="<?php echo e(route('fees.cheque.clear', $p->id)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-success btn-xs">Cleared</button>
                                </form>
                                <button @click="bounceOpen=!bounceOpen" class="btn btn-danger btn-xs">Bounced</button>
                            </div>
                            <div x-show="bounceOpen" x-transition class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <form method="POST" action="<?php echo e(route('fees.cheque.bounce', $p->id)); ?>" class="space-y-2">
                                    <?php echo csrf_field(); ?>
                                    <input type="text" name="bounce_reason" class="input text-sm" placeholder="Bounce reason (e.g. Insufficient funds)">
                                    <input type="number" name="bounce_charge" class="input text-sm w-32" placeholder="Bounce charge ₹" min="0" step="0.01">
                                    <button type="submit" class="btn btn-danger btn-xs">Confirm Bounce</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="td text-center text-slate-400 py-8">No pending cheques.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\cheque-pending.blade.php ENDPATH**/ ?>