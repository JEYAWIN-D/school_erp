<?php $__env->startSection('title', 'Fine Outstanding'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="<?php echo e(route('library.members')); ?>" class="btn-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="page-title">Fine Outstanding — <?php echo e($student->full_name); ?></h1>
    </div>

    <div class="card flex items-center gap-4 bg-red-50 border border-red-200">
        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-red-600">₹<?php echo e(number_format($totalFine, 2)); ?></p>
            <p class="text-sm text-red-400">Total unpaid fine</p>
        </div>
    </div>

    <?php $__empty_1 = true; $__currentLoopData = $issues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card">
        <div class="flex justify-between items-start">
            <div>
                <p class="font-semibold text-slate-800"><?php echo e($issue->book?->title); ?></p>
                <p class="text-sm text-slate-400"><?php echo e($issue->book?->author); ?></p>
            </div>
            <span class="text-red-600 font-bold">₹<?php echo e(number_format($issue->fine_amount, 2)); ?></span>
        </div>
        <div class="mt-2 flex gap-4 text-xs text-slate-500">
            <span>Due: <?php echo e($issue->due_date?->format('d M Y')); ?></span>
            <span>Returned: <?php echo e($issue->return_date?->format('d M Y') ?? '—'); ?></span>
        </div>
        <div class="mt-3">
            <form method="POST" action="<?php echo e(route('library.fine.waive', $issue->id)); ?>" class="inline"
                  onsubmit="return confirm('Waive this fine?')">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="reason" value="Member request">
                <button class="btn btn-ghost btn-sm text-blue-600">Waive Fine</button>
            </form>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="card text-center py-8 text-slate-400">No outstanding fines for this member.</div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\member-outstanding.blade.php ENDPATH**/ ?>