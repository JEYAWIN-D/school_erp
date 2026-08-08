<?php $__env->startSection('title', 'Vehicle Utilisation Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <h1 class="page-title">Vehicle Utilisation Report</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php
            $totalCapacity = $data->sum('capacity');
            $totalAssigned = $data->sum('assigned');
            $overallPct    = $totalCapacity > 0 ? round(($totalAssigned / $totalCapacity) * 100, 1) : 0;
        ?>
        <div class="card text-center">
            <div class="text-sm text-gray-500">Total Capacity</div>
            <div class="text-3xl font-bold text-gray-800"><?php echo e($totalCapacity); ?></div>
        </div>
        <div class="card text-center">
            <div class="text-sm text-gray-500">Students Assigned</div>
            <div class="text-3xl font-bold text-blue-700"><?php echo e($totalAssigned); ?></div>
        </div>
        <div class="card text-center">
            <div class="text-sm text-gray-500">Overall Utilisation</div>
            <div class="text-3xl font-bold <?php echo e($overallPct >= 90 ? 'text-red-600' : ($overallPct >= 70 ? 'text-yellow-600' : 'text-green-600')); ?>">
                <?php echo e($overallPct); ?>%
            </div>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Vehicle</th>
                        <th class="th">Type</th>
                        <th class="th">Route</th>
                        <th class="th text-center">Capacity</th>
                        <th class="th text-center">Assigned</th>
                        <th class="th text-center">Available</th>
                        <th class="th">Utilisation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $color = $row['utilisation'] >= 90 ? 'red' : ($row['utilisation'] >= 70 ? 'yellow' : 'green'); ?>
                    <tr class="tr">
                        <td class="td font-medium"><?php echo e($row['vehicle']->vehicle_number); ?></td>
                        <td class="td"><?php echo e($row['vehicle']->vehicle_type ?? '—'); ?></td>
                        <td class="td text-sm"><?php echo e($row['vehicle']->route?->route_name ?? 'Unassigned'); ?></td>
                        <td class="td text-center"><?php echo e($row['capacity']); ?></td>
                        <td class="td text-center font-semibold"><?php echo e($row['assigned']); ?></td>
                        <td class="td text-center <?php echo e($row['available'] == 0 ? 'text-red-600' : 'text-green-600'); ?>">
                            <?php echo e($row['available']); ?>

                        </td>
                        <td class="td w-48">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full bg-<?php echo e($color); ?>-500"
                                         style="width: <?php echo e(min(100, $row['utilisation'])); ?>%"></div>
                                </div>
                                <span class="text-sm font-semibold text-<?php echo e($color); ?>-700 w-12">
                                    <?php echo e($row['utilisation']); ?>%
                                </span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\vehicle-utilisation.blade.php ENDPATH**/ ?>