<?php $__env->startSection('title', 'Bus Attendants'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showForm: false, editId: null, editData: {} }">
    <div class="flex justify-between items-center">
        <h1 class="page-title">Bus Attendants</h1>
        <button @click="showForm = !showForm" class="btn-primary">+ Add Attendant</button>
    </div>

    <div x-show="showForm" x-transition class="card">
        <h2 class="font-semibold text-gray-700 mb-4">Add New Attendant</h2>
        <form method="POST" action="<?php echo e(route('transport.attendants.store')); ?>" enctype="multipart/form-data"
              class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="label">Full Name *</label>
                <input type="text" name="name" class="input w-full" required>
            </div>
            <div>
                <label class="label">Mobile</label>
                <input type="text" name="mobile" class="input w-full" maxlength="15">
            </div>
            <div>
                <label class="label">Aadhaar Number</label>
                <input type="text" name="aadhaar" class="input w-full" maxlength="12">
            </div>
            <div>
                <label class="label">Photo</label>
                <input type="file" name="photo" class="input w-full" accept="image/*">
            </div>
            <div>
                <label class="label">Assign to Vehicle</label>
                <select name="vehicle_id" class="select w-full">
                    <option value="">— None —</option>
                    <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($v->id); ?>"><?php echo e($v->vehicle_number); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label">Assign to Route</label>
                <select name="route_id" class="select w-full">
                    <option value="">— None —</option>
                    <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($r->id); ?>"><?php echo e($r->route_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="md:col-span-2 flex gap-3 justify-end">
                <button type="button" @click="showForm = false" class="btn-secondary">Cancel</button>
                <button class="btn-primary">Save Attendant</button>
            </div>
        </form>
    </div>

    <?php if(session('success')): ?>
        <div class="alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="GET" class="flex gap-3 items-end">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search by name…" class="input flex-1">
            <button class="btn-primary">Search</button>
        </form>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Name</th>
                        <th class="th">Mobile</th>
                        <th class="th">Aadhaar</th>
                        <th class="th">Vehicle</th>
                        <th class="th">Route</th>
                        <th class="th text-center">Status</th>
                        <th class="th text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $attendants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="tr">
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <?php if($a->photo): ?>
                                <img src="<?php echo e(asset('storage/' . $a->photo)); ?>" class="w-8 h-8 rounded-full object-cover">
                                <?php else: ?>
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500"><?php echo e(strtoupper(substr($a->name, 0, 1))); ?></div>
                                <?php endif; ?>
                                <span class="font-medium"><?php echo e($a->name); ?></span>
                            </div>
                        </td>
                        <td class="td"><?php echo e($a->mobile ?? '—'); ?></td>
                        <td class="td text-sm text-gray-500"><?php echo e($a->aadhaar ? '****' . substr($a->aadhaar, -4) : '—'); ?></td>
                        <td class="td"><?php echo e($a->vehicle?->vehicle_number ?? '—'); ?></td>
                        <td class="td"><?php echo e($a->route?->route_name ?? '—'); ?></td>
                        <td class="td text-center">
                            <span class="badge-<?php echo e($a->is_active ? 'success' : 'secondary'); ?>">
                                <?php echo e($a->is_active ? 'Active' : 'Inactive'); ?>

                            </span>
                        </td>
                        <td class="td text-center">
                            <form method="POST" action="<?php echo e(route('transport.attendants.delete', $a->id)); ?>"
                                  onsubmit="return confirm('Remove attendant?')" class="inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="text-red-500 text-sm hover:underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="td text-center text-gray-400">No attendants found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo e($attendants->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\attendants.blade.php ENDPATH**/ ?>