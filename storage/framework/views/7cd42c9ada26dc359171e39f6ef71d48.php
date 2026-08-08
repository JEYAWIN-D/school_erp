<?php $__env->startSection('title', 'Teacher Workload Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Teacher Workload Report</h1>
            <p class="page-subtitle">Periods per week per teacher — max limit: <?php echo e($maxPerWeek); ?> periods</p>
        </div>
        <a href="<?php echo e(route('academics.timetable')); ?>" class="btn btn-secondary">Class Timetable</a>
    </div>

    <?php $overAllocated = $workload->where('over', true)->count(); ?>
    <?php if($overAllocated > 0): ?>
    <div class="alert-warning">
        <?php echo e($overAllocated); ?> teacher(s) exceed the maximum workload of <?php echo e($maxPerWeek); ?> periods/week.
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Teacher</th>
                    <th class="th">Employee No.</th>
                    <th class="th text-center">Periods / Week</th>
                    <th class="th text-center">Max Allowed</th>
                    <th class="th" style="min-width:180px">Load</th>
                    <th class="th text-center">Status</th>
                </tr></thead>
                <tbody>
                    <?php $__currentLoopData = $workload; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="tr <?php echo e($row['over'] ? 'bg-red-50' : ''); ?>">
                        <td class="td font-medium"><?php echo e($row['teacher']->first_name); ?> <?php echo e($row['teacher']->last_name); ?></td>
                        <td class="td text-slate-500"><?php echo e($row['teacher']->employee_number); ?></td>
                        <td class="td text-center font-semibold <?php echo e($row['over'] ? 'text-red-600' : ''); ?>"><?php echo e($row['periods']); ?></td>
                        <td class="td text-center text-slate-400"><?php echo e($row['max']); ?></td>
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full <?php echo e($row['over'] ? 'bg-red-500' : ($row['pct'] >= 80 ? 'bg-amber-400' : 'bg-green-500')); ?>"
                                         style="width: <?php echo e(min(100, $row['pct'])); ?>%"></div>
                                </div>
                                <span class="text-xs text-slate-500"><?php echo e($row['pct']); ?>%</span>
                            </div>
                        </td>
                        <td class="td text-center">
                            <?php if($row['over']): ?>
                                <span class="badge badge-danger">Over-allocated</span>
                            <?php elseif($row['pct'] >= 80): ?>
                                <span class="badge badge-warning">Near Limit</span>
                            <?php elseif($row['periods'] == 0): ?>
                                <span class="badge badge-secondary">Unassigned</span>
                            <?php else: ?>
                                <span class="badge badge-success">OK</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card text-sm text-slate-500">
        <p>Max periods/week is configured in <a href="<?php echo e(route('settings.index')); ?>" class="text-indigo-600 hover:underline">School Settings</a> → <code>teacher_max_periods_per_week</code> (default: 30).</p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\teacher-workload.blade.php ENDPATH**/ ?>