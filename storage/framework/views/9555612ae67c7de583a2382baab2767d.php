<?php $__env->startSection('title', 'Terms & Semesters'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showForm: false, editId: null, editData: {} }">
    <div class="flex items-center justify-between">
        <h1 class="page-title">Terms & Semesters</h1>
        <button @click="showForm = !showForm" class="btn btn-primary btn-sm">+ Add Term</button>
    </div>

    
    <form method="GET" class="card py-3">
        <div class="flex gap-3 items-center">
            <select name="academic_year_id" class="select w-52" onchange="this.form.submit()">
                <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($yr->id); ?>" <?php if($selectedYear?->id == $yr->id): echo 'selected'; endif; ?>><?php echo e($yr->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </form>

    
    <div x-show="showForm" x-transition class="card">
        <h2 class="font-semibold text-slate-700 mb-4">New Term / Semester</h2>
        <form method="POST" action="<?php echo e(route('academics.terms.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="academic_year_id" value="<?php echo e($selectedYear?->id); ?>">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="label">Name</label>
                    <input name="name" class="input" placeholder="Term 1 / Semester 1" required>
                </div>
                <div>
                    <label class="label">Type</label>
                    <select name="type" class="select">
                        <option value="term">Term</option>
                        <option value="semester">Semester</option>
                        <option value="quarter">Quarter</option>
                    </select>
                </div>
                <div>
                    <label class="label">Order</label>
                    <input name="order_position" type="number" min="1" value="1" class="input w-24">
                </div>
                <div>
                    <label class="label">Start Date</label>
                    <input name="start_date" type="date" class="input" required>
                </div>
                <div>
                    <label class="label">End Date</label>
                    <input name="end_date" type="date" class="input" required>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" @click="showForm = false" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-error"><?php echo e(session('error')); ?></div><?php endif; ?>

    <?php $__empty_1 = true; $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card flex items-center justify-between">
        <div>
            <p class="font-semibold text-slate-800"><?php echo e($term->name); ?></p>
            <p class="text-sm text-slate-500 capitalize"><?php echo e($term->type); ?> &bull; Order <?php echo e($term->order_position); ?></p>
            <p class="text-xs text-slate-400 mt-1"><?php echo e($term->start_date->format('d M Y')); ?> — <?php echo e($term->end_date->format('d M Y')); ?></p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="<?php echo e(route('academics.terms.update', $term->id)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <button type="button" x-data @click="$dispatch('open-edit-<?php echo e($term->id); ?>')" class="btn btn-ghost btn-sm text-blue-600">Edit</button>
            </form>
            <form method="POST" action="<?php echo e(route('academics.terms.delete', $term->id)); ?>" onsubmit="return confirm('Delete this term?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="btn btn-ghost btn-sm text-red-500">Delete</button>
            </form>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="card text-center py-10 text-slate-400">No terms defined for <?php echo e($selectedYear?->name ?? 'this year'); ?>. Add one above.</div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\terms.blade.php ENDPATH**/ ?>