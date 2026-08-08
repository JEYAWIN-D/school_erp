<?php $__env->startSection('title', 'Marks Entry Progress'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Marks Entry Progress</h1>
            <p class="page-subtitle">Track % of marks entered per subject per class</p>
        </div>
    </div>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="label">Exam</label>
                <select name="exam_id" class="select" required>
                    <option value="">Select Exam</option>
                    <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($exam->id); ?>" <?php if(request('exam_id') == $exam->id): echo 'selected'; endif; ?>><?php echo e($exam->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">View Progress</button>
        </form>
    </div>

    <?php if(request('exam_id')): ?>
    <?php if($data->isEmpty()): ?>
        <div class="card text-center py-8 text-slate-400">No exam schedules found for this exam.</div>
    <?php else: ?>
    <div class="card">
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Class</th>
                    <th class="th">Subject</th>
                    <th class="th">Exam Date</th>
                    <th class="th text-center">Students</th>
                    <th class="th text-center">Marks Entered</th>
                    <th class="th" style="min-width:200px">Progress</th>
                </tr></thead>
                <tbody>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="tr">
                        <td class="td font-medium"><?php echo e($row['class']); ?></td>
                        <td class="td"><?php echo e($row['subject']); ?></td>
                        <td class="td text-slate-500"><?php echo e($row['date'] ? \Carbon\Carbon::parse($row['date'])->format('d M Y') : '—'); ?></td>
                        <td class="td text-center"><?php echo e($row['total']); ?></td>
                        <td class="td text-center"><?php echo e($row['entered']); ?></td>
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full <?php echo e($row['pct'] >= 100 ? 'bg-green-500' : ($row['pct'] >= 50 ? 'bg-amber-400' : 'bg-red-400')); ?>"
                                         style="width: <?php echo e($row['pct']); ?>%"></div>
                                </div>
                                <span class="text-sm font-semibold <?php echo e($row['pct'] >= 100 ? 'text-green-600' : ($row['pct'] >= 50 ? 'text-amber-600' : 'text-red-500')); ?>">
                                    <?php echo e($row['pct']); ?>%
                                </span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\marks-progress.blade.php ENDPATH**/ ?>