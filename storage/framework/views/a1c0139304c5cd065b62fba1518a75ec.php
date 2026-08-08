<?php $__env->startSection('title', 'Subject-wise Performance Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <h1 class="page-title">Subject-wise Performance Report</h1>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="label">Exam</label>
                <select name="exam_id" class="select" required>
                    <option value="">— Select Exam —</option>
                    <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($e->id); ?>" <?php if(request('exam_id') == $e->id): echo 'selected'; endif; ?>><?php echo e($e->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label">Class</label>
                <select name="class_id" class="select" required>
                    <option value="">— Select Class —</option>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php if(request('class_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button class="btn-primary">Generate Report</button>
        </form>
    </div>

    <?php if(!empty($data)): ?>
    <div class="card p-0 overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <h2 class="font-semibold text-gray-700"><?php echo e($exam->name ?? ''); ?> — <?php echo e($class->name ?? ''); ?></h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Subject</th>
                        <th class="th text-center">Max Marks</th>
                        <th class="th text-center">Pass Marks</th>
                        <th class="th text-center">Students Appeared</th>
                        <th class="th text-center">Passed</th>
                        <th class="th text-center">Failed</th>
                        <th class="th text-center">Pass %</th>
                        <th class="th text-center">Avg Marks</th>
                        <th class="th text-center">Highest</th>
                        <th class="th text-center">Lowest</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="tr">
                        <td class="td font-medium"><?php echo e($row['subject']); ?></td>
                        <td class="td text-center"><?php echo e($row['max_marks']); ?></td>
                        <td class="td text-center"><?php echo e($row['pass_marks']); ?></td>
                        <td class="td text-center"><?php echo e($row['total']); ?></td>
                        <td class="td text-center text-green-600 font-semibold"><?php echo e($row['passed']); ?></td>
                        <td class="td text-center text-red-600 font-semibold"><?php echo e($row['failed']); ?></td>
                        <td class="td text-center">
                            <span class="inline-flex items-center gap-1">
                                <span class="font-semibold <?php echo e($row['pass_pct'] >= 75 ? 'text-green-600' : ($row['pass_pct'] >= 50 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                    <?php echo e($row['pass_pct']); ?>%
                                </span>
                            </span>
                        </td>
                        <td class="td text-center"><?php echo e($row['avg']); ?></td>
                        <td class="td text-center text-green-700"><?php echo e($row['highest']); ?></td>
                        <td class="td text-center text-red-700"><?php echo e($row['lowest']); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php elseif(request('exam_id')): ?>
    <div class="alert-info">No data found for the selected exam and class.</div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\subject-performance.blade.php ENDPATH**/ ?>