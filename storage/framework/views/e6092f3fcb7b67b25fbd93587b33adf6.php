<?php $__env->startSection('title', 'Subject-wise Attendance Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Subject-wise Attendance</h1>
            <p class="page-subtitle">Period-wise attendance per subject per student</p>
        </div>
    </div>

    <div class="card">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="label">Class</label>
                <select name="class_id" class="select" onchange="this.form.submit()">
                    <option value="">Select Class</option>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cls->id); ?>" <?php if(request('class_id') == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label">Student</label>
                <select name="student_id" class="select">
                    <option value="">Select Student</option>
                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $en): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($en->student_id); ?>" <?php if(request('student_id') == $en->student_id): echo 'selected'; endif; ?>>
                            <?php echo e($en->student?->first_name); ?> <?php echo e($en->student?->last_name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label">From Date</label>
                <input type="date" name="from_date" class="input" value="<?php echo e(request('from_date')); ?>">
            </div>
            <div>
                <label class="label">To Date</label>
                <input type="date" name="to_date" class="input" value="<?php echo e(request('to_date')); ?>">
            </div>
            <div class="md:col-span-4">
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </div>
        </form>
    </div>

    <?php if($student): ?>
    <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">
            <?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?>

            <span class="text-slate-400 font-normal text-sm ml-2"><?php echo e($student->admission_number); ?></span>
        </h3>
        <?php if($report->isEmpty()): ?>
            <p class="text-slate-400 text-sm text-center py-8">No period-wise attendance data found for the selected range.</p>
        <?php else: ?>
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Subject</th>
                    <th class="th text-center">Total Periods</th>
                    <th class="th text-center">Present</th>
                    <th class="th text-center">Absent</th>
                    <th class="th text-center">Attendance %</th>
                    <th class="th text-center">Status</th>
                </tr></thead>
                <tbody>
                    <?php $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="tr">
                        <td class="td font-medium"><?php echo e($row['subject']); ?></td>
                        <td class="td text-center"><?php echo e($row['total']); ?></td>
                        <td class="td text-center text-green-600"><?php echo e($row['present']); ?></td>
                        <td class="td text-center text-red-500"><?php echo e($row['absent']); ?></td>
                        <td class="td text-center font-semibold <?php echo e($row['percentage'] < 75 ? 'text-red-600' : 'text-green-600'); ?>">
                            <?php echo e($row['percentage']); ?>%
                        </td>
                        <td class="td text-center">
                            <?php if($row['percentage'] >= 75): ?>
                                <span class="badge badge-success">OK</span>
                            <?php elseif($row['percentage'] >= 60): ?>
                                <span class="badge badge-warning">Low</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Shortage</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php elseif(request('class_id')): ?>
        <div class="card text-center py-8 text-slate-400">Select a student to view subject-wise report.</div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\subject-report.blade.php ENDPATH**/ ?>