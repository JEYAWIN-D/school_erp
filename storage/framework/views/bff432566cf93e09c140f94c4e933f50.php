<?php $__env->startSection('title', 'Warden Duty Roster'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="page-title">Warden Duty Roster</h1>
        <form method="GET" class="flex gap-2">
            <input type="week" name="week" value="<?php echo e($week); ?>" class="input" onchange="this.form.submit()">
        </form>
    </div>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

    <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hostel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-lg bg-slate-700 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75"/></svg>
            </div>
            <div>
                <p class="font-semibold text-slate-800"><?php echo e($hostel->name); ?></p>
                <p class="text-xs text-slate-400 capitalize"><?php echo e($hostel->type); ?></p>
            </div>
        </div>

        <?php if($hostel->wardens?->count()): ?>
        <div class="grid grid-cols-7 gap-2">
            <?php $__currentLoopData = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border border-slate-200 rounded-lg p-2 text-center">
                <p class="text-xs font-semibold text-slate-500 mb-2"><?php echo e($day); ?></p>
                <select name="roster[<?php echo e($hostel->id); ?>][<?php echo e($day); ?>]" class="select text-xs w-full">
                    <option value="">—</option>
                    <?php $__currentLoopData = $hostel->wardens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warden): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($warden->id); ?>"><?php echo e($warden->first_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <p class="text-sm text-slate-400">No wardens assigned to this hostel. <a href="<?php echo e(route('hostel.wardens')); ?>" class="text-blue-600 hover:underline">Assign wardens</a> first.</p>
        <?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php if($hostels->count()): ?>
    <form method="POST" action="<?php echo e(route('hostel.warden-roster.save')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="week" value="<?php echo e($week); ?>">
        <button type="submit" class="btn btn-primary">Save Roster</button>
    </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\warden-roster.blade.php ENDPATH**/ ?>