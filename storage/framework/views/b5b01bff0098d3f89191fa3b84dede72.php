<?php $__env->startSection('title', 'Promotion Eligibility'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Promotion Eligibility</h1>
            <p class="page-subtitle">Identify pass/fail students from exam results</p>
        </div>
        <a href="<?php echo e(route('examinations.index')); ?>" class="btn btn-secondary">Back to Exams</a>
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
            <div>
                <label class="label">Class</label>
                <select name="class_id" class="select" required>
                    <option value="">Select Class</option>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cls->id); ?>" <?php if(request('class_id') == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Check Eligibility</button>
        </form>
    </div>

    <?php if(request('exam_id') && request('class_id')): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="card">
            <div class="flex items-center gap-2 mb-4">
                <span class="badge badge-success">Eligible for Promotion</span>
                <span class="text-sm text-slate-500">(<?php echo e($eligible->count()); ?> students)</span>
            </div>
            <?php if($eligible->isEmpty()): ?>
                <p class="text-slate-400 text-sm py-4 text-center">No students found.</p>
            <?php else: ?>
            <div class="table-wrap">
                <table class="w-full">
                    <thead><tr>
                        <th class="th">Student</th>
                        <th class="th">Admission No.</th>
                    </tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $eligible; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="tr">
                            <td class="td"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                            <td class="td text-slate-500"><?php echo e($student->admission_number); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="<?php echo e(route('students.promotions', ['class_id' => request('class_id'), 'preselect_ids' => $eligible->pluck('id')->implode(',')])); ?>"
                   class="btn btn-primary text-sm">
                    Promote These Students
                </a>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="card">
            <div class="flex items-center gap-2 mb-4">
                <span class="badge badge-danger">Not Eligible (Failed)</span>
                <span class="text-sm text-slate-500">(<?php echo e($ineligible->count()); ?> students)</span>
            </div>
            <?php if($ineligible->isEmpty()): ?>
                <p class="text-slate-400 text-sm py-4 text-center">No failed students.</p>
            <?php else: ?>
            <div class="table-wrap">
                <table class="w-full">
                    <thead><tr>
                        <th class="th">Student</th>
                        <th class="th">Admission No.</th>
                    </tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $ineligible; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="tr">
                            <td class="td"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                            <td class="td text-slate-500"><?php echo e($student->admission_number); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <?php $schemes = \App\Models\ScholarshipScheme::where('is_active',true)->where('criteria_type','merit')->whereNotNull('marks_threshold')->get(); ?>
    <?php if($schemes->isNotEmpty()): ?>
    <div class="card">
        <h3 class="font-semibold text-slate-700 mb-3">Merit Scholarship Auto-Apply</h3>
        <p class="text-sm text-slate-500 mb-4">
            Active schemes:
            <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="badge badge-info"><?php echo e($s->name); ?> ≥<?php echo e($s->marks_threshold); ?>%</span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </p>
        <form method="POST" action="<?php echo e(route('examinations.merit-scholarships')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="exam_id" value="<?php echo e(request('exam_id')); ?>">
            <input type="hidden" name="class_id" value="<?php echo e(request('class_id')); ?>">
            <button type="submit" class="btn btn-primary">Apply Merit Scholarships to Eligible Students</button>
        </form>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\promotion-eligibility.blade.php ENDPATH**/ ?>