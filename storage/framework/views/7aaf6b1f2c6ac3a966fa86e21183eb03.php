<?php $__env->startSection('title', 'Student Result History'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ search: '<?php echo e(request('search')); ?>' }">
    <h1 class="page-title">Student-wise Result History</h1>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-64">
                <label class="label">Search Student</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                    placeholder="Name, admission number…" class="input w-full">
            </div>
            <button class="btn-primary">Search</button>
        </form>

        <?php if(isset($students) && $students->count() > 0 && !$student): ?>
        <div class="mt-4 divide-y border rounded-lg overflow-hidden">
            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('examinations.student-result-history', ['student_id' => $s->id])); ?>"
               class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition">
                <div>
                    <div class="font-medium text-gray-800"><?php echo e($s->full_name); ?></div>
                    <div class="text-sm text-gray-500">Adm# <?php echo e($s->admission_number); ?></div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>

    <?php if($student): ?>
    <div class="card bg-blue-50 border-blue-200">
        <div class="font-semibold text-blue-900 text-lg"><?php echo e($student->full_name); ?></div>
        <div class="text-sm text-blue-700">Admission No: <?php echo e($student->admission_number); ?></div>
    </div>

    <?php if($history->count()): ?>
    <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card p-0 overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b flex justify-between items-center">
            <div>
                <span class="font-semibold text-gray-800"><?php echo e($item['exam']->name); ?></span>
                <span class="ml-2 text-sm text-gray-500"><?php echo e($item['exam']->start_date?->format('M Y')); ?></span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold"><?php echo e($item['total']); ?>/<?php echo e($item['max']); ?> (<?php echo e($item['pct']); ?>%)</span>
                <span class="badge-<?php echo e($item['passed'] ? 'success' : 'danger'); ?>">
                    <?php echo e($item['passed'] ? 'PASS' : 'FAIL'); ?>

                </span>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Subject</th>
                        <th class="th text-center">Max</th>
                        <th class="th text-center">Pass</th>
                        <th class="th text-center">Obtained</th>
                        <th class="th text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $item['marks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="tr">
                        <td class="td"><?php echo e($m->examSchedule?->subject?->name); ?></td>
                        <td class="td text-center"><?php echo e($m->examSchedule?->max_marks); ?></td>
                        <td class="td text-center"><?php echo e($m->examSchedule?->pass_marks); ?></td>
                        <td class="td text-center font-semibold">
                            <?php if($m->is_absent): ?> <span class="badge-warning">ABS</span>
                            <?php else: ?> <?php echo e($m->marks_obtained ?? '—'); ?>

                            <?php endif; ?>
                        </td>
                        <td class="td text-center">
                            <?php if(!$m->is_absent && $m->marks_obtained !== null): ?>
                                <?php if($m->marks_obtained >= $m->examSchedule?->pass_marks): ?>
                                    <span class="badge-success">P</span>
                                <?php else: ?>
                                    <span class="badge-danger">F</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
    <div class="alert-info">No exam results found for this student.</div>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\student-result-history.blade.php ENDPATH**/ ?>