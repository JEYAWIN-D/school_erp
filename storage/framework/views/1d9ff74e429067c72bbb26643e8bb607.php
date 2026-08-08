<?php $__env->startSection('title', 'Library Settings'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">
    <h1 class="page-title">Library Settings</h1>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-error"><?php echo e(session('error')); ?></div><?php endif; ?>

    <form method="POST" action="<?php echo e(route('library.settings.save')); ?>">
        <?php echo csrf_field(); ?>
        <?php $__currentLoopData = ['student' => $student, 'staff' => $staff]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card mb-4">
            <h2 class="font-semibold text-slate-700 mb-4 capitalize"><?php echo e($type); ?> Settings</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Fine Per Day (₹)</label>
                    <input type="number" name="types[<?php echo e($type); ?>][fine_per_day]" step="0.01" min="0"
                        value="<?php echo e($setting->fine_per_day); ?>" class="input">
                </div>
                <div>
                    <label class="label">Loan Period (days)</label>
                    <input type="number" name="types[<?php echo e($type); ?>][loan_days]" min="1"
                        value="<?php echo e($setting->loan_days); ?>" class="input">
                </div>
                <div>
                    <label class="label">Max Books at a Time</label>
                    <input type="number" name="types[<?php echo e($type); ?>][max_books]" min="1"
                        value="<?php echo e($setting->max_books); ?>" class="input">
                </div>
                <div>
                    <label class="label">Max Renewals</label>
                    <input type="number" name="types[<?php echo e($type); ?>][max_renewals]" min="0"
                        value="<?php echo e($setting->max_renewals); ?>" class="input">
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <button type="submit" class="btn btn-primary w-full">Save Library Settings</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\settings.blade.php ENDPATH**/ ?>