<?php $__env->startSection('title', 'Teacher-wise Timetable'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Teacher Timetable</h1>
            <p class="page-subtitle">View period schedule for individual teachers</p>
        </div>
        <a href="<?php echo e(route('academics.timetable')); ?>" class="btn btn-secondary">Class Timetable</a>
    </div>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="label">Select Teacher</label>
                <select name="teacher_id" class="select" required>
                    <option value="">Choose Teacher</option>
                    <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($t->id); ?>" <?php if(request('teacher_id') == $t->id): echo 'selected'; endif; ?>>
                            <?php echo e($t->first_name); ?> <?php echo e($t->last_name); ?> (<?php echo e($t->employee_number); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">View Timetable</button>
        </form>
    </div>

    <?php if($teacher): ?>
    <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">
            <?php echo e($teacher->first_name); ?> <?php echo e($teacher->last_name); ?> — Weekly Schedule
        </h3>
        <?php if($timetable->isEmpty()): ?>
            <p class="text-slate-400 text-sm text-center py-6">No timetable entries found for this teacher.</p>
        <?php else: ?>
        <?php $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; ?>
        <div class="space-y-4">
            <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayNum => $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $periods = $timetable[$dayNum + 1] ?? collect(); ?>
            <?php if($periods->isNotEmpty()): ?>
            <div>
                <h4 class="text-sm font-semibold text-indigo-600 mb-2"><?php echo e($dayName); ?></h4>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg px-3 py-2 text-sm">
                        <p class="font-medium text-slate-800"><?php echo e($p->subject?->name ?? '—'); ?></p>
                        <p class="text-xs text-slate-500">Period <?php echo e($p->period_number); ?></p>
                        <p class="text-xs text-indigo-600">
                            <?php echo e($p->class?->name ?? ''); ?> <?php echo e($p->section?->name ?? ''); ?>

                        </p>
                        <?php if($p->start_time && $p->end_time): ?>
                        <p class="text-xs text-slate-400"><?php echo e(\Carbon\Carbon::parse($p->start_time)->format('h:i A')); ?> – <?php echo e(\Carbon\Carbon::parse($p->end_time)->format('h:i A')); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="mt-6 pt-4 border-t border-slate-100">
            <p class="text-sm text-slate-500">
                <strong>Total periods/week:</strong> <?php echo e($timetable->flatten()->count()); ?>

            </p>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\teacher-timetable.blade.php ENDPATH**/ ?>