<?php $__env->startSection('title', 'Attendance Condonation'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <h1 class="page-title">Attendance Condonation</h1>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-error"><?php echo e(session('error')); ?></div><?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1">
            <div class="card">
                <h2 class="font-semibold text-slate-700 mb-4">Grant Condonation</h2>
                <form method="POST" action="<?php echo e(route('attendance.condonation.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="space-y-4">
                        <div>
                            <label class="label">Class</label>
                            <select name="class_id" class="select" onchange="this.form.submit()">
                                <option value="">Select Class</option>
                                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cls->id); ?>" <?php if(request('class_id') == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <?php if($students->count()): ?>
                        <div>
                            <label class="label">Student</label>
                            <select name="student_id" class="select" required>
                                <option value="">Select Student</option>
                                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($enrollment->student?->id); ?>"><?php echo e($enrollment->student?->full_name); ?> (<?php echo e($enrollment->student?->admission_number); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div>
                            <label class="label">Days to Condone</label>
                            <input name="days_condoned" type="number" min="1" max="365" class="input" required>
                        </div>
                        <div>
                            <label class="label">Reason / Justification</label>
                            <textarea name="reason" rows="3" class="input" required placeholder="Medical grounds, principal's discretion..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-full">Grant Condonation</button>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="lg:col-span-2">
            <div class="card p-0 overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-100">
                    <p class="font-semibold text-slate-700">Recent Condonations — <?php echo e($currentYear?->name); ?></p>
                </div>
                <?php if($recentCondonations->count()): ?>
                <div class="table-wrap">
                    <table>
                        <thead><tr>
                            <th class="th">Student</th>
                            <th class="th text-center">Days</th>
                            <th class="th">Reason</th>
                            <th class="th">Condoned By</th>
                            <th class="th">Date</th>
                        </tr></thead>
                        <tbody>
                            <?php $__currentLoopData = $recentCondonations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="tr">
                                <td class="td">
                                    <div class="font-medium text-slate-800"><?php echo e($c->student?->full_name); ?></div>
                                    <div class="text-xs text-slate-400"><?php echo e($c->student?->admission_number); ?></div>
                                </td>
                                <td class="td text-center">
                                    <span class="badge-blue font-semibold"><?php echo e($c->days_condoned); ?></span>
                                </td>
                                <td class="td text-sm text-slate-600"><?php echo e(Str::limit($c->reason, 60)); ?></td>
                                <td class="td text-sm"><?php echo e($c->condonedBy?->name); ?></td>
                                <td class="td text-sm"><?php echo e($c->condoned_on?->format('d M Y')); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-8 text-center text-slate-400">No condonations recorded yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\condonation.blade.php ENDPATH**/ ?>