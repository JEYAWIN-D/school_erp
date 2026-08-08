<?php $__env->startSection('title', 'Hostel Floor Master'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showForm: false }">
    <div class="flex items-center justify-between">
        <h1 class="page-title">Hostel Floor Master</h1>
        <button @click="showForm = !showForm" class="btn btn-primary btn-sm">+ Add Floor</button>
    </div>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-error"><?php echo e(session('error')); ?></div><?php endif; ?>

    
    <div x-show="showForm" x-transition class="card">
        <h2 class="font-semibold text-slate-700 mb-4">New Floor</h2>
        <form method="POST" action="<?php echo e(route('hostel.floors.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="label">Hostel</label>
                    <select name="hostel_id" class="select" required>
                        <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($h->id); ?>"><?php echo e($h->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="label">Floor Name</label>
                    <input name="name" class="input" placeholder="Ground Floor / Floor 1" required>
                </div>
                <div>
                    <label class="label">Floor Number</label>
                    <input name="floor_number" type="number" min="0" value="0" class="input w-24">
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Add Floor</button>
                <button type="button" @click="showForm = false" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>

    
    <form method="GET" class="card py-3">
        <select name="hostel_id" class="select w-52" onchange="this.form.submit()">
            <option value="">All Hostels</option>
            <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($h->id); ?>" <?php if(request('hostel_id') == $h->id): echo 'selected'; endif; ?>><?php echo e($h->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </form>

    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead><tr>
                    <th class="th">Hostel</th>
                    <th class="th">Floor Name</th>
                    <th class="th text-center">Floor No.</th>
                    <th class="th text-center">Rooms</th>
                    <th class="th text-center">Actions</th>
                </tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $floors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $floor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="tr">
                        <td class="td text-slate-500"><?php echo e($floor->hostel?->name); ?></td>
                        <td class="td font-medium"><?php echo e($floor->name); ?></td>
                        <td class="td text-center"><?php echo e($floor->floor_number); ?></td>
                        <td class="td text-center"><?php echo e($floor->rooms->count()); ?></td>
                        <td class="td text-center">
                            <form method="POST" action="<?php echo e(route('hostel.floors.delete', $floor->id)); ?>"
                                  onsubmit="return confirm('Delete this floor?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="text-xs text-red-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="td text-center text-slate-400 py-8">No floors defined yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo e($floors->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\floors.blade.php ENDPATH**/ ?>