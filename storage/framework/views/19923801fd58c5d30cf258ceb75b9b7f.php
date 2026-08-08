<?php $__env->startSection('title', 'Maintenance Complaint Status Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <h1 class="page-title">Maintenance Complaint Status Report</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card text-center border-yellow-200 bg-yellow-50">
            <div class="text-sm text-yellow-700">Open</div>
            <div class="text-3xl font-bold text-yellow-800"><?php echo e($statusCounts['open'] ?? 0); ?></div>
        </div>
        <div class="card text-center border-blue-200 bg-blue-50">
            <div class="text-sm text-blue-700">In Progress</div>
            <div class="text-3xl font-bold text-blue-800"><?php echo e($statusCounts['in_progress'] ?? 0); ?></div>
        </div>
        <div class="card text-center border-green-200 bg-green-50">
            <div class="text-sm text-green-700">Resolved</div>
            <div class="text-3xl font-bold text-green-800"><?php echo e($statusCounts['resolved'] ?? 0); ?></div>
        </div>
    </div>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="label">Hostel Block</label>
                <select name="hostel_id" class="select">
                    <option value="">All Hostels</option>
                    <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($h->id); ?>" <?php if(request('hostel_id') == $h->id): echo 'selected'; endif; ?>><?php echo e($h->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label">Status</label>
                <select name="status" class="select">
                    <option value="">All Statuses</option>
                    <option value="open" <?php if(request('status') == 'open'): echo 'selected'; endif; ?>>Open</option>
                    <option value="in_progress" <?php if(request('status') == 'in_progress'): echo 'selected'; endif; ?>>In Progress</option>
                    <option value="resolved" <?php if(request('status') == 'resolved'): echo 'selected'; endif; ?>>Resolved</option>
                </select>
            </div>
            <div>
                <label class="label">From Date</label>
                <input type="date" name="from_date" value="<?php echo e(request('from_date')); ?>" class="input">
            </div>
            <div>
                <label class="label">To Date</label>
                <input type="date" name="to_date" value="<?php echo e(request('to_date')); ?>" class="input">
            </div>
            <button class="btn-primary">Filter</button>
        </form>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Student</th>
                        <th class="th">Room</th>
                        <th class="th">Category</th>
                        <th class="th">Description</th>
                        <th class="th">Status</th>
                        <th class="th">Resolution</th>
                        <th class="th">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $complaints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="tr">
                        <td class="td">
                            <div class="font-medium"><?php echo e($c->student?->full_name ?? '—'); ?></div>
                        </td>
                        <td class="td text-sm"><?php echo e($c->room?->room_number ?? '—'); ?></td>
                        <td class="td">
                            <span class="badge-secondary"><?php echo e(ucfirst($c->category ?? 'general')); ?></span>
                        </td>
                        <td class="td text-sm text-gray-700 max-w-xs"><?php echo e(Str::limit($c->description, 80)); ?></td>
                        <td class="td">
                            <span class="badge-<?php echo e($c->status === 'resolved' ? 'success' :
                                ($c->status === 'in_progress' ? 'info' : 'warning')); ?>"><?php echo e(ucwords(str_replace('_', ' ', $c->status))); ?></span>
                        </td>
                        <td class="td text-sm text-gray-600"><?php echo e(Str::limit($c->resolution_notes, 60) ?? '—'); ?></td>
                        <td class="td text-sm text-gray-500"><?php echo e($c->created_at->format('d M Y')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="td text-center text-gray-400">No complaints found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo e($complaints->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\complaints-report.blade.php ENDPATH**/ ?>