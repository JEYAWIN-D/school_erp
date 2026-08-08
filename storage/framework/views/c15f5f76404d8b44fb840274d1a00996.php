<?php $__env->startSection('title', 'Marks Import'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">
    <h1 class="page-title">Import Marks via Excel</h1>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-error"><?php echo e(session('error')); ?></div><?php endif; ?>

    <div class="card">
        <h2 class="font-semibold text-slate-700 mb-4">Upload Marks File</h2>
        <form method="POST" action="<?php echo e(route('examinations.marks-import.process')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="space-y-4">
                <div>
                    <label class="label">Exam</label>
                    <select name="exam_id" class="select" required>
                        <option value="">Select Exam</option>
                        <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($exam->id); ?>"><?php echo e($exam->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="label">Class</label>
                    <select name="class_id" class="select" required>
                        <option value="">Select Class</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cls->id); ?>"><?php echo e($cls->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="label">Excel File (.xlsx / .csv)</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="input" required>
                </div>
                <button type="submit" class="btn btn-primary w-full">Import Marks</button>
            </div>
        </form>
    </div>

    <div class="card bg-blue-50 border border-blue-200">
        <h3 class="font-semibold text-blue-800 mb-2">Excel Format Instructions</h3>
        <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
            <li>Row 1: Headers — <code>Admission No</code>, then one column per subject name (e.g. <code>Mathematics</code>, <code>Science</code>)</li>
            <li>Row 2 onwards: Admission number in column A, marks per subject in subsequent columns</li>
            <li>Leave a cell blank (or enter <code>AB</code>) to mark as Absent</li>
            <li>Subject names must exactly match those in the exam schedule</li>
            <li>Maximum file size: 2 MB</li>
        </ul>
        <div class="mt-3 overflow-x-auto">
            <table class="text-xs border-collapse border border-blue-300">
                <tr>
                    <th class="border border-blue-300 px-2 py-1 bg-blue-100">Admission No</th>
                    <th class="border border-blue-300 px-2 py-1 bg-blue-100">Mathematics</th>
                    <th class="border border-blue-300 px-2 py-1 bg-blue-100">Science</th>
                    <th class="border border-blue-300 px-2 py-1 bg-blue-100">English</th>
                </tr>
                <tr>
                    <td class="border border-blue-300 px-2 py-1">ADM001</td>
                    <td class="border border-blue-300 px-2 py-1">78</td>
                    <td class="border border-blue-300 px-2 py-1">85</td>
                    <td class="border border-blue-300 px-2 py-1">AB</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\marks-import.blade.php ENDPATH**/ ?>