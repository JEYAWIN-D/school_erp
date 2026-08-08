<?php $__env->startSection('title', 'Scholarship Renewal Reminders'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Scholarship Renewal Reminders</h1>
            <p class="page-subtitle">Concessions expiring soon or already expired</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <label class="label mb-0">Days ahead</label>
            <input type="number" name="days" value="<?php echo e($daysAhead); ?>" class="input w-20" min="1" max="365">
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>

    
    <div class="card">
        <h3 class="font-semibold text-amber-600 mb-4 pb-2 border-b border-amber-100">
            Expiring in ≤ <?php echo e($daysAhead); ?> days
            <span class="text-slate-400 font-normal text-sm">(<?php echo e($expiring->count()); ?> concessions)</span>
        </h3>
        <?php if($expiring->isEmpty()): ?>
            <p class="text-slate-400 text-sm text-center py-6">No concessions expiring in this window.</p>
        <?php else: ?>
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Student</th>
                    <th class="th">Scheme / Type</th>
                    <th class="th">Value</th>
                    <th class="th">Expiry Date</th>
                    <th class="th">Days Left</th>
                    <th class="th">Action</th>
                </tr></thead>
                <tbody>
                    <?php $__currentLoopData = $expiring; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $daysLeft = now()->diffInDays($c->valid_to, false); ?>
                    <tr class="tr">
                        <td class="td">
                            <p class="font-medium"><?php echo e($c->student?->first_name); ?> <?php echo e($c->student?->last_name); ?></p>
                            <p class="text-xs text-slate-400"><?php echo e($c->student?->admission_number); ?></p>
                        </td>
                        <td class="td"><?php echo e($c->scheme?->name ?? ucfirst($c->concession_type)); ?></td>
                        <td class="td">
                            <?php echo e($c->value_type === 'percentage' ? $c->value . '%' : '₹' . number_format($c->value, 2)); ?>

                        </td>
                        <td class="td"><?php echo e($c->valid_to?->format('d M Y')); ?></td>
                        <td class="td">
                            <span class="badge <?php echo e($daysLeft <= 7 ? 'badge-danger' : 'badge-warning'); ?>">
                                <?php echo e($daysLeft); ?> day<?php echo e($daysLeft != 1 ? 's' : ''); ?>

                            </span>
                        </td>
                        <td class="td">
                            <a href="<?php echo e(route('students.concessions', $c->student_id)); ?>" class="btn btn-primary btn-xs">Renew</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="card">
        <h3 class="font-semibold text-red-600 mb-4 pb-2 border-b border-red-100">
            Already Expired (still marked active)
            <span class="text-slate-400 font-normal text-sm">(<?php echo e($expired->count()); ?> concessions)</span>
        </h3>
        <?php if($expired->isEmpty()): ?>
            <p class="text-slate-400 text-sm text-center py-6">No expired active concessions found.</p>
        <?php else: ?>
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Student</th>
                    <th class="th">Scheme / Type</th>
                    <th class="th">Expiry Date</th>
                    <th class="th">Action</th>
                </tr></thead>
                <tbody>
                    <?php $__currentLoopData = $expired; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="tr">
                        <td class="td">
                            <p class="font-medium"><?php echo e($c->student?->first_name); ?> <?php echo e($c->student?->last_name); ?></p>
                            <p class="text-xs text-slate-400"><?php echo e($c->student?->admission_number); ?></p>
                        </td>
                        <td class="td"><?php echo e($c->scheme?->name ?? ucfirst($c->concession_type)); ?></td>
                        <td class="td text-red-500"><?php echo e($c->valid_to?->format('d M Y')); ?></td>
                        <td class="td">
                            <div class="flex gap-1">
                                <a href="<?php echo e(route('students.concessions', $c->student_id)); ?>" class="btn btn-primary btn-xs">Renew</a>
                                <form method="POST" action="<?php echo e(route('fees.concessions.delete', $c->id)); ?>" class="inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-secondary btn-xs" onclick="return confirm('Revoke this expired concession?')">Revoke</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\scholarship-renewals.blade.php ENDPATH**/ ?>