<?php $__env->startSection('title', 'Working Days Configuration'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto space-y-6">
    <h1 class="page-title">Working Days Configuration</h1>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-error"><?php echo e(session('error')); ?></div><?php endif; ?>

    <div class="card">
        <p class="text-sm text-slate-500 mb-4">Select which days of the week are school working days.</p>
        <form method="POST" action="<?php echo e(route('academics.working-days.save')); ?>">
            <?php echo csrf_field(); ?>
            <?php
                $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                $active = $config['days'] ?? ['Monday','Tuesday','Wednesday','Thursday','Friday'];
            ?>
            <div class="space-y-2 mb-6">
                <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50">
                    <input type="checkbox" name="days[days][]" value="<?php echo e($day); ?>" class="w-4 h-4 text-indigo-600"
                        <?php if(in_array($day, $active)): echo 'checked'; endif; ?>>
                    <span class="font-medium text-slate-700"><?php echo e($day); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="submit" class="btn btn-primary w-full">Save Working Days</button>
        </form>
    </div>

    <div class="card bg-amber-50 border border-amber-200">
        <p class="text-sm text-amber-700"><strong>Note:</strong> This setting is used for attendance calculations, leave counting, and working day reports. Changes apply from the current date.</p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\working-days.blade.php ENDPATH**/ ?>