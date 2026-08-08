<?php $__env->startSection('title', 'Apply Leave'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="<?php echo e(route('hr.leaves')); ?>" class="btn-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="page-title">Apply Leave</h1>
    </div>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

    <div class="card" x-data="{ halfDay: false, isSick: false }">
        <form method="POST" action="<?php echo e(route('hr.leaves.store')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="space-y-4">
                <div>
                    <label class="label">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" class="select" required>
                        <option value="">Select Employee</option>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id') == $emp->id): echo 'selected'; endif; ?>>
                                <?php echo e($emp->full_name); ?> (<?php echo e($emp->employee_number); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="label">Leave Type <span class="text-red-500">*</span></label>
                    <select name="leave_type_id" class="select" required
                        @change="isSick = $event.target.options[$event.target.selectedIndex].text.toLowerCase().includes('sick') || $event.target.options[$event.target.selectedIndex].text.toLowerCase().includes('medical')">
                        <option value="">Select Type</option>
                        <?php $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($lt->id); ?>" <?php if(old('leave_type_id') == $lt->id): echo 'selected'; endif; ?>><?php echo e($lt->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_half_day" value="1" id="halfDay"
                        class="w-4 h-4 text-indigo-600" x-model="halfDay" <?php if(old('is_half_day')): echo 'checked'; endif; ?>>
                    <label for="halfDay" class="label cursor-pointer">Half Day Leave</label>
                </div>

                <div x-show="halfDay" x-transition>
                    <label class="label">Session</label>
                    <select name="half_day_session" class="select w-48">
                        <option value="morning">Morning</option>
                        <option value="afternoon">Afternoon</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">From Date <span class="text-red-500">*</span></label>
                        <input name="from_date" type="date" class="input" value="<?php echo e(old('from_date')); ?>" required>
                    </div>
                    <div x-show="!halfDay">
                        <label class="label">To Date</label>
                        <input name="to_date" type="date" class="input" value="<?php echo e(old('to_date')); ?>"
                            :required="!halfDay" x-bind:value="halfDay ? $el.closest('form').querySelector('[name=from_date]').value : ''">
                    </div>
                </div>

                <div>
                    <label class="label">Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" class="input" required><?php echo e(old('reason')); ?></textarea>
                </div>

                <div>
                    <label class="label">
                        Medical Certificate / Supporting Document
                        <span x-show="isSick" class="text-red-500">*</span>
                        <span x-show="!isSick" class="text-slate-400 text-xs font-normal">(optional)</span>
                    </label>
                    <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
                        :required="isSick"
                        class="block w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-lg p-1">
                    <p class="text-xs text-slate-400 mt-1">PDF, JPG or PNG; max 2 MB. Required for Sick/Medical Leave.</p>
                    <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <?php if($errors->any()): ?>
                <div class="alert-error">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary flex-1">Submit Application</button>
                    <a href="<?php echo e(route('hr.leaves')); ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\leave-apply.blade.php ENDPATH**/ ?>