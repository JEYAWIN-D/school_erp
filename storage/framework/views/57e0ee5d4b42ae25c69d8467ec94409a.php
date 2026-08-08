<?php $__env->startSection('title', 'Room Transfer'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <h1 class="page-title">Room Transfer</h1>

    <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-error"><?php echo e(session('error')); ?></div><?php endif; ?>

    
    <form method="GET" class="card py-3">
        <select name="hostel_id" class="select w-52" onchange="this.form.submit()">
            <option value="">All Hostels</option>
            <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($h->id); ?>" <?php if(request('hostel_id') == $h->id): echo 'selected'; endif; ?>><?php echo e($h->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </form>

    <div class="card p-0 overflow-hidden">
        <div class="p-3 bg-slate-50 border-b border-slate-100">
            <p class="font-semibold text-slate-700">Current Allotments — click Transfer to move a student</p>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr>
                    <th class="th">Student</th>
                    <th class="th">Current Room</th>
                    <th class="th">Hostel</th>
                    <th class="th text-center">Transfer</th>
                </tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $allotments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allotment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="tr" x-data="{ open: false }">
                        <td class="td">
                            <div class="font-medium text-slate-800"><?php echo e($allotment->student?->full_name); ?></div>
                            <div class="text-xs text-slate-400"><?php echo e($allotment->student?->admission_number); ?></div>
                        </td>
                        <td class="td"><?php echo e($allotment->room?->room_number); ?></td>
                        <td class="td text-slate-500"><?php echo e($allotment->room?->hostel?->name); ?></td>
                        <td class="td text-center">
                            <button @click="open = !open" class="btn btn-ghost btn-sm text-blue-600">Transfer</button>
                            <div x-show="open" x-transition class="mt-3 p-3 bg-slate-50 rounded-lg border border-slate-200 text-left">
                                <form method="POST" action="<?php echo e(route('hostel.transfer.process')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="allotment_id" value="<?php echo e($allotment->id); ?>">
                                    <div class="space-y-2">
                                        <div>
                                            <label class="label text-xs">New Room</label>
                                            <select name="new_room_id" class="select text-sm" required>
                                                <option value="">Select Room</option>
                                                <?php $__currentLoopData = $availableRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($room->id !== $allotment->room_id): ?>
                                                    <option value="<?php echo e($room->id); ?>"><?php echo e($room->hostel?->name); ?> — Room <?php echo e($room->room_number); ?></option>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="label text-xs">Reason</label>
                                            <input name="transfer_reason" class="input text-sm" placeholder="Optional reason">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm w-full">Confirm Transfer</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="td text-center text-slate-400 py-8">No active allotments found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo e($allotments->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\room-transfer.blade.php ENDPATH**/ ?>